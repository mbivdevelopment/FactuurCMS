<?php
$me = auth_user();
$dienstenCount = $me !== null ? (int) \App\Core\Database::first('SELECT COUNT(*) AS c FROM Diensten WHERE KlantId = ?', [$me->id()])['c'] : 0;
$openFacturen = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status IN ('open','vervallen')", [$me->id()])['c'] : 0;
$openTickets = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Tickets WHERE KlantId = ? AND Status IN ('open','beantwoord')", [$me->id()])['c'] : 0;
$svcGroups = [
    'Domeinen' => 'Domein & SSL',
    'Hosting' => 'Webhosting',
    'E-mail' => 'Webhosting',
    'VPS' => 'Servers',
    'Websites' => 'Development & ICT',
    'Overig' => 'Development & ICT',
];
$svcCols = ['Domein & SSL' => [], 'Webhosting' => [], 'Servers' => [], 'Development & ICT' => []];
$svcIcons = ['Domein & SSL' => 'globe', 'Webhosting' => 'server', 'Servers' => 'cpu', 'Development & ICT' => 'layout'];
if ($me !== null) {
    foreach (\App\Core\Database::all('SELECT s.Naam, s.Status, s.Bron, p.Groep FROM Diensten s LEFT JOIN Producten p ON p.Id = s.ProductId WHERE s.KlantId = ? ORDER BY s.Naam LIMIT 15', [$me->id()]) as $svc) {
        $col = $svcGroups[(string) ($svc['Groep'] ?? '')] ?? (((string) $svc['Bron'] === 'domein') ? 'Domein & SSL' : 'Webhosting');
        $svcCols[$col][] = ['/klant/diensten', $svcIcons[$col], (string) $svc['Naam'], $svc['Status'] === 'actief' ? 'Actief' : \App\Models\Service::STATUSES[$svc['Status']] ?? $svc['Status']];
    }
}
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
      'columns' => $svcColsIdx,
      'footer' => [['/klant/diensten', 'server', 'Al je diensten beheren']],
      ],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'count' => $openFacturen, 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen', $openFacturen], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren'], ['/klant/tegoed', 'wallet', 'Tegoed', 'Saldo opwaarderen en mutaties']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => $openTickets, 'items' => [['/klant/tickets', 'ticket', 'Tickets', 'Je supporttickets', $openTickets], ['/klant/tickets/nieuw', 'plus', 'Nieuw ticket', 'Stel een vraag of meld een probleem'], ['/kennisbank', 'book', 'Kennisbank', 'Handleidingen en veelgestelde vragen'], ['/contact', 'mail', 'Contact', 'Neem contact met ons op']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens', 'Naam, adres en bedrijfsgegevens'], ['/', 'home', 'Terug naar de website', 'Naar de publieke site']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => [['/diensten', 'cart', 'Nieuwe dienst bestellen', false]], 'body' => $yield('content'),
]);
