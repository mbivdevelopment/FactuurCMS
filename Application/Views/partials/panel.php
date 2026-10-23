<?php
/** Paneel-frame (QEVANTO-ALMA-opbouw): horizontale navy menubalk, witte topbar, titelcard met sectietabs eronder. */
$user = auth_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$path = rtrim($path, '/') ?: '/';
$matches = static fn (string $to): bool => $path === $to || (substr_count($to, '/') > 1 && str_starts_with($path, $to . '/'));
$current = null;
foreach ($navSections as $s) {
    foreach (array_merge(array_column($s['items'], 0), $s['also'] ?? []) as $to) {
        if ($matches($to)) { $current = $s; break 2; }
    }
}
$crumb = null;
foreach ($current['items'] ?? [] as $i) { if ($matches($i[0])) { $crumb = $i[2]; } }
$tabs = $current && count($current['items']) > 1 && in_array($path, array_column($current['items'], 0), true) ? $current['items'] : [];
$roleKey = (string) $user?->Rol;
$initial = mb_strtoupper(mb_substr((string) ($user?->Voornaam ?: $user?->Email), 0, 1));
?>
<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head', ['sheet' => 'beheer']) ?></head>
<body>
<div class="app">
    <header class="nav" id="nav">
        <div class="nav-in">
            <a class="nav-brand" href="<?= e(url($home)) ?>"><?php $logo = branding_url(branding_url('logo_licht') !== '' ? 'logo_licht' : 'logo'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e(company_name()) ?>"><?php else: ?><span class="brand-mark"><?= icon('server') ?></span><span><?= e(company_name()) ?><small><?= e($tagline) ?></small></span><?php endif; ?></a>
            <button type="button" class="tb-icon menu-btn" data-menu-toggle="#nav" aria-label="Menu"><?= icon('layout') ?></button>
            <nav class="nav-links">
                <?php foreach ($navSections as $s): ?><a href="<?= e(url($s['items'][0][0])) ?>" class="nav-title<?= ($current['key'] ?? '') === $s['key'] ? ' active' : '' ?>"><?= icon($s['icon']) ?><span><?= e($s['label']) ?></span><?php if (($s['count'] ?? 0) > 0): ?><span class="nav-count"><?= (int) $s['count'] ?></span><?php endif; ?></a><?php endforeach; ?>
            </nav>
            <div class="nav-foot"><?php foreach ($footLinks as [$href, $ico, $text, $blank]): ?><a class="nav-link" href="<?= e(url($href)) ?>"<?= $blank ? ' target="_blank"' : '' ?>><?= icon($ico) ?><?= e($text) ?></a><?php endforeach; ?></div>
        </div>
    </header>
    <div class="main">
        <header class="topbar2">
            <div class="crumbs"><span><?= e($current['label'] ?? 'Account') ?></span><?= icon('chevron-right') ?><strong><?= e($crumb ?? ($title ?? '')) ?></strong></div>
            <div class="tb-right">
                <span class="role-pill role-<?= e($roleKey) ?>"><?= e(\App\Models\User::ROLES[$roleKey] ?? '') ?></span>
                <a href="<?= e(url($profile)) ?>" class="tb-user2" title="<?= e($user?->Email) ?>"><span class="avatar"><?= e($initial) ?></span><span class="tb-user-text"><strong><?= e($user?->name()) ?></strong><small><?= e($user?->Email) ?></small></span></a>
                <form method="post" action="<?= e(url('/uitloggen')) ?>"><?= csrf_field() ?><button type="submit" class="tb-icon" title="Uitloggen" aria-label="Uitloggen"><?= icon('logout') ?></button></form>
            </div>
        </header>
        <?php if (\App\Core\Auth::impersonator()): ?>
            <div class="impersonate"><span>Je bekijkt het portaal als <b><?= e($user?->display()) ?></b> (<?= e($user?->Email) ?>).</span>
                <form method="post" action="<?= e(url('/klant/terug')) ?>"><?= csrf_field() ?><button class="btn btn-ghost btn-sm"><?= icon('arrow-right', 'icon icon-sm') ?> Terug naar beheer</button></form></div>
        <?php endif; ?>
        <main class="content">
            <?php if ($tabs !== []): ?><nav class="tabs section-tabs"><?php foreach ($tabs as [$to, $ico, $text]): ?><a href="<?= e(url($to)) ?>" class="<?= $path === $to ? 'active' : '' ?>"><?= icon($ico, 'icon icon-sm') ?><?= e($text) ?></a><?php endforeach; ?></nav><?php endif; ?>
            <?= $partial('partials.flash', ['toast' => true]) ?>
            <?= $body ?>
        </main>
        <footer class="footer2"><span>&copy; <?= date('Y') ?> <?= e(company_name()) ?> · <?= e($tagline) ?></span><span>Ingelogd als <?= e($user?->Email) ?></span></footer>
    </div>
</div>
</body>
</html>
