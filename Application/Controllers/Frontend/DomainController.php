<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Log;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Models\User;
use App\Services\Billing;
use App\Services\DomainCheck;
use App\Services\OnlinePayment;
use App\Services\RealtimeRegister;

/** Domeinen zoeken en bestellen in stappen: winkelwagen → extra's → gegevens → overzicht en betalen. */
final class DomainController extends Controller
{
    public const COUNTRIES = ['NL' => 'Nederland', 'BE' => 'België', 'DE' => 'Duitsland', 'FR' => 'Frankrijk', 'LU' => 'Luxemburg', 'GB' => 'Verenigd Koninkrijk', 'ES' => 'Spanje', 'IT' => 'Italië', 'AT' => 'Oostenrijk', 'CH' => 'Zwitserland', 'US' => 'Verenigde Staten'];

    public function search(Request $request): Response
    {
        $raw = $request->string('domein');
        if ($request->string('ext') !== '') {
            $raw = DomainCheck::parse($raw)[0] . '.' . ltrim(strtolower(trim($request->string('ext'))), '.');
        }
        [$name, $ext] = DomainCheck::parse($raw);
        $products = DomainCheck::products();
        $main = $ext !== null && isset($products[$ext]) ? $ext : array_key_first($products);
        $preferred = DomainCheck::extensions();
        $popular = array_values(array_filter(array_keys($products), static fn ($x): bool => in_array((string) $x, $preferred, true) && $x !== $main));
        foreach (array_keys($products) as $x) {
            if (count($popular) >= 4) {
                break;
            }
            if ($x !== $main && !in_array($x, $popular, true)) {
                $popular[] = $x;
            }
        }

        return $this->view('frontend.domains', ['name' => $name, 'typedExt' => $ext, 'main' => $main, 'popular' => array_slice($popular, 0, 8), 'products' => $products, 'cart' => self::cart()]);
    }

    public function check(Request $request): Response
    {
        [$name, $ext] = DomainCheck::parse($request->string('domein'));
        if ($name === '' || $ext === null || !isset(DomainCheck::products()[$ext])) {
            return Response::json(['status' => 'onbekend', 'premium' => false], 422);
        }

        return Response::json(DomainCheck::status($name . '.' . $ext));
    }

    public function add(Request $request): Response
    {
        [$name, $ext] = DomainCheck::parse($request->string('domein'));
        $back = '/domeinen?domein=' . urlencode($name);
        $product = $ext !== null ? (DomainCheck::products()[$ext] ?? null) : null;
        if ($name === '' || $product === null) {
            return $this->reply($request, $back, 'Deze domeinnaam kunnen we niet leveren.', false);
        }
        $domain = $name . '.' . $ext;
        $action = $request->string('actie') === 'verhuizen' ? 'verhuizen' : 'registreren';
        if ($action === 'registreren' && DomainCheck::status($domain)['status'] === 'bezet') {
            return $this->reply($request, $back, $domain . ' is al geregistreerd. Is hij van jou? Kies dan Verhuizen.', false);
        }
        $cart = (array) Session::get('domeinmand', []);
        $cart[$domain] = ['domein' => $domain, 'actie' => $action, 'productId' => $product->id(), 'authcode' => (string) ($cart[$domain]['authcode'] ?? '')];
        Session::set('domeinmand', $cart);

        return $this->reply($request, $back, $domain . ' staat in je winkelwagen.', true);
    }

    public function remove(Request $request): Response
    {
        $cart = (array) Session::get('domeinmand', []);
        unset($cart[strtolower($request->string('domein'))]);
        Session::set('domeinmand', $cart);
        $back = $request->string('terug');

        return $this->reply($request, str_starts_with($back, '/domeinen') ? $back : '/domeinen/winkelwagen', 'Verwijderd uit je winkelwagen.', true);
    }

    public function basket(Request $request): Response
    {
        return $this->step(1);
    }

    public function saveBasket(Request $request): Response
    {
        $codes = (array) $request->input('Autorisatiecode', []);
        $cart = (array) Session::get('domeinmand', []);
        foreach ($cart as $domain => $item) {
            if ($item['actie'] === 'verhuizen') {
                $cart[$domain]['authcode'] = mb_substr(trim((string) ($codes[$domain] ?? '')), 0, 120);
            }
        }
        Session::set('domeinmand', $cart);

        return $this->redirect($cart === [] ? '/domeinen' : '/domeinen/extras');
    }

    public function extras(Request $request): Response
    {
        return $this->guard() ?? $this->step(2);
    }

