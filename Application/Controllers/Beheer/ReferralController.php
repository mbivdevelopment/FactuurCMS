<?php

declare(strict_types=1);

namespace App\Controllers\Beheer;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\ReferralService;

final class ReferralController extends Controller
{
    public function index(Request $request): Response
    {
        $rows = Database::all(
            "SELECT r.*, a.Voornaam AS AanbrengerVoornaam, a.Achternaam AS AanbrengerAchternaam,
                    a.Bedrijfsnaam AS AanbrengerBedrijf, b.Voornaam AS LidVoornaam, b.Achternaam AS LidAchternaam,
                    b.Bedrijfsnaam AS LidBedrijf, b.Email AS LidEmail,
                    COALESCE(SUM(rb.Bedrag), 0) AS Beloond
             FROM Referrals r
             JOIN Gebruikers a ON a.Id = r.AanbrengerId
             JOIN Gebruikers b ON b.Id = r.AangebrachtId
             LEFT JOIN ReferralBeloningen rb ON rb.ReferralId = r.Id
             GROUP BY r.Id
             ORDER BY r.Id DESC"
        );
        $total = (int) (Database::scalar('SELECT COALESCE(SUM(Bedrag), 0) FROM ReferralBeloningen') ?? 0);

        return $this->view('beheer.referrals.index', ['rows' => $rows, 'total' => $total, 'threshold' => ReferralService::THRESHOLD, 'rate' => ReferralService::RATE]);
    }
}
