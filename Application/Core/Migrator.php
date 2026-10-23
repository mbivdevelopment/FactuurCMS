<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Voert Application/Database/schema.sql (idempotent), Application/Database/Migrations/*.sql (eenmalig, bijgehouden in
 * settings.group = 'migrations') en Application/Database/seed.sql (idempotent via INSERT IGNORE) uit.
 * Wordt gebruikt door composer migrate/seed én door de updater.
 */
final class Migrator
{
    public function __construct(private readonly string $root)
    {
    }

    /** @return list<string> */
    public function migrate(): array
    {
        $messages = [];
        $this->runFile($this->root . '/Application/Database/schema.sql');
        $messages[] = 'Schema toegepast.';

        $done = array_column(Database::all("SELECT Naam AS `key` FROM Migraties"), 'key');
        $files = glob($this->root . '/Application/Database/Migrations/*.sql') ?: [];
        sort($files);
        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $done, true)) {
                continue;
            }
            $this->runFile($file);
            Database::insert("Migraties", ["Naam" => $name, "UitgevoerdOp" => now()]);
            $messages[] = "Migratie: $name";
        }

        return $messages;
    }

    /** @return list<string> */
    public function seed(): array
    {
        $file = $this->root . '/Application/Database/seed.sql';
        if (!is_file($file)) {
            return ['Geen seed.sql gevonden.'];
        }
        $count = $this->runFile($file);

        return ["Seed uitgevoerd ($count statements)."];
    }

    private function runFile(string $file): int
    {
        $count = 0;
        foreach (self::split((string) file_get_contents($file)) as $statement) {
            Database::pdo()->exec($statement);
            $count++;
        }

        return $count;
    }

    /**
     * Splitst SQL op ';' buiten quotes en laat commentaarregels weg.
     * @return list<string>
     */
    public static function split(string $sql): array
    {
        $statements = [];
        $current = '';
        $quote = null;
        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            if ($quote !== null) {
                $current .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sql[++$i];
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($char === '-' && ($sql[$i + 1] ?? '') === '-') {
                $end = strpos($sql, "\n", $i);
                $i = $end === false ? $length : $end;
                continue;
            }
            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
            } elseif ($char === ';') {
                if (trim($current) !== '') {
                    $statements[] = trim($current);
                }
                $current = '';
                continue;
            }
            $current .= $char;
        }
        if (trim($current) !== '') {
            $statements[] = trim($current);
        }

        return $statements;
    }
}
