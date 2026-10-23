<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Crypt;
use App\Core\Database;

/** Sleutel/waarde-instellingen (tabel Instellingen); waarden worden als JSON opgeslagen. */
final class Setting
{
    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /** @return array<string, mixed> */
    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            try {
                foreach (Database::all('SELECT Sleutel, Waarde FROM Instellingen') as $row) {
                    $decoded = json_decode((string) $row['Waarde'], true);
                    self::$cache[(string) $row['Sleutel']] = json_last_error() === JSON_ERROR_NONE ? $decoded : $row['Waarde'];
                }
            } catch (\Throwable) {
                self::$cache = [];
            }
        }

        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::all()[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    public static function set(string $key, mixed $value): void
    {
        Database::run(
            'INSERT INTO Instellingen (Sleutel, Waarde) VALUES (?, ?) ON DUPLICATE KEY UPDATE Waarde = VALUES(Waarde)',
            [$key, json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
        );
        self::all();
        self::$cache[$key] = $value;
    }

    public static function getSecret(string $key): string
    {
        $value = (string) self::get($key, '');
        if ($value === '') {
            return '';
        }
        try {
            return Crypt::decrypt($value);
        } catch (\Throwable) {
            return '';
        }
    }

    public static function setSecret(string $key, string $plain): void
    {
        self::set($key, $plain === '' ? '' : Crypt::encrypt($plain));
    }
}
