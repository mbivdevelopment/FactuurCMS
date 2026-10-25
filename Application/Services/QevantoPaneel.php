<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** Qevanto hostingpaneel (hosting.qevanto.nl): hostingabonnementen via de API met een qv_-sleutel. */
final class QevantoPaneel
{
    /** @return list<array<string, mixed>> */
    public static function subscriptions(): array
    {
        $url = rtrim(trim((string) setting('qevanto.url', '')), '/');
        $token = Setting::getSecret('qevanto.token');
        if ($url === '' || $token === '') {
            throw new \RuntimeException('Stel eerst de URL en API-sleutel van het Qevanto hostingpaneel in.');
        }

        return array_values(array_filter(Http::json('GET', $url . '/api/subscriptions', ['Authorization: Bearer ' . $token]), 'is_array'));
    }

    public static function test(): string
    {
        return 'Verbonden met het Qevanto hostingpaneel: ' . count(self::subscriptions()) . ' hostingabonnementen.';
    }
}
