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

    /** @return list<string> */
    public function features(): array
    {
        return array_column($this->featureItems(), 'tekst');
    }
}
