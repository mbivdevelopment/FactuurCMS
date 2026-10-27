<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Database;
use App\Core\Response;
use App\Models\Product;
use App\Models\Service;

final class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $kind = Product::soortFromSlug(basename($request->path), true) ?? $request->string('soort');
        $q = $request->string('q');
        $cat = $request->string('cat', 'domeinen');
        $catDef = Service::catFilter($cat);
        $sql = 'SELECT * FROM Producten WHERE 1 = 1';
        $params = [];
        if ($catDef !== null) {
            $real = array_values(array_filter($catDef[1], static fn (string $x): bool => $x !== 'domein'));
            if ($real !== []) {
                $sql .= ' AND Dienstsoort IN (' . implode(',', array_fill(0, count($real), '?')) . ')';
                array_push($params, ...$real);
            }
        }
        if ($kind !== '') {
            $sql .= ' AND Dienstsoort = ?';
            $params[] = $kind;
        }
        if ($q !== '') {
            $sql .= ' AND (Naam LIKE ? OR Nummer LIKE ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        $products = Product::query($sql . ' ORDER BY Nummer', $params);
        $soortCounts = [];
        foreach (Database::all('SELECT Dienstsoort AS Soort, COUNT(*) AS c FROM Producten GROUP BY Soort') as $r) {
            $soortCounts[(string) ($r['Soort'] ?? '')] = (int) $r['c'];
        }
        $catCounts = array_fill_keys(array_keys(Service::CATEGORIES), 0);
        foreach (Service::CATEGORIES as $key => $def) {
            foreach ($def[1] as $soort) {
                $catCounts[$key] += $soortCounts[$soort] ?? 0;
            }
        }
        unset($soortCounts['domein']);

        return $this->view('beheer.products.index', ['products' => $products, 'kind' => $kind, 'q' => $q, 'cat' => $cat, 'catDef' => $catDef, 'catCounts' => $catCounts, 'soorten' => $soortCounts]);
    }

    public function create(Request $request): Response
    {
        return $this->view('beheer.products.form', ['product' => new Product(['Groep' => 'Hosting', 'Dienstsoort' => in_array($request->string('soort'), array_merge(...array_map('array_keys', array_values(Product::SOORTEN))), true) ? $request->string('soort') : null, 'Periode' => 'maand', 'BtwTarief' => setting('facturatie.btw', 21), 'Zichtbaar' => 1, 'Prijs' => 0, 'Instelkosten' => 0, 'Volgorde' => Product::count() + 1])]);
    }

    public function store(Request $request): Response
    {
        $product = Product::create($this->input($request, null));

        return $this->redirect('/beheer/producten')->with('success', 'Product ' . $product->Naam . ' is aangemaakt.');
    }

    public function edit(Request $request): Response
    {
        return $this->view('beheer.products.form', ['product' => $this->product($request)]);
    }

    public function update(Request $request): Response
    {
        $product = $this->product($request);
        $product->fill($this->input($request, $product))->save();

        return $this->redirect('/beheer/producten')->with('success', 'Product opgeslagen.');
    }

    public function destroy(Request $request): Response
    {
        $this->product($request)->delete();

        return $this->redirect('/beheer/producten')->with('success', 'Product verwijderd.');
    }

    /** @return array<string, mixed> */
    private function input(Request $request, ?Product $product): array
    {
        $data = $this->validate($request, [
            'Naam' => 'required|max:120', 'Groep' => 'required|max:40', 'Omschrijving' => 'max:2000',
            'Periode' => 'required|in:' . implode(',', array_keys(Product::PERIODS)), 'Volgorde' => 'int',
        ]);
        $slug = slugify($request->string('Slug') ?: (string) $data['Naam']);
        $exists = Product::firstWhere(['Slug' => $slug]);
        if ($exists !== null && $exists->id() !== $product?->id()) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        return $data + [
            'Slug' => $slug,
            'Dienstsoort' => in_array($request->string('Dienstsoort'), array_merge(...array_map('array_keys', array_values(Product::SOORTEN))), true) ? $request->string('Dienstsoort') : null,
            'Kenmerken' => array_values(array_filter(array_map(static fn ($row): array => [
                'icoon' => isset(Product::FEATURE_ICONS[$row['icoon'] ?? '']) ? (string) $row['icoon'] : 'check',
                'tekst' => trim((string) ($row['tekst'] ?? '')),
            ], array_filter((array) ($request->input('Kenmerk') ?? []), 'is_array')), static fn (array $row): bool => $row['tekst'] !== '')),
            'Prijs' => parse_money($request->string('Prijs')),
            'Instelkosten' => parse_money($request->string('Instelkosten')),
            'BtwTarief' => (float) str_replace(',', '.', $request->string('BtwTarief', '21')),
            'Uitgelicht' => $request->bool('Uitgelicht') ? 1 : 0,
            'Zichtbaar' => $request->bool('Zichtbaar') ? 1 : 0,
            'Volgorde' => (int) ($data['Volgorde'] ?? 0),
            'Bron' => in_array($request->string('Bron'), ['qevanto', 'realtimeregister', 'interserver'], true) ? $request->string('Bron') : null,
        ];
    }

    private function product(Request $request): Product
    {
        return Product::find((int) $request->param('id')) ?? throw new HttpException(404, 'Product niet gevonden.');
    }
}
