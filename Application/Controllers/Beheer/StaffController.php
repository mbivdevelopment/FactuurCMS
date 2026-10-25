<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;

final class StaffController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('beheer.staff.index', ['staff' => User::query("SELECT * FROM Gebruikers WHERE Rol IN ('beheerder','medewerker') ORDER BY Nummer")]);
    }

    public function create(Request $request): Response
    {
        return $this->view('beheer.staff.form', ['member' => new User(['Rol' => 'medewerker', 'Actief' => 1])]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request, ['Voornaam' => 'required|max:80', 'Achternaam' => 'max:80', 'Email' => 'required|email|unique:Gebruikers,Email', 'Telefoon' => 'max:40', 'Rol' => 'required|in:beheerder,medewerker', 'Wachtwoord' => 'required|min:8']);
        $data['Wachtwoord'] = Auth::hash((string) $data['Wachtwoord']);
        User::create($data + ['Actief' => 1]);

        return $this->redirect('/beheer/medewerkers')->with('success', 'Medewerker aangemaakt.');
    }

    public function edit(Request $request): Response
    {
        return $this->view('beheer.staff.form', ['member' => $this->member($request)]);
    }

    public function update(Request $request): Response
    {
        $member = $this->member($request);
        $data = $this->validate($request, ['Voornaam' => 'required|max:80', 'Achternaam' => 'max:80', 'Email' => 'required|email|unique:Gebruikers,Email,' . $member->id(), 'Telefoon' => 'max:40', 'Rol' => 'required|in:beheerder,medewerker']);
        if ($member->id() === Auth::id()) {
            $data['Rol'] = $member->Rol;
            $data['Actief'] = 1;
        } else {
            $data['Actief'] = $request->bool('Actief') ? 1 : 0;
        }
        if ($request->string('Wachtwoord') !== '') {
            $this->validate($request, ['Wachtwoord' => 'min:8']);
            $data['Wachtwoord'] = Auth::hash($request->string('Wachtwoord'));
        }
        $member->fill($data)->save();

        return $this->redirect('/beheer/medewerkers')->with('success', 'Medewerker opgeslagen.');
    }

    public function destroy(Request $request): Response
    {
        $member = $this->member($request);
        if ($member->id() === Auth::id()) {
            return Response::back('/beheer/medewerkers')->with('error', 'Je kunt jezelf niet verwijderen.');
        }
        $member->delete();

        return $this->redirect('/beheer/medewerkers')->with('success', 'Medewerker verwijderd.');
    }

    private function member(Request $request): User
    {
        $member = User::find((int) $request->param('id'));
        if ($member === null || !$member->isStaff()) {
            throw new HttpException(404, 'Medewerker niet gevonden.');
        }

        return $member;
    }
}
