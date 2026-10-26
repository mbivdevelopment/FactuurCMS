<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Service extends Model
{
    public const STATUSES = ['in_afwachting' => 'In afwachting', 'actief' => 'Actief', 'opgeschort' => 'Opgeschort', 'opgezegd' => 'Opgezegd'];

    /** Beheer-categorieën in het klantportaal: sleutel => [label, dienstsoorten (of 'domein' voor Bron=domein)]. */
    public const CATEGORIES = [
        'domeinnamen' => ['Domeinnamen', ['Domeinnamen', 'domein']],
        'ssl' => ['SSL certificaten', ['SSL certificaten']],
        'webhosting' => ['Webhosting', ['Webhosting', 'Reseller Hosting', 'E-mailhosting']],
        'reseller' => ['Reseller hosting', ['Reseller Hosting']],
        'e-mailhosting' => ['E-mailhosting', ['E-mailhosting']],
        'vps' => ['CloudVPS', ['Cloud VPS', 'VPS']],
        'dedicated' => ['Dedicated', ['Dedicated Server', 'Dedicated']],
        'big-storage' => ['Big Storage', ['Big Storage']],
        'websites' => ['Websites & webshops', ['Informatieve Websites', 'Webshops', 'Websites', 'API Koppelingen']],
        'connectiviteit' => ['Connectiviteit', ['VoIP', 'VPN', 'Licenties', 'Werkplek beheer', 'Werkplekbeheer']],
        'marketing' => ['Marketing', ['Marketing SEO', 'Linkbuilding', 'Social Media Onderhoud']],
        'ict' => ['ICT & overig', ['Overig']],
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
