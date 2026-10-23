<?php
$user = auth_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$openTickets = (int) \App\Core\Database::scalar("SELECT COUNT(*) FROM Tickets WHERE Status = 'open'");
$nav = [
    'Overzicht' => [['/beheer', 'dashboard', 'Dashboard', 0]],
    'Relaties' => [['/beheer/klanten', 'users', 'Klanten', 0], ['/beheer/diensten', 'server', 'Diensten', 0]],
    'Financieel' => [['/beheer/facturen', 'file', 'Facturen', 0], ['/beheer/offertes', 'quote', 'Offertes', 0]],
    'Support' => [['/beheer/tickets', 'ticket', 'Tickets', $openTickets]],
    'Beheer' => array_values(array_filter([
        ['/beheer/producten', 'box', 'Producten', 0],
        $user?->isAdmin() ? ['/beheer/medewerkers', 'shield', 'Medewerkers', 0] : null,
        $user?->isAdmin() ? ['/beheer/instellingen', 'settings', 'Instellingen', 0] : null,
        $user?->isAdmin() ? ['/beheer/updates', 'refresh', 'Updates', 0] : null,
    ])),
];
?>
<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head') ?></head>
<body>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= e(url('/beheer')) ?>"><?= $partial('partials.brand', ['dark' => true]) ?></a>
        <?php foreach ($nav as $label => $items): ?>
            <div class="nav-label"><?= e($label) ?></div>
            <nav class="nav">
                <?php foreach ($items as [$href, $ico, $text, $count]): ?>
                    <?php $active = $href === '/beheer' ? $path === '/beheer' : str_starts_with($path, $href); ?>
                    <a href="<?= e(url($href)) ?>" class="<?= $active ? 'active' : '' ?>"><?= icon($ico) ?> <?= e($text) ?><?php if ($count > 0): ?><span class="count"><?= $count ?></span><?php endif; ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endforeach; ?>
        <div class="nav-label">Website</div>
        <nav class="nav"><a href="<?= e(url('/')) ?>" target="_blank"><?= icon('globe') ?> Bekijk website</a></nav>
    </aside>
    <div class="main">
        <header class="topbar">
            <div class="row">
                <button type="button" class="btn btn-ghost btn-icon menu-btn" data-menu-toggle="#sidebar"><?= icon('layout') ?></button>
                <form class="search" method="get" action="<?= e(url('/beheer/klanten')) ?>"><?= icon('search') ?><input name="q" placeholder="Zoek klant, e-mail of nummer…" value="<?= e($_GET['q'] ?? '') ?>"></form>
            </div>
            <div class="user">
                <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) $user?->Voornaam, 0, 1) . mb_substr((string) $user?->Achternaam, 0, 1))) ?></span>
                <div><strong><?= e($user?->name()) ?></strong><div class="small muted"><?= e(\App\Models\User::ROLES[$user?->Rol] ?? '') ?></div></div>
                <form method="post" action="<?= e(url('/uitloggen')) ?>"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" type="submit"><?= icon('logout', 'icon icon-sm') ?> Uitloggen</button></form>
            </div>
        </header>
        <main class="content">
            <?= $partial('partials.flash') ?>
            <?= $yield('content') ?>
        </main>
    </div>
</div>
</body>
</html>
