<?php
$me = auth_user();
$dienstenCount = $me !== null ? (int) \App\Core\Database::first('SELECT COUNT(*) AS c FROM Diensten WHERE KlantId = ?', [$me->id()])['c'] : 0;
$openFacturen = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status IN ('open','vervallen')", [$me->id()])['c'] : 0;
$openTickets = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Tickets WHERE KlantId = ? AND Status IN ('open','beantwoord')", [$me->id()])['c'] : 0;
$navSections = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/klant', 'dashboard', 'Overzicht']]],
    ['key' => 'diensten', 'label' => 'Mijn diensten', 'icon' => 'server', 'count' => $dienstenCount, 'wide' => true, 'items' => [
        // Domeinen
        ['/domeinen', 'globe', 'Domein zoeken', 'Check en registreer je domein'],
        ['/oplossingen/domeinnaam-registreren', 'globe', 'Domeinnaam registreren', 'Alles over domeinregistratie'],
        ['/oplossingen/ssl-certificaten', 'lock', 'SSL-certificaten', 'HTTPS voor je website'],
        // Hosting
        ['/diensten?groep=Hosting', 'server', 'Webhosting', 'Snelle hostingpakketten'],
        ['/oplossingen/reseller-hosting', 'users', 'Reseller hosting', 'Hosting voor je eigen klanten'],
        ['/oplossingen/e-mailhosting', 'mail', 'E-mailhosting', 'Zakelijke mailboxen'],
        // Servers
        ['/oplossingen/vps-servers', 'cpu', 'Cloud VPS', 'Flexibele virtuele servers'],
        ['/oplossingen/dedicated-servers', 'server', 'Dedicated servers', 'Eigen hardware in het DC'],
        ['/oplossingen/big-storage', 'database', 'Big Storage', 'Ruime opslag voor backups en data'],
        // Development
        ['/oplossingen/websites', 'layout', 'Websites', 'Websites en webshops op maat'],
        ['/oplossingen/webshops', 'cart', 'Webshops', 'Complete webshop-oplossingen'],
        ['/portfolio', 'image', 'Portfolio', 'Bekijk ons gerealiseerde werk'],
        // ICT
        ['/oplossingen/werkplek-beheer', 'laptop', 'Werkplek beheer', 'Beheer van je werkplekken'],
        ['/oplossingen/voip', 'phone', 'VoIP', 'Zakelijk bellen via internet'],
        ['/oplossingen/vpn', 'shield', 'VPN', 'Veilige verbinding op afstand'],
    ], 'columns' => ['Domeinen' => [0, 1, 2], 'Hosting' => [3, 4, 5], 'Servers' => [6, 7, 8], 'Development' => [9, 10, 11], 'ICT' => [12, 13, 14]],
      'footer' => [['/klant/diensten', 'server', 'Mijn diensten'], ['/diensten', 'cart', 'Alle diensten & pakketten']], 'action' => ['/domeinen', 'Domein registreren']],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'count' => $openFacturen, 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen', $openFacturen], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren'], ['/klant/tegoed', 'wallet', 'Tegoed', 'Saldo opwaarderen en mutaties']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => $openTickets, 'items' => [['/klant/tickets', 'ticket', 'Tickets', 'Je supporttickets', $openTickets], ['/klant/tickets/nieuw', 'plus', 'Nieuw ticket', 'Stel een vraag of meld een probleem'], ['/kennisbank', 'book', 'Kennisbank', 'Handleidingen en veelgestelde vragen'], ['/contact', 'mail', 'Contact', 'Neem contact met ons op']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens', 'Naam, adres en bedrijfsgegevens'], ['/', 'home', 'Terug naar de website', 'Naar de publieke site']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => [['/diensten', 'cart', 'Nieuwe dienst bestellen', false]], 'body' => $yield('content'),
]);
