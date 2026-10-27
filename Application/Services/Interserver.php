<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** InterServer CloudVPS: bestellen en beheren via de REST API (apiv2) met een X-API-KEY. */
final class Interserver
{
    public const BASE = 'https://my.interserver.net/apiv2';
    public const PLATFORMS = ['kvm' => 'KVM VPS', 'kvmstorage' => 'KVM Big Storage', 'hyperv' => 'Windows VPS'];
    public const LOCATIONS = [1 => 'Secaucus, NJ (VS)', 2 => 'Los Angeles, CA (VS)', 3 => 'Nederland'];
    public const OS = [
        'ubuntu|ubuntu24' => 'Ubuntu 24.04 LTS',
        'ubuntu|ubuntu22' => 'Ubuntu 22.04 LTS',
        'debian|debian12' => 'Debian 12',
        'debian|debian11' => 'Debian 11',
        'almalinux|alma9' => 'AlmaLinux 9',
        'almalinux|alma8' => 'AlmaLinux 8',
        'rocky|rocky9' => 'Rocky Linux 9',
        'windows|windows2022' => 'Windows Server 2022',
    ];

    public static function configured(): bool
    {
        return Setting::getSecret('is.apikey') !== '';
    }

    /** Ordercatalogus: platforms, OS-templates, locaties en prijzen. @return array<string, mixed> */
    public static function catalog(): array
    {
        return self::get('/vps/order');
    }

    /** Nieuwe VPS bestellen; geeft o.a. id en prijs terug. @param array<string, mixed> $cfg @return array<string, mixed> */
    public static function order(array $cfg): array
    {
        return self::post('/vps/order', $cfg);
    }

    /** Gegevens van één VPS (status, ip, slices e.d.). @return array<string, mixed> */
    public static function vps(int $id): array
    {
        return self::get('/vps/' . $id);
    }

    /** Poweracties: start, stop of restart. */
    public static function power(int $id, string $action): void
    {
        if (!in_array($action, ['start', 'stop', 'restart'], true)) {
            throw new \InvalidArgumentException('Onbekende actie.');
        }
        self::get('/vps/' . $id . '/' . $action);
    }

    public static function test(): string
    {
        $vps = self::get('/vps');

        return 'Verbonden met InterServer: ' . count(array_filter(is_array($vps) ? $vps : [], 'is_array')) . ' VPS\'en op dit account.';
    }

    /** @return array<string, mixed>|list<mixed> */
    private static function get(string $path): array
    {
        return Http::json('GET', self::BASE . $path, ['X-API-KEY: ' . self::key()]);
    }

    /** @param array<string, mixed> $body @return array<string, mixed> */
    private static function post(string $path, array $body): array
    {
        return Http::json('POST', self::BASE . $path, ['X-API-KEY: ' . self::key()], (string) json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    private static function key(): string
    {
        $key = Setting::getSecret('is.apikey');
        if ($key === '') {
            throw new \RuntimeException('Stel eerst de InterServer API-sleutel in bij Koppelingen.');
        }

        return $key;
    }
}
