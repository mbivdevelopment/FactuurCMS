<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public const ROLES = ['beheerder' => 'Beheerder', 'medewerker' => 'Medewerker', 'klant' => 'Klant'];

    protected static string $table = 'Gebruikers';

    protected static function prefixFor(array $attributes): ?string
    {
        return ($attributes['Rol'] ?? 'klant') === 'klant' ? 'K' : 'M';
    }

    public function isStaff(): bool
    {
        return in_array($this->Rol, ['beheerder', 'medewerker'], true);
    }

    public function isAdmin(): bool
    {
        return $this->Rol === 'beheerder';
    }

    public function name(): string
    {
        return trim((string) $this->Voornaam . ' ' . (string) $this->Achternaam);
    }

    /** Bedrijfsnaam of anders de persoonsnaam. */
    public function display(): string
    {
        return (string) ($this->Bedrijfsnaam ?: $this->name() ?: $this->Email);
    }
}
