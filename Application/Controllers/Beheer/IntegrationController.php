<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\Cryptomus;
use App\Services\Mollie;
use App\Services\QevantoPaneel;
use App\Services\RealtimeRegister;

final class IntegrationController extends Controller
{
    public const LABELS = ['realtimeregister' => 'Realtime Register', 'mollie' => 'Mollie', 'cryptomus' => 'Cryptomus', 'qevanto' => 'Qevanto hostingpaneel'];
    private const STATUS_MAP = ['active' => 'actief', 'suspended' => 'opgeschort', 'ended' => 'opgezegd'];

    public function index(Request $request): Response
    {
        return $this->view('beheer.integrations.index', ['set' => self::configured()]);
    }

    /** @return array<string, bool> */
    public static function configured(): array
    {
        return [
            'realtimeregister' => Setting::getSecret('rr.apikey') !== '',
            'mollie' => Setting::getSecret('mollie.apikey') !== '',
            'cryptomus' => Setting::getSecret('cryptomus.apikey') !== '' && (string) setting('cryptomus.merchant', '') !== '',
            'qevanto' => Setting::getSecret('qevanto.token') !== '' && (string) setting('qevanto.url', '') !== '',
        ];
    }

    public function save(Request $request): Response
    {
        $name = $this->name($request);
        $secret = static function (string $key, string $field) use ($request): void {
            $value = trim($request->string($field));
            if ($value !== '') {
                Setting::setSecret($key, $value);
            }
        };
        switch ($name) {
            case 'realtimeregister':
                $secret('rr.apikey', 'rr_apikey');
                Setting::set('rr.customer', trim($request->string('rr_customer')));
                Setting::set('rr.url', $request->string('rr_url') === RealtimeRegister::OTE ? RealtimeRegister::OTE : RealtimeRegister::LIVE);
                Setting::set('rr.marge', max(0, min(500, $request->int('rr_marge'))));
                break;
            case 'mollie':
                $secret('mollie.apikey', 'mollie_apikey');
                Setting::set('mollie.actief', $request->bool('mollie_actief'));
                break;
            case 'cryptomus':
                Setting::set('cryptomus.merchant', trim($request->string('cryptomus_merchant')));
                $secret('cryptomus.apikey', 'cryptomus_apikey');
                Setting::set('cryptomus.actief', $request->bool('cryptomus_actief'));
                break;
            case 'qevanto':
                Setting::set('qevanto.url', rtrim(trim($request->string('qevanto_url')), '/'));
                $secret('qevanto.token', 'qevanto_token');
                break;
        }

        return $this->redirect('/beheer/koppelingen#' . $name)->with('success', self::LABELS[$name] . ' opgeslagen.');
    }

    public function test(Request $request): Response
    {
        $name = $this->name($request);
        try {
            $message = match ($name) {
                'realtimeregister' => RealtimeRegister::test(),
                'mollie' => Mollie::test(),
                'cryptomus' => Cryptomus::test(),
                default => QevantoPaneel::test(),
            };
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen#' . $name)->with('error', self::LABELS[$name] . ': ' . $e->getMessage());
        }

        return $this->redirect('/beheer/koppelingen#' . $name)->with('success', $message);
    }

    public function rrDomains(Request $request): Response
    {
        try {
            $domains = RealtimeRegister::domains();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen#realtimeregister')->with('error', $e->getMessage());
        }
        $products = self::tldProducts();
        $present = self::domainServices();
        $owners = self::domainOwners();
        $rows = [];
        foreach ($domains as $domain => $d) {
            $rows[] = $d + ['imported' => isset($present[$domain]), 'klant' => $owners[$domain] ?? 0, 'product' => self::productFor($domain, $products)];
        }

        return $this->view('beheer.integrations.rr-domains', ['rows' => $rows, 'customers' => self::customers()]);
    }

