<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $kind = $request->string('soort');

        return $this->view('beheer.products.index', ['products' => Product::where($kind !== '' ? ['Dienstsoort' => $kind] : [], 'Groep, Volgorde, Id'), 'kind' => $kind]);
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
            'Kenmerken' => array_values(array_filter(array_map('trim', preg_split('/\R/', $request->string('Kenmerken')) ?: []))),
            'Prijs' => parse_money($request->string('Prijs')),
            'Instelkosten' => parse_money($request->string('Instelkosten')),
            'BtwTarief' => (float) str_replace(',', '.', $request->string('BtwTarief', '21')),
            'Uitgelicht' => $request->bool('Uitgelicht') ? 1 : 0,
            'Zichtbaar' => $request->bool('Zichtbaar') ? 1 : 0,
            'Volgorde' => (int) ($data['Volgorde'] ?? 0),
        ];
    }

    private function product(Request $request): Product
    {
        return Product::find((int) $request->param('id')) ?? throw new HttpException(404, 'Product niet gevonden.');
    }
}