    public function saveExtras(Request $request): Response
    {
        $hosting = $request->int('Hosting');
        $ids = array_map(static fn (Product $p): int => $p->id(), self::hostingProducts());
        $ns = [];
        if ($request->string('Nameservers') === 'eigen') {
            $ns = array_values(array_unique(array_filter(array_map(static fn ($v): string => strtolower(trim((string) $v)), (array) $request->input('NS', [])))));
            foreach ($ns as $host) {
                if (!preg_match('/^(?=.{4,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $host)) {
                    return $this->redirect('/domeinen/extras')->with('error', $host . ' is geen geldige nameserver.');
                }
            }
            if (count($ns) < 2) {
                return $this->redirect('/domeinen/extras')->with('error', 'Vul minimaal twee eigen nameservers in.');
            }
        }
        Session::set('domeinextra', ['hosting' => in_array($hosting, $ids, true) ? $hosting : 0, 'ns' => $ns]);

        return $this->redirect('/domeinen/gegevens');
    }

    public function details(Request $request): Response
    {
        if (($redirect = $this->guard()) !== null) {
            return $redirect;
        }
        if (Auth::user() === null) {
            Session::set('intended', '/domeinen/gegevens');
        }

        return $this->step(3);
    }

    public function saveDetails(Request $request): Response
    {
        if (($redirect = $this->guard()) !== null) {
            return $redirect;
        }
        $user = Auth::user();
        if ($user?->isStaff()) {
            return $this->redirect('/domeinen/gegevens')->with('error', 'Log in als klant om te bestellen.');
        }
        $rules = [
            'Voornaam' => 'required|max:80', 'Achternaam' => 'required|max:80', 'Bedrijfsnaam' => 'max:150',
            'Adres' => 'required|max:150', 'Postcode' => 'required|max:12', 'Plaats' => 'required|max:80', 'Land' => 'required|max:2', 'Telefoon' => 'required|max:40',
        ];
        if ($user === null) {
            $rules += ['Email' => 'required|email|max:190|unique:Gebruikers,Email', 'Wachtwoord' => 'required|min:8|confirmed'];
        }
        $data = $this->validate($request, $rules);
        $country = isset(self::COUNTRIES[$data['Land']]) ? (string) $data['Land'] : 'NL';
        try {
            RealtimeRegister::voice((string) $data['Telefoon'], $country);
        } catch (\RuntimeException) {
            return Response::back('/domeinen/gegevens')->withInput($request->all())->with('error', 'Vul een geldig telefoonnummer in, bijvoorbeeld 0612345678.');
        }
        $holder = [
            'Voornaam' => trim((string) $data['Voornaam']), 'Achternaam' => trim((string) $data['Achternaam']), 'Bedrijfsnaam' => trim((string) ($data['Bedrijfsnaam'] ?? '')) ?: null,
            'Adres' => trim((string) $data['Adres']), 'Postcode' => strtoupper(trim((string) $data['Postcode'])), 'Plaats' => trim((string) $data['Plaats']), 'Land' => $country, 'Telefoon' => trim((string) $data['Telefoon']),
        ];
        if ($user === null) {
            $user = User::create($holder + ['Rol' => 'klant', 'Email' => strtolower(trim((string) $data['Email'])), 'Wachtwoord' => Auth::hash((string) $data['Wachtwoord']), 'Actief' => 1]);
            Log::db('registratie', $user->Email, $user->id());
            Mailer::send((string) $user->Email, 'Welkom bij ' . company_name(), "Beste {$user->Voornaam},\n\nJe account is aangemaakt. Je klantnummer is {$user->Nummer}.\nInloggen kan via " . absolute_url('/login') . "\n\nMet vriendelijke groet,\n" . company_name());
            Auth::login($user);
            Session::forget('intended');
        } else {
            $user->fill($holder)->save();
        }

        return $this->redirect('/domeinen/overzicht');
    }

    public function overview(Request $request): Response
    {
        if (($redirect = $this->guard()) !== null) {
            return $redirect;
        }
        $user = Auth::user();
        if ($user === null || $user->isStaff() || trim((string) $user->Adres) === '' || trim((string) $user->Telefoon) === '') {
            return $this->redirect('/domeinen/gegevens');
        }

        return $this->step(4);
    }

    public function order(Request $request): Response
    {
        if (($redirect = $this->guard()) !== null) {
            return $redirect;
        }
        $user = Auth::user();
        if ($user === null || $user->isStaff()) {
            return $this->redirect('/domeinen/gegevens');
        }
        if ($request->string('Akkoord') === '') {
            return $this->redirect('/domeinen/overzicht')->with('error', 'Ga akkoord met de voorwaarden om te bestellen.');
        }
        $items = self::cart();
        $extra = self::extra();
        $hosting = $extra['hosting'] > 0 ? Product::find($extra['hosting']) : null;
        $doc = Billing::orderDomains($user, $items, $hosting, $extra['ns']);
        Billing::send($doc);
        Session::forget('domeinmand');
        Session::forget('domeinextra');
        $admin = (string) setting('bedrijf.email', '');
        if ($admin !== '') {
            Mailer::send($admin, 'Nieuwe bestelling: domeinnamen', "Klant {$user->display()} ({$user->Nummer}) heeft besteld: " . implode(', ', array_map(static fn (array $i): string => $i['domein'] . ' (' . $i['actie'] . ')', $items)) . ($hosting !== null ? ' + ' . $hosting->Naam : '') . "\nFactuur: {$doc->Nummer}\n" . absolute_url('/beheer/documenten/' . $doc->id()));
        }
        $method = $request->string('Betaalmethode');
        if (isset(OnlinePayment::enabled()[$method])) {
            try {
                return Response::redirect(OnlinePayment::start($doc, $method), 303);
            } catch (\Throwable $e) {
                return $this->redirect('/klant/documenten/' . $doc->id())->with('error', 'Je bestelling is geplaatst, maar online betalen lukt nu niet: ' . $e->getMessage());
            }
        }

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Bedankt voor je bestelling! Zodra de factuur is betaald, leggen we je domeinnamen vast.');
    }

    private function step(int $step): Response
    {
        $cart = self::cart();
        $extra = self::extra();
        $hosting = $extra['hosting'] > 0 ? Product::find($extra['hosting']) : null;

        return $this->view('frontend.domain-checkout', [
            'step' => $step,
            'cart' => $cart,
            'extra' => $extra,
            'hosting' => $hosting,
            'hostingProducts' => $step === 2 ? self::hostingProducts() : [],
            'user' => Auth::user(),
            'countries' => self::COUNTRIES,
            'methods' => OnlinePayment::enabled(),
            'totals' => self::totals($cart, $hosting),
        ]);
    }

    private function guard(): ?Response
    {
        return self::cart() === [] ? $this->redirect('/domeinen')->with('error', 'Je winkelwagen is leeg. Zoek eerst een domeinnaam.') : null;
    }

    private function reply(Request $request, string $to, string $message, bool $ok): Response
    {
        if (str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
            return Response::json(['ok' => $ok, 'message' => $message, 'cart' => self::cartJson()], $ok ? 200 : 422);
        }

        return $this->redirect($to)->with($ok ? 'success' : 'error', $message);
    }

    /** @return list<Product> */
    private static function hostingProducts(): array
    {
        return Product::query("SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = 'Webhosting' ORDER BY Volgorde, Prijs, Id");
    }

    /** @return array{hosting: int, ns: list<string>} */
    private static function extra(): array
    {
        $extra = (array) Session::get('domeinextra', []);

        return ['hosting' => (int) ($extra['hosting'] ?? 0), 'ns' => array_values(array_map('strval', (array) ($extra['ns'] ?? [])))];
    }

    /** @return list<array{domein: string, actie: string, product: Product, authcode: string}> */
    public static function cart(): array
    {
        $out = [];
        foreach ((array) Session::get('domeinmand', []) as $item) {
            $product = Product::find((int) ($item['productId'] ?? 0));
            if ($product !== null && $product->Zichtbaar) {
                $out[] = ['domein' => (string) $item['domein'], 'actie' => (string) $item['actie'], 'product' => $product, 'authcode' => (string) ($item['authcode'] ?? '')];
            }
        }

        return $out;
    }

    /** @return array{count: int, total: string, items: list<array{domein: string, actie: string, prijs: string}>} */
    private static function cartJson(): array
    {
        $cart = self::cart();

        return [
            'count' => count($cart),
            'total' => money(array_sum(array_map(static fn (array $i): int => (int) $i['product']->Prijs, $cart))),
            'items' => array_map(static fn (array $i): array => ['domein' => $i['domein'], 'actie' => $i['actie'] === 'verhuizen' ? 'Verhuizen' : 'Registreren', 'prijs' => money((int) $i['product']->Prijs)], $cart),
        ];
    }

    /** @param list<array{domein: string, actie: string, product: Product, authcode: string}> $cart @return array{lines: list<array{0: string, 1: string, 2: int}>, subtotal: int, vat: int, total: int} */
    private static function totals(array $cart, ?Product $hosting): array
    {
        $lines = [];
        $vat = 0;
        foreach ($cart as $i) {
            $lines[] = [$i['domein'], ($i['actie'] === 'verhuizen' ? 'Verhuizen' : 'Registreren') . ' · 1 jaar', (int) $i['product']->Prijs];
            $vat += (int) round((int) $i['product']->Prijs * (float) $i['product']->BtwTarief / 100);
        }
        if ($hosting !== null) {
            $first = (int) $hosting->Prijs + (int) $hosting->Instelkosten;
            $lines[] = [$hosting->Naam, period_label((string) $hosting->Periode) . ((int) $hosting->Instelkosten > 0 ? ' · incl. instelkosten' : ''), $first];
            $vat += (int) round($first * (float) $hosting->BtwTarief / 100);
        }
        $subtotal = array_sum(array_column($lines, 2));

        return ['lines' => $lines, 'subtotal' => $subtotal, 'vat' => $vat, 'total' => $subtotal + $vat];
    }
}