    public function rrImportDomains(Request $request): Response
    {
        try {
            $domains = RealtimeRegister::domains();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen/realtimeregister/domeinen')->with('error', $e->getMessage());
        }
        $chosen = (array) ($request->input('klant') ?? []);
        $products = self::tldProducts();
        $present = self::domainServices();
        $customers = self::customers();
        $done = 0;
        $skipped = [];
        foreach ((array) ($request->input('domein') ?? []) as $domain) {
            $domain = strtolower((string) $domain);
            $d = $domains[$domain] ?? null;
            if ($d === null || isset($present[$domain])) {
                continue;
            }
            $klant = (int) ($chosen[$domain] ?? 0);
            if (!isset($customers[$klant])) {
                $skipped[] = $domain;
                continue;
            }
            $product = self::productFor($domain, $products);
            $tld = substr($domain, (int) strpos($domain, '.') + 1);
            Service::create([
                'KlantId' => $klant,
                'ProductId' => $product['Id'] ?? null,
                'Naam' => $product['Naam'] ?? ('.' . $tld . '-domeinnaam'),
                'Omschrijving' => $domain,
                'Prijs' => (int) ($product['Prijs'] ?? 0),
                'BtwTarief' => $product['BtwTarief'] ?? 21,
                'Periode' => 'jaar',
                'Status' => 'actief',
                'StartDatum' => date('Y-m-d'),
                'VolgendeFactuur' => $d['expiry'] ?? date('Y-m-d', strtotime('+1 year')),
                'Gegevens' => json_encode(['realtimeregister' => ['verloopt' => $d['expiry'], 'autoRenew' => $d['autoRenew'], 'status' => $d['status']]], JSON_UNESCAPED_UNICODE),
                'Bron' => 'realtimeregister',
                'ExternId' => $domain,
            ]);
            $present[$domain] = true;
            $done++;
        }
        $message = $done . ' domein(en) als dienst geïmporteerd.' . ($skipped !== [] ? ' Zonder klant overgeslagen: ' . implode(', ', $skipped) . '.' : '');

        return $this->redirect('/beheer/koppelingen/realtimeregister/domeinen')->with($done > 0 || $skipped === [] ? 'success' : 'error', $message);
    }

    public function rrTlds(Request $request): Response
    {
        try {
            $prices = RealtimeRegister::tldPrices();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen#realtimeregister')->with('error', $e->getMessage());
        }
        $margin = (int) setting('rr.marge', 30);
        $products = self::tldProducts();
        $rows = [];
        foreach ($prices as $tld => $p) {
            $product = $products[$tld] ?? null;
            $rows[] = ['tld' => $tld, 'buy' => self::buyPrice($p)] + $p + ['product' => $product, 'sale' => $product !== null ? (int) $product['Prijs'] : self::withMargin(self::buyPrice($p), $margin)];
        }

        return $this->view('beheer.integrations.rr-tlds', ['rows' => $rows, 'margin' => $margin]);
    }

    public function rrImportTlds(Request $request): Response
    {
        try {
            $prices = RealtimeRegister::tldPrices();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen/realtimeregister/extensies')->with('error', $e->getMessage());
        }
        $margin = (int) setting('rr.marge', 30);
        $sales = (array) ($request->input('prijs') ?? []);
        $products = self::tldProducts();
        $new = 0;
        $updated = 0;
        foreach ((array) ($request->input('tld') ?? []) as $tld) {
            $tld = strtolower((string) $tld);
            if (!isset($prices[$tld])) {
                continue;
            }
            $buy = self::buyPrice($prices[$tld]);
            $sale = parse_money($sales[$tld] ?? '');
            if ($sale <= 0) {
                $sale = self::withMargin($buy, $margin);
            }
            if (($product = $products[$tld] ?? null) !== null) {
                Database::update('Producten', ['Prijs' => $sale, 'Inkoopprijs' => $buy, 'Bron' => 'realtimeregister', 'ExternId' => $tld, 'BijgewerktOp' => now()], ['Id' => $product['Id']]);
                $updated++;
                continue;
            }
            Product::create([
                'Groep' => 'Domeinen',
                'Dienstsoort' => 'Domeinnamen',
                'Naam' => '.' . $tld,
                'Slug' => self::uniqueSlug('domein-' . trim((string) preg_replace('/[^a-z0-9]+/', '-', $tld), '-')),
                'Omschrijving' => 'Registratie of verhuizing van een .' . $tld . '-domeinnaam.',
                'Kenmerken' => '[]',
                'Prijs' => $sale,
                'Inkoopprijs' => $buy,
                'Periode' => 'jaar',
                'BtwTarief' => 21,
                'Zichtbaar' => 1,
                'Bron' => 'realtimeregister',
                'ExternId' => $tld,
            ]);
            $products[$tld] = ['Id' => 0, 'Naam' => '.' . $tld, 'Prijs' => $sale];
            $new++;
        }

        return $this->redirect('/beheer/koppelingen/realtimeregister/extensies')->with('success', $new . ' nieuwe extensie(s) als product toegevoegd, ' . $updated . ' bestaande bijgewerkt.');
    }

