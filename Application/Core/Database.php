<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                Config::get('database.host'),
                Config::get('database.port'),
                Config::get('database.name'),
            );
            self::$pdo = new PDO($dsn, (string) Config::get('database.user'), (string) Config::get('database.pass'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
            // Zelfde tijdzone als PHP, zodat NOW()/CURDATE() overeenkomen met now()
            self::$pdo->exec("SET time_zone = '" . (new \DateTime())->format('P') . "'");
        }

        return self::$pdo;
    }

    /** @param array<int|string, mixed> $params */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|null
     */
    public static function first(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /**
     * Paginated list (10 per page by default); current page from ?page=.
     *
     * @param array<int|string, mixed> $params
     * @return array{rows: array<int, array<string, mixed>>, page: int, pages: int, total: int, perPage: int}
     */
    public static function page(string $sql, array $params, string $orderBy, int $perPage = 10): array
    {
        $total = (int) self::scalar('SELECT COUNT(*) FROM (' . $sql . ') pageCount', $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($pages, max(1, (int) ($_GET['page'] ?? 1)));
        $rows = self::all($sql . ' ORDER BY ' . $orderBy . ' LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage), $params);

        return ['rows' => $rows, 'page' => $page, 'pages' => $pages, 'total' => $total, 'perPage' => $perPage];
    }

    /** @param array<int|string, mixed> $params */
    public static function scalar(string $sql, array $params = []): mixed
    {
        $value = self::run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    /** @param array<string, mixed> $data */
    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(static fn (string $c): string => "`$c`", $columns)),
            implode(', ', array_fill(0, count($columns), '?')),
        );
        self::run($sql, array_values(self::normalize($data)));

        return (int) self::pdo()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $where
     */
    public static function update(string $table, array $data, array $where): int
    {
        $set = implode(', ', array_map(static fn (string $c): string => "`$c` = ?", array_keys($data)));
        $cond = implode(' AND ', array_map(static fn (string $c): string => "`$c` = ?", array_keys($where)));
        $stmt = self::run("UPDATE `$table` SET $set WHERE $cond", [...array_values(self::normalize($data)), ...array_values($where)]);

        return $stmt->rowCount();
    }

    /** @param array<string, mixed> $where */
    public static function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(static fn (string $c): string => "`$c` = ?", array_keys($where)));

        return self::run("DELETE FROM `$table` WHERE $cond", array_values($where))->rowCount();
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::pdo();
        if ($pdo->inTransaction()) {
            return $callback();
        }
        $pdo->beginTransaction();
        try {
            $result = $callback();
            $pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function normalize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($value)) {
                $data[$key] = $value ? 1 : 0;
            }
        }

        return $data;
    }
}
