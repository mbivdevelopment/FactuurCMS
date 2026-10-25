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
use App\Services\OnlinePayment;
use App\Services\Wallet;

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
        $doc = $this->ownDocument($request);
        $paymentId = $request->int('betaling');
        if ($paymentId > 0 && ($row = Database::first('SELECT * FROM OnlineBetalingen WHERE Id = ? AND DocumentId = ?', [$paymentId, $doc->id()])) !== null) {
            try {
                $status = OnlinePayment::sync($row);
            } catch (\Throwable) {
                $status = (string) $row['Status'];
            }
            [$type, $message] = match ($status) {
                'betaald' => ['success', 'Bedankt, je betaling is ontvangen.'],
                'mislukt' => ['error', 'De betaling is niet gelukt of geannuleerd.'],
                default => ['success', 'Je betaling wordt verwerkt. Zodra die binnen is, staat de factuur op betaald.'],
            };

            return $this->redirect('/klant/documenten/' . $doc->id())->with($type, $message);
        }

        return $this->view('klant.document', ['doc' => $doc, 'methods' => OnlinePayment::payable($doc) ? OnlinePayment::enabled() : [], 'tegoed' => Wallet::balance(Auth::user())]);
    }

    public function pay(Request $request): Response
    {
        $doc = $this->ownDocument($request);
        $methode = $request->string('methode');
        if ($methode === 'tegoed') {
            $user = Auth::user();
            $open = $doc->outstanding();
            if ($open <= 0 || Wallet::balance($user) < $open) {
                return $this->redirect('/klant/documenten/' . $doc->id())->with('error', 'Je tegoed is niet voldoende; waardeer op of kies een andere betaalmethode.');
            }
            Wallet::mutate($user, -$open, 'Betaling ' . $doc->label() . ' ' . $doc->Nummer, $doc->id());
            Billing::registerPayment($doc, $open, date('Y-m-d'), 'Betaald met tegoed');

            return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Betaald met je tegoed.');
        }
        try {
            $url = OnlinePayment::start($doc, $methode);
        } catch (\Throwable $e) {
            return $this->redirect('/klant/documenten/' . $doc->id())->with('error', 'Online betalen lukt nu niet: ' . $e->getMessage());
        }

        return Response::redirect($url, 303);
    }

    /** Tegoedpagina: saldo, opwaarderen en mutaties. */
    public function wallet(Request $request): Response
    {
        $user = Auth::user();
        $mutaties = Database::all('SELECT * FROM TegoedMutaties WHERE GebruikerId = ? ORDER BY Id DESC LIMIT 50', [$user->id()]);

        return $this->view('klant.tegoed', ['saldo' => Wallet::balance($user), 'mutaties' => $mutaties, 'methods' => OnlinePayment::enabled()]);
    }

    /** Maakt een opwaardeer-factuur en stuurt door naar de factuur (daar betalen). */
    public function topup(Request $request): Response
    {
        $bedrag = (int) round((float) str_replace(',', '.', trim((string) $request->string('Bedrag'))) * 100);
        if ($bedrag < 500 || $bedrag > 5000000) {
            return $this->redirect('/klant/tegoed')->with('error', 'Vul een bedrag in tussen € 5 en € 50.000.');
        }
        $doc = Wallet::topupInvoice(Auth::user(), $bedrag);
        Billing::send($doc);

        return $this->redirect('/klant/documenten/' . $doc->id())->with('success', 'Factuur aangemaakt — betaal om je tegoed op te waarderen.');
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
