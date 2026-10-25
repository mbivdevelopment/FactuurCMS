<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Log;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;

final class AuthController extends Controller
{
    public function loginForm(Request $request): Response
    {
        return $this->view('auth.login');
    }

    public function login(Request $request): Response
    {
        $this->validate($request, ['Email' => 'required|email', 'Wachtwoord' => 'required']);
        $user = Auth::attempt($request->string('Email'), $request->string('Wachtwoord'));
        if ($user === null) {
            Log::db('login_mislukt', $request->string('Email'));

            return Response::back('/login')->with('error', 'Onjuist e-mailadres of wachtwoord.')->withInput(['Email' => $request->string('Email')]);
        }
        Auth::login($user);
        $intended = (string) Session::get('intended', '');
        Session::forget('intended');

        return $this->redirect($intended !== '' && str_starts_with($intended, '/') && !str_starts_with($intended, '//') ? $intended : Auth::homeFor($user));
    }

    public function registerForm(Request $request): Response
    {
        return $this->view('auth.register');
    }

    public function register(Request $request): Response
    {
        $data = $this->validate($request, [
            'Voornaam' => 'required|max:80',
            'Achternaam' => 'required|max:80',
            'Bedrijfsnaam' => 'max:150',
            'Email' => 'required|email|max:190|unique:Gebruikers,Email',
            'Telefoon' => 'max:40',
            'Wachtwoord' => 'required|min:8|confirmed',
        ]);
        $user = User::create([
            'Rol' => 'klant',
            'Voornaam' => $data['Voornaam'],
            'Achternaam' => $data['Achternaam'],
            'Bedrijfsnaam' => ($data['Bedrijfsnaam'] ?? '') ?: null,
            'Email' => strtolower(trim((string) $data['Email'])),
            'Telefoon' => ($data['Telefoon'] ?? '') ?: null,
            'Wachtwoord' => Auth::hash((string) $data['Wachtwoord']),
            'Actief' => 1,
        ]);
        Log::db('registratie', $user->Email, $user->id());
        [$sub, $body] = Mailer::tpl('welkom', ['voornaam' => $user->Voornaam, 'klantnummer' => $user->Nummer, 'link' => absolute_url('/login')]);
        Mailer::send((string) $user->Email, $sub, $body);
        Auth::login($user);
        $intended = (string) Session::get('intended', '');
        Session::forget('intended');

        return $this->redirect($intended !== '' && str_starts_with($intended, '/') ? $intended : '/klant')->with('success', 'Welkom! Je account is aangemaakt.');
    }

    public function forgotForm(Request $request): Response
    {
        return $this->view('auth.forgot');
    }

    public function forgot(Request $request): Response
    {
        $this->validate($request, ['Email' => 'required|email']);
        $user = User::firstWhere(['Email' => strtolower($request->string('Email')), 'Actief' => 1]);
        if ($user !== null) {
            $token = bin2hex(random_bytes(32));
            Database::update('Gebruikers', ['ResetToken' => hash('sha256', $token), 'ResetVerloopt' => date('Y-m-d H:i:s', time() + 3600)], ['Id' => $user->id()]);
            [$sub, $body] = Mailer::tpl('wachtwoord', ['voornaam' => $user->Voornaam, 'link' => absolute_url('/wachtwoord-herstellen/' . $token)]);
            Mailer::send((string) $user->Email, $sub, $body);
        }

        return Response::back('/login')->with('success', 'Als dit e-mailadres bekend is, ontvang je een mail met een herstellink.');
    }

    public function resetForm(Request $request): Response
    {
        return $this->view('auth.reset', ['token' => (string) $request->param('token')]);
    }

    public function reset(Request $request): Response
    {
        $this->validate($request, ['Wachtwoord' => 'required|min:8|confirmed']);
        $row = Database::first('SELECT Id FROM Gebruikers WHERE ResetToken = ? AND ResetVerloopt > ?', [hash('sha256', (string) $request->param('token')), now()]);
        if ($row === null) {
            return $this->redirect('/wachtwoord-vergeten')->with('error', 'Deze herstellink is ongeldig of verlopen.');
        }
        Database::update('Gebruikers', ['Wachtwoord' => Auth::hash($request->string('Wachtwoord')), 'ResetToken' => null, 'ResetVerloopt' => null], ['Id' => $row['Id']]);

        return $this->redirect('/login')->with('success', 'Je wachtwoord is gewijzigd. Je kunt nu inloggen.');
    }

    public function logout(Request $request): Response
    {
        Auth::logout();

        return $this->redirect('/login')->with('success', 'Je bent uitgelogd.');
    }
}
