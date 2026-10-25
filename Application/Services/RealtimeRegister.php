<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

/** Realtime Register REST API v2: domeinen en prijslijst (bedragen in centen). */
final class RealtimeRegister
{
    public const LIVE = 'https://api.yoursrs.com';
    public const OTE = 'https://api.yoursrs-ote.com';

    private const DIAL = ['NL' => '31', 'BE' => '32', 'DE' => '49', 'FR' => '33', 'GB' => '44', 'LU' => '352', 'ES' => '34', 'IT' => '39', 'AT' => '43', 'CH' => '41', 'US' => '1'];

    /** @param array<string, mixed> $query @return array<mixed> */
    private static function get(string $path, array $query = []): array
    {
        return self::request('GET', $path . ($query !== [] ? '?' . http_build_query($query) : ''));
    }

    /** @param array<string, mixed>|null $body @return array<mixed> */
    private static function request(string $method, string $path, ?array $body = null): array
    {
        $key = Setting::getSecret('rr.apikey');
        if ($key === '') {
            throw new \RuntimeException('Stel eerst de API-sleutel van Realtime Register in.');
        }
        $base = rtrim((string) setting('rr.url', self::LIVE), '/');

        return Http::json($method, $base . '/v2/' . $path, ['Authorization: ApiKey ' . $key], $body !== null ? (string) json_encode($body, JSON_UNESCAPED_UNICODE) : null);
    }

    public static function configured(): bool
    {
        return Setting::getSecret('rr.apikey') !== '' && trim((string) setting('rr.customer', '')) !== '';
    }

    /** Beschikbaarheid van één domeinnaam. @return array{status: string, premium: bool} status: vrij|bezet */
    public static function check(string $domain): array
    {
        $data = self::get('domains/' . rawurlencode($domain) . '/check');

        return ['status' => !empty($data['available']) ? 'vrij' : 'bezet', 'premium' => !empty($data['premium'])];
    }

    /** Houdercontact van een klant aanmaken of bijwerken; geeft de handle terug (bv. fck00012). */
    public static function ensureContact(User $customer): string
    {
        $name = trim($customer->Voornaam . ' ' . $customer->Achternaam) ?: (string) $customer->Bedrijfsnaam;
        foreach (['naam' => $name, 'adres' => $customer->Adres, 'postcode' => $customer->Postcode, 'plaats' => $customer->Plaats, 'telefoon' => $customer->Telefoon] as $label => $value) {
            if (trim((string) $value) === '') {
                throw new \RuntimeException('Klant ' . $customer->Nummer . ' mist een ' . $label . ' voor de domeinhouder.');
            }
        }
        $country = strtoupper((string) ($customer->Land ?: 'NL'));
        $data = array_filter([
            'organization' => trim((string) $customer->Bedrijfsnaam),
            'name' => $name,
            'addressLine' => [trim((string) $customer->Adres)],
            'postalCode' => trim((string) $customer->Postcode),
            'city' => trim((string) $customer->Plaats),
            'country' => $country,
            'email' => (string) $customer->Email,
            'voice' => self::voice((string) $customer->Telefoon, $country),
        ], static fn ($v): bool => $v !== '');
        $handle = 'fc' . strtolower((string) preg_replace('/[^A-Za-z0-9]/', '', (string) $customer->Nummer));
        $path = 'customers/' . rawurlencode((string) setting('rr.customer', '')) . '/contacts/' . rawurlencode($handle);
        try {
            self::get($path);
        } catch (\RuntimeException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
            self::request('POST', $path, $data);

            return $handle;
        }
        self::request('POST', $path . '/update', $data);

        return $handle;
    }

    /** Registreert een domein, of verhuist het als er een authcode is. @return array<mixed> antwoord van Realtime Register */
    /** @param list<string> $nameservers eigen nameservers van de klant; leeg = die uit de instellingen */
    public static function register(string $domain, string $registrant, ?string $authcode = null, array $nameservers = []): array
    {
        $contact = trim((string) setting('rr.contact', '')) ?: $registrant;
        $payload = [
            'customer' => (string) setting('rr.customer', ''),
            'registrant' => $registrant,
            'period' => 12,
            'autoRenew' => true,
            'contacts' => [['role' => 'ADMIN', 'handle' => $contact], ['role' => 'BILLING', 'handle' => $contact], ['role' => 'TECH', 'handle' => $contact]],
        ];
        $ns = count($nameservers) >= 2 ? array_values($nameservers) : array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', (string) setting('rr.ns', '')) ?: [])));
        if (count($ns) >= 2) {
            $payload['ns'] = $ns;
        }
        if ($authcode !== null && $authcode !== '') {
            $payload['authcode'] = $authcode;

            return self::request('POST', 'domains/' . rawurlencode($domain) . '/transfer', $payload);
        }

        return self::request('POST', 'domains/' . rawurlencode($domain), $payload);
    }

