<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Service extends Model
{
    public const STATUSES = ['in_afwachting' => 'In afwachting', 'actief' => 'Actief', 'opgeschort' => 'Opgeschort', 'opgezegd' => 'Opgezegd'];

    protected static string $table = 'Diensten';
    protected static ?string $prefix = 'D';
    protected static array $json = ['Gegevens'];

    public function customer(): ?User
    {
        return User::find((int) $this->KlantId);
    }

    /** Datum + één periode (maand/kwartaal/jaar); null bij eenmalig. */
    public static function addPeriod(string $date, string $period): ?string
    {
        $modifier = ['maand' => '+1 month', 'kwartaal' => '+3 months', 'jaar' => '+1 year'][$period] ?? null;

        return $modifier === null ? null : date('Y-m-d', (int) strtotime($date . ' ' . $modifier));
    }
}
