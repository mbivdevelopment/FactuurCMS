<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Crypt;
use App\Core\Database;
use App\Core\Log;
use App\Core\Mailer;
use App\Models\Document;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;

/** Facturen aanmaken uit diensten (bestellingen en herhaalfacturen) en documenten mailen. */
final class Billing
{
    public static function dueDate(string $date, bool $quote = false): string
    {
        $days = (int) setting($quote ? 'facturatie.offerte_geldig' : 'facturatie.betaaltermijn', $quote ? 30 : 14);

        return date('Y-m-d', (int) strtotime($date . ' +' . $days . ' days'));
    }

    /** Regel voor een dienst over één periode, bv. "Webhosting Pro - site.nl (01-11-2026 t/m 30-11-2026)". */
    public static function serviceLine(Service $service): array
    {
        $from = (string) ($service->VolgendeFactuur ?: date('Y-m-d'));
        $until = Service::addPeriod($from, (string) $service->Periode);
        $label = trim($service->Naam . ($service->Omschrijving ? ' - ' . $service->Omschrijving : ''));
        if ($until !== null) {
            $label .= ' (' . nl_date($from) . ' t/m ' . nl_date(date('Y-m-d', (int) strtotime($until . ' -1 day'))) . ')';
        }

        return ['Omschrijving' => $label, 'Aantal' => 1.0, 'Prijs' => (int) $service->Prijs, 'BtwTarief' => (float) $service->BtwTarief, 'DienstId' => $service->id()];
    }

    /**
     * Eén factuur voor een of meer diensten van dezelfde klant; schuift VolgendeFactuur een periode door.
     * @param list<Service> $services
     * @param list<array<string, mixed>> $extraLines
     */
    public static function invoiceServices(User $customer, array $services, array $extraLines = [], string $subject = ''): Document
    {
        return Database::transaction(static function () use ($customer, $services, $extraLines, $subject): Document {
            $lines = [];
            foreach ($services as $service) {
                $lines[] = self::serviceLine($service);
                $next = Service::addPeriod((string) ($service->VolgendeFactuur ?: date('Y-m-d')), (string) $service->Periode);
                $service->VolgendeFactuur = $next;
                $service->save();
            }
            $doc = Document::create([
                'Soort' => 'factuur',
                'KlantId' => $customer->id(),
                'Status' => 'open',
                'Datum' => date('Y-m-d'),
                'VervalDatum' => self::dueDate(date('Y-m-d')),
                'Onderwerp' => $subject !== '' ? $subject : implode(', ', array_map(static fn (Service $s): string => (string) $s->Naam, $services)),
            ]);
            $doc->syncLines([...$lines, ...$extraLines]);

            return $doc;
        });
    }

    /** Bestelling vanuit de frontend: dienst (in afwachting) + direct een open factuur. */
    /** Termijnen met korting voor terugkerende diensten: sleutel => [maanden, korting%]. */
    public const TERMIJNEN = ['maand' => [1, 0], 'kwartaal' => [3, 0], 'jaar' => [12, 0], '2jaar' => [24, 10], '4jaar' => [48, 25]];

    /** Prijs voor een termijn op basis van de maandprijs van het product. */
    public static function termijnPrijs(Product $product, string $termijn): int
    {
        [$months, $discount] = self::TERMIJNEN[$termijn] ?? [1, 0];
        $base = ['maand' => 1, 'kwartaal' => 3, 'jaar' => 12, '2jaar' => 24, '4jaar' => 48][(string) $product->Periode] ?? 1;
        $monthly = (int) $product->Prijs / $base;

        return (int) round($monthly * $months * (100 - $discount) / 100);
    }

