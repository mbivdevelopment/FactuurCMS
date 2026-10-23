<?php

declare(strict_types=1);

namespace App\Core;

/** AES-256-GCM versleuteling voor gevoelige Settings (API-keys van koppelingen, 2FA-secrets). */
final class Crypt
{
    public static function encrypt(string $plain): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            throw new \RuntimeException('Versleutelen mislukt.');
        }

        return base64_encode($iv . $tag . $cipher);
    }

    public static function decrypt(string $encoded): string
    {
        $raw = base64_decode($encoded, true);
        if ($raw === false || strlen($raw) < 29) {
            throw new \RuntimeException('Ongeldige versleutelde waarde.');
        }
        $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
        if ($plain === false) {
            throw new \RuntimeException('Ontsleutelen mislukt (app.key in Application/Config/app.php gewijzigd?).');
        }

        return $plain;
    }

    /** app.key uit de config, of anders een automatisch aangemaakte sleutel in storage/sleutel.php (niet in git, door de updater beschermd). */
    public static function key(): string
    {
        $key = (string) Config::get('app.key', '');
        if (strlen($key) < 32) {
            $file = App::instance()->path('storage/sleutel.php');
            if (is_file($file)) {
                $stored = require $file;
                $key = is_string($stored) ? $stored : '';
            }
            if (strlen($key) < 32) {
                $key = bin2hex(random_bytes(32));
                if (file_put_contents($file, "<?php\n\n// Automatisch aangemaakte sleutel voor versleutelde Settings; niet wijzigen of verwijderen.\nreturn " . var_export($key, true) . ";\n", LOCK_EX) === false) {
                    throw new \RuntimeException('Kon storage/sleutel.php niet schrijven; vul anders app.key in Application/Config/app.php in.');
                }
            }
        }

        return hash('sha256', $key, true);
    }
}
