<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimale active-record laag op PascalCase-tabellen (Id, AangemaaktOp, BijgewerktOp).
 * JSON-kolommen in $json worden automatisch ge(de)codeerd; $prefix geeft een doorlopend Nummer (bv. F00001).
 */
abstract class Model
{
    protected static string $table = '';
    protected static bool $timestamps = true;
    protected static ?string $prefix = null;

    /** @var string[] */
    protected static array $json = [];

    /** @param array<string, mixed> $attributes */
    final public function __construct(protected array $attributes = [])
    {
        foreach (static::$json as $column) {
            if (isset($this->attributes[$column]) && is_string($this->attributes[$column])) {
                $this->attributes[$column] = json_decode($this->attributes[$column], true);
            }
        }
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function id(): int
    {
        return (int) ($this->attributes['Id'] ?? 0);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public static function table(): string
    {
        return static::$table;
    }

    public static function find(int $id): ?static
    {
        $row = Database::first('SELECT * FROM `' . static::$table . '` WHERE Id = ?', [$id]);

        return $row === null ? null : new static($row);
    }

    public static function findOrFail(int $id): static
    {
        return static::find($id) ?? throw new HttpException(404);
    }

    /** @param array<string, mixed> $where */
    public static function firstWhere(array $where): ?static
    {
        [$sql, $params] = self::whereClause($where);
        $row = Database::first('SELECT * FROM `' . static::$table . '`' . $sql . ' LIMIT 1', $params);

        return $row === null ? null : new static($row);
    }

    /**
     * @param array<string, mixed> $where
     * @return list<static>
     */
    public static function where(array $where = [], string $orderBy = 'Id DESC'): array
    {
        [$sql, $params] = self::whereClause($where);

        return static::query('SELECT * FROM `' . static::$table . '`' . $sql . ' ORDER BY ' . $orderBy, $params);
    }

    /**
     * @param array<int|string, mixed> $params
     * @return list<static>
     */
    public static function query(string $sql, array $params = []): array
    {
        return array_map(static fn (array $row): static => new static($row), Database::all($sql, $params));
    }

    /** @param array<string, mixed> $where */
    public static function count(array $where = []): int
    {
        [$sql, $params] = self::whereClause($where);

        return (int) Database::scalar('SELECT COUNT(*) FROM `' . static::$table . '`' . $sql, $params);
    }

    /** @param array<string, mixed> $attributes */
    public static function create(array $attributes): static
    {
        if (static::$timestamps) {
            $attributes['AangemaaktOp'] ??= now();
        }
        $prefix = static::prefixFor($attributes);
        if ($prefix !== null && (string) ($attributes['Nummer'] ?? '') === '') {
            $attributes['Nummer'] = self::nextNumber($prefix);
        }
        $id = Database::insert(static::$table, $attributes);

        return static::find($id) ?? new static(['Id' => $id] + $attributes);
    }

    /** @param array<string, mixed> $attributes */
    protected static function prefixFor(array $attributes): ?string
    {
        return static::$prefix;
    }

    /** Of vrijgekomen nummers (bv. na verwijderen) opnieuw uitgegeven worden. */
    protected static function reusesNumbers(string $prefix): bool
    {
        return false;
    }

    /** Volgend nummer: prefix + 5 cijfers, doorlopend per prefix binnen de tabel, of het laagste vrije nummer. */
    public static function nextNumber(string $prefix): string
    {
        $start = strlen($prefix) + 1;
        $where = ' FROM `' . static::$table . '` WHERE Nummer LIKE ? AND SUBSTRING(Nummer, ?) REGEXP \'^[0-9]+$\'';
        $params = [$start, $prefix . '%', $start];
        if (static::reusesNumbers($prefix)) {
            $used = array_flip(array_map('intval', array_column(Database::all('SELECT CAST(SUBSTRING(Nummer, ?) AS UNSIGNED) AS Nr' . $where, $params), 'Nr')));
            $next = 1;
            while (isset($used[$next])) {
                $next++;
            }
        } else {
            $next = (int) Database::scalar('SELECT MAX(CAST(SUBSTRING(Nummer, ?) AS UNSIGNED))' . $where, $params) + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function save(): static
    {
        $data = $this->attributes;
        unset($data['Id'], $data['AangemaaktOp']);
        if (static::$timestamps) {
            $data['BijgewerktOp'] = now();
            $this->attributes['BijgewerktOp'] = $data['BijgewerktOp'];
        }
        Database::update(static::$table, $data, ['Id' => $this->id()]);

        return $this;
    }

    /** @param array<string, mixed> $attributes */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function delete(): void
    {
        Database::delete(static::$table, ['Id' => $this->id()]);
    }

    /**
     * @param array<string, mixed> $where
     * @return array{0: string, 1: array<int, mixed>}
     */
    private static function whereClause(array $where): array
    {
        if ($where === []) {
            return ['', []];
        }
        $parts = [];
        $params = [];
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                $parts[] = "`$column` IN (" . implode(', ', array_fill(0, max(1, count($value)), '?')) . ')';
                array_push($params, ...($value === [] ? [null] : array_values($value)));
            } elseif ($value === null) {
                $parts[] = "`$column` IS NULL";
            } else {
                $parts[] = "`$column` = ?";
                $params[] = $value;
            }
        }

        return [' WHERE ' . implode(' AND ', $parts), $params];
    }
}
