<?php

declare(strict_types=1);

namespace App\Controllers\Klant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Models\Document;
use App\Models\Service;
use App\Models\Ticket;
use App\Services\DocumentPdf;

final class PortalController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $id = Auth::id();

        return $this->view('klant.dashboard', [
            'services' => Service::query("SELECT * FROM Diensten WHERE KlantId = ? AND Status <> 'opgezegd' ORDER BY Id DESC LIMIT 5", [$id]),
            'invoices' => Document::query("SELECT * FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status <> 'concept' ORDER BY Datum DESC, Id DESC LIMIT 5", [$id]),
            'openAmount' => (int) Database::scalar("SELECT COALESCE(SUM(Totaal - Betaald), 0) FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status = 'open'", [$id]),
            'activeCount' => Service::count(['KlantId' => $id, 'Status' => 'actief']),
            'ticketCount' => (int) Database::scalar("SELECT COUNT(*) FROM Tickets WHERE KlantId = ? AND Status <> 'gesloten'", [$id]),
            'quoteCount' => (int) Database::scalar("SELECT COUNT(*) FROM Documenten WHERE KlantId = ? AND Soort = 'offerte' AND Status = 'verzonden'", [$id]),
        ]);
    }

    public function services(Request $request): Response
    {
        return $this->view('klant.services', ['services' => Service::where(['KlantId' => Auth::id()])]);
    }

    public function invoices(Request $request): Response
    {
        return $this->view('klant.documents', ['kind' => 'factuur', 'docs' => Document::query("SELECT * FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status <> 'concept' ORDER BY Datum DESC, Id DESC", [Auth::id()])]);
    }

    public function quotes(Request $request): Response
    {
        return $this->view('klant.documents', ['kind' => 'offerte', 'docs' => Document::query("SELECT * FROM Documenten WHERE KlantId = ? AND Soort = 'offerte' AND Status <> 'concept' ORDER BY Datum DESC, Id DESC", [Auth::id()])]);
    }

    public function document(Request $request): Response
    {
        return $this->view('klant.document', ['doc' => $this->ownDocument($request)]);
    }

    public function pdf(Request $request): Response
    {
        $doc = $this->ownDocument($request);

        return (new Response(DocumentPdf::render($doc), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="' . $doc->label() . '-' . $doc->Nummer . '.pdf"']));
    }

    public function acceptQuote(Request $request): Response
    {
        return $this->decideQuote($request, 'geaccepteerd');
    }

    public function rejectQuote(Request $request): Response
    {
        return $this->decideQuote($request, 'afgewezen');
    }

    public function tickets(Request $request): Response
    {
        return $this->view('klant.tickets', ['tickets' => Ticket::query('SELECT * FROM Tickets WHERE KlantId = ? ORDER BY Status = \'gesloten\', COALESCE(LaatsteReactie, AangemaaktOp) DESC', [Auth::id()])]);
    }

    public function newTicket(Request $request): Response
    {
        return $this->view('klant.ticket-new', ['services' => Service::where(['KlantId' => Auth::id()])]);
    }

    public function storeTicket(Request $request): Response
    {
        $data = $this->validate($request, ['Onderwerp' => 'required|max:190', 'Afdeling' => 'required|in:' . implode(',', Ticket::DEPARTMENTS), 'Bericht' => 'required|max:10000']);
        $serviceId = $request->int('DienstId');
        $ticket = Ticket::create([
            'KlantId' => Auth::id(),
            'DienstId' => $serviceId > 0 && Service::firstWhere(['Id' => $serviceId, 'KlantId' => Auth::id()]) ? $serviceId : null,
            'Onderwerp' => $data['Onderwerp'],
            'Afdeling' => $data['Afdeling'],
            'Prioriteit' => in_array($request->string('Prioriteit'), array_keys(Ticket::PRIORITIES), true) ? $request->string('Prioriteit') : 'normaal',
            'Status' => 'open',
        ]);
        $ticket->addMessage(Auth::id(), (string) $data['Bericht']);
        $this->notifyStaff($ticket, (string) $data['Bericht']);

        return $this->redirect('/klant/tickets/' . $ticket->id())->with('success', 'Je ticket is aangemaakt; we reageren zo snel mogelijk.');
    }

    public function ticket(Request $request): Response
    {
        $ticket = $this->ownTicket($request);

        return $this->view('klant.ticket', ['ticket' => $ticket, 'messages' => $ticket->messages(false)]);
    }

    public function replyTicket(Request $request): Response
    {
        $ticket = $this->ownTicket($request);
        $data = $this->validate($request, ['Bericht' => 'required|max:10000']);
        $ticket->addMessage(Auth::id(), (string) $data['Bericht']);
        $ticket->Status = 'open';
        $ticket->save();
        $this->notifyStaff($ticket, (string) $data['Bericht']);

        return $this->redirect('/klant/tickets/' . $ticket->id())->with('success', 'Je reactie is verstuurd.');
    }

    public function profile(Request $request): Response
    {
        return $this->view('klant.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): Response
    {
        $user = Auth::user();
        $data = $this->validate($request, [
            'Voornaam' => 'required|max:80', 'Achternaam' => 'required|max:80', 'Bedrijfsnaam' => 'max:150',
            'Email' => 'required|email|unique:Gebruikers,Email,' . $user?->id(), 'Telefoon' => 'max:40', 'Adres' => 'max:150',
            'Postcode' => 'max:12', 'Plaats' => 'max:80', 'KvkNummer' => 'max:20', 'BtwNummer' => 'max:30',
        ]);
        if ($request->string('Wachtwoord') !== '') {
            $this->validate($request, ['Wachtwoord' => 'min:8|confirmed']);
            $data['Wachtwoord'] = Auth::hash($request->string('Wachtwoord'));
        }
        $user?->fill(array_map(static fn ($v) => $v === '' ? null : $v, $data))->save();

        return $this->redirect('/klant/profiel')->with('success', 'Je gegevens zijn opgeslagen.');
    }

    private function decideQuote(Request $request, string $status): Response
    {
        $doc = $this->ownDocument($request);
        if (!$doc->isQuote() || $doc->status() !== 'verzonden') {
            return Response::back('/klant/offertes')->with('error', 'Deze offerte kan niet meer worden gewijzigd.');
        }
        $doc->Status = $status;
        $doc->save();
        Mailer::send((string) setting('bedrijf.email', 'info@localhost'), 'Offerte ' . $doc->Nummer . ' ' . $status, 'Klant ' . Auth::user()?->display() . ' heeft offerte ' . $doc->Nummer . ' ' . $status . '.' . "\n" . absolute_url('/beheer/documenten/' . $doc->id()));

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', $status === 'geaccepteerd' ? 'Bedankt! De offerte is geaccepteerd.' : 'De offerte is afgewezen.');
    }

    private function ownDocument(Request $request): Document
    {
        $doc = Document::find((int) $request->param('id'));
        if ($doc === null || (int) $doc->KlantId !== Auth::id() || $doc->Status === 'concept') {
            throw new HttpException(404, 'Document niet gevonden.');
        }

        return $doc;
    }

    private function ownTicket(Request $request): Ticket
    {
        $ticket = Ticket::find((int) $request->param('id'));
        if ($ticket === null || (int) $ticket->KlantId !== Auth::id()) {
            throw new HttpException(404, 'Ticket niet gevonden.');
        }

        return $ticket;
    }

    private function notifyStaff(Ticket $ticket, string $message): void
    {
        Mailer::send((string) setting('bedrijf.email', 'info@localhost'), 'Ticket ' . $ticket->Nummer . ': ' . $ticket->Onderwerp, Auth::user()?->display() . " schreef:\n\n" . $message . "\n\n" . absolute_url('/beheer/tickets/' . $ticket->id()));
    }
}
