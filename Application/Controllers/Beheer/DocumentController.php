<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Log;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Models\Document;
use App\Models\Product;
use App\Models\User;
use App\Services\Billing;
use App\Services\DocumentPdf;

/** Facturen en offertes (tabel Documenten). */
final class DocumentController extends Controller
{
    public function index(Request $request, string $kind): Response
    {
        $status = $request->string('status');
        $q = $request->string('q');
        $sql = 'SELECT d.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Documenten d JOIN Gebruikers g ON g.Id = d.KlantId WHERE d.Soort = ?';
        $params = [$kind];
        if ($status === 'vervallen') {
            $sql .= " AND d.Status = 'open' AND d.VervalDatum < CURDATE()";
        } elseif ($status === 'verlopen') {
            $sql .= " AND d.Status = 'verzonden' AND d.VervalDatum < CURDATE()";
        } elseif ($status !== '') {
            $sql .= ' AND d.Status = ?';
            $params[] = $status;
        }
        if ($q !== '') {
            $sql .= ' AND (d.Nummer LIKE ? OR d.Onderwerp LIKE ? OR g.Bedrijfsnaam LIKE ? OR CONCAT(g.Voornaam, " ", g.Achternaam) LIKE ?)';
            array_push($params, ...array_fill(0, 4, '%' . $q . '%'));
        }
        $page = Database::page($sql, $params, 'd.Nummer ASC', 15);
        $page['rows'] = array_map(static fn (array $r): Document => new Document($r), $page['rows']);
        $totals = Database::first("SELECT COALESCE(SUM(Totaal), 0) AS Totaal, COALESCE(SUM(CASE WHEN Status IN ('open','verzonden') THEN Totaal - Betaald ELSE 0 END), 0) AS Open FROM Documenten WHERE Soort = ? AND Status <> 'concept'", [$kind]);

        return $this->view('beheer.documents.index', ['kind' => $kind, 'page' => $page, 'status' => $status, 'q' => $q, 'totals' => $totals]);
    }

    public function create(Request $request, string $kind): Response
    {
        $date = date('Y-m-d');
        $doc = new Document(['Soort' => $kind, 'KlantId' => $request->int('klant'), 'Datum' => $date, 'VervalDatum' => Billing::dueDate($date, $kind === 'offerte'), 'Status' => 'concept']);

        return $this->form($doc, [['Omschrijving' => '', 'Aantal' => 1, 'Prijs' => 0, 'BtwTarief' => (float) setting('facturatie.btw', 21)]]);
    }

    public function store(Request $request, string $kind): Response
    {
        [$data, $lines] = $this->input($request);
        $doc = Document::create($data + ['Soort' => $kind]);
        $doc->syncLines($lines);
        Log::db('document', $doc->label() . ' ' . $doc->Nummer . ' aangemaakt', Auth::id());

        return $this->afterSave($request, $doc, 'aangemaakt');
    }

    public function show(Request $request): Response
    {
        $doc = $this->doc($request);

        return $this->view('beheer.documents.show', ['doc' => $doc, 'credits' => Document::where(['BronId' => $doc->id()])]);
    }

    public function edit(Request $request): Response
    {
        $doc = $this->doc($request);

        return $this->form($doc, $doc->lines());
    }

    public function update(Request $request): Response
    {
        $doc = $this->doc($request);
        [$data, $lines] = $this->input($request);
        $doc->fill($data)->save();
        $doc->syncLines($lines);

        return $this->afterSave($request, $doc, 'opgeslagen');
    }

    public function destroy(Request $request): Response
    {
        $doc = $this->doc($request);
        $kind = $doc->isQuote() ? 'offertes' : 'facturen';
        Log::db('document', $doc->label() . ' ' . $doc->Nummer . ' verwijderd', Auth::id());
        $doc->delete();

        return $this->redirect('/beheer/' . $kind)->with('success', 'Document verwijderd.');
    }

