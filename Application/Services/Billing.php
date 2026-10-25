<?php

declare(strict_types=1);

namespace App\Services;

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
    public static function order(User $customer, Product $product, string $note): Document
    {
        $service = Service::create([
            'KlantId' => $customer->id(),
            'ProductId' => $product->id(),
            'Naam' => $product->Naam,
            'Omschrijving' => $note !== '' ? mb_substr($note, 0, 255) : null,
            'Prijs' => (int) $product->Prijs,
            'BtwTarief' => (float) $product->BtwTarief,
            'Periode' => $product->Periode,
            'Status' => 'in_afwachting',
            'StartDatum' => date('Y-m-d'),
            'VolgendeFactuur' => date('Y-m-d'),
        ]);
        $extra = (int) $product->Instelkosten > 0
            ? [['Omschrijving' => 'Eenmalige instelkosten ' . $product->Naam, 'Aantal' => 1.0, 'Prijs' => (int) $product->Instelkosten, 'BtwTarief' => (float) $product->BtwTarief]]
            : [];
        $doc = self::invoiceServices($customer, [$service], $extra, 'Bestelling ' . $product->Naam);
        if ($product->Periode === 'eenmalig') {
            $service->VolgendeFactuur = null;
            $service->save();
        }
        Log::db('bestelling', $customer->Email . ' · ' . $product->Naam, $customer->id());

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
        $body = $doc->isQuote()
            ? "Beste {$customer->Voornaam},\n\nIn de bijlage vind je offerte {$doc->Nummer} van " . money((int) $doc->Totaal) . " (geldig t/m " . nl_date((string) $doc->VervalDatum) . ").\nJe kunt de offerte online bekijken en accepteren via " . absolute_url('/klant/documenten/' . $doc->id()) . "\n\nMet vriendelijke groet,\n" . company_name()
            : "Beste {$customer->Voornaam},\n\nIn de bijlage vind je {$label} {$doc->Nummer} van " . money((int) $doc->Totaal) . ".\nGraag ontvangen we de betaling vóór " . nl_date((string) $doc->VervalDatum) . " op " . setting('bedrijf.iban', '-') . " t.n.v. " . company_name() . " o.v.v. {$doc->Nummer}.\n\nOnline bekijken: " . absolute_url('/klant/documenten/' . $doc->id()) . "\n\nMet vriendelijke groet,\n" . company_name();
        $ok = Mailer::send((string) $customer->Email, $label . ' ' . $doc->Nummer . ' van ' . company_name(), $body, [
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
