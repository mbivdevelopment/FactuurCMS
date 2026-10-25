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

    public static function kindLabel(string $kind): string
    {
        return self::KINDS[$kind][0] ?? ucfirst($kind);
    }

    /** @return list<array<string, mixed>> */
    private static function get(string $path): array
    {
        $url = rtrim(trim((string) setting('qevanto.url', '')), '/');
        $token = Setting::getSecret('qevanto.token');
        if ($url === '' || $token === '') {
            throw new \RuntimeException('Stel eerst de URL en API-sleutel van het Qevanto hostingpaneel in.');
        }

        return array_values(array_filter(Http::json('GET', $url . $path, ['Authorization: Bearer ' . $token]), 'is_array'));
    }

    public static function test(): string
    {
        return 'Verbonden met het Qevanto hostingpaneel: ' . count(self::subscriptions()) . ' abonnementen en ' . count(self::packages()) . ' pakketten.';
    }
}
