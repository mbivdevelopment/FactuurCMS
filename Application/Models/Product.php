<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Product extends Model
{
    public const GROUPS = ['Hosting' => 'server', 'Domeinen' => 'globe', 'VPS' => 'cpu', 'E-mail' => 'mail', 'Websites' => 'layout', 'Overig' => 'box'];
    public const PERIODS = ['eenmalig' => 'Eenmalig', 'maand' => 'Per maand', 'kwartaal' => 'Per kwartaal', 'jaar' => 'Per jaar'];

    protected static string $table = 'Producten';
    protected static ?string $prefix = 'A';
    protected static array $json = ['Kenmerken'];

    /** @return list<string> */
    public function features(): array
    {
        return array_values(array_filter(array_map('strval', (array) $this->Kenmerken)));
    }
}
