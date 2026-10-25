<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/** Herkent en verwijdert de voorbeeldgegevens uit seed.sql; echte gegevens blijven staan. */
final class DemoData
{
    private const CUSTOMER_EMAILS = ['klant@factuurcms.nl', 'info@bakkerijjansen.nl'];
    private const CUSTOMER_COMPANIES = ['De Vries Webdesign', 'Bakkerij Jansen'];
    private const SUPPLIER_EMAILS = ['facturen@datacenterpartners.nl', 'billing@registrar.example'];
    private const LEAD_EMAILS = ['sophie@jansenadvies.nl', 'ruben@bakkerlogistiek.nl'];
    private const PRODUCT_SLUGS = ['webhosting-start', 'webhosting-pro', 'webhosting-business', 'nl-domeinnaam', 'vps-basic', 'website-laten-maken', 'com-domeinnaam', 'eu-domeinnaam', 'be-domeinnaam'];

    /** @return list<array<string, mixed>> */
    public static function customers(): array
    {
        return Database::all(
            "SELECT Id, Nummer, Voornaam, Achternaam, Bedrijfsnaam, Email FROM Gebruikers WHERE Rol = 'klant' AND (Email IN (?, ?) OR Bedrijfsnaam IN (?, ?)) ORDER BY Nummer",
            [...self::CUSTOMER_EMAILS, ...self::CUSTOMER_COMPANIES],
        );
    }

    /** @return list<array<string, mixed>> */
    public static function otherCustomers(): array
    {
        $ids = self::ids();

        return Database::all(
            "SELECT Nummer, Voornaam, Achternaam, Bedrijfsnaam FROM Gebruikers WHERE Rol = 'klant'" . ($ids ? ' AND Id NOT IN (' . self::marks($ids) . ')' : '') . ' ORDER BY Nummer LIMIT 25',
            $ids,
        );
    }

    /**
     * Demoproducten (niet gekoppeld aan een koppeling), met het aantal diensten van echte klanten.
     * @return list<array<string, mixed>>
     */
    public static function products(): array
    {
        $ids = self::ids();
        $notDemo = $ids ? ' AND d.KlantId NOT IN (' . self::marks($ids) . ')' : '';
        $own = self::hasColumn('Producten', 'Bron') ? ' AND p.Bron IS NULL' : '';

        return Database::all(
            'SELECT p.Id, p.Nummer, p.Naam, p.Groep, (SELECT COUNT(*) FROM Diensten d WHERE d.ProductId = p.Id' . $notDemo . ') AS InGebruik FROM Producten p WHERE p.Slug IN (' . self::marks(self::PRODUCT_SLUGS) . ')' . $own . ' ORDER BY p.Nummer',
            [...$ids, ...self::PRODUCT_SLUGS],
        );
    }

