<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

/** Cryptomus Merchant API: sign = md5(base64(json-body) + API-sleutel). */
final class Cryptomus
{
    /** @param array<string, mixed> $body @return array<mixed> */
    private static function call(string $path, array $body): array
    {
        $merchant = trim((string) setting('cryptomus.merchant', ''));
        $key = Setting::getSecret('cryptomus.apikey');
        if ($merchant === '' || $key === '') {
            throw new \RuntimeException('Stel eerst de merchant-ID en API-sleutel van Cryptomus in.');
        }
        $json = $body === [] ? '' : (string) json_encode($body, JSON_UNESCAPED_UNICODE);
        $base = rtrim((string) setting('cryptomus.url', 'https://api.cryptomus.com'), '/');
        $response = Http::json('POST', $base . '/v1/' . $path, ['merchant: ' . $merchant, 'sign: ' . md5(base64_encode($json) . $key)], $json);
        if ((int) ($response['state'] ?? 0) !== 0) {
            throw new \RuntimeException('Cryptomus: ' . (string) ($response['message'] ?? 'onbekende fout'));
        }

        return (array) ($response['result'] ?? []);
    }

    public static function test(): string
    {
        return 'Verbonden met Cryptomus: ' . count(self::call('payment/services', [])) . ' coins/netwerken beschikbaar.';
    }

    /** @return array{id: string, url: string} */
    public static function create(int $cents, string $orderId, string $returnUrl, ?string $callbackUrl): array
    {
        $body = ['amount' => number_format($cents / 100, 2, '.', ''), 'currency' => 'EUR', 'order_id' => $orderId, 'url_return' => $returnUrl, 'url_success' => $returnUrl];
        if ($callbackUrl !== null) {
            $body['url_callback'] = $callbackUrl;
        }
        $result = self::call('payment', $body);

        return ['id' => (string) ($result['uuid'] ?? ''), 'url' => (string) ($result['url'] ?? '')];
    }

    public static function status(string $uuid): string
    {
        $result = self::call('payment/info', ['uuid' => $uuid]);

        return (string) ($result['payment_status'] ?? $result['status'] ?? '');
    }

    public static function isPaid(string $status): bool
    {
        return in_array($status, ['paid', 'paid_over'], true);
    }

    /** @param array<string, mixed> $data webhook-body */
    public static function verify(array $data): bool
    {
        $key = Setting::getSecret('cryptomus.apikey');
        $sign = (string) ($data['sign'] ?? '');
        unset($data['sign']);

        return $key !== '' && $sign !== '' && hash_equals(md5(base64_encode((string) json_encode($data, JSON_UNESCAPED_UNICODE)) . $key), $sign);
    }
}
