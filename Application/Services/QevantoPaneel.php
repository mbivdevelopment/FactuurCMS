<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** Qevanto hostingpaneel (hosting.qevanto.nl): hostingabonnementen via de API met een qv_-sleutel. */
final class QevantoPaneel
{
    /** Soort abonnement/pakket in het paneel => [label, productgroep, dienstsoort, icoon]. */
    public const KINDS = [
        'hosting' => ['Hosting', 'Hosting', 'Webhosting', 'server'],
        'reseller' => ['Reseller', 'Hosting', 'Reseller Hosting', 'users'],
        'vpn' => ['VPN', 'Overig', 'VPN', 'shield'],
        'workplace' => ['Werkplek', 'Overig', 'Werkplek Beheer', 'headset'],
    ];

    /** Hostingabonnementen, resellers, VPN en werkplekken. @return list<array<string, mixed>> */
    public static function subscriptions(): array
    {
        return self::get('/api/subscriptions');
    }

    /** Pakketten (hosting, reseller, VPN, werkplek). @return list<array<string, mixed>> */
    public static function packages(): array
    {
        return self::get('/api/packages');
    }

    /** Eén abonnement met limieten en gebruik. @return array<mixed> */
    public static function subscription(int $id): array
    {
        [$url, $token] = self::credentials();

        return Http::json('GET', $url . '/api/subscriptions/' . $id, ['Authorization: Bearer ' . $token]);
    }

    /** Verbruiksreeks (cpu/geheugen/disk/verkeer) van een abonnement. @return array<mixed> */
    public static function stats(int $id, string $range = '30d'): array
    {
        [$url, $token] = self::credentials();

        return Http::json('GET', $url . '/api/subscriptions/' . $id . '/stats?' . http_build_query(['range' => $range]), ['Authorization: Bearer ' . $token]);
    }

    /** Zet de status van een abonnement (active/suspended). */
    public static function setStatus(int $id, string $status): void
    {
        [$url, $token] = self::credentials();
        Http::json('PATCH', $url . '/api/subscriptions/' . $id, ['Authorization: Bearer ' . $token], (string) json_encode(['status' => $status], JSON_UNESCAPED_UNICODE));
    }

    private const FEATURES = [
        'websites' => '%s websites', 'diskGb' => '%s GB opslag', 'bandwidthGb' => '%s GB dataverkeer', 'mailboxes' => '%s mailboxen',
        'databases' => '%s databases', 'ftpAccounts' => '%s FTP-accounts', 'cronJobs' => '%s cronjobs', 'memoryMb' => '%s MB geheugen',
        'phpWorkers' => '%s PHP-workers', 'cpuPercent' => '%s%% CPU', 'netMbit' => '%s Mbit/s internetsnelheid', 'cacheMb' => '%s MB Redis-cache',
        'customers' => '%s klanten', 'subscriptions' => '%s abonnementen', 'peers' => '%s apparaten', 'workplaces' => '%s werkplekken',
    ];

    private const FEATURE_ICONS = ['websites' => 'globe', 'diskGb' => 'box', 'bandwidthGb' => 'zap', 'mailboxes' => 'mail', 'databases' => 'server', 'ftpAccounts' => 'link', 'cronJobs' => 'clock', 'memoryMb' => 'cpu', 'phpWorkers' => 'code', 'cpuPercent' => 'cpu', 'netMbit' => 'zap', 'cacheMb' => 'zap', 'customers' => 'users', 'subscriptions' => 'server', 'peers' => 'shield', 'workplaces' => 'headset'];

    /** Pakketlimieten als kenmerken met icoon. @param array<string, mixed> $limits @return list<array{icoon: string, tekst: string}> */
    public static function features(array $limits): array
    {
        $out = [];
        foreach (self::FEATURES as $key => $format) {
            if (isset($limits[$key]) && is_numeric($limits[$key])) {
                $out[] = ['icoon' => self::FEATURE_ICONS[$key] ?? 'check', 'tekst' => (int) $limits[$key] === 0 ? 'Onbeperkt ' . trim(str_replace(['%s%%', '%s'], '', $format)) : sprintf($format, (int) $limits[$key])];
            }
        }
        if (!empty($limits['backups'])) {
            $out[] = ['icoon' => 'refresh', 'tekst' => 'Dagelijkse back-ups'];
        }
        if (!empty($limits['ssh'])) {
            $out[] = ['icoon' => 'lock', 'tekst' => 'SSH-toegang'];
        }

        return $out;
    }

    /** @param array<string, mixed> $body @return array<mixed> */
    public static function post(string $path, array $body): array
    {
        [$url, $token] = self::credentials();

        return Http::json('POST', $url . $path, ['Authorization: Bearer ' . $token], (string) json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    /** @return list<array<string, mixed>> */
    public static function customers(string $q): array
    {
        return self::get('/api/customers?' . http_build_query(['q' => $q]));
    }

    public static function kindLabel(string $kind): string
    {
        return self::KINDS[$kind][0] ?? ucfirst($kind);
    }

    /** @return list<array<string, mixed>> */
    private static function get(string $path): array
    {
        [$url, $token] = self::credentials();

        return array_values(array_filter(Http::json('GET', $url . $path, ['Authorization: Bearer ' . $token]), 'is_array'));
    }

    /** @return array{0: string, 1: string} */
    private static function credentials(): array
    {
        $url = rtrim(trim((string) setting('qevanto.url', '')), '/');
        $token = Setting::getSecret('qevanto.token');
        if ($url === '' || $token === '') {
            throw new \RuntimeException('Stel eerst de URL en API-sleutel van het Qevanto hostingpaneel in.');
        }

        return [$url, $token];
    }

    public static function test(): string
    {
        return 'Verbonden met het Qevanto hostingpaneel: ' . count(self::subscriptions()) . ' abonnementen en ' . count(self::packages()) . ' pakketten.';
    }
}
