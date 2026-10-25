<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

/** Controleert of een domeinnaam vrij is (Realtime Register of WHOIS) en welke extensies te bestellen zijn. */
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

    /** Splitst invoer als "mijnbedrijf", "www.mijnbedrijf.nl" of "mijnbedrijf.co.uk" in [naam, extensie]. @return array{0: string, 1: ?string} */
    public static function parse(string $input): array
    {
        $input = strtolower(trim($input));
        $input = (string) preg_replace('#^(https?://)?(www\.)?#', '', $input);
        $input = explode('/', $input)[0];
        $parts = explode('.', $input, 2);
        $name = (string) preg_replace('/[^a-z0-9-]/', '', $parts[0]);
        $name = trim(substr($name, 0, 63), '-');
        $ext = isset($parts[1]) && preg_match('/^[a-z0-9]+(\.[a-z0-9]+)?$/', $parts[1]) ? $parts[1] : null;

        return [$name, $ext];
    }

    /** Domeinextensies die je kunt bestellen, populairste eerst. @return array<string, Product> extensie => product */
    public static function products(): array
    {
        $rr = RealtimeRegister::configured();
        $out = [];
        foreach (Product::query("SELECT * FROM Producten WHERE Zichtbaar = 1 AND (Dienstsoort = 'Domeinnamen' OR (Dienstsoort IS NULL AND Groep = 'Domeinen')) ORDER BY Bron = 'realtimeregister' DESC, Volgorde, Id") as $p) {
            $x = ltrim($p->extensionLabel(), '.');
            if (!isset($out[$x]) && ($rr || isset(self::SERVERS[$x]))) {
                $out[$x] = $p;
            }
        }
        $rank = array_flip(self::extensions());
        uksort($out, static fn (string $a, string $b): int => [$rank[$a] ?? 999, $a] <=> [$rank[$b] ?? 999, $b]);

        return $out;
    }

    private const EUROPE = ['nl', 'be', 'eu', 'de', 'fr', 'es', 'it', 'at', 'ch', 'lu', 'uk', 'co.uk', 'dk', 'se', 'no', 'pl', 'pt', 'ie', 'fi', 'cz', 'frl', 'amsterdam', 'brussels', 'vlaanderen'];
    private const GENERIC = ['com', 'net', 'org', 'info', 'biz', 'io', 'co', 'me', 'tv', 'cc', 'name', 'mobi', 'pro'];

    /** Tabblad op de bestelpagina: europa, generiek of nieuw. */
    public static function category(string $ext): string
    {
        $last = substr((string) strrchr('.' . $ext, '.'), 1);

        return in_array($ext, self::EUROPE, true) || in_array($last, self::EUROPE, true) ? 'europa' : (in_array($ext, self::GENERIC, true) ? 'generiek' : 'nieuw');
    }

    /** Is een domeinnaam vrij? Via Realtime Register als dat is ingesteld, anders via WHOIS. @return array{status: string, premium: bool} status: vrij|bezet|onbekend */
    public static function status(string $domain): array
    {
        if (RealtimeRegister::configured()) {
            try {
                return RealtimeRegister::check($domain);
            } catch (\Throwable) {
                return ['status' => 'onbekend', 'premium' => false];
            }
        }
        [, $ext] = explode('.', $domain, 2) + [1 => ''];
        $status = 'onbekend';
        $socket = isset(self::SERVERS[$ext]) ? @fsockopen(self::SERVERS[$ext], 43, $errno, $errstr, 4) : false;
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

        return ['status' => $status, 'premium' => false];
    }
}