    /** @param array{domein: string, product: Product}|null $domain te registreren domeinnaam */
    public static function order(User $customer, Product $product, string $note, string $termijn = '', ?array $domain = null, ?array $gegevens = null): Document
    {
        $periode = isset(self::TERMIJNEN[$termijn]) && $product->Periode !== 'eenmalig' ? $termijn : (string) $product->Periode;
        $service = Service::create([
            'KlantId' => $customer->id(),
            'ProductId' => $product->id(),
            'Naam' => $product->Naam,
            'Omschrijving' => $note !== '' ? mb_substr($note, 0, 255) : null,
            'Prijs' => $periode === (string) $product->Periode ? (int) $product->Prijs : self::termijnPrijs($product, $periode),
            'BtwTarief' => (float) $product->BtwTarief,
            'Periode' => $periode,
            'Status' => 'in_afwachting',
            'StartDatum' => date('Y-m-d'),
            'VolgendeFactuur' => date('Y-m-d'),
            'Gegevens' => $gegevens ?? [],
        ]);
        $services = [$service];
        if ($domain !== null) {
            $ext = $domain['product'];
            $services[] = Service::create([
                'KlantId' => $customer->id(),
                'ProductId' => $ext->id(),
                'Naam' => $domain['domein'],
                'Omschrijving' => ($domain['actie'] ?? 'registreren') === 'verhuizen' ? 'Verhuizing' : 'Registratie',
                'Prijs' => (int) $ext->Prijs,
                'BtwTarief' => (float) $ext->BtwTarief,
                'Periode' => $ext->Periode === 'eenmalig' ? 'jaar' : $ext->Periode,
                'Status' => 'in_afwachting',
                'StartDatum' => date('Y-m-d'),
                'VolgendeFactuur' => date('Y-m-d'),
                'Gegevens' => ['domein' => array_filter(['naam' => $domain['domein'], 'actie' => $domain['actie'] ?? 'registreren', 'authcode' => ($domain['authcode'] ?? '') !== '' ? \App\Core\Crypt::encrypt((string) $domain['authcode']) : null], static fn ($v): bool => $v !== null)],
            ]);
        }
        $extra = (int) $product->Instelkosten > 0
            ? [['Omschrijving' => 'Eenmalige instelkosten ' . $product->Naam, 'Aantal' => 1.0, 'Prijs' => (int) $product->Instelkosten, 'BtwTarief' => (float) $product->BtwTarief]]
            : [];
        $doc = self::invoiceServices($customer, $services, $extra, 'Bestelling ' . $product->Naam);
        if ($periode === 'eenmalig') {
            $service->VolgendeFactuur = null;
            $service->save();
        }
        Log::db('bestelling', $customer->Email . ' · ' . $product->Naam, $customer->id());

        return $doc;
    }

    /** Bestelling van domeinnamen (registreren of verhuizen): per domein een dienst, samen op één factuur.
     * @param list<array{domein: string, actie: string, product: Product, authcode: string}> $items
     * @param list<string> $nameservers eigen nameservers; leeg = de onze */
    public static function orderDomains(User $customer, array $items, ?Product $hosting = null, array $nameservers = []): Document
    {
        $services = [];
        $extra = [];
        foreach ($items as $item) {
            $product = $item['product'];
            $services[] = Service::create([
                'KlantId' => $customer->id(),
                'ProductId' => $product->id(),
                'Naam' => $item['domein'],
                'Omschrijving' => $item['actie'] === 'verhuizen' ? 'Verhuizing' : 'Registratie',
                'Prijs' => (int) $product->Prijs,
                'BtwTarief' => (float) $product->BtwTarief,
                'Periode' => $product->Periode === 'eenmalig' ? 'jaar' : $product->Periode,
                'Status' => 'in_afwachting',
                'StartDatum' => date('Y-m-d'),
                'VolgendeFactuur' => date('Y-m-d'),
                'Gegevens' => ['domein' => array_filter(['naam' => $item['domein'], 'actie' => $item['actie'], 'authcode' => $item['authcode'] !== '' ? Crypt::encrypt($item['authcode']) : null, 'ns' => $nameservers !== [] ? array_values($nameservers) : null], static fn ($v): bool => $v !== null)],
            ]);
        }
        if ($hosting !== null) {
            $services[] = Service::create([
                'KlantId' => $customer->id(),
                'ProductId' => $hosting->id(),
                'Naam' => $hosting->Naam,
                'Omschrijving' => (string) ($items[0]['domein'] ?? ''),
                'Prijs' => (int) $hosting->Prijs,
                'BtwTarief' => (float) $hosting->BtwTarief,
                'Periode' => $hosting->Periode,
                'Status' => 'in_afwachting',
                'StartDatum' => date('Y-m-d'),
                'VolgendeFactuur' => date('Y-m-d'),
            ]);
            if ((int) $hosting->Instelkosten > 0) {
                $extra[] = ['Omschrijving' => 'Eenmalige instelkosten ' . $hosting->Naam, 'Aantal' => 1.0, 'Prijs' => (int) $hosting->Instelkosten, 'BtwTarief' => (float) $hosting->BtwTarief];
            }
        }
        $doc = self::invoiceServices($customer, $services, $extra, 'Bestelling domeinnamen');
        Log::db('bestelling', $customer->Email . ' · ' . implode(', ', array_column($items, 'domein')), $customer->id());

        return $doc;
    }

