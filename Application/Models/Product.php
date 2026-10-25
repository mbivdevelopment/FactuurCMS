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

    public function path(): string
    {
        return self::pathFor((string) $this->Slug, $this->Dienstsoort, $this->Groep);
    }

    protected static ?string $prefix = 'A';
    protected static array $json = ['Kenmerken'];

    /** @return list<string> */
    public function features(): array
    {
        return array_values(array_filter(array_map('strval', (array) $this->Kenmerken)));
    }
}
