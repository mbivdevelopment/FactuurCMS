<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Log;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Services\PageContent;
use App\Models\User;
use App\Services\Billing;
use App\Services\DomainCheck;
use App\Services\OnlinePayment;

final class SiteController extends Controller
{
    public function home(Request $request): Response
    {
        $logos = [];
        foreach (Database::all('SELECT * FROM Logos WHERE Zichtbaar = 1 ORDER BY (SELECT MIN(l2.Id) FROM Logos l2 WHERE l2.Groep = Logos.Groep), Volgorde, Id') as $logo) {
            $logos[$logo['Groep']][] = $logo;
        }

        return $this->view('frontend.home', [
            'featured' => Product::query("SELECT * FROM Producten WHERE Zichtbaar = 1 AND Groep <> 'Domeinen' ORDER BY Uitgelicht DESC, Volgorde, Id LIMIT 4"),
            'packages' => Product::query("SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = 'Webhosting' ORDER BY Prijs, Id LIMIT 4"),
            'posts' => Database::all('SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() ORDER BY PublicatieDatum DESC, Id DESC LIMIT 9'),
            'projects' => Database::all('SELECT * FROM PortfolioItems WHERE Zichtbaar = 1 ORDER BY Volgorde, Id DESC LIMIT 9'),
            'logos' => $logos,
            'datacenters' => Database::all('SELECT * FROM Datacenters WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
            'articles' => Database::all('SELECT * FROM KennisbankArtikelen WHERE Zichtbaar = 1 ORDER BY Weergaven DESC, Volgorde, Id LIMIT 6'),
            'extensions' => DomainCheck::extensions(),
            'extProducts' => self::extensionProducts(),
            'kindPrices' => self::kindPrices(),
            'soortPages' => self::soortPages(),
            'uptime' => self::uptime(),
            'solutionPages' => Database::all("SELECT Titel, Slug, Icoon, Samenvatting, Groep FROM Paginas WHERE Zichtbaar = 1 AND Categorie = 'cloud' ORDER BY Groep, Volgorde, Id"),
        ]);
    }

    /** Netwerkstatus: dagstatussen van de laatste 90 dagen + uptime-percentage. @return array{dagen: list<array{0:string,1:string}>, pct: string, actief: list<array>} */
    private static function uptime(): array
    {
        try {
            $rows = Database::all("SELECT Ernst, Dienst, StartTijd, EindTijd FROM Storingen WHERE StartTijd >= CURDATE() - INTERVAL 91 DAY");
        } catch (\Throwable) {
            $rows = [];
        }
        $rank = ['ok' => 0, 'plan' => 1, 'warn' => 2, 'down' => 3];
        $diensten = ['Webhosting', 'E-mail', 'SMTP Relay', 'Domeinen', 'Cloud VPS', 'Klantportaal'];
        $maps = ['*' => []];
        foreach ($rows as $r) {
            $sev = match ($r['Ernst']) { 'uitval', 'groot' => 'down', 'klein' => 'warn', default => 'plan' };
            $key = trim((string) ($r['Dienst'] ?? ''));
            $start = strtotime('today', strtotime((string) $r['StartTijd'])); $end = $r['EindTijd'] !== null ? strtotime((string) $r['EindTijd']) : time();
            for ($t = $start; $t <= $end; $t += 86400) {
                $d = date('Y-m-d', $t);
                if ($rank[$sev] > $rank[$maps[$key][$d] ?? 'ok']) { $maps[$key][$d] = $sev; }
            }
        }
        foreach ($maps as $k => $m) {
            if ($k === '*') { continue; }
            foreach ($m as $d => $sev) {
                if ($rank[$sev] > $rank[$maps['*'][$d] ?? 'ok']) { $maps['*'][$d] = $sev; }
            }
        }
        $card = static function (string $key) use ($maps, $rank): array {
            $map = $key === '*' ? $maps['*'] : array_merge($maps['*'], array_filter($maps[$key] ?? [], static fn (string $v): bool => $rank[$v] > 0));
            if ($key !== '*') {
                $map = $maps['*'];
                foreach (($maps[$key] ?? []) as $d => $sev) {
                    if ($rank[$sev] > $rank[$map[$d] ?? 'ok']) { $map[$d] = $sev; }
                }
            }
            $dagen = []; $down = 0;
            for ($i = 89; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $st = $map[$d] ?? 'ok';
                if ($st === 'down') { $down++; }
                $dagen[] = [$d, $st];
            }

            return ['dagen' => $dagen, 'pct' => number_format(100 * (90 - $down) / 90, 2, ',', ''), 'st' => ($map[date('Y-m-d')] ?? 'ok')];
        };
        $services = array_map(static fn (string $d): array => ['naam' => $d] + $card($d), $diensten);
        $alles = $card('*');
        try {
            $actief = Database::all("SELECT * FROM Storingen WHERE Status <> 'opgelost' AND Status <> 'gepland' ORDER BY StartTijd DESC LIMIT 5");
        } catch (\Throwable) {
            $actief = [];
        }

        return ['dagen' => $alles['dagen'], 'pct' => $alles['pct'], 'actief' => $actief, 'services' => $services];
    }

    /** Domeinextensies als producten, in de volgorde van de hero-extensies. @return list<Product> */
    public static function extensionProducts(): array
    {
        [$domains] = self::splitExtensions(Product::query("SELECT * FROM Producten WHERE Zichtbaar = 1 AND (Dienstsoort = 'Domeinnamen' OR (Dienstsoort IS NULL AND Groep = 'Domeinen')) ORDER BY Volgorde, Id"));

        return $domains;
    }

    /** Laagste prijs en aantal per dienstsoort. @return array<string, array{min:int, n:int, periode:string}> */
    private static function kindPrices(): array
    {
        $out = [];
        foreach (Database::all("SELECT Dienstsoort, MIN(Prijs) AS MinPrijs, COUNT(*) AS Aantal, MIN(Periode) AS Periode FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort IS NOT NULL GROUP BY Dienstsoort") as $r) {
            $out[(string) $r['Dienstsoort']] = ['min' => (int) $r['MinPrijs'], 'n' => (int) $r['Aantal'], 'periode' => (string) $r['Periode']];
        }

        return $out;
    }

    public function page(Request $request): Response
    {
        if ((string) $request->param('slug') === 'domeinnaam-registreren') {
            return $this->redirect('/domeinen');
        }
        $page = Database::first('SELECT * FROM Paginas WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Deze pagina bestaat niet (meer).');
        $soort = self::soortForPage($page);
        $products = $soort !== null ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = ? ORDER BY Volgorde, Prijs, Id', [$soort]) : [];
        $products = $products !== [] ? $products : ((string) $page['ProductGroep'] !== '' ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Groep = ? ORDER BY Volgorde, Id', [$page['ProductGroep']]) : []);
        [$domains, $products] = self::splitExtensions($products);
        if ($page['Slug'] === 'smtp-relay') {
            return $this->redirect('/smtp-relay', 301);
        }

        return $this->view('frontend.page', [
            'page' => $page,
            'soort' => $soort,
            'products' => $products,
            'domains' => $domains,
            'related' => Database::all('SELECT * FROM Paginas WHERE Zichtbaar = 1 AND Categorie = ? AND Id <> ? ORDER BY Groep, Volgorde LIMIT 6', [$page['Categorie'], $page['Id']]),
            'extensions' => DomainCheck::extensions(),
        ]);
    }

    public function smtpRelay(Request $request): Response
    {
        $page = Database::first('SELECT * FROM Paginas WHERE Slug = ? AND Zichtbaar = 1', ['smtp-relay'])
            ?? throw new HttpException(404, 'Deze pagina bestaat niet (meer).');
        $products = Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = ? ORDER BY Volgorde, Prijs, Id', ['SMTP Relay']);
        $posts = Database::all("SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() AND (Titel LIKE '%SMTP%' OR Titel LIKE '%mail%' OR Titel LIKE '%DNS%' OR Titel LIKE '%SSL%' OR Titel LIKE '%PHP%') ORDER BY PublicatieDatum DESC, Id DESC LIMIT 9");

        return $this->view('frontend.smtp-relay', [
            'page' => $page,
            'products' => $products,
            'benefits' => PageContent::benefits('SMTP Relay'),
            'steps' => PageContent::steps('SMTP Relay', true),
            'posts' => $posts !== [] ? $posts : Database::all('SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() ORDER BY PublicatieDatum DESC, Id DESC LIMIT 9'),
        ]);
    }

    public function blog(Request $request): Response
    {
        $category = $request->string('categorie');
        $categories = Database::all('SELECT Categorie, COUNT(*) AS Aantal FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() GROUP BY Categorie ORDER BY Categorie');
        if ($category === '' && $categories !== []) { $category = $categories[0]['Categorie']; }
        $where = 'Zichtbaar = 1 AND PublicatieDatum <= CURDATE()' . ($category !== '' ? ' AND Categorie = ?' : '');

        return $this->view('frontend.blog', [
            'posts' => Database::all("SELECT * FROM BlogBerichten WHERE $where ORDER BY PublicatieDatum DESC, Id DESC", $category !== '' ? [$category] : []),
            'categories' => $categories,
            'category' => $category,
        ]);
    }

    public function blogPost(Request $request): Response
    {
        $post = Database::first('SELECT * FROM BlogBerichten WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Dit bericht bestaat niet (meer).');

        return $this->view('frontend.blog-post', ['post' => $post, 'recent' => Database::all('SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND Id <> ? AND PublicatieDatum <= CURDATE() ORDER BY PublicatieDatum DESC LIMIT 4', [$post['Id']])]);
    }

    public function portfolio(Request $request): Response
    {
        $category = $request->string('categorie');
        $pageNr = max(1, $request->int('pagina'));
        $where = 'Zichtbaar = 1' . ($category !== '' ? ' AND Categorie = ?' : '');
        $params = $category !== '' ? [$category] : [];
        $total = (int) Database::scalar("SELECT COUNT(*) FROM PortfolioItems WHERE $where", $params);
        $perPage = 6;
        $pages = max(1, (int) ceil($total / $perPage));
        $pageNr = min($pageNr, $pages);

        return $this->view('frontend.portfolio', [
            'projects' => Database::all("SELECT * FROM PortfolioItems WHERE $where ORDER BY Volgorde, Id DESC LIMIT $perPage OFFSET " . (($pageNr - 1) * $perPage), $params),
            'categories' => Database::all('SELECT Categorie, COUNT(*) AS Aantal FROM PortfolioItems WHERE Zichtbaar = 1 GROUP BY Categorie ORDER BY Categorie'),
            'category' => $category,
            'page' => $pageNr,
            'pages' => $pages,
        ]);
    }

    public function portfolioItem(Request $request): Response
    {
        $item = Database::first('SELECT * FROM PortfolioItems WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Dit project bestaat niet (meer).');

        return $this->view('frontend.portfolio-item', ['item' => $item, 'more' => Database::all('SELECT * FROM PortfolioItems WHERE Zichtbaar = 1 AND Id <> ? ORDER BY Volgorde LIMIT 3', [$item['Id']])]);
    }

    public function knowledge(Request $request): Response
    {
        $q = $request->string('q');
        $category = $request->string('categorie');
        $where = ['Zichtbaar = 1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(Titel LIKE ? OR Samenvatting LIKE ? OR Inhoud LIKE ?)';
            array_push($params, "%$q%", "%$q%", "%$q%");
        }
        if ($category !== '') {
            $where[] = 'Categorie = ?';
            $params[] = $category;
        }
        $articles = [];
        $cats = Database::all('SELECT Categorie, COUNT(*) AS Aantal FROM KennisbankArtikelen WHERE Zichtbaar = 1 GROUP BY Categorie ORDER BY Categorie');
        if ($category === '' && $q === '' && $cats !== []) { $category = $cats[0]['Categorie']; $where = ['Zichtbaar = 1', 'Categorie = ?']; $params = [$category]; }
        foreach (Database::all('SELECT * FROM KennisbankArtikelen WHERE ' . implode(' AND ', $where) . ' ORDER BY Categorie, Volgorde, Id', $params) as $a) {
            $articles[$a['Categorie']][] = $a;
        }

        return $this->view('frontend.knowledge', [
            'articles' => $articles, 'q' => $q, 'category' => $category,
            'categories' => $cats,
        ]);
    }

    public function article(Request $request): Response
    {
        $article = Database::first('SELECT * FROM KennisbankArtikelen WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Dit artikel bestaat niet (meer).');
        Database::run('UPDATE KennisbankArtikelen SET Weergaven = Weergaven + 1 WHERE Id = ?', [$article['Id']]);

        return $this->view('frontend.article', ['article' => $article, 'categories' => Database::all('SELECT Categorie, COUNT(*) AS Aantal FROM KennisbankArtikelen WHERE Zichtbaar = 1 GROUP BY Categorie ORDER BY Categorie')]);
    }

    public function about(Request $request): Response
    {
        return $this->view('frontend.about', [
            'team' => Database::all('SELECT * FROM Teamleden WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
            'timeline' => Database::all('SELECT * FROM Tijdlijn WHERE Zichtbaar = 1 ORDER BY Volgorde, Jaar, Id'),
            'datacenters' => Database::all('SELECT * FROM Datacenters WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
        ]);
    }

    public function extensieRedirect(Request $request): Response
    {
        return $this->redirect('/domeinen');
    }

    public function servicesRedirect(Request $request): Response
    {
        return Response::redirect('/oplossingen/webhosting', 301);
    }

    public function product(Request $request): Response
    {
        $product = $this->findProduct($request);
        if ('/' . trim($request->path, '/') !== $product->path()) {
            $query = (string) ($_SERVER['QUERY_STRING'] ?? '');

            return Response::redirect($product->path() . ($query !== '' ? '?' . $query : ''), 301);
        }

        if (!$product->isExtension()) {
            return Response::redirect($product->path() . '/bestellen', 301);
        }

        return $this->view('frontend.product', ['product' => $product, 'siblings' => [], 'soortPage' => null]);
    }

    public function checkout(Request $request): Response
    {
        $product = $this->findProduct($request);
        $canonical = $product->path() . '/bestellen';
        if ('/' . trim($request->path, '/') !== $canonical) {
            $query = (string) ($_SERVER['QUERY_STRING'] ?? '');

            return Response::redirect($canonical . ($query !== '' ? '?' . $query : ''), 301);
        }
        $extPrijzen = [];
        foreach (DomainCheck::products() as $ext => $extProduct) {
            $extPrijzen[$ext] = ['prijs' => (int) $extProduct->Prijs, 'btw' => (float) $extProduct->BtwTarief];
        }

        return $this->view('frontend.checkout', ['product' => $product, 'methods' => OnlinePayment::enabled(), 'extPrijzen' => $extPrijzen]);
    }

    public function order(Request $request): Response
    {
        $product = $this->findProduct($request);
        $user = Auth::user();
        // Ook beheerders/medewerkers bestellen op hun eigen account.
        $hosting = $product->Periode !== 'eenmalig' && $product->Groep !== 'Domeinen' && $product->Dienstsoort !== 'Domeinnamen';
        $rules = ['Akkoord' => 'required'];
        if ($hosting) {
            $rules += ['Domeinnaam' => 'required|max:200', 'DomeinActie' => 'required|in:hebik,registreren,verhuizen'];
        }
        if ($user === null) {
            $rules += ['Voornaam' => 'required|max:80', 'Achternaam' => 'required|max:80', 'Email' => 'required|email|max:190|unique:Gebruikers,Email', 'Wachtwoord' => 'required|min:8|confirmed'];
        }
        $data = $this->validate($request, $rules);
        if ($user === null) {
            $user = User::create([
                'Rol' => 'klant', 'Voornaam' => trim((string) $data['Voornaam']), 'Achternaam' => trim((string) $data['Achternaam']),
                'Email' => strtolower(trim((string) $data['Email'])), 'Wachtwoord' => Auth::hash((string) $data['Wachtwoord']), 'Actief' => 1,
            ]);
            Log::db('registratie', $user->Email, $user->id());
            [$sub, $body] = Mailer::tpl('welkom', ['voornaam' => $user->Voornaam, 'klantnummer' => $user->Nummer, 'link' => absolute_url('/login')]);
            Mailer::send((string) $user->Email, $sub, $body);
            Auth::login($user);
            Session::forget('intended');
        }
        $domain = null;
        if ($hosting) {
            $domein = strtolower(trim((string) $data['Domeinnaam']));
            if (!preg_match('/^(?=.{4,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\\.)+[a-z]{2,}$/', $domein)) {
                return Response::back($product->path() . '/bestellen')->withInput($request->all())->with('error', 'Vul een geldige domeinnaam in, bijvoorbeeld mijnbedrijf.nl.');
            }
            if ($data['DomeinActie'] === 'registreren' || $data['DomeinActie'] === 'verhuizen') {
                $ext = strtolower(substr((string) strrchr('.' . $domein, '.'), 1));
                $extProduct = DomainCheck::products()[$ext] ?? null;
                if ($extProduct === null) {
                    return Response::back($product->path() . '/bestellen')->withInput($request->all())->with('error', 'De extensie .' . $ext . ' kunnen we niet verwerken; kies "Ik heb deze domeinnaam al".');
                }
                $domain = ['domein' => $domein, 'product' => $extProduct, 'actie' => $data['DomeinActie'] === 'verhuizen' ? 'verhuizen' : 'registreren', 'authcode' => trim((string) $request->string('EppCode'))];
            }
        }
        $gegevens = null;
        if ((string) $product->Bron === 'interserver') {
            $platform = $request->string('VpsPlatform');
            $os = $request->string('VpsOs');
            if (!isset(\App\Services\Interserver::PLATFORMS[$platform]) || !isset(\App\Services\Interserver::OS[$os])) {
                return Response::back($product->path() . '/bestellen')->withInput($request->all())->with('error', 'Kies een platform en besturingssysteem voor je VPS.');
            }
            [$osDistro, $osVersion] = explode('|', $os, 2);
            $termijn = $request->string('Termijn');
            $months = \App\Services\Billing::TERMIJNEN[$termijn][0] ?? (\App\Services\Billing::TERMIJNEN[(string) $product->Periode][0] ?? 1);
            $gegevens = ['interserver' => [
                'vpsPlatform' => $platform,
                'slices' => min(32, max(1, $request->int('VpsSlices') ?: 1)),
                'location' => min(3, max(1, $request->int('VpsLocatie') ?: 1)),
                'osDistro' => $osDistro,
                'osVersion' => $osVersion,
                'hostname' => ($hosting ? ($domein ?? '') : trim((string) $request->string('Notitie'))) ?: 'vps-' . strtolower(bin2hex(random_bytes(4))),
                'period' => min(12, $months),
            ]];
        }
        $doc = Billing::order($user, $product, $hosting ? $domein : $request->string('Notitie'), $request->string('Termijn'), $domain, $gegevens);
        Billing::send($doc);
        $admin = (string) setting('bedrijf.email', '');
        if ($admin !== '') {
            Mailer::send($admin, 'Nieuwe bestelling: ' . $product->Naam, "Klant {$user->display()} ({$user->Nummer}) heeft {$product->Naam} besteld.\nFactuur: {$doc->Nummer}\n" . absolute_url('/beheer/documenten/' . $doc->id()));
        }

        $method = $request->string('Betaalmethode');
        if ($method === 'tegoed') {
            $total = (int) $doc->Totaal;
            if (\App\Services\Wallet::balance($user) >= $total) {
                \App\Services\Wallet::mutate($user, -$total, 'Betaling ' . $doc->Nummer, $doc->id());
                Billing::registerPayment($doc, $total, date('Y-m-d'), 'Betaald met tegoed');

                return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Betaald met je tegoed!');
            }
            return $this->redirect('/klant/documenten/' . $doc->id())->with('error', 'Je tegoed is niet voldoende; betaal de factuur online of per factuur.');
        }
        if (isset(OnlinePayment::enabled()[$method])) {
            try {
                $payUrl = safe_external_url(OnlinePayment::start($doc, $method));
                return Response::redirect($payUrl ?? '/klant/documenten/' . $doc->id(), 303);
            } catch (\Throwable $e) {
                return $this->redirect('/klant/documenten/' . $doc->id())->with('error', 'Je bestelling is geplaatst, maar online betalen lukt nu niet: ' . $e->getMessage());
            }
        }

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Bedankt voor je bestelling! De dienst wordt geactiveerd zodra de factuur is betaald.');
    }

    public function status(Request $request): Response
    {
        try {
            $open = Database::all("SELECT * FROM Storingen WHERE Status <> 'opgelost' ORDER BY StartTijd DESC");
            $done = Database::all("SELECT * FROM Storingen WHERE Status = 'opgelost' ORDER BY StartTijd DESC LIMIT 30");
        } catch (\Throwable) {
            $open = $done = [];
        }

        try {
            $posts = Database::all('SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() ORDER BY PublicatieDatum DESC, Id DESC LIMIT 9');
        } catch (\Throwable) {
            $posts = [];
        }

        return $this->view('frontend.storingen', ['open' => $open, 'done' => $done, 'uptime' => self::uptime(), 'posts' => $posts]);
    }

    public function contact(Request $request): Response
    {
        return $this->view('frontend.contact');
    }

    public function sendContact(Request $request): Response
    {
        $data = $this->validate($request, ['Naam' => 'required|max:100', 'Email' => 'required|email', 'Bericht' => 'required|max:5000']);
        if ($request->string('website') !== '') {
            return $this->redirect('/contact');
        }
        Mailer::send((string) setting('bedrijf.email', 'info@localhost'), 'Contactformulier: ' . $data['Naam'], "Naam: {$data['Naam']}\nE-mail: {$data['Email']}\n\n{$data['Bericht']}");

        return $this->redirect('/contact')->with('success', 'Bedankt voor je bericht, we nemen snel contact op.');
    }

    /** Cronjob via URL (bv. dagelijks): /cron/{sleutel} uit Instellingen → Facturatie. */
    public function cron(Request $request): Response
    {
        $key = (string) setting('cron.sleutel', '');
        if ($key === '' || !hash_equals($key, (string) $request->param('key'))) {
            throw new HttpException(404, '');
        }

        return Response::html('Herhaalfacturen aangemaakt: ' . Billing::runRecurring());
    }

    /** Dienstsoort die bij een websitepagina hoort, op titel of slug (bv. "Email Hosting" = "E-mail Hosting"). @param array<string, mixed> $page */
    private static function soortForPage(array $page): ?string
    {
        $norm = static fn (string $s): string => (string) preg_replace('/[^a-z0-9]/', '', strtolower($s));
        foreach (Product::SOORTEN as $kinds) {
            foreach (array_keys($kinds) as $kind) {
                if (in_array($norm($kind), [$norm((string) $page['Titel']), $norm((string) $page['Slug'])], true)) {
                    return $kind;
                }
            }
        }

        return null;
    }

    /** @return array<string, string> dienstsoort => paginaslug */
    private static function soortPages(): array
    {
        $map = [];
        foreach (Database::all('SELECT Titel, Slug FROM Paginas WHERE Zichtbaar = 1 ORDER BY Volgorde, Id') as $page) {
            $soort = self::soortForPage($page);
            if ($soort !== null && !isset($map[$soort])) {
                $map[$soort] = (string) $page['Slug'];
            }
        }

        return $map;
    }

    /** Splitst producten in [domeinextensies (populairste eerst), pakketten]. @param list<Product> $products @return array{0: list<Product>, 1: list<Product>} */
    private static function splitExtensions(array $products): array
    {
        $domains = array_values(array_filter($products, static fn (Product $p): bool => $p->isExtension()));
        $rank = array_flip(DomainCheck::extensions());
        usort($domains, static fn (Product $a, Product $b): int => [$rank[ltrim($a->extensionLabel(), '.')] ?? 999, $a->extensionLabel()] <=> [$rank[ltrim($b->extensionLabel(), '.')] ?? 999, $b->extensionLabel()]);

        return [$domains, array_values(array_filter($products, static fn (Product $p): bool => !$p->isExtension()))];
    }

    private function findProduct(Request $request): Product
    {
        $slug = (string) $request->param('slug');
        $product = Product::firstWhere(['Slug' => $slug, 'Zichtbaar' => 1]);
        if ($product === null) {
            $legacy = [
                'smtp-relay-100' => 'smtp-starter',
                'smtp-relay-250' => 'smtp-plus',
                'smtp-relay-1000' => 'smtp-compleet',
                'smtp-relay-2500' => 'smtp-maximaal',
            ];
            $product = isset($legacy[$slug]) ? Product::firstWhere(['Slug' => $legacy[$slug], 'Zichtbaar' => 1]) : null;
        }
        if ($product === null && str_starts_with($slug, 'domein-')) {
            $product = Product::firstWhere(['Slug' => substr($slug, 7), 'Zichtbaar' => 1]);
        }
        if ($product === null) {
            throw new HttpException(404, 'Deze dienst bestaat niet (meer).');
        }

        return $product;
    }
}
