<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/** BTW-overzicht per kwartaal volgens de rubrieken van de Nederlandse btw-aangifte. */
final class VatController extends Controller
{
    public function index(Request $request): Response
    {
        $year = max(2000, min(2100, $request->int('jaar', (int) date('Y'))));
        $quarter = max(1, min(4, $request->int('kwartaal', (int) ceil((int) date('n') / 3))));
        $from = sprintf('%04d-%02d-01', $year, ($quarter - 1) * 3 + 1);
        $to = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $quarter * 3)));

        $sales = ['hoog' => [0, 0], 'laag' => [0, 0], 'overig' => [0, 0], 'nul' => [0, 0]];
        foreach (Database::all("SELECT r.BtwTarief, SUM(ROUND(r.Aantal * r.Prijs)) AS Omzet FROM DocumentRegels r JOIN Documenten d ON d.Id = r.DocumentId WHERE d.Soort = 'factuur' AND d.Status <> 'concept' AND d.Datum BETWEEN ? AND ? GROUP BY r.BtwTarief", [$from, $to]) as $row) {
            $rate = (float) $row['BtwTarief'];
            $key = $rate >= 21 ? 'hoog' : ($rate == 9.0 ? 'laag' : ($rate > 0 ? 'overig' : 'nul'));
            $sales[$key][0] += (int) $row['Omzet'];
            $sales[$key][1] += (int) round((int) $row['Omzet'] * $rate / 100);
        }
        $purchase = Database::first("SELECT COUNT(*) AS Aantal, COALESCE(SUM(Bedrag), 0) AS Bedrag, COALESCE(SUM(BtwBedrag), 0) AS Btw FROM Inkoopfacturen WHERE Datum BETWEEN ? AND ?", [$from, $to]) ?? ['Aantal' => 0, 'Bedrag' => 0, 'Btw' => 0];
        $invoices = (int) Database::scalar("SELECT COUNT(*) FROM Documenten WHERE Soort = 'factuur' AND Status <> 'concept' AND Datum BETWEEN ? AND ?", [$from, $to]);

        $owed = $sales['hoog'][1] + $sales['laag'][1] + $sales['overig'][1];

        return $this->view('beheer.btw', [
            'year' => $year, 'quarter' => $quarter, 'from' => $from, 'to' => $to, 'sales' => $sales,
            'purchase' => $purchase, 'invoices' => $invoices, 'owed' => $owed, 'input' => (int) $purchase['Btw'],
        ]);
    }
}
