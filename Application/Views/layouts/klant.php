<?php
$me = auth_user();
$dienstenCount = $me !== null ? (int) \App\Core\Database::first('SELECT COUNT(*) AS c FROM Diensten WHERE KlantId = ?', [$me->id()])['c'] : 0;
$openFacturen = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Documenten WHERE KlantId = ? AND Soort = 'factuur' AND Status IN ('open','vervallen')", [$me->id()])['c'] : 0;
$openTickets = $me !== null ? (int) \App\Core\Database::first("SELECT COUNT(*) AS c FROM Tickets WHERE KlantId = ? AND Status IN ('open','beantwoord')", [$me->id()])['c'] : 0;
$catCounts = array_fill_keys(array_keys(\App\Models\Service::CATEGORIES), 0);
if ($me !== null) {
    $soortCounts = [];
    foreach (\App\Core\Database::all(
        "SELECT p.Dienstsoort AS Soort, s.Bron, COUNT(*) AS c
         FROM Diensten s LEFT JOIN Producten p ON p.Id = s.ProductId
         WHERE s.KlantId = ? GROUP BY Soort, s.Bron",
        [$me->id()]
    ) as $r) {
        $key = $r['Soort'] !== null && $r['Soort'] !== '' ? (string) $r['Soort'] : (((string) $r['Bron'] === 'domein') ? 'domein' : 'Overig');
        $soortCounts[$key] = ($soortCounts[$key] ?? 0) + (int) $r['c'];
    }
    foreach (\App\Models\Service::CATEGORIES as $key => $def) {
        foreach ($def[1] as $soort) {
            $catCounts[$key] += $soortCounts[$soort] ?? 0;
        }
    }
}
$svcDesc = static function (int $n): string {
    return $n === 0 ? 'Beheer hier' : $n . ' ' . ($n === 1 ? 'dienst' : 'diensten');
};
$svcCols = [
    'Domein & SSL' => [
        ['domeinnamen', 'globe'],
        ['ssl', 'lock'],
    ],
    'Webhosting' => [
        ['webhosting', 'server'],
        ['reseller', 'server'],
        ['e-mailhosting', 'mail'],
    ],
    'Servers' => [
        ['vps', 'cpu'],
        ['dedicated', 'server'],
        ['big-storage', 'cloud'],
    ],
    'Development & ICT' => [
        ['websites', 'layout'],
        ['connectiviteit', 'phone'],
        ['marketing', 'trend'],
        ['ict', 'settings'],
    ],
];
$svcItems = [];
$svcColsIdx = [];
foreach ($svcCols as $col => $items) {
    foreach ($items as [$cat, $icn]) {
        $svcColsIdx[$col][] = count($svcItems);
        $svcItems[] = ['/klant/diensten?cat=' . $cat, $icn, \App\Models\Service::CATEGORIES[$cat][0], $svcDesc($catCounts[$cat])];
    }
}
$navSections = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/klant', 'dashboard', 'Overzicht']]],
    ['key' => 'diensten', 'label' => 'Mijn diensten', 'icon' => 'server', 'count' => $dienstenCount, 'wide' => true,
      'items' => $svcItems,
      'columns' => $svcColsIdx],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'count' => $openFacturen, 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen', $openFacturen], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren'], ['/klant/tegoed', 'wallet', 'Tegoed', 'Saldo opwaarderen en mutaties']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => $openTickets, 'items' => [['/klant/tickets', 'ticket', 'Tickets', 'Je supporttickets', $openTickets], ['/klant/tickets/nieuw', 'plus', 'Nieuw ticket', 'Stel een vraag of meld een probleem'], ['/kennisbank', 'book', 'Kennisbank', 'Handleidingen en veelgestelde vragen'], ['/contact', 'mail', 'Contact', 'Neem contact met ons op']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens', 'Naam, adres en bedrijfsgegevens'], ['/', 'home', 'Terug naar de website', 'Naar de publieke site']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => array_merge($me !== null && $me->isStaff() ? [['/beheer', 'settings', 'Terug naar beheer', false]] : [], [['/oplossingen/webhosting', 'cart', 'Nieuwe dienst bestellen', false]]), 'body' => $yield('content'),
]);
