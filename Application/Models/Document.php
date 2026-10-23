<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

/** Facturen, creditfacturen en offertes in één tabel (Soort = factuur | offerte). */
final class Document extends Model
{
    public const INVOICE_STATUSES = ['concept' => 'Concept', 'open' => 'Open', 'vervallen' => 'Vervallen', 'betaald' => 'Betaald', 'gecrediteerd' => 'Gecrediteerd'];
    public const QUOTE_STATUSES = ['concept' => 'Concept', 'verzonden' => 'Verzonden', 'geaccepteerd' => 'Geaccepteerd', 'afgewezen' => 'Afgewezen', 'verlopen' => 'Verlopen'];

    protected static string $table = 'Documenten';
    protected static array $json = ['Betalingen'];

    protected static function prefixFor(array $attributes): ?string
    {
        return ($attributes['Soort'] ?? 'factuur') === 'offerte'
            ? (string) setting('facturatie.prefix_offerte', 'O')
            : (string) setting('facturatie.prefix_factuur', 'F');
    }

    public function isQuote(): bool
    {
        return $this->Soort === 'offerte';
    }

    public function label(): string
    {
        return $this->isQuote() ? 'Offerte' : ((int) $this->Totaal < 0 ? 'Creditfactuur' : 'Factuur');
    }

    public function customer(): ?User
    {
        return User::find((int) $this->KlantId);
    }

    /** @return list<array<string, mixed>> */
    public function lines(): array
    {
        return Database::all('SELECT * FROM DocumentRegels WHERE DocumentId = ? ORDER BY Volgorde, Id', [$this->id()]);
    }

    public function outstanding(): int
    {
        return max(0, (int) $this->Totaal - (int) $this->Betaald);
    }

    /** Status inclusief afgeleide 'vervallen' (open + vervaldatum verstreken) en 'verlopen' (offerte). */
    public function status(): string
    {
        $due = (string) $this->VervalDatum;
        if (!$this->isQuote() && $this->Status === 'open' && $due !== '' && $due < date('Y-m-d')) {
            return 'vervallen';
        }
        if ($this->isQuote() && $this->Status === 'verzonden' && $due !== '' && $due < date('Y-m-d')) {
            return 'verlopen';
        }

        return (string) $this->Status;
    }

    public function statusLabel(): string
    {
        return ($this->isQuote() ? self::QUOTE_STATUSES : self::INVOICE_STATUSES)[$this->status()] ?? ucfirst($this->status());
    }

    /**
     * Regels vervangen en totalen herberekenen. Prijzen in centen excl. btw.
     * @param list<array{Omschrijving: string, Aantal: float, Prijs: int, BtwTarief: float, DienstId?: ?int}> $lines
     */
    public function syncLines(array $lines): void
    {
        Database::transaction(function () use ($lines): void {
            Database::delete('DocumentRegels', ['DocumentId' => $this->id()]);
            $subtotal = 0;
            $vat = 0;
            foreach (array_values($lines) as $i => $line) {
                $amount = (int) round($line['Aantal'] * $line['Prijs']);
                $subtotal += $amount;
                $vat += (int) round($amount * $line['BtwTarief'] / 100);
                Database::insert('DocumentRegels', [
                    'DocumentId' => $this->id(),
                    'DienstId' => $line['DienstId'] ?? null,
                    'Omschrijving' => $line['Omschrijving'],
                    'Aantal' => $line['Aantal'],
                    'Prijs' => $line['Prijs'],
                    'BtwTarief' => $line['BtwTarief'],
                    'Volgorde' => $i,
                ]);
            }
            $this->Subtotaal = $subtotal;
            $this->Btw = $vat;
            $this->Totaal = $subtotal + $vat;
            $this->save();
        });
    }
}
