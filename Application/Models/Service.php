<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Service extends Model
{
    public const STATUSES = ['in_afwachting' => 'In afwachting', 'actief' => 'Actief', 'opgeschort' => 'Opgeschort', 'opgezegd' => 'Opgezegd'];

    /** Beheer-categorieën in het klantportaal: sleutel => [label, dienstsoorten (of 'domein' voor Bron=domein)]. */
    public const CATEGORIES = [
        'domeinnamen' => ['Domeinnamen', ['Domeinnamen', 'domein'], 'globe'],
        'ssl' => ['SSL certificaten', ['SSL certificaten'], 'shield'],
        'webhosting' => ['Webhosting', ['Webhosting', 'Reseller Hosting', 'E-mailhosting'], 'server'],
        'reseller' => ['Reseller hosting', ['Reseller Hosting'], 'users'],
        'e-mailhosting' => ['E-mailhosting', ['E-mailhosting'], 'mail'],
        'vps' => ['CloudVPS', ['Cloud VPS', 'VPS'], 'cloud'],
        'dedicated' => ['Dedicated', ['Dedicated Server', 'Dedicated'], 'cpu'],
        'big-storage' => ['Big Storage', ['Big Storage'], 'box'],
        'websites' => ['Websites & webshops', ['Informatieve Websites', 'Webshops', 'Websites', 'API Koppelingen'], 'code'],
        'connectiviteit' => ['Connectiviteit', ['VoIP', 'VPN', 'Licenties', 'Werkplek beheer', 'Werkplekbeheer'], 'zap'],
        'marketing' => ['Marketing', ['Marketing SEO', 'Linkbuilding', 'Social Media Onderhoud'], 'trend'],
        'ict' => ['ICT & overig', ['Overig'], 'settings'],
    ];

    protected static string $table = 'Diensten';
    protected static ?string $prefix = 'D';
    protected static array $json = ['Gegevens'];

    public function customer(): ?User
    {
        return User::find((int) $this->KlantId);
    }

    public function product(): ?Product
    {
        return $this->ProductId !== null ? Product::find((int) $this->ProductId) : null;
    }

    /** Datum + één periode (maand/kwartaal/jaar); null bij eenmalig. */
    public static function addPeriod(string $date, string $period): ?string
    {
        $modifier = ['maand' => '+1 month', 'kwartaal' => '+3 months', 'jaar' => '+1 year', '2jaar' => '+2 years', '4jaar' => '+4 years'][$period] ?? null;

        return $modifier === null ? null : date('Y-m-d', (int) strtotime($date . ' ' . $modifier));
    }
}
