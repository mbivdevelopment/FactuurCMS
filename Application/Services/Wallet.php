<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\User;

/** Klanttegoed (online portemonnee): saldo in centen op Gebruikers.Tegoed + mutatielog. */
final class Wallet
{
    /** Onderwerp van opwaardeer-facturen; bij betaling wordt het tegoed bijgeschreven. */
    public const TOPUP_SUBJECT = 'Tegoed opwaarderen';

    public static function balance(User $user): int
    {
        return (int) ($user->Tegoed ?? 0);
    }

    /** Boek bedrag (negatief = afschrijven) en log de mutatie. */
    public static function mutate(User $user, int $bedrag, string $reden, ?int $docId = null): void
    {
        Database::transaction(static function () use ($user, $bedrag, $reden, $docId): void {
            Database::run('UPDATE Gebruikers SET Tegoed = Tegoed + ? WHERE Id = ?', [$bedrag, $user->id()]);
            Database::insert('TegoedMutaties', [
                'GebruikerId' => $user->id(), 'DocumentId' => $docId, 'Bedrag' => $bedrag, 'Reden' => $reden, 'AangemaaktOp' => now(),
            ]);
            $user->Tegoed = (int) ($user->Tegoed ?? 0) + $bedrag;
        });
    }

    /** Open factuur aanmaken om tegoed op te waarderen. */
    public static function topupInvoice(User $user, int $bedrag): \App\Models\Document
    {
        $doc = \App\Models\Document::create([
            'Soort' => 'factuur', 'KlantId' => $user->id(), 'Status' => 'open',
            'Datum' => date('Y-m-d'), 'VervalDatum' => Billing::dueDate(date('Y-m-d')),
            'Onderwerp' => self::TOPUP_SUBJECT,
        ]);
        $doc->syncLines([['Omschrijving' => self::TOPUP_SUBJECT, 'Aantal' => 1.0, 'Prijs' => $bedrag, 'BtwTarief' => (float) setting('facturatie.btw', 21)]]);

        return $doc;
    }
}