    public function qevanto(Request $request): Response
    {
        try {
            $subs = QevantoPaneel::subscriptions();
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen#qevanto')->with('error', $e->getMessage());
        }
        $imported = self::externIds('qevanto');
        $byEmail = [];
        $byCompany = [];
        foreach (Database::all("SELECT Id, Email, Bedrijfsnaam FROM Gebruikers WHERE Rol = 'klant'") as $c) {
            $byEmail[strtolower((string) $c['Email'])] = (int) $c['Id'];
            if ((string) $c['Bedrijfsnaam'] !== '') {
                $byCompany[strtolower((string) $c['Bedrijfsnaam'])] = (int) $c['Id'];
            }
        }
        $owners = self::domainOwners();
        $byProduct = [];
        foreach (Database::all('SELECT Id, Naam FROM Producten') as $p) {
            $byProduct[strtolower((string) $p['Naam'])] = (int) $p['Id'];
        }
        $rows = [];
        foreach ($subs as $s) {
            $user = (array) ($s['user'] ?? []);
            $domains = array_values(array_filter(array_map(static fn ($d): string => strtolower((string) ($d['domain'] ?? '')), (array) ($s['siteDots'] ?? []))));
            $klant = $byEmail[strtolower((string) ($user['email'] ?? ''))] ?? $byCompany[strtolower((string) ($user['company'] ?? ''))] ?? 0;
            foreach ($domains as $domain) {
                $klant = $klant ?: ($owners[$domain] ?? 0);
            }
            $rows[] = [
                'id' => (int) ($s['id'] ?? 0),
                'number' => (string) ($s['number'] ?? ''),
                'label' => (string) ($s['label'] ?? ''),
                'status' => (string) ($s['status'] ?? ''),
                'package' => (string) ($s['package']['name'] ?? ''),
                'server' => (string) ($s['server']['name'] ?? ''),
                'user' => $user,
                'domains' => $domains,
                'imported' => isset($imported[(string) ($s['id'] ?? '')]),
                'klant' => $klant ?: (($user['email'] ?? '') !== '' ? 'nieuw' : ''),
                'product' => $byProduct[strtolower((string) ($s['package']['name'] ?? ''))] ?? 0,
            ];
        }
        $products = [];
        foreach (Database::all('SELECT Id, Naam, Groep, Prijs, Periode FROM Producten ORDER BY Groep, Naam') as $p) {
            $products[(int) $p['Id']] = $p['Naam'] . ' (' . $p['Groep'] . ', ' . money((int) $p['Prijs']) . ' per ' . $p['Periode'] . ')';
        }

        return $this->view('beheer.integrations.qevanto', ['rows' => $rows, 'customers' => self::customers(), 'products' => $products, 'firstInvoice' => date('Y-m-01', strtotime('first day of next month'))]);
    }