    /** Telefoonnummer in EPP-formaat, bv. +31.612345678. */
    public static function voice(string $phone, string $country = 'NL'): string
    {
        $d = (string) preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($d, '00')) {
            $d = '+' . substr($d, 2);
        } elseif (str_starts_with($d, '0')) {
            $d = '+' . (self::DIAL[$country] ?? '31') . substr($d, 1);
        } elseif (!str_starts_with($d, '+')) {
            $d = '+' . (self::DIAL[$country] ?? '31') . $d;
        }
        $digits = substr($d, 1);
        if (!ctype_digit($digits) || strlen($digits) < 8 || strlen($digits) > 15) {
            throw new \RuntimeException('Ongeldig telefoonnummer voor de domeinhouder: ' . $phone);
        }
        $cc = '';
        foreach (self::DIAL as $code) {
            if (str_starts_with($digits, $code) && strlen($code) > strlen($cc)) {
                $cc = $code;
            }
        }
        $cc = $cc !== '' ? $cc : substr($digits, 0, 2);

        return '+' . $cc . '.' . substr($digits, strlen($cc));
    }

    public static function test(): string
    {
        $page = self::get('domains', ['limit' => 1]);

        return 'Verbonden met Realtime Register: ' . (int) ($page['pagination']['total'] ?? count((array) ($page['entities'] ?? []))) . ' domeinen.';
    }

    /** @return array<string, array{domain: string, expiry: ?string, status: string, autoRenew: bool}> */
    public static function domains(): array
    {
        $out = [];
        $offset = 0;
        do {
            $page = self::get('domains', ['limit' => 100, 'offset' => $offset]);
            $entities = (array) ($page['entities'] ?? []);
            foreach ($entities as $d) {
                $name = strtolower((string) ($d['domainName'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $out[$name] = [
                    'domain' => $name,
                    'expiry' => !empty($d['expiryDate']) ? date('Y-m-d', (int) strtotime((string) $d['expiryDate'])) : null,
                    'status' => implode(', ', array_map('strval', (array) ($d['status'] ?? []))),
                    'autoRenew' => (bool) ($d['autoRenew'] ?? false),
                ];
            }
            $total = (int) ($page['pagination']['total'] ?? 0);
            $offset += 100;
        } while ($entities !== [] && $offset < $total && $offset < 20000);
        ksort($out);

        return $out;
    }

    /** Alleen gewone extensies (nl, com, co.uk); geen varianten als .centralnic, .legacy, _sld, .month of xn--. */
    public static function isRegularTld(string $tld): bool
    {
        if (preg_match('/^[a-z]{2,}(\.[a-z]{2,})*$/', $tld) !== 1) {
            return false;
        }
        foreach (explode('.', $tld) as $label) {
            if (in_array($label, ['centralnic', 'legacy', 'slb', 'sld', 'month', 'year', 'premium', 'promo'], true) || preg_match('/(month|year)s?$/', $label) === 1) {
                return false;
            }
        }

        return true;
    }

    /** Inkoopprijzen per extensie. @return array<string, array{create: int, renew: int, transfer: int}> */
    public static function tldPrices(): array
    {
        $customer = trim((string) setting('rr.customer', ''));
        if ($customer === '') {
            throw new \RuntimeException('Vul eerst je klanthandle (customer) van Realtime Register in.');
        }
        $out = [];
        foreach ((array) (self::get('customers/' . rawurlencode($customer) . '/pricelist', ['currency' => 'EUR'])['prices'] ?? []) as $price) {
            $product = (string) ($price['product'] ?? '');
            $action = strtolower((string) ($price['action'] ?? ''));
            if (!str_starts_with($product, 'domain_') || ($price['currency'] ?? 'EUR') !== 'EUR' || !in_array($action, ['create', 'renew', 'transfer'], true)) {
                continue;
            }
            $tld = strtolower(substr($product, 7));
            if (!self::isRegularTld($tld)) {
                continue;
            }
            $out[$tld] ??= ['create' => 0, 'renew' => 0, 'transfer' => 0];
            $out[$tld][$action] = (int) ($price['price'] ?? 0);
        }
        ksort($out);

        return $out;
    }
}
