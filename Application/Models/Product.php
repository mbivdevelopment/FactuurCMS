<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Product extends Model
{
    public const GROUPS = ['Hosting' => 'server', 'Domeinen' => 'globe', 'VPS' => 'cpu', 'E-mail' => 'mail', 'Websites' => 'layout', 'Overig' => 'box'];
    /** Dienstsoorten per megamenu-kolom, met icoon. */
    public const SOORTEN = [
        'Domeinen' => ['Domeinnamen' => 'globe', 'SSL Certificaten' => 'lock'],
        'Hosting' => ['Webhosting' => 'server', 'Reseller Hosting' => 'users', 'E-mail Hosting' => 'mail'],
        'Servers' => ['Cloud VPS' => 'cloud', 'Dedicated Server' => 'cpu', 'Big Storage' => 'box'],
        'Connectiviteit' => ['VPN' => 'shield', 'VoIP' => 'phone', 'Licenties' => 'tag', 'Werkplek Beheer' => 'headset'],
        'Development' => ['Informatieve Websites' => 'layout', 'Webshops' => 'cart', 'API Koppelingen' => 'code'],
        'Marketing' => ['Marketing SEO' => 'trend', 'Social Media Onderhoud' => 'chat', 'Linkbuilding' => 'link'],
    ];
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
