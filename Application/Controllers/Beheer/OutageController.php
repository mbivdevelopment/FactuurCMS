<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;

/** Beheer van storingen en gepland onderhoud voor de statuspagina. */
final class OutageController extends Controller
{
    public const SEVERITIES = ['onderhoud' => 'Onderhoud', 'klein' => 'Kleine verstoring', 'groot' => 'Grote verstoring', 'uitval' => 'Uitval'];
    public const STATUSES = ['onderzocht' => 'Wordt onderzocht', 'bevestigd' => 'Bevestigd', 'monitoring' => 'Wordt gemonitord', 'opgelost' => 'Opgelost', 'gepland' => 'Gepland'];
    public const SERVICES = ['Webhosting', 'E-mail & SMTP', 'Domeinen', 'Cloud VPS', 'Klantportaal'];

    public function index(Request $request): Response
    {
        return $this->view('beheer.storingen.index', [
            'open' => Database::all("SELECT * FROM Storingen WHERE Status <> 'opgelost' ORDER BY FIELD(Status, 'onderzocht', 'bevestigd', 'monitoring', 'gepland'), StartTijd DESC"),
            'done' => Database::page("SELECT * FROM Storingen WHERE Status = 'opgelost'", [], 'StartTijd DESC', 15),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('beheer.storingen.form', ['i' => null]);
    }

    public function store(Request $request): Response
    {
        $data = $this->input($request);
        $data['AangemaaktOp'] = now();
        Database::insert('Storingen', $data);

        return $this->redirect('/beheer/storingen')->with('success', 'Storing aangemaakt en zichtbaar op de statuspagina.');
    }

    public function edit(Request $request): Response
    {
        return $this->view('beheer.storingen.form', ['i' => $this->outage($request)]);
    }

    public function update(Request $request): Response
    {
        $i = $this->outage($request);
        $data = $this->input($request);
        $data['BijgewerktOp'] = now();
        Database::update('Storingen', $data, ['Id' => (int) $i['Id']]);

        return $this->redirect('/beheer/storingen')->with('success', 'Storing bijgewerkt.');
    }

    public function status(Request $request): Response
    {
        $i = $this->outage($request);
        $status = $request->string('Status');
        if (!array_key_exists($status, self::STATUSES)) {
            throw HttpException::unprocessable('Onbekende status.');
        }
        $data = ['Status' => $status, 'BijgewerktOp' => now()];
        if ($status === 'opgelost' && empty($i['EindTijd'])) {
            $data['EindTijd'] = now();
        }
        Database::update('Storingen', $data, ['Id' => (int) $i['Id']]);

        return $this->redirect('/beheer/storingen')->with('success', 'Status aangepast naar ' . self::STATUSES[$status] . '.');
    }

    public function destroy(Request $request): Response
    {
        $i = $this->outage($request);
        Database::delete('Storingen', ['Id' => (int) $i['Id']]);

        return $this->redirect('/beheer/storingen')->with('success', 'Storing verwijderd.');
    }

    private function input(Request $request): array
    {
        $data = $this->validate($request, [
            'Titel' => 'required|max:190',
            'Omschrijving' => 'nullable',
            'Dienst' => 'nullable|max:60',
            'Ernst' => 'required|in:' . implode(',', array_keys(self::SEVERITIES)),
            'Status' => 'required|in:' . implode(',', array_keys(self::STATUSES)),
            'StartTijd' => 'required',
            'EindTijd' => 'nullable',
        ]);
        $data['Dienst'] = trim((string) $data['Dienst']) ?: null;
        $data['Omschrijving'] = trim((string) $data['Omschrijving']) ?: null;
        $data['StartTijd'] = str_replace('T', ' ', (string) $data['StartTijd']);
        $data['EindTijd'] = trim((string) $data['EindTijd']) !== '' ? str_replace('T', ' ', (string) $data['EindTijd']) : null;
        if ($data['Status'] === 'opgelost' && $data['EindTijd'] === null) {
            $data['EindTijd'] = now();
        }

        return $data;
    }

    private function outage(Request $request): array
    {
        $i = Database::first('SELECT * FROM Storingen WHERE Id = ?', [(int) $request->param('id')]);
        if ($i === null) {
            throw HttpException::notFound('Storing niet gevonden.');
        }

        return $i;
    }
}