    /** @return array{customers: list<array<string, mixed>>, counts: array<string, int>, suppliers: list<array<string, mixed>>, leads: list<array<string, mixed>>} */
    public static function summary(): array
    {
        $ids = self::ids();
        $in = $ids ? self::marks($ids) : 'NULL';
        $count = static fn (string $table, string $sql, array $params = []): int => self::exists($table) ? (int) Database::scalar('SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $sql, $params) : 0;

        return [
            'customers' => self::customers(),
            'counts' => array_filter([
                'Diensten' => $count('Diensten', "KlantId IN ($in)", $ids),
                'Facturen' => $count('Documenten', "Soort <> 'offerte' AND KlantId IN ($in)", $ids),
                'Offertes' => $count('Documenten', "Soort = 'offerte' AND KlantId IN ($in)", $ids),
                'Tickets' => $count('Tickets', "KlantId IN ($in)", $ids),
                'Projecten' => $count('Projecten', "KlantId IN ($in)", $ids),
                'Projecttaken' => $count('ProjectTaken', "ProjectId IN (SELECT Id FROM Projecten WHERE KlantId IN ($in))", $ids),
                'Afspraken' => $count('Afspraken', "KlantId IN ($in)", $ids),
                'Contracten' => $count('Contracten', "KlantId IN ($in)", $ids),
            ]),
            'suppliers' => self::exists('Crediteuren') ? Database::all('SELECT Id, Bedrijfsnaam, Email FROM Crediteuren WHERE Email IN (?, ?)', self::SUPPLIER_EMAILS) : [],
            'leads' => self::exists('Leads') ? Database::all('SELECT Id, Naam, Bedrijf, Email FROM Leads WHERE Email IN (?, ?)', self::LEAD_EMAILS) : [],
            'products' => array_values(array_filter($products = self::products(), static fn (array $p): bool => (int) $p['InGebruik'] === 0)),
            'productsKept' => array_values(array_filter($products, static fn (array $p): bool => (int) $p['InGebruik'] > 0)),
        ];
    }

    public static function isEmpty(): bool
    {
        $s = self::summary();

        return $s['customers'] === [] && $s['suppliers'] === [] && $s['leads'] === [] && $s['products'] === [];
    }

    /** Verwijdert alle demo-data en geeft het aantal verwijderde records terug. */
    public static function remove(): int
    {
        return (int) Database::transaction(static function (): int {
            $ids = self::ids();
            $n = 0;
            if ($ids) {
                $in = self::marks($ids);
                $run = static function (string $table, string $sql) use ($ids, &$n): void {
                    if (self::exists($table)) {
                        $n += Database::run($sql, $ids)->rowCount();
                    }
                };
                $run('ProjectTaken', "DELETE FROM ProjectTaken WHERE ProjectId IN (SELECT Id FROM Projecten WHERE KlantId IN ($in))");
                $run('Projecten', "DELETE FROM Projecten WHERE KlantId IN ($in)");
                $run('Afspraken', "DELETE FROM Afspraken WHERE KlantId IN ($in)");
                $run('Contracten', "DELETE FROM Contracten WHERE KlantId IN ($in)");
                $run('TicketBerichten', "DELETE FROM TicketBerichten WHERE TicketId IN (SELECT Id FROM Tickets WHERE KlantId IN ($in))");
                $run('Tickets', "DELETE FROM Tickets WHERE KlantId IN ($in)");
                $run('DocumentRegels', "DELETE FROM DocumentRegels WHERE DocumentId IN (SELECT Id FROM Documenten WHERE KlantId IN ($in))");
                Database::run("UPDATE DocumentRegels SET DienstId = NULL WHERE DienstId IN (SELECT Id FROM Diensten WHERE KlantId IN ($in))", $ids);
                $run('Documenten', "DELETE FROM Documenten WHERE KlantId IN ($in)");
                $run('Diensten', "DELETE FROM Diensten WHERE KlantId IN ($in)");
                $run('Gebruikers', "DELETE FROM Gebruikers WHERE Rol = 'klant' AND Id IN ($in)");
            }
            if (self::exists('Crediteuren')) {
                $n += Database::run('DELETE FROM Crediteuren WHERE Email IN (?, ?)', self::SUPPLIER_EMAILS)->rowCount();
            }
            if (self::exists('Leads')) {
                $n += Database::run('DELETE FROM Leads WHERE Email IN (?, ?)', self::LEAD_EMAILS)->rowCount();
            }
            $unused = array_map('intval', array_column(array_filter(self::products(), static fn (array $p): bool => (int) $p['InGebruik'] === 0), 'Id'));
            if ($unused !== []) {
                $n += Database::run('DELETE FROM Producten WHERE Id IN (' . self::marks($unused) . ')', $unused)->rowCount();
            }

            return $n;
        });
    }

    /** @return list<int> */
    private static function ids(): array
    {
        return array_map('intval', array_column(self::customers(), 'Id'));
    }

    /** @param list<int|string> $ids */
    private static function marks(array $ids): string
    {
        return implode(',', array_fill(0, count($ids), '?'));
    }

    private static function hasColumn(string $table, string $column): bool
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?', [$table, $column]) > 0;
    }

    private static function exists(string $table): bool
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]) > 0;
    }
}
