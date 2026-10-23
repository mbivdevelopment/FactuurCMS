<?php

declare(strict_types=1);

namespace App\Core;

class HttpException extends \RuntimeException
{
    public function __construct(int $status, string $message = '')
    {
        parent::__construct($message !== '' ? $message : self::defaultMessage($status), $status);
    }

    private static function defaultMessage(int $status): string
    {
        return match ($status) {
            401 => 'Je moet ingelogd zijn.',
            403 => 'Geen toegang.',
            404 => 'Pagina niet gevonden.',
            419 => 'Sessie verlopen, probeer het opnieuw.',
            429 => 'Te veel pogingen, probeer het later opnieuw.',
            default => 'Er is een fout opgetreden.',
        };
    }
}
