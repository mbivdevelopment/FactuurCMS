<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Log;
use App\Core\Request;
use App\Core\Response;
use App\Models\Document;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;

final class CustomerController extends Controller
{
    private const RULES = [
        'Voornaam' => 'required|max:80', 'Achternaam' => 'required|max:80', 'Bedrijfsnaam' => 'max:150', 'Telefoon' => 'max:40',
        'Adres' => 'max:150', 'Postcode' => 'max:12', 'Plaats' => 'max:80', 'Land' => 'max:2', 'KvkNummer' => 'max:20',
        'BtwNummer' => 'max:30', 'Notities' => 'max:5000', 'Type' => 'in:klant,reseller,medewerker,beheerder',
    ];

    public function index(Request $request): Response
    {
        $q = $request->string('q');
        $type = $request->string('type');
        $sql = "SELECT g.*, (SELECT COUNT(*) FROM Diensten s WHERE s.KlantId = g.Id AND s.Status = 'actief') AS Diensten,
                (SELECT COALESCE(SUM(Totaal - Betaald), 0) FROM Documenten d WHERE d.KlantId = g.Id AND d.Soort = 'factuur' AND d.Status = 'open') AS Openstaand
                FROM Gebruikers g WHERE 1=1";
        $params = [];
        if ($type !== '') {
            $sql .= ' AND g.Type = ?';
            $params[] = $type;
        }
        if ($q !== '') {
            $sql .= ' AND (g.Email LIKE ? OR g.Bedrijfsnaam LIKE ? OR g.Nummer LIKE ? OR CONCAT(g.Voornaam, " ", g.Achternaam) LIKE ? OR g.Plaats LIKE ?)';
            $params = array_fill(0, 5, '%' . $q . '%');
        }

        return $this->view('beheer.customers.index', ['page' => Database::page($sql, $params, 'g.Nummer ASC', 15), 'q' => $q, 'type' => $type]);
    }

    public function create(Request $request): Response
    {
        return $this->view('beheer.customers.form', ['customer' => new User(['Land' => 'NL', 'Actief' => 1])]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request, self::RULES + ['Email' => 'required|email|unique:Gebruikers,Email']);
        $data = array_map(static fn ($v) => $v === '' ? null : $v, $data);
        $password = $request->string('Wachtwoord') !== '' ? $request->string('Wachtwoord') : bin2hex(random_bytes(6));
        $customer = User::create($data + ['Rol' => 'klant', 'Wachtwoord' => Auth::hash($password), 'Actief' => 1, 'Land' => $data['Land'] ?? 'NL']);
        Log::db('klant', 'Klant aangemaakt: ' . $customer->Email, Auth::id());

        return $this->redirect('/beheer/klanten/' . $customer->id())->with('success', 'Klant ' . $customer->Nummer . ' is aangemaakt.');
    }

