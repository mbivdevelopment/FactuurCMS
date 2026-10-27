<?php
/** Paneel-frame (QEVANTO-ALMA-opbouw): horizontale navy menubalk, dropdowns per sectie, witte topbar, titelcard. */
$user = auth_user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$path = rtrim($path, '/') ?: '/';
$matches = static fn (string $to): bool => $path === $to || (substr_count($to, '/') > 1 && str_starts_with($path, $to . '/'));
$current = null;
foreach ([false, true] as $useAlso) {
    foreach ($navSections as $s) {
        foreach ($useAlso ? $s['also'] ?? [] : array_column(array_filter($s['items'], static fn ($i) => empty($i[5])), 0) as $to) {
            if ($matches($to)) { $current = $s; break 3; }
        }
    }
}
$crumb = null;
foreach ($current['items'] ?? [] as $i) { if ($matches($i[0])) { $crumb = $i[2]; } }
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
            <a class="nav-brand" href="<?= e(url($home)) ?>"><?php $logo = branding_url(branding_url('logo_licht') !== '' ? 'logo_licht' : 'logo'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>"<?= branding_url('logo_licht') === '' ? ' data-white' : '' ?> alt="<?= e(company_name()) ?>"><?php else: ?><span class="brand-mark"><?= icon('server') ?></span><span><?= e(company_name()) ?><small><?= e($tagline) ?></small></span><?php endif; ?></a>
            <button type="button" class="tb-icon menu-btn" data-menu-toggle="#nav" aria-label="Menu"><?= icon('layout') ?></button>
            <nav class="nav-links">
                <?php foreach ($navSections as $s): ?>
                    <?php $drop = count($s['items']) > 1 && empty($s['sidebar']); $isActive = ($current['key'] ?? '') === $s['key']; ?>
                    <div class="nav-item<?= $drop ? ' has-drop' : '' ?>">
                        <a href="<?= e(url($s['href'] ?? $s['items'][0][0])) ?>" class="nav-title<?= $isActive ? ' active' : '' ?>"<?= $drop ? ' aria-haspopup="true"' : '' ?>><?= icon($s['icon']) ?><span><?= e($s['label']) ?></span><?php if (($s['count'] ?? 0) > 0): ?><span class="nav-count"><?= (int) $s['count'] ?></span><?php endif; ?><?php if ($drop): ?><?= icon('chevron-down', 'icon caret') ?><?php endif; ?></a>
                        <?php if ($drop): ?>
                            <div class="nav-drop<?= !empty($s['wide']) ? ' wide' : '' ?><?= !empty($s['columns']) ? ' cols' : '' ?>"<?= !empty($s['columns']) ? ' style="--cols:' . ($s['cols'] ?? count($s['columns'])) . '"' : '' ?>>
                                <?php $colsDef = $s['columns'] ?? ['' => array_keys($s['items'])]; ?>
                                <?php foreach ($colsDef as $colTitle => $idx): $colItems = array_map(static fn ($n) => $s['items'][$n], $idx); ?>
                                <?php if ($colTitle !== ''): ?><div class="drop-col"><div class="drop-h"><?= e($colTitle) ?></div><?php endif; ?>
                                <?php foreach ($colItems as $it): ?>
                                    <a href="<?= e(url($it[0])) ?>" class="drop-link<?= $matches($it[0]) || ($isActive && $crumb === $it[2]) ? ' active' : '' ?>"><span class="drop-i"><?= icon($it[1]) ?></span><span class="drop-t"><strong><?= e($it[2]) ?></strong><?php if (($it[3] ?? '') !== ''): ?><small><?= e($it[3]) ?></small><?php endif; ?></span><?php if (($it[4] ?? 0) > 0): ?><span class="nav-count drop-count"><?= (int) $it[4] ?></span><?php endif; ?></a>
                                <?php endforeach; ?>
                                <?php if ($colTitle !== ''): ?></div><?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (isset($s['footer'])): ?><div class="drop-foot"><?php foreach ($s['footer'] as [$fh, $fi, $fl]): ?><a href="<?= e(url($fh)) ?>"><?= icon($fi, 'icon icon-sm') ?><?= e($fl) ?></a><?php endforeach; ?><?php if (isset($s['action'])): ?><a href="<?= e(url($s['action'][0])) ?>" class="drop-foot-action"><?= icon('plus', 'icon icon-sm') ?><?= e($s['action'][1]) ?></a><?php endif; ?></div><?php elseif (isset($s['action'])): ?><a href="<?= e(url($s['action'][0])) ?>" class="drop-action"><?= icon('plus', 'icon icon-sm') ?><?= e($s['action'][1]) ?></a><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </nav>
            <div class="nav-foot"><a href="<?= e(url($profile)) ?>" class="nav-user" title="<?= e($user?->Email) ?>"><span class="avatar"><?= e($initial) ?></span><span class="nav-user-text"><strong><?= e($user?->name()) ?></strong><small><?= e(\App\Models\User::ROLES[$roleKey] ?? '') ?></small></span></a><?php foreach ($footLinks as [$href, $ico, $text, $blank]): ?><a class="nav-ico" href="<?= e(url($href)) ?>" title="<?= e($text) ?>" aria-label="<?= e($text) ?>"<?= $blank ? ' target="_blank"' : '' ?>><?= icon($ico) ?></a><?php endforeach; ?><form method="post" action="<?= e(url('/uitloggen')) ?>"><?= csrf_field() ?><button type="submit" class="nav-logout" title="Uitloggen" aria-label="Uitloggen"><?= icon('logout') ?></button></form></div>
        </div>
    </header>
    <div class="main">
        <?php if (\App\Core\Auth::impersonator()): ?>
            <div class="impersonate"><span>Je bekijkt het portaal als <b><?= e($user?->display()) ?></b> (<?= e($user?->Email) ?>).</span>
                <form method="post" action="<?= e(url('/klant/terug')) ?>"><?= csrf_field() ?><button class="btn btn-ghost btn-sm"><?= icon('arrow-right', 'icon icon-sm') ?> Terug naar beheer</button></form></div>
        <?php endif; ?>
        <main class="content">
            <?= $partial('partials.flash', ['toast' => true]) ?>
            <?php if (!empty($current['sidebar'])): ?>
            <div class="svc-split">
                <aside>
                    <div class="card"><div class="card-body">
                        <?php foreach (($current['columns'] ?? ['' => array_keys($current['items'])]) as $colTitle => $idx): ?>
                            <h3 class="side-title"><?= e($colTitle !== '' ? $colTitle : $current['label']) ?></h3>
                            <nav class="sidecat" style="margin-bottom:10px">
                                <?php foreach ($idx as $n): $it = $current['items'][$n]; $mp = explode('#', $it[0])[0]; ?>
                                    <a href="<?= e(url($it[0])) ?>"<?= ($matches($mp) && (!str_contains($it[0], '#') || $it[0] === $current['items'][0][0])) || ($crumb === $it[2]) ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon($it[1], 'icon icon-sm') ?></span><span><?= e($it[2]) ?></span><?php if (($it[4] ?? 0) > 0): ?><small><?= (int) $it[4] ?></small><?php endif; ?></a>
                                <?php endforeach; ?>
                            </nav>
                        <?php endforeach; ?>
                        <?php if (isset($current['action'])): ?><a class="btn btn-sm" href="<?= e(url($current['action'][0])) ?>" style="width:100%"><?= icon('plus', 'icon icon-sm') ?> <?= e($current['action'][1]) ?></a><?php endif; ?>
                    </div></div>
                </aside>
                <div style="min-width:0">
            <?php endif; ?>
            <?= $body ?>
            <?php if (!empty($current['sidebar'])): ?>
                </div>
            </div>
            <?php endif; ?>
        </main>
        <footer class="footer2"><span>&copy; <?= date('Y') ?> <?= e(company_name()) ?> · <?= e($tagline) ?></span><span>Ingelogd als <?= e($user?->Email) ?></span></footer>
    </div>
</div>
</body>
</html>
