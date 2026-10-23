<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class Ticket extends Model
{
    public const STATUSES = ['open' => 'Open', 'beantwoord' => 'Beantwoord', 'gesloten' => 'Gesloten'];
    public const DEPARTMENTS = ['Support', 'Facturatie', 'Verkoop'];
    public const PRIORITIES = ['laag' => 'Laag', 'normaal' => 'Normaal', 'hoog' => 'Hoog'];

    protected static string $table = 'Tickets';
    protected static ?string $prefix = 'T';

    /** @return list<array<string, mixed>> */
    public function messages(bool $withInternal): array
    {
        return Database::all(
            'SELECT b.*, g.Voornaam, g.Achternaam, g.Rol FROM TicketBerichten b LEFT JOIN Gebruikers g ON g.Id = b.GebruikerId
             WHERE b.TicketId = ?' . ($withInternal ? '' : ' AND b.Intern = 0') . ' ORDER BY b.Id',
            [$this->id()],
        );
    }

    public function addMessage(int $userId, string $message, bool $internal = false): void
    {
        Database::insert('TicketBerichten', ['TicketId' => $this->id(), 'GebruikerId' => $userId, 'Bericht' => $message, 'Intern' => $internal ? 1 : 0, 'AangemaaktOp' => now()]);
        $this->LaatsteReactie = now();
        $this->save();
    }
}