    public function show(Request $request): Response
    {
        $customer = $this->customer($request);
        $cat = $request->string('cat');
        $catDef = Service::CATEGORIES[$cat] ?? null;
        $sql = 'SELECT s.* FROM Diensten s LEFT JOIN Producten p ON p.Id = s.ProductId WHERE s.KlantId = ?';
        $params = [$customer->id()];
        if ($catDef !== null) {
            $real = array_values(array_filter($catDef[1], static fn (string $x): bool => $x !== 'domein'));
            $or = [];
            if ($real !== []) {
                $or[] = 'p.Dienstsoort IN (' . implode(',', array_fill(0, count($real), '?')) . ')';
                array_push($params, ...$real);
            }
            if (in_array('domein', $catDef[1], true)) {
                $or[] = "s.Bron IN ('domein', 'realtimeregister')";
            }
            if ($or !== []) {
                $sql .= ' AND (' . implode(' OR ', $or) . ')';
            }
        }
        $services = array_map(static fn (array $row): Service => new Service($row), Database::all($sql . ' ORDER BY s.Nummer DESC', $params));
        $soortCounts = [];
        foreach (Database::all(
            'SELECT p.Dienstsoort AS Soort, s.Bron, COUNT(*) AS c FROM Diensten s LEFT JOIN Producten p ON p.Id = s.ProductId WHERE s.KlantId = ? GROUP BY Soort, s.Bron',
            [$customer->id()]
        ) as $r) {
            $soort = (string) ($r['Soort'] ?? '');
            if ($soort !== '') {
                $soortCounts[$soort] = ($soortCounts[$soort] ?? 0) + (int) $r['c'];
            }
            if (in_array((string) $r['Bron'], ['domein', 'realtimeregister'], true)) {
                $soortCounts['domein'] = ($soortCounts['domein'] ?? 0) + (int) $r['c'];
            }
        }
        $catCounts = array_fill_keys(array_keys(Service::CATEGORIES), 0);
        foreach (Service::CATEGORIES as $key => $def) {
            foreach ($def[1] as $soort) {
                $catCounts[$key] += $soortCounts[$soort] ?? 0;
            }
        }

        return $this->view('beheer.customers.show', [
            'customer' => $customer,
            'services' => $services,
            'allServices' => Service::where(['KlantId' => $customer->id()]),
            'cat' => $cat,
            'catDef' => $catDef,
            'catCounts' => $catCounts,
            'totalDiensten' => (int) Database::scalar('SELECT COUNT(*) FROM Diensten WHERE KlantId = ?', [$customer->id()]),
            'invoices' => Document::where(['KlantId' => $customer->id(), 'Soort' => 'factuur'], 'Datum DESC, Id DESC'),
            'quotes' => Document::where(['KlantId' => $customer->id(), 'Soort' => 'offerte'], 'Datum DESC, Id DESC'),
            'tickets' => Ticket::where(['KlantId' => $customer->id()]),
            'open' => (int) Database::scalar("SELECT COALESCE(SUM(Totaal - Betaald), 0) FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status = 'open'", [$customer->id()]),
            'revenue' => (int) Database::scalar("SELECT COALESCE(SUM(Betaald), 0) FROM Documenten WHERE KlantId = ? AND Soort = 'factuur'", [$customer->id()]),
        ]);
    }

    public function update(Request $request): Response
    {
        $customer = $this->customer($request);
        $data = $this->validate($request, self::RULES + ['Email' => 'required|email|unique:Gebruikers,Email,' . $customer->id()]);
        $data = array_map(static fn ($v) => $v === '' ? null : $v, $data);
        $data['Actief'] = $request->bool('Actief') ? 1 : 0;
        if ($request->string('Wachtwoord') !== '') {
            $this->validate($request, ['Wachtwoord' => 'min:8']);
            $data['Wachtwoord'] = Auth::hash($request->string('Wachtwoord'));
        }
        $customer->fill($data)->save();

        return $this->redirect('/beheer/klanten/' . $customer->id() . '#gegevens')->with('success', 'Klantgegevens opgeslagen.');
    }

    public function destroy(Request $request): Response
    {
        $customer = $this->customer($request);
        Log::db('klant', 'Klant verwijderd: ' . $customer->Email, Auth::id());
        $customer->delete();

        return $this->redirect('/beheer/klanten')->with('success', 'Klant is verwijderd.');
    }

    public function impersonate(Request $request): Response
    {
        $customer = $this->customer($request);
        Auth::impersonate(Auth::user(), $customer);

        return $this->redirect('/klant');
    }

    public function stopImpersonating(Request $request): Response
    {
        $customer = Auth::user();
        Auth::stopImpersonating();

        return $this->redirect($customer ? '/beheer/klanten/' . $customer->id() : '/beheer');
    }

    private function customer(Request $request): User
    {
        $customer = User::find((int) $request->param('id'));
        if ($customer === null) {
            throw new HttpException(404, 'Klant niet gevonden.');
        }

        return $customer;
    }
}
