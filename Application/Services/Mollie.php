<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** Mollie Payments API v2. */
final class Mollie
{
    /** @param array<string, mixed>|null $body @return array<mixed> */
    private static function call(string $method, string $path, ?array $body = null): array
    {
        $key = Setting::getSecret('mollie.apikey');
        if ($key === '') {
            throw new \RuntimeException('Stel eerst de API-sleutel van Mollie in.');
        }
        $base = rtrim((string) setting('mollie.url', 'https://api.mollie.com'), '/');

        return Http::json($method, $base . '/v2/' . $path, ['Authorization: Bearer ' . $key], $body === null ? null : (string) json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public static function test(): string
    {
        $methods = (array) (self::call('GET', 'methods')['_embedded']['methods'] ?? []);
        $mode = str_starts_with(Setting::getSecret('mollie.apikey'), 'test_') ? 'testmodus' : 'live';

        return 'Verbonden met Mollie (' . $mode . '): ' . count($methods) . ' betaalmethodes actief.';
    }

    /** @param array<string, mixed> $metadata @return array{id: string, url: string} */
    public static function create(int $cents, string $description, string $redirectUrl, ?string $webhookUrl, array $metadata): array
    {
        $body = ['amount' => ['currency' => 'EUR', 'value' => number_format($cents / 100, 2, '.', '')], 'description' => $description, 'redirectUrl' => $redirectUrl, 'metadata' => $metadata];
        if ($webhookUrl !== null) {
            $body['webhookUrl'] = $webhookUrl;
        }
        $payment = self::call('POST', 'payments', $body);

        return ['id' => (string) ($payment['id'] ?? ''), 'url' => (string) ($payment['_links']['checkout']['href'] ?? '')];
    }

    public static function status(string $id): string
    {
        return (string) (self::call('GET', 'payments/' . rawurlencode($id))['status'] ?? '');
    }

    public static function isPaid(string $status): bool
    {
        return $status === 'paid';
    }
}