    public function pdf(Request $request): Response
    {
        $doc = $this->doc($request);
        $disposition = $request->string('download') !== '' ? 'attachment' : 'inline';

        return new Response(DocumentPdf::render($doc), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => $disposition . '; filename="' . $doc->label() . '-' . $doc->Nummer . '.pdf"']);
    }

    public function send(Request $request): Response
    {
        $doc = $this->doc($request);
        $ok = Billing::send($doc);

        return $this->redirect('/beheer/documenten/' . $doc->id())->with($ok ? 'success' : 'error', $ok ? $doc->label() . ' is verstuurd naar ' . $doc->customer()?->Email . '.' : 'Versturen mislukt: ' . (Mailer::$lastError ?? 'onbekende fout'));
    }

    public function status(Request $request): Response
    {
        $doc = $this->doc($request);
        $allowed = array_keys($doc->isQuote() ? Document::QUOTE_STATUSES : Document::INVOICE_STATUSES);
        $status = $request->string('Status');
        if (!in_array($status, $allowed, true) || $status === 'vervallen' || $status === 'verlopen') {
            throw new HttpException(422, 'Ongeldige status.');
        }
        $doc->Status = $status;
        if ($status === 'betaald' && (int) $doc->Betaald < (int) $doc->Totaal) {
            Billing::registerPayment($doc, (int) $doc->Totaal - (int) $doc->Betaald, date('Y-m-d'), 'Handmatig op betaald gezet');
        }
        $doc->save();

        return $this->redirect('/beheer/documenten/' . $doc->id())->with('success', 'Status gewijzigd naar ' . $doc->statusLabel() . '.');
    }

    public function payment(Request $request): Response
    {
        $doc = $this->doc($request);
        $amount = parse_money($request->string('Bedrag'));
        $date = $request->string('Datum') ?: date('Y-m-d');
        if ($amount <= 0 || strtotime($date) === false) {
            return Response::back()->with('error', 'Vul een geldig bedrag en datum in.');
        }
        Billing::registerPayment($doc, $amount, $date, $request->string('Notitie'));

        return $this->redirect('/beheer/documenten/' . $doc->id())->with('success', 'Betaling van ' . money($amount) . ' geregistreerd.');
    }

    public function toInvoice(Request $request): Response
    {
        $quote = $this->doc($request);
        if (!$quote->isQuote()) {
            throw new HttpException(422, 'Alleen offertes kunnen worden omgezet.');
        }
        $invoice = $this->copy($quote, 'factuur', 1, 'Offerte ' . $quote->Nummer . ($quote->Onderwerp ? ': ' . $quote->Onderwerp : ''));
        $quote->Status = 'geaccepteerd';
        $quote->save();

        return $this->redirect('/beheer/documenten/' . $invoice->id())->with('success', 'Factuur ' . $invoice->Nummer . ' is aangemaakt uit de offerte.');
    }

    public function credit(Request $request): Response
    {
        $invoice = $this->doc($request);
        if ($invoice->isQuote() || (int) $invoice->Totaal <= 0) {
            throw new HttpException(422, 'Deze factuur kan niet worden gecrediteerd.');
        }
        $credit = $this->copy($invoice, 'factuur', -1, 'Creditering van factuur ' . $invoice->Nummer);
        $credit->Status = 'open';
        $credit->BronId = $invoice->id();
        $credit->save();
        $invoice->Status = 'gecrediteerd';
        $invoice->save();

        return $this->redirect('/beheer/documenten/' . $credit->id())->with('success', 'Creditfactuur ' . $credit->Nummer . ' aangemaakt.');
    }

    public function duplicate(Request $request): Response
    {
        $doc = $this->doc($request);
        $copy = $this->copy($doc, (string) $doc->Soort, 1, (string) $doc->Onderwerp);

        return $this->redirect('/beheer/documenten/' . $copy->id() . '/bewerken')->with('success', 'Kopie ' . $copy->Nummer . ' aangemaakt.');
    }

    private function copy(Document $source, string $kind, int $sign, string $subject): Document
    {
        $date = date('Y-m-d');
        $doc = Document::create([
            'Soort' => $kind, 'KlantId' => $source->KlantId, 'Status' => 'concept', 'Datum' => $date,
            'VervalDatum' => Billing::dueDate($date, $kind === 'offerte'), 'Onderwerp' => $subject, 'Opmerking' => $source->Opmerking,
        ]);
        $doc->syncLines(array_map(static fn (array $l): array => [
            'Omschrijving' => (string) $l['Omschrijving'], 'Aantal' => (float) $l['Aantal'], 'Prijs' => $sign * (int) $l['Prijs'],
            'BtwTarief' => (float) $l['BtwTarief'], 'DienstId' => $l['DienstId'],
        ], $source->lines()));

        return $doc;
    }

    /** @return array{0: array<string, mixed>, 1: list<array<string, mixed>>} */
    private function input(Request $request): array
    {
        $data = $this->validate($request, ['KlantId' => 'required|exists:Gebruikers,Id', 'Datum' => 'required|date', 'VervalDatum' => 'date', 'Onderwerp' => 'max:190', 'Opmerking' => 'max:5000']);
        $lines = [];
        foreach ((array) $request->input('Regels', []) as $line) {
            $text = trim((string) ($line['Omschrijving'] ?? ''));
            if ($text === '') {
                continue;
            }
            $lines[] = [
                'Omschrijving' => mb_substr($text, 0, 500),
                'Aantal' => (float) str_replace(',', '.', (string) ($line['Aantal'] ?? '1')),
                'Prijs' => parse_money($line['Prijs'] ?? '0'),
                'BtwTarief' => (float) str_replace(',', '.', (string) ($line['BtwTarief'] ?? '21')),
            ];
        }
        if ($lines === []) {
            throw new \App\Core\ValidationException(['Regels' => 'Voeg minimaal één regel toe.'], $request);
        }

        return [[
            'KlantId' => (int) $data['KlantId'], 'Datum' => $data['Datum'], 'VervalDatum' => ($data['VervalDatum'] ?? '') ?: null,
            'Onderwerp' => ($data['Onderwerp'] ?? '') ?: null, 'Opmerking' => ($data['Opmerking'] ?? '') ?: null,
        ], $lines];
    }

    private function afterSave(Request $request, Document $doc, string $verb): Response
    {
        if ($request->string('versturen') !== '') {
            $request->params['id'] = (string) $doc->id();

            return $this->send($request);
        }

        return $this->redirect('/beheer/documenten/' . $doc->id())->with('success', $doc->label() . ' ' . $doc->Nummer . ' is ' . $verb . '.');
    }

    /** @param list<array<string, mixed>> $lines */
    private function form(Document $doc, array $lines): Response
    {
        return $this->view('beheer.documents.form', [
            'doc' => $doc,
            'lines' => $lines,
            'customers' => User::query("SELECT * FROM Gebruikers WHERE Rol = 'klant' ORDER BY COALESCE(Bedrijfsnaam, Achternaam)"),
            'products' => Product::query('SELECT * FROM Producten ORDER BY Groep, Volgorde'),
        ]);
    }

    private function doc(Request $request): Document
    {
        $doc = Document::find((int) $request->param('id'));
        if ($doc === null) {
            throw new HttpException(404, 'Document niet gevonden.');
        }

        return $doc;
    }
}
