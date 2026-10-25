<?php
$navSections = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/klant', 'dashboard', 'Overzicht']]],
    ['key' => 'diensten', 'label' => 'Mijn diensten', 'icon' => 'server', 'items' => [['/klant/diensten', 'server', 'Mijn diensten']]],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'items' => [['/klant/facturen', 'file', 'Facturen', 'Je facturen en betalingen'], ['/klant/offertes', 'quote', 'Offertes', 'Offertes bekijken en accepteren']], 'also' => ['/klant/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'items' => [['/klant/tickets', 'ticket', 'Tickets']]],
    ['key' => 'account', 'label' => 'Mijn gegevens', 'icon' => 'user', 'items' => [['/klant/profiel', 'user', 'Mijn gegevens']]],
];
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/klant', 'profile' => '/klant/profiel', 'tagline' => 'Klantportaal',
    'footLinks' => [['/diensten', 'cart', 'Nieuwe dienst bestellen', false]], 'body' => $yield('content'),
]);
