<?php
$me = auth_user();
$dienstenCount = $me !== null ? (int) \App\Core\Database::first('SELECT COUNT(*) AS c FROM Diensten WHERE KlantId = ?', [$me->id()])['c'] : 0;
$openFacturen = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status IN ('open','vervallen')", [$me->id()])['c'] : 0;
$openTickets = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Tickets WHERE KlantId = ? AND Status IN ('open','beantwoord')", [$me->id()])['c'] : 0;
$svcCols = [
    'Domein & SSL' => [
        ['/oplossingen/domeinnaam-registreren', 'globe', 'Domeinnamen'],
        ['/oplossingen/ssl-certificaten', 'lock', 'SSL certificaten'],
    ],
    'Webhosting' => [
        ['/oplossingen/webhosting', 'server', 'Hosting'],
        ['/oplossingen/reseller-hosting', 'server', 'Reseller hosting'],
        ['/oplossingen/e-mailhosting', 'mail', 'E-mailhosting'],
    ],
    'Servers' => [
        ['/oplossingen/vps-servers', 'cpu', 'CloudVPS'],
        ['/oplossingen/dedicated-servers', 'server', 'Dedicated'],
        ['/oplossingen/big-storage', 'cloud', 'Big Storage'],
    ],
    'Connectiviteit' => [
        ['/oplossingen/voip', 'phone', 'VoIP'],
        ['/oplossingen/vpn', 'shield', 'VPN'],
        ['/oplossingen/licenties', 'file', 'Licenties'],
        ['/oplossingen/werkplek-beheer', 'layout', 'Werkplekbeheer'],
    ],
    'Development' => [
        ['/oplossingen/websites', 'layout', 'Informatieve websites'],
        ['/oplossingen/webshops', 'cart', 'Webshops'],
        ['/oplossingen/api-koppelingen', 'code', 'API-koppelingen'],
    ],
    'Marketing' => [
        ['/oplossingen/marketing-seo', 'trend', 'Marketing SEO'],
        ['/oplossingen/linkbuilding', 'link', 'Linkbuilding'],
        ['/oplossingen/social-media-onderhoud', 'send', 'Social media onderhoud'],
    ],
];
$svcItems = [];
$svcColsIdx = [];
foreach ($svcCols as $col => $items) {
    foreach ($items as $it) { $svcColsIdx[$col][] = count($svcItems); $svcItems[] = $it; }
}
$svcLast = count($svcItems);
$navSections = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/klant', 'dashboard', 'Overzicht']]],
    ['key' => 'diensten', 'label' => 'Mijn diensten', 'icon' => 'server', 'count' => $dienstenCount, 'wide' => true,
      'items' => array_merge($svcItems, [['/klant/diensten', 'server', 'Mijn diensten', 'Al je diensten op een rij']]),
      'columns' => $svcColsIdx + ['Alle diensten' => [$svcLast]],
      'footer' => [['/diensten', 'cart', 'Dienst bestellen'], ['/domeinen', 'globe', 'Domein registreren']]],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'count' => $openFacturen, 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen', $openFacturen], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren'], ['/klant/tegoed', 'wallet', 'Tegoed', 'Saldo opwaarderen en mutaties']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => $openTickets, 'items' => [['/klant/tickets', 'ticket', 'Tickets', 'Je supporttickets', $openTickets], ['/klant/tickets/nieuw', 'plus', 'Nieuw ticket', 'Stel een vraag of meld een probleem'], ['/kennisbank', 'book', 'Kennisbank', 'Handleidingen en veelgestelde vragen'], ['/contact', 'mail', 'Contact', 'Neem contact met ons op']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens', 'Naam, adres en bedrijfsgegevens'], ['/', 'home', 'Terug naar de website', 'Naar de publieke site']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => [['/diensten', 'cart', 'Nieuwe dienst bestellen', false]], 'body' => $yield('content'),
]);
