<?php

declare(strict_types=1);

namespace App\Controllers\Frontend;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Product;
use App\Services\Billing;
use App\Services\DomainCheck;

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
            'posts' => Database::all('SELECT * FROM BlogBerichten WHERE Zichtbaar = 1 AND PublicatieDatum <= CURDATE() ORDER BY PublicatieDatum DESC, Id DESC LIMIT 9'),
            'projects' => Database::all('SELECT * FROM PortfolioItems WHERE Zichtbaar = 1 ORDER BY Volgorde, Id DESC LIMIT 9'),
            'logos' => $logos,
            'datacenters' => Database::all('SELECT * FROM Datacenters WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
            'articles' => Database::all('SELECT * FROM KennisbankArtikelen WHERE Zichtbaar = 1 ORDER BY Weergaven DESC, Volgorde, Id LIMIT 6'),
            'extensions' => DomainCheck::extensions(),
        ]);
    }

    public function page(Request $request): Response
    {
        $page = Database::first('SELECT * FROM Paginas WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Deze pagina bestaat niet (meer).');
        $soort = self::soortForPage($page);
        $products = $soort !== null ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = ? ORDER BY Volgorde, Prijs, Id', [$soort]) : [];
        $products = $products !== [] ? $products : ((string) $page['ProductGroep'] !== '' ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Groep = ? ORDER BY Volgorde, Id', [$page['ProductGroep']]) : []);
        [$domains, $products] = self::splitExtensions($products);

        return $this->view('frontend.page', [
            'page' => $page,
            'soort' => $soort,
            'products' => $products,
            'domains' => $domains,
            'related' => Database::all('SELECT * FROM Paginas WHERE Zichtbaar = 1 AND Categorie = ? AND Id <> ? ORDER BY Groep, Volgorde LIMIT 6', [$page['Categorie'], $page['Id']]),
            'extensions' => DomainCheck::extensions(),
        ]);
    }

    public function domains(Request $request): Response
    {
        [$name, $ext] = DomainCheck::parse($request->string('domein'));
        $extensions = DomainCheck::extensions();
        $results = [];
        if ($name !== '') {
            $chosen = $ext ?? ($request->string('ext') !== '' ? ltrim($request->string('ext'), '.') : null);
            $list = $chosen !== null ? array_values(array_unique(array_merge([$chosen], $extensions))) : $extensions;
            foreach (array_slice($list, 0, 8) as $x) {
                $results[] = DomainCheck::check($name, $x);
            }
        }

        return $this->view('frontend.domains', ['name' => $name, 'results' => $results, 'extensions' => $extensions]);
    }

    public function blog(Request $request): Response
    {
        $category = $request->string('categorie');
        $where = 'Zichtbaar = 1 AND PublicatieDatum <= CURDATE()' . ($category !== '' ? ' AND Categorie = ?' : '');

        return $this->view('frontend.blog', [
            'posts' => Database::all("SELECT * FROM BlogBerichten WHERE $where ORDER BY PublicatieDatum DESC, Id DESC", $category !== '' ? [$category] : []),
            'categories' => array_column(Database::all('SELECT DISTINCT Categorie FROM BlogBerichten WHERE Zichtbaar = 1 ORDER BY Categorie'), 'Categorie'),
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

        return $this->view('frontend.portfolio', [
            'projects' => Database::all('SELECT * FROM PortfolioItems WHERE Zichtbaar = 1' . ($category !== '' ? ' AND Categorie = ?' : '') . ' ORDER BY Volgorde, Id DESC', $category !== '' ? [$category] : []),
            'categories' => array_column(Database::all('SELECT DISTINCT Categorie FROM PortfolioItems WHERE Zichtbaar = 1 ORDER BY Categorie'), 'Categorie'),
            'category' => $category,
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
        foreach (Database::all('SELECT * FROM KennisbankArtikelen WHERE ' . implode(' AND ', $where) . ' ORDER BY Categorie, Volgorde, Id', $params) as $a) {
            $articles[$a['Categorie']][] = $a;
        }

        return $this->view('frontend.knowledge', [
            'articles' => $articles, 'q' => $q, 'category' => $category,
            'categories' => Database::all('SELECT Categorie, COUNT(*) AS Aantal FROM KennisbankArtikelen WHERE Zichtbaar = 1 GROUP BY Categorie ORDER BY Categorie'),
        ]);
    }

    public function article(Request $request): Response
    {
        $article = Database::first('SELECT * FROM KennisbankArtikelen WHERE Slug = ? AND Zichtbaar = 1', [(string) $request->param('slug')]) ?? throw new HttpException(404, 'Dit artikel bestaat niet (meer).');
        Database::run('UPDATE KennisbankArtikelen SET Weergaven = Weergaven + 1 WHERE Id = ?', [$article['Id']]);

        return $this->view('frontend.article', ['article' => $article, 'related' => Database::all('SELECT * FROM KennisbankArtikelen WHERE Zichtbaar = 1 AND Categorie = ? AND Id <> ? ORDER BY Volgorde LIMIT 6', [$article['Categorie'], $article['Id']])]);
    }

    public function about(Request $request): Response
    {
        return $this->view('frontend.about', [
            'team' => Database::all('SELECT * FROM Teamleden WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
            'timeline' => Database::all('SELECT * FROM Tijdlijn WHERE Zichtbaar = 1 ORDER BY Volgorde, Jaar, Id'),
            'datacenters' => Database::all('SELECT * FROM Datacenters WHERE Zichtbaar = 1 ORDER BY Volgorde, Id'),
        ]);
    }

    public function services(Request $request): Response
    {
        $group = $request->string('groep');
        $products = $group !== ''
            ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Groep = ? ORDER BY Volgorde, Prijs, Id', [$group])
            : Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 ORDER BY Volgorde, Prijs, Id');
        [$domains, $products] = self::splitExtensions($products);
        $sections = [];
        foreach (Product::SOORTEN as $kinds) {
            foreach (array_keys($kinds) as $kind) {
                $list = array_values(array_filter($products, static fn (Product $p): bool => $p->Dienstsoort === $kind));
                if ($list !== []) {
                    $sections[$kind] = $list;
                }
            }
        }
        $rest = array_values(array_filter($products, static fn (Product $p): bool => !isset($sections[(string) $p->Dienstsoort])));
        if ($rest !== []) {
            $sections['Overige diensten'] = $rest;
        }

        return $this->view('frontend.services', [
            'kinds' => $sections,
            'domains' => $domains,
            'soortPages' => self::soortPages(),
            'group' => $group,
            'groups' => array_column(Database::all('SELECT DISTINCT Groep FROM Producten WHERE Zichtbaar = 1 ORDER BY Groep'), 'Groep'),
        ]);
    }

    public function product(Request $request): Response
    {
        $product = $this->findProduct($request);
        if ('/' . trim($request->path, '/') !== $product->path()) {
            $query = (string) ($_SERVER['QUERY_STRING'] ?? '');

            return Response::redirect($product->path() . ($query !== '' ? '?' . $query : ''), 301);
        }

        $siblings = $product->Dienstsoort !== null && !$product->isExtension()
            ? Product::query('SELECT * FROM Producten WHERE Zichtbaar = 1 AND Dienstsoort = ? ORDER BY Volgorde, Prijs, Id', [$product->Dienstsoort])
            : [];

        return $this->view('frontend.product', [
            'product' => $product,
            'siblings' => count($siblings) > 1 ? $siblings : [],
            'soortPage' => self::soortPages()[(string) $product->Dienstsoort] ?? null,
        ]);
    }

    public function order(Request $request): Response
    {
        $product = $this->findProduct($request);
        $user = Auth::user();
        if ($user === null) {
            Session::set('intended', $product->path());

            return $this->redirect('/registreren')->with('success', 'Maak een account aan of log in om ' . $product->Naam . ' te bestellen.');
        }
        if ($user->isStaff()) {
            return Response::back('/diensten')->with('error', 'Log in als klant om te bestellen (of maak de dienst aan in het beheer).');
        }
        if ($request->string('Akkoord') === '') {
            return Response::back('/diensten')->with('error', 'Ga akkoord met de voorwaarden om te bestellen.');
        }
        $doc = Billing::order($user, $product, $request->string('Notitie'));
        Billing::send($doc);
        $admin = (string) setting('bedrijf.email', '');
        if ($admin !== '') {
            Mailer::send($admin, 'Nieuwe bestelling: ' . $product->Naam, "Klant {$user->display()} ({$user->Nummer}) heeft {$product->Naam} besteld.\nFactuur: {$doc->Nummer}\n" . absolute_url('/beheer/documenten/' . $doc->id()));
        }

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Bedankt voor je bestelling! De dienst wordt geactiveerd zodra de factuur is betaald.');
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
        if ($product === null && str_starts_with($slug, 'domein-')) {
            $product = Product::firstWhere(['Slug' => substr($slug, 7), 'Zichtbaar' => 1]);
        }
        if ($product === null) {
            throw new HttpException(404, 'Deze dienst bestaat niet (meer).');
        }

        return $product;
    }
}
