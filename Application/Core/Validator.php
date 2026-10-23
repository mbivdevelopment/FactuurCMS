<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Regels: required, email, min:N, max:N, confirmed, numeric, int, in:a,b, boolean,
 * unique:table,column[,ignoreId], exists:table,column, regex:/.../, url, date, accepted
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, string> */
    private const LABELS = [
        'Email' => 'e-mailadres',
        'Wachtwoord' => 'wachtwoord',
        'Wachtwoord_confirmation' => 'wachtwoordbevestiging',
        'KvkNummer' => 'KvK-nummer',
        'BtwNummer' => 'btw-nummer',
        'Iban' => 'IBAN',
    ];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     */
    public function __construct(private readonly array $data, array $rules)
    {
        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleString);
            $isRequired = in_array('required', $ruleList, true);
            $empty = $value === null || $value === '' || $value === [];

            if ($empty && !$isRequired) {
                continue;
            }

            foreach ($ruleList as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, '');
                $message = $this->check($name, $arg, $field, $value);
                if ($message !== null) {
                    $this->errors[$field] = $message;
                    break;
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     */
    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    private function check(string $rule, string $arg, string $field, mixed $value): ?string
    {
        $label = self::LABELS[$field] ?? strtolower(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace('_', ' ', $field))));
        $str = is_scalar($value) ? (string) $value : '';

        return match ($rule) {
            'required' => ($value === null || $value === '' || $value === []) ? "Het veld $label is verplicht." : null,
            'email' => filter_var($str, FILTER_VALIDATE_EMAIL) === false ? "Vul een geldig $label in." : null,
            'min' => mb_strlen($str) < (int) $arg ? "Het veld $label moet minimaal $arg tekens bevatten." : null,
            'max' => mb_strlen($str) > (int) $arg ? "Het veld $label mag maximaal $arg tekens bevatten." : null,
            'confirmed' => ($this->data[$field . '_confirmation'] ?? null) !== $value ? "De bevestiging van $label komt niet overeen." : null,
            'numeric' => !is_numeric($value) ? "Het veld $label moet een getal zijn." : null,
            'int' => filter_var($value, FILTER_VALIDATE_INT) === false ? "Het veld $label moet een geheel getal zijn." : null,
            'in' => !in_array($str, explode(',', $arg), true) ? "De waarde van $label is ongeldig." : null,
            'boolean' => !in_array($value, [true, false, 0, 1, '0', '1', 'on', 'off'], true) ? "Het veld $label is ongeldig." : null,
            'accepted' => !in_array($value, [true, 1, '1', 'on', 'yes'], true) ? "Je moet de $label accepteren." : null,
            'url' => filter_var($str, FILTER_VALIDATE_URL) === false ? "Vul een geldige URL in voor $label." : null,
            'date' => strtotime($str) === false ? "Vul een geldige datum in voor $label." : null,
            'regex' => !preg_match($arg, $str) ? "Het formaat van $label is ongeldig." : null,
            'unique' => $this->unique($arg, $str) ? "Dit $label is al in gebruik." : null,
            'exists' => !$this->exists($arg, $str) ? "De gekozen waarde voor $label bestaat niet." : null,
            default => null,
        };
    }

    private function unique(string $arg, string $value): bool
    {
        [$table, $column, $ignore] = array_pad(explode(',', $arg), 3, null);
        $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
        $params = [$value];
        if ($ignore !== null && $ignore !== '') {
            $sql .= ' AND Id <> ?';
            $params[] = (int) $ignore;
        }

        return (int) Database::scalar($sql, $params) > 0;
    }

    private function exists(string $arg, string $value): bool
    {
        [$table, $column] = array_pad(explode(',', $arg), 2, 'id');

        return (int) Database::scalar("SELECT COUNT(*) FROM `$table` WHERE `$column` = ?", [$value]) > 0;
    }
}
