<?php
$user = auth_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$nav = [
    ['/klant', 'dashboard', 'Overzicht'],
    ['/klant/diensten', 'server', 'Mijn diensten'],
    ['/klant/facturen', 'file', 'Facturen'],
    ['/klant/offertes', 'quote', 'Offertes'],
    ['/klant/tickets', 'ticket', 'Support'],
    ['/klant/profiel', 'user', 'Mijn gegevens'],
];
?>
<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head') ?></head>
<body>
<?php if ($imp = \App\Core\Auth::impersonator()): ?>
    <div class="impersonate"><span>Je bekijkt het portaal als <strong><?= e($user?->display()) ?></strong>.</span>
        <form method="post" action="<?= e(url('/klant/terug')) ?>"><?= csrf_field() ?><button class="btn btn-sm">Terug naar beheer</button></form></div>
<?php endif; ?>
<div class="app klant-app">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= e(url('/klant')) ?>"><?= $partial('partials.brand') ?></a>
        <nav class="nav">
            <?php foreach ($nav as [$href, $ico, $text]): ?>
                <?php $active = $href === '/klant' ? $path === '/klant' : str_starts_with($path, $href); ?>
                <a href="<?= e(url($href)) ?>" class="<?= $active ? 'active' : '' ?>"><?= icon($ico) ?> <?= e($text) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="nav-label">Bestellen</div>
        <nav class="nav"><a href="<?= e(url('/diensten')) ?>"><?= icon('cart') ?> Nieuwe dienst</a></nav>
    </aside>
    <div class="main">
        <header class="topbar">
            <button type="button" class="btn btn-ghost btn-icon menu-btn" data-menu-toggle="#sidebar"><?= icon('layout') ?></button>
            <div class="small muted">Klantnummer <?= e($user?->Nummer) ?></div>
            <div class="user">
                <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) $user?->Voornaam, 0, 1) . mb_substr((string) $user?->Achternaam, 0, 1))) ?></span>
                <strong><?= e($user?->display()) ?></strong>
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
