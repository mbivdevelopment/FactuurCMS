<?php
$me = auth_user();
$dienstenCount = $me !== null ? (int) \App\Core\Database::first('SELECT COUNT(*) AS c FROM Diensten WHERE KlantId = ?', [$me->id()])['c'] : 0;
$openFacturen = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status IN ('open','vervallen')", [$me->id()])['c'] : 0;
$openTickets = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Tickets WHERE KlantId = ? AND Status IN ('open','beantwoord')", [$me->id()])['c'] : 0;
$navSections = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/klant', 'dashboard', 'Overzicht']]],
    ['key' => 'diensten', 'label' => 'Mijn diensten', 'icon' => 'server', 'count' => $dienstenCount, 'items' => [
        ['/klant/diensten', 'server', 'Mijn diensten', 'Al je hosting, domeinen en diensten'],
        ['/diensten', 'cart', 'Dienst bestellen', 'Nieuw hostingpakket of dienst'],
        ['/domeinen', 'globe', 'Domein registreren', 'Zoek en registreer een domeinnaam'],
    ], 'columns' => ['Beheer' => [0], 'Bestellen' => [1, 2]]],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'count' => $openFacturen, 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen', $openFacturen], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren'], ['/klant/tegoed', 'wallet', 'Tegoed', 'Saldo opwaarderen en mutaties']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => $openTickets, 'items' => [['/klant/tickets', 'ticket', 'Tickets', 'Je supporttickets', $openTickets], ['/klant/tickets/nieuw', 'plus', 'Nieuw ticket', 'Stel een vraag of meld een probleem'], ['/kennisbank', 'book', 'Kennisbank', 'Handleidingen en veelgestelde vragen'], ['/contact', 'mail', 'Contact', 'Neem contact met ons op']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens', 'Naam, adres en bedrijfsgegevens'], ['/', 'home', 'Terug naar de website', 'Naar de publieke site']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => [['/diensten', 'cart', 'Nieuwe dienst bestellen', false]], 'body' => $yield('content'),
]);
