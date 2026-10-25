<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Product extends Model
{
    public const GROUPS = ['Hosting' => 'server', 'Domeinen' => 'globe', 'VPS' => 'cpu', 'E-mail' => 'mail', 'Websites' => 'layout', 'Overig' => 'box'];
    /** Dienstsoorten per megamenu-kolom: naam => [icoon, omschrijving]. */
    public const SOORTEN = [
        'Domeinen' => ['Domeinnamen' => ['globe', 'Registraties en verhuizingen'], 'SSL Certificaten' => ['lock', 'Beveiligde verbindingen']],
        'Hosting' => ['Webhosting' => ['server', 'Hosting voor websites'], 'Reseller Hosting' => ['users', 'Hosting voor doorverkoop'], 'E-mail Hosting' => ['mail', 'Zakelijke mailboxen']],
        'Servers' => ['Cloud VPS' => ['cloud', 'Virtuele servers'], 'Dedicated Server' => ['cpu', 'Eigen fysieke server'], 'Big Storage' => ['box', 'Grote opslag en back-ups']],
        'Connectiviteit' => ['VPN' => ['shield', 'Veilig verbinden op afstand'], 'VoIP' => ['phone', 'Zakelijke telefonie'], 'Licenties' => ['tag', 'Software en Microsoft 365'], 'Werkplek Beheer' => ['headset', 'Beheer van werkplekken']],
        'Development' => ['Informatieve Websites' => ['layout', 'Bedrijfs- en informatiesites'], 'Webshops' => ['cart', 'Online winkels'], 'API Koppelingen' => ['code', 'Koppelingen tussen systemen']],
        'Marketing' => ['Marketing SEO' => ['trend', 'Vindbaarheid in Google'], 'Social Media Onderhoud' => ['chat', 'Beheer van social kanalen'], 'Linkbuilding' => ['link', 'Backlinks en autoriteit']],
    ];
    public const PERIODS = ['eenmalig' => 'Eenmalig', 'maand' => 'Per maand', 'kwartaal' => 'Per kwartaal', 'jaar' => 'Per jaar'];

    protected static string $table = 'Producten';
    /** Weergavenaam van een dienstsoort bij producten: een domeinproduct is een extensie. */
    /** Icoon van een dienstsoort, anders van de groep. */
    public static function soortIcon(?string $soort, ?string $groep = null): string
    {
        foreach (self::SOORTEN as $kinds) {
            if (isset($kinds[(string) $soort])) {
                return $kinds[(string) $soort][0];
            }
        }

        return self::GROUPS[(string) $groep] ?? 'box';
    }

    /** Korte omschrijving van een dienstsoort, bv. "Hosting voor websites". */
    public static function soortText(?string $soort): string
    {
        foreach (self::SOORTEN as $kinds) {
            if (isset($kinds[(string) $soort])) {
                return $kinds[(string) $soort][1];
            }
        }

        return '';
    }

    public function isExtension(): bool
    {
        return $this->Dienstsoort === 'Domeinnamen' || ($this->Dienstsoort === null && $this->Groep === 'Domeinen');
    }

    /** ".nl" voor een domeinextensie. */
    public function extensionLabel(): string
    {
        return '.' . explode('-', ltrim((string) $this->Naam, '.'))[0];
    }

    public static function soortLabel(?string $soort): string
    {
        return $soort === 'Domeinnamen' ? 'Domeinextensies' : (string) $soort;
    }

    /** Publieke URL: domeinextensies onder /extensies/{slug}, de rest onder /diensten/{slug}. */
    public static function pathFor(string $slug, ?string $soort, ?string $groep = null): string
    {
        $extension = $soort === 'Domeinnamen' || (($soort === null || $soort === '') && $groep === 'Domeinen');

        return ($extension ? '/extensies/' : '/diensten/') . $slug;
    }

    /** Slug van een dienstsoort in het beheer (bv. ssl-certificaten); bij producten op de weergavenaam (domeinextensies). */
    public static function soortSlug(string $soort, bool $products = false): string
    {
        return slugify($products ? self::soortLabel($soort) : $soort);
    }

    /** Dienstsoort bij een slug, of null. */
    public static function soortFromSlug(string $slug, bool $products = false): ?string
    {
        foreach (self::SOORTEN as $kinds) {
            foreach (array_keys($kinds) as $soort) {
                if (self::soortSlug($soort, $products) === $slug) {
                    return $soort;
                }
            }
        }

        return null;
    }

    public function path(): string
    {
        return self::pathFor((string) $this->Slug, $this->Dienstsoort, $this->Groep);
    }

    protected static ?string $prefix = 'A';
    protected static array $json = ['Kenmerken'];

    /** @return list<string> */
    /** Iconen die je bij een kenmerk kunt kiezen. */
    public const FEATURE_ICONS = ['check' => 'Vinkje', 'server' => 'Server', 'globe' => 'Wereldbol', 'mail' => 'E-mail', 'box' => 'Opslag', 'cpu' => 'Processor', 'cloud' => 'Cloud', 'zap' => 'Snelheid', 'lock' => 'Slot', 'shield' => 'Beveiliging', 'refresh' => 'Back-up', 'users' => 'Gebruikers', 'headset' => 'Support', 'clock' => 'Tijd', 'code' => 'Code', 'layout' => 'Pagina', 'cart' => 'Winkelwagen', 'trend' => 'Groei', 'link' => 'Link', 'chat' => 'Chat', 'star' => 'Ster', 'phone' => 'Telefoon', 'calendar' => 'Kalender', 'tag' => 'Label'];

    /** Kenmerken als [icoon, tekst]; oude kenmerken (alleen tekst) krijgen een vinkje. @return list<array{icoon: string, tekst: string}> */
    public function featureItems(): array
    {
        $out = [];
        foreach ((array) $this->Kenmerken as $item) {
            $text = trim((string) (is_array($item) ? ($item['tekst'] ?? '') : $item));
            $icon = is_array($item) && isset(self::FEATURE_ICONS[$item['icoon'] ?? '']) ? (string) $item['icoon'] : 'check';
            if ($text !== '') {
                $out[] = ['icoon' => $icon, 'tekst' => $text];
            }
        }

        return $out;
    }

    /**
     * Bekende prestatie-specificaties uit de kenmerkteksten halen.
     * @return array{specs: list<array{label:string, num:float, disp:string, max:float}>, rest: list<array{icoon:string, tekst:string}>}
     */
    public function specItems(): array
    {
        $defs = [
            'cpu' => ['CPU', 400, '/([\d.,]+)\s*%?\s*cpu/i', 1.0, '%'],
            'geheugen' => ['Geheugen', 8192, '/([\d.,]+)\s*(mb|gb)\s*[\w-]*?(geheugen|ram)/i', 1024, 'MB'],
            'schijf' => ['Schijfruimte', 500, '/([\d.,]+)\s*(gb|mb)\s*[\w-]*?(opslag|schijf)/i', 1024, 'GB'],
            'data' => ['Dataverkeer', 2000, '/([\d.,]+)\s*gb\s*[\w-]*?dataverkeer/i', 1024, 'GB'],
            'netwerk' => ['Netwerk', 2000, '/([\d.,]+)\s*mbit\/s/i', 1.0, 'Mbit/s'],
        ];
        $specs = [];
        $rest = [];
        foreach ($this->featureItems() as $f) {
            $t = $f['tekst'];
            $matched = false;
            foreach ($defs as $key => [$label, $max, $re, $norm, $unit]) {
                if (isset($specs[$key]) || !preg_match($re, $t, $mm)) {
                    continue;
                }
                $val = (float) str_replace(',', '.', $mm[1]);
                $kind = strtolower($mm[2] ?? '');
                if ($key === 'geheugen' && $kind === 'gb') { $val *= 1024; }
                if ($key === 'schijf' && $kind === 'mb') { $val /= 1024; }
                $disp = $val >= 1000 && in_array($key, ['geheugen', 'schijf'], true)
                    ? rtrim(rtrim(number_format($val / 1024, $val % 1024 ? 1 : 0, ',', ''), '0'), ',') . ' ' . ($key === 'geheugen' ? 'GB' : 'TB')
                    : rtrim(rtrim(number_format($val, $val == (int) $val ? 0 : 1, ',', ''), '0'), ',') . ' ' . $unit;
                $specs[$key] = ['label' => $label, 'num' => $val, 'disp' => $disp, 'max' => $max];
                $matched = true;
                break;
            }
            if (!$matched) {
                $rest[] = $f;
            }
        }
        uasort($specs, fn($a, $b) => 0);
        $order = array_flip(['cpu', 'geheugen', 'schijf', 'data', 'netwerk']);
        uksort($specs, fn($a, $b) => $order[$a] <=> $order[$b]);
        return ['specs' => array_values($specs), 'rest' => $rest];
    }

    /** @return list<string> */
    public function features(): array
    {
        return array_column($this->featureItems(), 'tekst');
    }
}
