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

    /** Is dit een domeinnaamdienst (geregistreerd of geïmporteerd via Realtime Register)? */
    public function isDomain(): bool
    {
        if ((string) $this->Bron === 'qevanto' || $this->isHosting()) {
            return false;
        }

        return in_array((string) $this->Bron, ['domein', 'realtimeregister'], true) || $this->domainName() !== '';
    }

    /** Is dit een webhosting-dienst (Qevanto-abonnement of hosting-product)? */
    public function isHosting(): bool
    {
        $soort = $this->product()?->Dienstsoort;
        if ($soort !== null && in_array($soort, ['Werkplek Beheer', 'VPN'], true)) {
            return false;
        }
        $kind = (string) ($this->qevanto()['kind'] ?? '');
        if ((string) $this->Bron === 'qevanto') {
            return $kind === '' || in_array($kind, ['hosting', 'reseller'], true);
        }

        return $soort !== null && in_array($soort, ['Webhosting', 'Reseller Hosting', 'E-mailhosting'], true);
    }

    /** Is dit een VPS/server-dienst (InterServer)? */
    public function isVps(): bool
    {
        return (string) $this->Bron === 'interserver';
    }

    /** Is dit een werkplekbeheer-dienst (Qevanto werkplekpakket)? */
    public function isWorkplace(): bool
    {
        if ((string) $this->Bron === 'qevanto' && (string) ($this->qevanto()['kind'] ?? '') === 'workplace') {
            return true;
        }

        return $this->product()?->Dienstsoort === 'Werkplek Beheer';
    }

    /** De Qevanto-gegevens van deze dienst uit de Gegevens-kolom. @return array<string, mixed> */
    private function qevanto(): array
    {
        $g = is_array($this->Gegevens) ? $this->Gegevens : (json_decode((string) $this->Gegevens, true) ?: []);

        return (array) ($g['qevanto'] ?? []);
    }

    /** De domeinnaam van deze dienst, indien bekend. */
    public function domainName(): string
    {
        if ((string) $this->ExternId !== '' && in_array((string) $this->Bron, ['domein', 'realtimeregister'], true)) {
            return strtolower((string) $this->ExternId);
        }
        $g = is_array($this->Gegevens) ? $this->Gegevens : (json_decode((string) $this->Gegevens, true) ?: []);
        foreach (['domein', 'domain'] as $key) {
            $value = $g[$key] ?? '';
            if (is_array($value)) {
                $value = $value['domein'] ?? $value['naam'] ?? $value['domain'] ?? '';
            }
            if (is_string($value) && preg_match('/^[a-z0-9]([a-z0-9-]*\.)+[a-z]{2,}$/i', $value) === 1) {
                return strtolower($value);
            }
        }
        foreach ([$this->Omschrijving, $this->Naam] as $value) {
            $v = strtolower(trim((string) $value));
            if (preg_match('/^[a-z0-9]([a-z0-9-]*\.)+[a-z]{2,}$/', $v) === 1) {
                return $v;
            }
        }

        return '';
    }

    /** Update een sleutel binnen Gegevens en slaat op. */
    public function putData(string $key, mixed $value): void
    {
        $g = is_array($this->Gegevens) ? $this->Gegevens : (json_decode((string) $this->Gegevens, true) ?: []);
        $g[$key] = $value;
        $this->Gegevens = json_encode($g, JSON_UNESCAPED_UNICODE);
        $this->save();
    }

    /** Datum + één periode (maand/kwartaal/jaar); null bij eenmalig. */
    public static function addPeriod(string $date, string $period): ?string
    {
        $modifier = ['maand' => '+1 month', 'kwartaal' => '+3 months', 'jaar' => '+1 year', '2jaar' => '+2 years', '4jaar' => '+4 years'][$period] ?? null;

        return $modifier === null ? null : date('Y-m-d', (int) strtotime($date . ' ' . $modifier));
    }
}
