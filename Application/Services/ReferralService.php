<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Document;
use App\Models\User;

/** Referraltegoed: 10% beloning nadat een aangebracht lid €500 heeft betaald. */
final class ReferralService
{
    public const THRESHOLD = 50000;
    public const RATE = 10;

    public static function code(User $user): string
    {
        $code = trim((string) $user->ReferralCode);
        if ($code !== '') {
            return $code;
        }
        do {
            $code = 'REF' . strtoupper(bin2hex(random_bytes(4)));
        } while (Database::first('SELECT Id FROM Gebruikers WHERE ReferralCode = ?', [$code]) !== null);
        Database::update('Gebruikers', ['ReferralCode' => $code, 'BijgewerktOp' => now()], ['Id' => $user->id()]);
        $user->ReferralCode = $code;

        return $code;
    }

    public static function attach(User $newUser, string $code): void
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return;
        }
        $referrer = Database::first("SELECT Id FROM Gebruikers WHERE ReferralCode = ? AND Rol = 'klant' AND Id <> ?", [$code, $newUser->id()]);
        if ($referrer === null || Database::first('SELECT Id FROM Referrals WHERE AangebrachtId = ?', [$newUser->id()]) !== null) {
            return;
        }
        Database::insert('Referrals', [
            'AanbrengerId' => (int) $referrer['Id'],
            'AangebrachtId' => $newUser->id(),
            'Code' => $code,
            'Drempel' => self::THRESHOLD,
            'AangemaaktOp' => now(),
        ]);
    }

    public static function rewardForPayment(Document $document, int $amount): void
    {
        if ($amount <= 0 || $document->isQuote() || (string) $document->Onderwerp === Wallet::TOPUP_SUBJECT) {
            return;
        }
        $referral = Database::first('SELECT * FROM Referrals WHERE AangebrachtId = ?', [$document->KlantId]);
        if ($referral === null) {
            return;
        }
        $previousInvoices = (int) Database::scalar(
            "SELECT COALESCE(SUM(Betaald), 0) FROM Documenten
             WHERE KlantId = ? AND Soort = 'factuur' AND Status <> 'concept' AND Id <> ? AND Onderwerp <> ?",
            [$document->KlantId, $document->id(), Wallet::TOPUP_SUBJECT]
        );
        $previousPaid = $previousInvoices + max(0, (int) $document->Betaald - $amount);
        $totalPaid = $previousPaid + $amount;
        Database::update('Referrals', [
            'Besteed' => $totalPaid,
            'GekwalificeerdOp' => $totalPaid >= (int) $referral['Drempel'] && $referral['GekwalificeerdOp'] === null ? now() : $referral['GekwalificeerdOp'],
        ], ['Id' => (int) $referral['Id']]);
        if ($previousPaid < (int) $referral['Drempel']) {
            return;
        }
        $reward = (int) round($amount * self::RATE / 100);
        if ($reward <= 0 || Database::first('SELECT Id FROM ReferralBeloningen WHERE ReferralId = ? AND DocumentId = ?', [(int) $referral['Id'], $document->id()]) !== null) {
            return;
        }
        $referrer = User::find((int) $referral['AanbrengerId']);
        if ($referrer === null) {
            return;
        }
        Database::transaction(static function () use ($referral, $document, $amount, $reward, $referrer): void {
            if (Database::first('SELECT Id FROM ReferralBeloningen WHERE ReferralId = ? AND DocumentId = ?', [(int) $referral['Id'], $document->id()]) !== null) {
                return;
            }
            Wallet::mutate($referrer, $reward, 'Referralbeloning ' . $document->Nummer, $document->id());
            Database::insert('ReferralBeloningen', [
                'ReferralId' => (int) $referral['Id'],
                'DocumentId' => $document->id(),
                'BasisBedrag' => $amount,
                'Bedrag' => $reward,
                'AangemaaktOp' => now(),
            ]);
        });
    }

    /** @return array{count:int, qualified:int, rewards:int} */
    public static function summary(int $userId): array
    {
        $row = Database::first(
            'SELECT COUNT(*) AS count, SUM(GekwalificeerdOp IS NOT NULL) AS qualified FROM Referrals WHERE AanbrengerId = ?',
            [$userId]
        ) ?: [];

        return [
            'count' => (int) ($row['count'] ?? 0),
            'qualified' => (int) ($row['qualified'] ?? 0),
            'rewards' => (int) (Database::scalar('SELECT COALESCE(SUM(Bedrag), 0) FROM ReferralBeloningen rb JOIN Referrals r ON r.Id = rb.ReferralId WHERE r.AanbrengerId = ?', [$userId]) ?? 0),
        ];
    }
}
