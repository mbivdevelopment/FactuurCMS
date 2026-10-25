<?php

declare(strict_types=1);

namespace App\Services;

/** Kleine JSON-client (cURL) voor de koppelingen. */
final class Http
{
    /**
     * @param list<string> $headers
     * @return array<mixed>
     */
    public static function json(string $method, string $url, array $headers = [], ?string $body = null, int $timeout = 25): array
    {
        $headers = array_merge(['Accept: application/json', 'User-Agent: FactuurCMS'], $headers);
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $host = (string) parse_url($url, PHP_URL_HOST);
        if ($raw === false) {
            throw new \RuntimeException($host . ' is niet bereikbaar: ' . $error);
        }
        $data = json_decode((string) $raw, true);
        if ($status >= 400) {
            $message = '';
            foreach (['detail', 'message', 'error', 'title'] as $key) {
                if (is_array($data) && isset($data[$key])) {
                    $message = is_string($data[$key]) ? $data[$key] : (string) json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                    break;
                }
            }
            throw new \RuntimeException($host . ': ' . ($message !== '' ? $message : 'HTTP ' . $status));
        }

        return is_array($data) ? $data : [];
    }
}
