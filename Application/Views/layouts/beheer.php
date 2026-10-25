<?php
$admin = (bool) auth_user()?->isAdmin();
$navSections = array_values(array_filter([
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'items' => [['/beheer', 'dashboard', 'Overzicht']]],
    ['key' => 'klanten', 'label' => 'Klanten', 'icon' => 'users', 'items' => [['/beheer/klanten', 'users', 'Klanten', 'Alle klanten en klantkaarten'], ['/beheer/diensten', 'server', 'Diensten', 'Hosting, domeinen en abonnementen']], 'action' => ['/beheer/klanten/nieuw', 'Nieuwe klant']],
    ['key' => 'facturatie', 'label' => 'Facturatie', 'icon' => 'euro', 'items' => [['/beheer/facturen', 'file', 'Facturen', 'Maken, versturen en betalingen'], ['/beheer/offertes', 'quote', 'Offertes', 'Maken en omzetten naar factuur']], 'action' => ['/beheer/facturen/nieuw', 'Nieuwe factuur'], 'also' => ['/beheer/documenten']],
    ['key' => 'support', 'label' => 'Support', 'icon' => 'ticket', 'count' => (int) \App\Core\Database::scalar("SELECT COUNT(*) FROM Tickets WHERE Status = 'open'"), 'items' => [['/beheer/tickets', 'ticket', 'Tickets']]],
    ['key' => 'producten', 'label' => 'Producten', 'icon' => 'box', 'items' => [['/beheer/producten', 'box', 'Producten']]],
    $admin ? ['key' => 'platform', 'label' => 'Platform', 'icon' => 'settings', 'items' => [['/beheer/medewerkers', 'shield', 'Medewerkers', 'Toegang voor je team'], ['/beheer/instellingen', 'settings', 'Instellingen', 'Bedrijf, branding, facturatie en e-mail'], ['/beheer/updates', 'refresh', 'Updates', 'GitHub-updater en migraties']]] : null,
]));
echo $partial('partials.panel', [
    'navSections' => $navSections, 'home' => '/beheer', 'profile' => '/beheer/medewerkers/' . \App\Core\Auth::id(), 'tagline' => 'Hosting facturatie',
    'footLinks' => [['/', 'globe', 'Bekijk website', true]], 'body' => $yield('content'),
]);