    /** Herhaalfacturen voor actieve diensten waarvan VolgendeFactuur binnen X dagen valt. @return int aantal facturen */
    public static function runRecurring(): int
    {
        $until = date('Y-m-d', strtotime('+' . (int) setting('facturatie.dagen_vooraf', 7) . ' days'));
        $rows = Database::all("SELECT * FROM Diensten WHERE Status = 'actief' AND Periode <> 'eenmalig' AND VolgendeFactuur IS NOT NULL AND VolgendeFactuur <= ? ORDER BY KlantId, Id", [$until]);
        $perCustomer = [];
        foreach ($rows as $row) {
            $perCustomer[(int) $row['KlantId']][] = new Service($row);
        }
        $count = 0;
        foreach ($perCustomer as $customerId => $services) {
            $customer = User::find($customerId);
            if ($customer === null) {
                continue;
            }
            $doc = self::invoiceServices($customer, $services, [], 'Periodieke factuur');
            self::send($doc);
            $count++;
        }

        return $count;
    }

    /** Document (pdf als bijlage) naar de klant mailen; concept wordt open/verzonden. */
    public static function send(Document $doc): bool
    {
        $customer = $doc->customer();
        if ($customer === null) {
            return false;
        }
        $label = $doc->label();
        [$subject, $body] = Mailer::tpl($doc->isQuote() ? 'offerte' : 'factuur', [
            'voornaam' => $customer->Voornaam,
            'nummer' => $doc->Nummer,
            'bedrag' => money((int) $doc->Totaal),
            'vervaldatum' => nl_date((string) $doc->VervalDatum),
            'iban' => setting('bedrijf.iban', '-'),
            'link' => absolute_url('/klant/documenten/' . $doc->id()),
        ]);
        $ok = Mailer::send((string) $customer->Email, $subject, $body, [
            ['name' => $label . '-' . $doc->Nummer . '.pdf', 'content' => DocumentPdf::render($doc), 'type' => 'application/pdf'],
        ]);
        if ($ok) {
            if ($doc->Status === 'concept') {
                $doc->Status = $doc->isQuote() ? 'verzonden' : 'open';
            }
            $doc->VerzondenOp = now();
            $doc->save();
        }

        return $ok;
    }

    /** Betaling registreren; volledig betaald → status betaald en gekoppelde diensten (in afwachting) actief. */
    public static function registerPayment(Document $doc, int $amount, string $date, string $note): void
    {
        $payments = (array) ($doc->Betalingen ?? []);
        $payments[] = ['Datum' => $date, 'Bedrag' => $amount, 'Notitie' => $note];
        $doc->Betalingen = $payments;
        $doc->Betaald = (int) $doc->Betaald + $amount;
        if ((int) $doc->Betaald >= (int) $doc->Totaal) {
            $doc->Status = 'betaald';
            $doc->BetaaldOp = $date . ' ' . date('H:i:s');
            if ((string) $doc->Onderwerp === Wallet::TOPUP_SUBJECT && ($customer = $doc->customer()) !== null) {
                Wallet::mutate($customer, (int) $doc->Totaal, Wallet::TOPUP_SUBJECT . ' (' . $doc->Nummer . ')', $doc->id());
            }
            foreach ($doc->lines() as $line) {
                if ($line['DienstId'] !== null) {
                    Database::run("UPDATE Diensten SET Status = 'actief' WHERE Id = ? AND Status = 'in_afwachting'", [$line['DienstId']]);
                    Provisioning::run((int) $line['DienstId']);
                }
            }
        }
        $doc->save();
    }
}