    public function qevantoImport(Request $request): Response
    {
        try {
            $subs = [];
            foreach (QevantoPaneel::subscriptions() as $s) {
                $subs[(int) ($s['id'] ?? 0)] = $s;
            }
        } catch (\Throwable $e) {
            return $this->redirect('/beheer/koppelingen/qevanto/diensten')->with('error', $e->getMessage());
        }
        $chosen = (array) ($request->input('klant') ?? []);
        $chosenProducts = (array) ($request->input('product') ?? []);
        $first = $request->string('eerste_factuur');
        $first = preg_match('/^\d{4}-\d{2}-\d{2}$/', $first) === 1 ? $first : date('Y-m-01', strtotime('first day of next month'));
        $imported = self::externIds('qevanto');
        $customers = self::customers();
        $done = 0;
        $created = 0;
        $skipped = [];
        foreach ((array) ($request->input('abonnement') ?? []) as $id) {
            $id = (int) $id;
            $s = $subs[$id] ?? null;
            if ($s === null || isset($imported[(string) $id])) {
                continue;
            }
            $user = (array) ($s['user'] ?? []);
            $choice = (string) ($chosen[$id] ?? '');
            $klant = (int) $choice;
            if ($choice === 'nieuw') {
                [$klant, $isNew] = self::customerFromPanel($user);
                $created += $isNew ? 1 : 0;
                $customers[$klant] = true;
            }
            if (!isset($customers[$klant])) {
                $skipped[] = (string) ($s['number'] ?? $id);
                continue;
            }
            $product = ($pid = (int) ($chosenProducts[$id] ?? 0)) > 0 ? Product::find($pid) : null;
            $domains = array_values(array_filter(array_map(static fn ($d): string => (string) ($d['domain'] ?? ''), (array) ($s['siteDots'] ?? []))));
            $status = self::STATUS_MAP[(string) ($s['status'] ?? '')] ?? 'in_afwachting';
            Service::create([
                'KlantId' => $klant,
                'ProductId' => $product?->id(),
                'Naam' => $product !== null ? (string) $product->Naam : ((string) ($s['package']['name'] ?? '') ?: 'Hosting'),
                'Omschrijving' => $domains[0] ?? ((string) ($s['label'] ?? '') ?: (string) ($s['number'] ?? '')),
                'Prijs' => $product !== null ? (int) $product->Prijs : 0,
                'BtwTarief' => $product !== null ? $product->BtwTarief : 21,
                'Periode' => $product !== null && $product->Periode !== 'eenmalig' ? (string) $product->Periode : 'maand',
                'Status' => $status,
                'StartDatum' => !empty($s['createdAt']) ? date('Y-m-d', (int) strtotime((string) $s['createdAt'])) : date('Y-m-d'),
                'VolgendeFactuur' => $status === 'actief' ? $first : null,
                'Gegevens' => json_encode(['qevanto' => ['nummer' => $s['number'] ?? null, 'systeemgebruiker' => $s['unixUser'] ?? null, 'pakket' => $s['package']['name'] ?? null, 'server' => $s['server']['name'] ?? null, 'domeinen' => $domains]], JSON_UNESCAPED_UNICODE),
                'Bron' => 'qevanto',
                'ExternId' => (string) $id,
            ]);
            $imported[(string) $id] = true;
            $done++;
        }
        $message = $done . ' hostingabonnement(en) als dienst geïmporteerd' . ($created > 0 ? ', ' . $created . ' nieuwe klant(en) aangemaakt' : '') . '.' . ($skipped !== [] ? ' Zonder klant overgeslagen: ' . implode(', ', $skipped) . '.' : '');

        return $this->redirect('/beheer/koppelingen/qevanto/diensten')->with($done > 0 || $skipped === [] ? 'success' : 'error', $message);
    }

    /** @param array<string, mixed> $user @return array{0: int, 1: bool} */
    private static function customerFromPanel(array $user): array
    {
        $email = strtolower(trim((string) ($user['email'] ?? '')));
        if ($email === '') {
            return [0, false];
        }
        $existing = Database::first('SELECT Id, Rol FROM Gebruikers WHERE Email = ?', [$email]);
        if ($existing !== null) {
            return [$existing['Rol'] === 'klant' ? (int) $existing['Id'] : 0, false];
        }
        $name = preg_split('/\s+/', trim((string) ($user['name'] ?? '')), 2) ?: [''];
        $customer = User::create([
            'Rol' => 'klant',
            'Email' => $email,
            'Wachtwoord' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
            'Voornaam' => (string) ($name[0] ?? ''),
            'Achternaam' => (string) ($name[1] ?? ''),
            'Bedrijfsnaam' => ((string) ($user['company'] ?? '')) ?: null,
            'Land' => 'NL',
            'Actief' => 1,
        ]);

        return [$customer->id(), true];
    }

