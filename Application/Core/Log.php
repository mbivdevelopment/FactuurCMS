<?php

declare(strict_types=1);

namespace App\Core;

/** Schrijft naar storage/logs en naar de tabel Logboek (activiteit, mail, login, throttle). */
final class Log
{
    /** @param array<string, mixed> $context */
    public static function info(string $message, array $context = []): void
    {
        self::file('info', $message, $context);
    }

    /** @param array<string, mixed> $context */
    public static function error(string $message, array $context = []): void
    {
        self::file('error', $message, $context);
    }

    public static function db(string $type, string $message, ?int $userId = null): void
    {
        try {
            Database::insert('Logboek', [
                'Type' => mb_substr($type, 0, 40),
                'GebruikerId' => $userId ?: null,
                'Bericht' => mb_substr($message, 0, 255),
                'Ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'AangemaaktOp' => now(),
            ]);
        } catch (\Throwable $e) {
            self::file('error', 'Log naar database mislukt: ' . $e->getMessage());
        }
    }

    /** @param array<string, mixed> $context */
    private static function file(string $level, string $message, array $context = []): void
    {
        $line = sprintf("[%s] %s: %s %s\n", now(), strtoupper($level), $message, $context === [] ? '' : json_encode($context, JSON_UNESCAPED_UNICODE));
        @file_put_contents(App::instance()->path('storage/logs/app-' . date('Y-m-d') . '.log'), $line, FILE_APPEND);
    }
}
