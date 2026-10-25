<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** Realtime Register REST API v2: domeinen en prijslijst (bedragen in centen). */
final class RealtimeRegister
{
    public const LIVE = 'https://api.yoursrs.com';
    public const OTE = 'https://api.yoursrs-ote.com';

    /** @param array<string, mixed> $query @return array<mixed> */
    private static function get(string $path, array $query = []): array
    {
        $key = Setting::getSecret('rr.apikey');
        if ($key === '') {
            throw new \RuntimeException('Stel eerst de API-sleutel van Realtime Register in.');
        }
        $base = rtrim((string) setting('rr.url', self::LIVE), '/');

        return Http::json('GET', $base . '/v2/' . $path . ($query !== [] ? '?' . http_build_query($query) : ''), ['Authorization: ApiKey ' . $key]);
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
