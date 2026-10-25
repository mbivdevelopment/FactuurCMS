<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;

final class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status', 'actief');
        $sql = 'SELECT t.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Tickets t JOIN Gebruikers g ON g.Id = t.KlantId WHERE 1 = 1';
        $params = [];
        if ($status === 'actief') {
            $sql .= " AND t.Status <> 'gesloten'";
        } elseif ($status !== '' && $status !== 'alle') {
            $sql .= ' AND t.Status = ?';
            $params[] = $status;
        }

        return $this->view('beheer.tickets.index', ['page' => Database::page($sql, $params, 't.Nummer ASC', 20), 'status' => $status]);
    }

    public function create(Request $request): Response
    {
        return $this->view('beheer.tickets.form', ['customers' => User::query("SELECT * FROM Gebruikers WHERE Rol = 'klant' ORDER BY COALESCE(Bedrijfsnaam, Achternaam)"), 'klantId' => $request->int('klant')]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request, ['KlantId' => 'required|exists:Gebruikers,Id', 'Onderwerp' => 'required|max:190', 'Afdeling' => 'required|in:' . implode(',', Ticket::DEPARTMENTS), 'Prioriteit' => 'required|in:laag,normaal,hoog', 'Bericht' => 'required']);
        $ticket = Ticket::create(['KlantId' => (int) $data['KlantId'], 'Onderwerp' => $data['Onderwerp'], 'Afdeling' => $data['Afdeling'], 'Prioriteit' => $data['Prioriteit'], 'Status' => 'beantwoord']);
        $ticket->addMessage(Auth::id(), (string) $data['Bericht']);
        $this->notifyCustomer($ticket, (string) $data['Bericht']);

        return $this->redirect('/beheer/tickets/' . $ticket->id())->with('success', 'Ticket aangemaakt en klant geïnformeerd.');
    }

    public function show(Request $request): Response
    {
        $ticket = $this->ticket($request);

        return $this->view('beheer.tickets.show', [
            'ticket' => $ticket,
            'messages' => $ticket->messages(true),
            'customer' => User::find((int) $ticket->KlantId),
            'service' => $ticket->DienstId ? Service::find((int) $ticket->DienstId) : null,
        ]);
    }

    public function reply(Request $request): Response
    {
        $ticket = $this->ticket($request);
        $data = $this->validate($request, ['Bericht' => 'required']);
        $internal = $request->bool('Intern');
        $ticket->addMessage(Auth::id(), (string) $data['Bericht'], $internal);
        if (!$internal) {
            $ticket->Status = $request->bool('Sluiten') ? 'gesloten' : 'beantwoord';
            $ticket->save();
            $this->notifyCustomer($ticket, (string) $data['Bericht']);
        }

        return $this->redirect('/beheer/tickets/' . $ticket->id())->with('success', $internal ? 'Interne notitie opgeslagen.' : 'Reactie verstuurd.');
    }

    public function status(Request $request): Response
    {
        $ticket = $this->ticket($request);
        $status = $request->string('Status');
        $priority = $request->string('Prioriteit');
        if (isset(Ticket::STATUSES[$status])) {
            $ticket->Status = $status;
        }
        if (isset(Ticket::PRIORITIES[$priority])) {
            $ticket->Prioriteit = $priority;
        }
        $ticket->save();

        return $this->redirect('/beheer/tickets/' . $ticket->id())->with('success', 'Ticket bijgewerkt.');
    }

    public function destroy(Request $request): Response
    {
        $this->ticket($request)->delete();

        return $this->redirect('/beheer/tickets')->with('success', 'Ticket verwijderd.');
    }

    private function notifyCustomer(Ticket $ticket, string $message): void
    {
        $customer = User::find((int) $ticket->KlantId);
        if ($customer !== null) {
            [$sub, $body] = Mailer::tpl('ticket', ['voornaam' => $customer->Voornaam, 'nummer' => $ticket->Nummer, 'onderwerp' => $ticket->Onderwerp, 'bericht' => $message, 'link' => absolute_url('/klant/tickets/' . $ticket->id()), 'medewerker' => Auth::user()?->name()]);
            Mailer::send((string) $customer->Email, $sub, $body);
        }
    }

    private function ticket(Request $request): Ticket
    {
        return Ticket::find((int) $request->param('id')) ?? throw new HttpException(404, 'Ticket niet gevonden.');
    }
}
