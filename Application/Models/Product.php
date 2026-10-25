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
    protected static ?string $prefix = 'A';
    protected static array $json = ['Kenmerken'];

    /** @return list<string> */
    public function features(): array
    {
        return array_values(array_filter(array_map('strval', (array) $this->Kenmerken)));
    }
}
