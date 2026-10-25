<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/** Controleert via WHOIS (poort 43) of een domeinnaam vrij is en koppelt het bijbehorende Domeinen-product. */
final class DomainCheck
{
    private const SERVERS = [
        'nl' => 'whois.domain-registry.nl', 'com' => 'whois.verisign-grs.com', 'net' => 'whois.verisign-grs.com',
        'eu' => 'whois.eu', 'be' => 'whois.dns.be', 'org' => 'whois.publicinterestregistry.org', 'de' => 'whois.denic.de',
        'io' => 'whois.nic.io', 'info' => 'whois.nic.info', 'online' => 'whois.nic.online', 'shop' => 'whois.nic.shop',
    ];
    private const FREE = '/no match|not found|is free|status:\s*(available|free)|no entries found|no data found|domain not found|no object found/i';

    /** @return list<string> */
    public static function extensions(): array
    {
        $list = array_map(static fn (string $x): string => ltrim(strtolower(trim($x)), '.'), explode(',', (string) setting('hero.extensies', '.nl,.com,.eu,.be,.net,.org')));

        return array_values(array_unique(array_filter($list, static fn (string $x): bool => isset(self::SERVERS[$x]))));
    }

    /** Splitst invoer als "mijnbedrijf", "www.mijnbedrijf.nl" of "mijnbedrijf.com" in [naam, extensie]. */
    public static function parse(string $input): array
    {
        $input = strtolower(trim($input));
        $input = (string) preg_replace('#^(https?://)?(www\.)?#', '', $input);
        $input = explode('/', $input)[0];
        $parts = explode('.', $input, 2);
        $name = (string) preg_replace('/[^a-z0-9-]/', '', $parts[0]);
        $name = trim(substr($name, 0, 63), '-');

        return [$name, isset($parts[1]) && isset(self::SERVERS[$parts[1]]) ? $parts[1] : null];
    }

    /** @return array{domain: string, ext: string, status: string, product: ?array<string, mixed>} status: vrij|bezet|onbekend */
    public static function check(string $name, string $ext): array
    {
        $domain = $name . '.' . $ext;
        $status = 'onbekend';
        $socket = @fsockopen(self::SERVERS[$ext], 43, $errno, $errstr, 4);
        if ($socket !== false) {
            stream_set_timeout($socket, 4);
            fwrite($socket, ($ext === 'de' ? '-T dn ' : '') . $domain . "\r\n");
            $answer = '';
            while (!feof($socket) && strlen($answer) < 20000) {
                $chunk = fread($socket, 2048);
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $answer .= $chunk;
            }
            fclose($socket);
            if (trim($answer) !== '') {
                $status = preg_match(self::FREE, $answer) ? 'vrij' : 'bezet';
            }
        }
        if ($status === 'onbekend' && function_exists('checkdnsrr') && @checkdnsrr($domain, 'NS')) {
            $status = 'bezet';
        }
        $product = Database::first("SELECT * FROM Producten WHERE Groep = 'Domeinen' AND Zichtbaar = 1 AND (Slug LIKE ? OR Naam LIKE ?) ORDER BY Volgorde LIMIT 1", [$ext . '-%', '.' . $ext . '%']);

        return ['domain' => $domain, 'ext' => $ext, 'status' => $status, 'product' => $product];
    }
}