    private function name(Request $request): string
    {
        $name = (string) $request->param('naam');
        if (!isset(self::LABELS[$name])) {
            throw new HttpException(404, 'Koppeling niet gevonden.');
        }

        return $name;
    }

    /** @return array<int, string> */
    private static function customers(): array
    {
        $out = [];
        foreach (Database::all("SELECT Id, Nummer, Voornaam, Achternaam, Bedrijfsnaam FROM Gebruikers WHERE Rol = 'klant' ORDER BY Nummer") as $c) {
            $out[(int) $c['Id']] = $c['Nummer'] . ' · ' . ((string) $c['Bedrijfsnaam'] ?: trim($c['Voornaam'] . ' ' . $c['Achternaam']));
        }

        return $out;
    }

    /** Domeinproducten per extensie. @return array<string, array<string, mixed>> */
    private static function tldProducts(): array
    {
        $out = [];
        foreach (Database::all("SELECT Id, Naam, Prijs, BtwTarief, Bron, ExternId FROM Producten WHERE Groep = 'Domeinen' OR Dienstsoort = 'Domeinnamen' ORDER BY Bron = 'realtimeregister' DESC, Id") as $p) {
            $tld = $p['Bron'] === 'realtimeregister' && (string) $p['ExternId'] !== ''
                ? (string) $p['ExternId']
                : (preg_match('/^\.([a-z0-9]+(?:\.[a-z0-9]+)*)/i', (string) $p['Naam'], $m) === 1 ? strtolower($m[1]) : '');
            if ($tld !== '' && !isset($out[$tld])) {
                $out[$tld] = $p;
            }
        }

        return $out;
    }

    /** @param array<string, array<string, mixed>> $products @return array<string, mixed>|null */
    private static function productFor(string $domain, array $products): ?array
    {
        $parts = explode('.', $domain);
        for ($i = 1, $n = count($parts); $i < $n; $i++) {
            $tld = implode('.', array_slice($parts, $i));
            if (isset($products[$tld])) {
                return $products[$tld];
            }
        }

        return null;
    }

    /** Domeinen die al als domeindienst bestaan. @return array<string, true> */
    private static function domainServices(): array
    {
        $out = self::externIds('realtimeregister');
        foreach (Database::all("SELECT d.Omschrijving FROM Diensten d JOIN Producten p ON p.Id = d.ProductId WHERE p.Groep = 'Domeinen' OR p.Dienstsoort = 'Domeinnamen'") as $d) {
            $out[strtolower(trim((string) $d['Omschrijving']))] = true;
        }

        return $out;
    }

    /** Klant per domein, op basis van bestaande diensten en het e-maildomein. @return array<string, int> */
    private static function domainOwners(): array
    {
        $out = [];
        foreach (Database::all("SELECT Id, Email FROM Gebruikers WHERE Rol = 'klant'") as $c) {
            $out[strtolower(substr((string) $c['Email'], (int) strpos((string) $c['Email'], '@') + 1))] = (int) $c['Id'];
        }
        foreach (Database::all('SELECT Omschrijving, KlantId FROM Diensten WHERE Omschrijving IS NOT NULL') as $d) {
            $out[strtolower(trim((string) $d['Omschrijving']))] = (int) $d['KlantId'];
        }

        return $out;
    }

    /** @return array<string, true> */
    private static function externIds(string $source): array
    {
        $out = [];
        foreach (Database::all('SELECT ExternId FROM Diensten WHERE Bron = ? AND ExternId IS NOT NULL', [$source]) as $d) {
            $out[(string) $d['ExternId']] = true;
        }

        return $out;
    }

    /** @param array{create: int, renew: int, transfer: int} $p */
    private static function buyPrice(array $p): int
    {
        return max($p['create'], $p['renew']);
    }

    private static function withMargin(int $cents, int $margin): int
    {
        return (int) ceil($cents * (100 + $margin) / 100);
    }

    private static function uniqueSlug(string $slug): string
    {
        $candidate = $slug;
        for ($i = 2; (int) Database::scalar('SELECT COUNT(*) FROM Producten WHERE Slug = ?', [$candidate]) > 0; $i++) {
            $candidate = $slug . '-' . $i;
        }

        return $candidate;
    }
}
