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
use App\Models\User;
use App\Services\Billing;

final class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status');
        $q = $request->string('q');
        $sql = 'SELECT s.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Diensten s JOIN Gebruikers g ON g.Id = s.KlantId WHERE 1 = 1';
        $params = [];
        if ($status !== '') {
            $sql .= ' AND s.Status = ?';
            $params[] = $status;
        }
        if ($q !== '') {
            $sql .= ' AND (s.Naam LIKE ? OR s.Omschrijving LIKE ? OR s.Nummer LIKE ? OR g.Bedrijfsnaam LIKE ?)';
            array_push($params, ...array_fill(0, 4, '%' . $q . '%'));
        }

        return $this->view('beheer.services.index', ['page' => Database::page($sql, $params, 's.Id DESC', 15), 'status' => $status, 'q' => $q]);
    }

    public function create(Request $request): Response
    {
        return $this->form(new Service(['KlantId' => $request->int('klant'), 'Status' => 'actief', 'Periode' => 'maand', 'BtwTarief' => setting('facturatie.btw', 21), 'StartDatum' => date('Y-m-d'), 'VolgendeFactuur' => date('Y-m-d'), 'Prijs' => 0]));
    }

    public function store(Request $request): Response
    {
        $service = Service::create($this->input($request));

        return $this->redirect('/beheer/klanten/' . $service->KlantId . '#diensten')->with('success', 'Dienst ' . $service->Nummer . ' is aangemaakt.');
    }

    public function edit(Request $request): Response
    {
        return $this->form($this->service($request));
    }

    public function update(Request $request): Response
    {
        $service = $this->service($request);
        $service->fill($this->input($request))->save();

        return $this->redirect('/beheer/diensten/' . $service->id())->with('success', 'Dienst opgeslagen.');
    }

    public function destroy(Request $request): Response
    {
        $service = $this->service($request);
        $service->delete();

        return $this->redirect('/beheer/diensten')->with('success', 'Dienst verwijderd.');
    }

    public function invoiceNow(Request $request): Response
    {
        $service = $this->service($request);
        $customer = User::find((int) $service->KlantId);
        if ($customer === null) {
            throw new HttpException(404, 'Klant niet gevonden.');
        }
        $doc = Billing::invoiceServices($customer, [$service]);
        if ($service->Periode === 'eenmalig') {
            $service->VolgendeFactuur = null;
            $service->save();
        }

        return $this->redirect('/beheer/documenten/' . $doc->id())->with('success', 'Factuur ' . $doc->Nummer . ' aangemaakt.');
    }

    /** @return array<string, mixed> */
    private function input(Request $request): array
    {
        $data = $this->validate($request, [
            'KlantId' => 'required|exists:Gebruikers,Id', 'Naam' => 'required|max:150', 'Omschrijving' => 'max:255',
            'Periode' => 'required|in:' . implode(',', array_keys(Product::PERIODS)), 'Status' => 'required|in:' . implode(',', array_keys(Service::STATUSES)),
            'StartDatum' => 'date', 'VolgendeFactuur' => 'date', 'EindDatum' => 'date',
        ]);
        $data['ProductId'] = $request->int('ProductId') ?: null;
        $data['Prijs'] = parse_money($request->string('Prijs'));
        $data['BtwTarief'] = (float) str_replace(',', '.', $request->string('BtwTarief', '21'));

        return array_map(static fn ($v) => $v === '' ? null : $v, $data);
    }

    private function form(Service $service): Response
    {
        return $this->view('beheer.services.form', [
            'service' => $service,
            'customers' => User::query("SELECT * FROM Gebruikers WHERE Rol = 'klant' ORDER BY COALESCE(Bedrijfsnaam, Achternaam)"),
            'products' => Product::query('SELECT * FROM Producten ORDER BY Groep, Volgorde'),
        ]);
    }

    private function service(Request $request): Service
    {
        return Service::find((int) $request->param('id')) ?? throw new HttpException(404, 'Dienst niet gevonden.');
    }
}
