<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

/**
 * Verstuurt mail volgens Beheer → Instellingen → E-mail: SMTP (eigen minimale client), PHP mail() of alleen loggen
 * (storage/logs/mail.log). Het SMTP-wachtwoord staat versleuteld in mail.wachtwoord.
 */
final class Mailer
{
    public const METHODS = ['log' => 'Alleen loggen (test)', 'mail' => 'PHP mail() van de server', 'smtp' => 'SMTP-server'];

    public static ?string $lastError = null;

    /** @param list<array{name: string, content: string, type?: string}> $attachments */
    public static function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        self::$lastError = null;
        $html = self::wrap($subject, $body);
        try {
            $ok = match ((string) Setting::get('mail.methode', 'log')) {
                'smtp' => self::smtp($to, $subject, $html, $attachments),
                'mail' => self::phpMail($to, $subject, $html, $attachments),
                default => self::log($to, $subject, $html, $attachments),
            };
        } catch (\Throwable $e) {
            $ok = false;
            self::$lastError = $e->getMessage();
            Log::error('Mail versturen mislukt', ['to' => $to, 'error' => $e->getMessage()]);
        }
        Log::db('mail', ($ok ? '' : 'MISLUKT ') . $to . ' · ' . $subject, Auth::id() ?: null);

        return $ok;
    }

    /** Mail in huisstijl: logo/naam bovenaan, platte tekst met regeleinden, bedrijfsgegevens onderaan. */
    private static function wrap(string $subject, string $body): string
    {
        $primary = e((string) setting('branding.kleur', '#ff5a1f'));
        $name = e((string) setting('bedrijf.naam', config('app.name')));
        $logo = branding_url('logo');
        $head = $logo !== '' && !str_ends_with($logo, '.svg')
            ? '<img src="' . e(absolute_url($logo)) . '" alt="' . $name . '" style="max-height:48px">'
            : '<strong style="font-size:20px;color:' . $primary . '">' . $name . '</strong>';
        $content = str_contains($body, '<') ? $body : nl2br(e($body));
        $footer = e(implode(' · ', array_filter([setting('bedrijf.naam'), setting('bedrijf.adres'), trim(setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', '')), setting('bedrijf.email'), setting('bedrijf.telefoon')])));

        return '<!doctype html><html><body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937">'
            . '<div style="max-width:620px;margin:0 auto;padding:24px"><div style="padding:16px 0">' . $head . '</div>'
            . '<div style="background:#fff;border-radius:8px;border-top:3px solid ' . $primary . ';padding:24px;font-size:14px;line-height:1.6">'
            . '<h2 style="margin:0 0 12px;font-size:18px">' . e($subject) . '</h2>' . $content . '</div>'
            . '<p style="font-size:12px;color:#6b7280;text-align:center">' . $footer . '</p></div></body></html>';
    }

    /** @return array{0: string, 1: string} */
    private static function sender(): array
    {
        return [
            (string) Setting::get('mail.afzender', setting('bedrijf.email', 'noreply@localhost')),
            (string) Setting::get('mail.afzendernaam', setting('bedrijf.naam', config('app.name'))),
        ];
    }

    /**
     * @param list<array{name: string, content: string, type?: string}> $attachments
     * @return array{0: list<string>, 1: string}
     */
    private static function mime(string $html, array $attachments): array
    {
        if ($attachments === []) {
            return [['Content-Type: text/html; charset=UTF-8', 'Content-Transfer-Encoding: base64'], chunk_split(base64_encode($html))];
        }
        $boundary = '=_fc_' . bin2hex(random_bytes(12));
        $body = "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html));
        foreach ($attachments as $file) {
            $name = str_replace(['"', "\r", "\n"], '', $file['name']);
            $body .= "--$boundary\r\nContent-Type: " . ($file['type'] ?? 'application/octet-stream') . "; name=\"$name\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$name\"\r\n\r\n" . chunk_split(base64_encode($file['content']));
        }

        return [["Content-Type: multipart/mixed; boundary=\"$boundary\""], $body . "--$boundary--\r\n"];
    }

    /** @param list<array{name: string, content: string, type?: string}> $attachments */
    private static function log(string $to, string $subject, string $html, array $attachments): bool
    {
        $files = $attachments === [] ? '' : 'BIJLAGEN: ' . implode(', ', array_column($attachments, 'name')) . "\n";
        $text = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br />', '</p>', '</h2>'], "\n", $html))));
        file_put_contents(App::instance()->path('storage/logs/mail.log'), sprintf("[%s] AAN: %s | ONDERWERP: %s\n%s%s\n%s\n", now(), $to, $subject, $files, $text, str_repeat('-', 60)), FILE_APPEND);

        return true;
    }

    /** @param list<array{name: string, content: string, type?: string}> $attachments */
    private static function phpMail(string $to, string $subject, string $html, array $attachments): bool
    {
        [$from, $fromName] = self::sender();
        [$mimeHeaders, $body] = self::mime($html, $attachments);
        $headers = ['From: =?UTF-8?B?' . base64_encode($fromName) . "?= <$from>", 'MIME-Version: 1.0', ...$mimeHeaders];

        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers), '-f' . $from);
    }

    /** @param list<array{name: string, content: string, type?: string}> $attachments */
    private static function smtp(string $to, string $subject, string $html, array $attachments): bool
    {
        $host = (string) Setting::get('mail.host', '');
        $port = (int) Setting::get('mail.poort', 587);
        $user = (string) Setting::get('mail.gebruiker', '');
        $pass = Setting::getSecret('mail.wachtwoord');
        [$from, $fromName] = self::sender();
        if ($host === '') {
            throw new \RuntimeException('SMTP-server is niet ingesteld.');
        }
        $ssl = $port === 465 || Setting::get('mail.beveiliging') === 'ssl';
        $socket = @stream_socket_client(($ssl ? 'ssl://' : '') . "$host:$port", $errno, $errstr, 15);
        if ($socket === false) {
            throw new \RuntimeException("SMTP-verbinding mislukt: $errstr");
        }
        stream_set_timeout($socket, 15);
        $read = static function () use ($socket): string {
            $data = '';
            while (($line = fgets($socket, 515)) !== false) {
                $data .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }

            return $data;
        };
        $cmd = static function (string $command, int $expect, string $label = '') use ($socket, $read): string {
            fwrite($socket, $command . "\r\n");
            $response = $read();
            if ((int) substr($response, 0, 3) !== $expect) {
                throw new \RuntimeException("SMTP-fout bij '" . ($label !== '' ? $label : strtok($command, ' ')) . "': " . trim($response));
            }

            return $response;
        };
        $read();
        $domain = parse_url(absolute_url('/'), PHP_URL_HOST) ?: 'localhost';
        $ehlo = $cmd("EHLO $domain", 250);
        if (!$ssl && stripos($ehlo, 'STARTTLS') !== false) {
            $cmd('STARTTLS', 220);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('SMTP: STARTTLS mislukt.');
            }
            $cmd("EHLO $domain", 250);
        }
        if ($user !== '') {
            $cmd('AUTH LOGIN', 334);
            $cmd(base64_encode($user), 334, 'AUTH gebruikersnaam');
            $cmd(base64_encode($pass), 235, 'AUTH wachtwoord');
        }
        $cmd('MAIL FROM:<' . (filter_var($user, FILTER_VALIDATE_EMAIL) ? $user : $from) . '>', 250);
        $cmd("RCPT TO:<$to>", 250);
        $cmd('DATA', 354);
        [$mimeHeaders, $body] = self::mime($html, $attachments);
        $headers = [
            'From: =?UTF-8?B?' . base64_encode($fromName) . "?= <$from>",
            "To: $to",
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            'MIME-Version: 1.0',
            ...$mimeHeaders,
            'Date: ' . date('r'),
            'Message-ID: <' . bin2hex(random_bytes(12)) . "@$domain>",
        ];
        $cmd(implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\r\n.", "\r\n..", $body) . "\r\n.", 250);
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    }
}
