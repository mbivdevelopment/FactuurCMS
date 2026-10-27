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

    /** Hoofdgroepen voor de categorie-sidebar: sleutel => [label, icoon, categorie-sleutels]. */
    public const CAT_GROUPS = [
        'domeinen' => ['Domeinnamen', 'globe', ['domeinnamen']],
        'ssl' => ['SSL certificaten', 'shield', ['ssl']],
        'webhosting' => ['Webhosting', 'server', ['webhosting', 'reseller', 'e-mailhosting']],
        'servers' => ['Servers', 'cpu', ['vps', 'dedicated', 'big-storage']],
        'ict' => ['ICT', 'zap', ['connectiviteit', 'ict']],
        'development' => ['Development', 'code', ['websites']],
        'marketing' => ['Marketing', 'trend', ['marketing']],
    ];

    /**
     * Vertaalt een cat-filter (hoofdgroep- of categorie-sleutel) naar [label, dienstsoorten].
     * @return array{0: string, 1: list<string>}|null
     */
    public static function catFilter(string $cat): ?array
    {
        if (isset(self::CAT_GROUPS[$cat])) {
            [$label,, $keys] = self::CAT_GROUPS[$cat];
            $soorten = [];
            foreach ($keys as $k) {
                array_push($soorten, ...self::CATEGORIES[$k][1]);
            }

            return [$label, array_values(array_unique($soorten))];
        }

        return isset(self::CATEGORIES[$cat]) ? [self::CATEGORIES[$cat][0], self::CATEGORIES[$cat][1]] : null;
    }

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
