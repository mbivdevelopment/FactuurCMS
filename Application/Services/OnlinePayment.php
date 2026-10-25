<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Document;
use App\Models\Setting;

/** Online betalen van facturen via Mollie of Cryptomus (tabel OnlineBetalingen). */
final class OnlinePayment
{
    /** @return array<string, string> actieve betaalmethodes voor het klantportaal */
    public static function enabled(): array
    {
        $out = [];
        if ((bool) setting('mollie.actief', false) && Setting::getSecret('mollie.apikey') !== '') {
            $out['mollie'] = 'Betalen met iDEAL of kaart';
        }
        if ((bool) setting('cryptomus.actief', false) && Setting::getSecret('cryptomus.apikey') !== '' && (string) setting('cryptomus.merchant', '') !== '') {
            $out['cryptomus'] = 'Betalen met crypto';
        }

        return $out;
    }

    public static function payable(Document $doc): bool
    {
        return !$doc->isQuote() && $doc->outstanding() > 0 && !in_array($doc->status(), ['concept', 'betaald', 'gecrediteerd'], true);
    }

    /** Maakt een betaling aan bij de provider en geeft de betaallink terug. */
    public static function start(Document $doc, string $provider): string
    {
        if (!self::payable($doc)) {
            throw new \RuntimeException('Deze factuur kan niet online betaald worden.');
        }
        if (!isset(self::enabled()[$provider])) {
            throw new \RuntimeException('Deze betaalmethode staat uit.');
        }
        $amount = $doc->outstanding();
        $id = Database::insert('OnlineBetalingen', ['DocumentId' => $doc->id(), 'Provider' => $provider, 'Bedrag' => $amount, 'Status' => 'open', 'AangemaaktOp' => now()]);
        $return = absolute_url('/klant/documenten/' . $doc->id() . '?betaling=' . $id);
        $payment = $provider === 'mollie'
            ? Mollie::create($amount, $doc->label() . ' ' . $doc->Nummer, $return, self::webhook('/betalen/mollie/webhook'), ['document' => $doc->id(), 'betaling' => $id])
            : Cryptomus::create($amount, preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $doc->Nummer) . '-' . $id, $return, self::webhook('/betalen/cryptomus/webhook'));
        if ($payment['id'] === '' || $payment['url'] === '') {
            throw new \RuntimeException('Geen betaallink ontvangen.');
        }
        Database::update('OnlineBetalingen', ['ExternId' => $payment['id'], 'Url' => $payment['url'], 'BijgewerktOp' => now()], ['Id' => $id]);

        return $payment['url'];
    }

    /** Webhook-URL, of null als de site lokaal draait (providers kunnen die niet bereiken). */
    private static function webhook(string $path): ?string
    {
        $url = absolute_url($path);
        $host = (string) parse_url($url, PHP_URL_HOST);

        return $host === '' || $host === 'localhost' || preg_match('/^(127\.|10\.|192\.168\.)/', $host) === 1 ? null : $url;
    }

    /** @return array<string, mixed>|null */
    public static function findByExtern(string $provider, string $externId): ?array
    {
        return Database::first('SELECT * FROM OnlineBetalingen WHERE Provider = ? AND ExternId = ?', [$provider, $externId]);
    }

    /** Status bij de provider ophalen en verwerken. @param array<string, mixed> $row */
    public static function sync(array $row): string
    {
        if ((string) $row['Status'] === 'betaald' || (string) ($row['ExternId'] ?? '') === '') {
            return (string) $row['Status'];
        }
        $status = $row['Provider'] === 'mollie' ? Mollie::status((string) $row['ExternId']) : Cryptomus::status((string) $row['ExternId']);

        return self::apply((int) $row['Id'], $status);
    }

    /** Verwerkt een providerstatus; bij betaald wordt de betaling eenmalig op de factuur geboekt. */
    public static function apply(int $id, string $providerStatus): string
    {
        return (string) Database::transaction(static function () use ($id, $providerStatus): string {
            $row = Database::first('SELECT * FROM OnlineBetalingen WHERE Id = ? FOR UPDATE', [$id]);
            if ($row === null) {
                return '';
            }
            if ($row['Status'] === 'betaald') {
                return 'betaald';
            }
            $paid = $row['Provider'] === 'mollie' ? Mollie::isPaid($providerStatus) : Cryptomus::isPaid($providerStatus);
            $failed = in_array($providerStatus, ['canceled', 'expired', 'failed', 'cancel', 'fail', 'system_fail', 'wrong_amount'], true);
            $status = $paid ? 'betaald' : ($failed ? 'mislukt' : 'open');
            Database::update('OnlineBetalingen', ['Status' => $status, 'ProviderStatus' => $providerStatus, 'BijgewerktOp' => now()], ['Id' => $id]);
            $doc = $paid ? Document::find((int) $row['DocumentId']) : null;
            if ($doc !== null && ($amount = min((int) $row['Bedrag'], $doc->outstanding())) > 0) {
                Billing::registerPayment($doc, $amount, date('Y-m-d'), 'Online betaald via ' . ucfirst((string) $row['Provider']) . ' (' . $row['ExternId'] . ')');
            }

            return $status;
        });
    }
}
