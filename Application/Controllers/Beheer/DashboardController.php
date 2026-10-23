<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Document;
use App\Models\Ticket;

final class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[date('Y-m', strtotime("first day of -$i month"))] = 0;
        }
        foreach (Database::all("SELECT DATE_FORMAT(Datum, '%Y-%m') AS Maand, SUM(Subtotaal) AS Omzet FROM Documenten WHERE Soort = 'factuur' AND Status NOT IN ('concept') AND Datum >= ? GROUP BY Maand", [array_key_first($months) . '-01']) as $row) {
            if (isset($months[$row['Maand']])) {
                $months[$row['Maand']] = (int) $row['Omzet'];
            }
        }

        return $this->view('beheer.dashboard', [
            'stats' => [
                'customers' => (int) Database::scalar("SELECT COUNT(*) FROM Gebruikers WHERE Rol = 'klant'"),
                'services' => (int) Database::scalar("SELECT COUNT(*) FROM Diensten WHERE Status = 'actief'"),
                'mrr' => (int) Database::scalar("SELECT COALESCE(SUM(CASE Periode WHEN 'maand' THEN Prijs WHEN 'kwartaal' THEN Prijs / 3 WHEN 'jaar' THEN Prijs / 12 ELSE 0 END), 0) FROM Diensten WHERE Status = 'actief'"),
                'open' => (int) Database::scalar("SELECT COALESCE(SUM(Totaal - Betaald), 0) FROM Documenten WHERE Soort = 'factuur' AND Status = 'open'"),
                'overdue' => (int) Database::scalar("SELECT COALESCE(SUM(Totaal - Betaald), 0) FROM Documenten WHERE Soort = 'factuur' AND Status = 'open' AND VervalDatum < CURDATE()"),
                'tickets' => (int) Database::scalar("SELECT COUNT(*) FROM Tickets WHERE Status = 'open'"),
                'quotes' => (int) Database::scalar("SELECT COUNT(*) FROM Documenten WHERE Soort = 'offerte' AND Status = 'verzonden'"),
                'pending' => (int) Database::scalar("SELECT COUNT(*) FROM Diensten WHERE Status = 'in_afwachting'"),
            ],
            'months' => $months,
            'openInvoices' => Document::query("SELECT d.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Documenten d JOIN Gebruikers g ON g.Id = d.KlantId WHERE d.Soort = 'factuur' AND d.Status = 'open' ORDER BY d.VervalDatum LIMIT 6"),
            'tickets' => Ticket::query("SELECT t.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Tickets t JOIN Gebruikers g ON g.Id = t.KlantId WHERE t.Status = 'open' ORDER BY t.Prioriteit = 'hoog' DESC, t.LaatsteReactie DESC LIMIT 6"),
            'upcoming' => Database::all("SELECT s.*, g.Bedrijfsnaam, g.Voornaam, g.Achternaam FROM Diensten s JOIN Gebruikers g ON g.Id = s.KlantId WHERE s.Status = 'actief' AND s.VolgendeFactuur IS NOT NULL ORDER BY s.VolgendeFactuur LIMIT 6"),
        ]);
    }
}
