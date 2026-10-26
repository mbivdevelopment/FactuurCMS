<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$user = auth_user();
$menu = ['cloud' => [], 'ict' => []];
foreach (\App\Core\Database::all("SELECT Titel, Slug, Icoon, Samenvatting, Categorie, Groep FROM Paginas WHERE Zichtbaar = 1 AND Categorie IN ('cloud', 'ict') ORDER BY Categorie, Groep, Volgorde, Id") as $mp) {
    $menu[$mp['Categorie']][$mp['Groep'] !== '' ? $mp['Groep'] : 'Overig'][] = $mp;
}
$activeCat = null;
if (str_starts_with($path, '/oplossingen/')) {
    foreach ($menu as $cat => $cols) { foreach ($cols as $items) { foreach ($items as $mp) { if ('/oplossingen/' . $mp['Slug'] === $path) { $activeCat = $cat; } } } }
}
$is = static fn (string $prefix): bool => $prefix === '/' ? $path === '/' : ($path === $prefix || str_starts_with($path, $prefix . '/'));
$tel = (string) setting('topbar.telefoon', (string) setting('bedrijf.telefoon', ''));
$mail = (string) setting('topbar.email', (string) setting('bedrijf.email', ''));
$webmail = (string) setting('links.webmail', '');
$paneel = (string) setting('links.paneel', '');
$colIcons = ['Domeinen' => 'globe', 'Hosting' => 'server', 'Servers' => 'cpu', 'Connectiviteit' => 'lock', 'Development' => 'code', 'Marketing' => 'trend'];
?>
<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head') ?><link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>"></head>
<body class="site">
<div class="stop">
    <div class="container stop-in">
        <div class="stop-contact">
            <?php if ($tel !== ''): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $tel)) ?>"><?= icon('phone', 'icon icon-sm') ?> <?= e($tel) ?></a><?php endif; ?>
            <?php if ($mail !== ''): ?><a href="mailto:<?= e($mail) ?>"><?= icon('mail', 'icon icon-sm') ?> <?= e($mail) ?></a><?php endif; ?>
        </div>
        <div class="stop-slogan"><?= icon('zap', 'icon icon-sm') ?> <?= e((string) setting('topbar.slogan', 'Vlot geregeld, ijzersterk verbonden.')) ?></div>
        <div class="stop-links">
            <?php if ($webmail !== ''): ?><a href="<?= e($webmail) ?>" target="_blank" rel="noopener"><?= icon('webmail', 'icon icon-sm') ?> Webmail</a><?php endif; ?>
            <?php if ($paneel !== ''): ?><a href="<?= e($paneel) ?>" target="_blank" rel="noopener"><?= icon('server', 'icon icon-sm') ?> Hostingpaneel</a><?php endif; ?>
        </div>
    </div>
</div>
<header class="shead" id="shead">
    <div class="container shead-in">
        <a class="site-logo" href="<?= e(url('/')) ?>"><?= $partial('partials.brand') ?></a>
        <nav class="mnav" aria-label="Hoofdmenu">
            <div class="mi"><a class="mi-link<?= $is('/') ? ' active' : '' ?>" href="<?= e(url('/')) ?>">Home</a></div>
            <div class="mi<?= $menu['cloud'] !== [] ? ' has-mega' : '' ?>">
                <a class="mi-link<?= $activeCat === 'cloud' || $is('/diensten') || $is('/domeinen') ? ' active' : '' ?>" href="<?= e(url($menu['cloud'] !== [] ? '/oplossingen/' . array_values($menu['cloud'])[0][0]['Slug'] : '/oplossingen/webhosting')) ?>">Cloudoplossing <?php if ($menu['cloud'] !== []): ?><button type="button" class="mi-caret" data-mega-toggle aria-label="Submenu"><?= icon('chevron-down', 'icon icon-sm') ?></button><?php endif; ?></a>
                <?php if ($menu['cloud'] !== []): ?><div class="mega">
                    <div class="container mega-in mega-cloud">
                        <div class="mega-aside">
                            <div class="mega-aside-t"><?= icon('globe') ?> Zoek je domeinnaam</div>
                            <p>Check direct of jouw domeinnaam nog vrij is en claim hem binnen een minuut.</p>
                            <a href="<?= e(url('/domeinen')) ?>" class="btn btn-sm">Domein zoeken <?= icon('arrow-right', 'icon icon-sm') ?></a>
                        </div>
                        <?php foreach ($menu['cloud'] as $group => $items): ?>
                            <div class="mega-col">
                                <div class="mega-h"><span><?= icon($colIcons[$group] ?? $items[0]['Icoon']) ?></span><?= e($group) ?></div>
                                <?php foreach ($items as $mp): ?>
                                    <a class="mega-link" href="<?= e(url('/oplossingen/' . $mp['Slug'])) ?>"><span class="ml-i"><?= icon($mp['Icoon']) ?></span><span class="ml-t"><strong><?= e($mp['Titel']) ?></strong><small title="<?= e((string) $mp['Samenvatting']) ?>"><?= e((string) $mp['Samenvatting']) ?></small></span></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><?php endif; ?>
            </div>
            <div class="mi<?= $menu['ict'] !== [] ? ' has-mega' : '' ?>">
                <a class="mi-link<?= $activeCat === 'ict' ? ' active' : '' ?>" href="<?= e(url($menu['ict'] !== [] ? '/oplossingen/' . array_values($menu['ict'])[0][0]['Slug'] : '/contact')) ?>">ICT &amp; Development <?php if ($menu['ict'] !== []): ?><button type="button" class="mi-caret" data-mega-toggle aria-label="Submenu"><?= icon('chevron-down', 'icon icon-sm') ?></button><?php endif; ?></a>
                <?php if ($menu['ict'] !== []): ?><div class="mega">
                    <div class="container mega-in mega-cloud">
                        <div class="mega-aside">
                            <div class="mega-aside-t"><?= icon('image') ?> Benieuwd naar ons werk?</div>
                            <p>Bekijk projecten die we voor andere ondernemers realiseerden.</p>
                            <a href="<?= e(url('/portfolio')) ?>" class="btn btn-sm">Naar portfolio <?= icon('arrow-right', 'icon icon-sm') ?></a>
                        </div>
                        <?php foreach ($menu['ict'] as $group => $items): ?>
                            <div class="mega-col">
                                <div class="mega-h"><span><?= icon($colIcons[$group] ?? $items[0]['Icoon']) ?></span><?= e($group) ?></div>
                                <?php foreach ($items as $mp): ?>
                                    <a class="mega-link" href="<?= e(url('/oplossingen/' . $mp['Slug'])) ?>"><span class="ml-i"><?= icon($mp['Icoon']) ?></span><span class="ml-t"><strong><?= e($mp['Titel']) ?></strong><small title="<?= e((string) $mp['Samenvatting']) ?>"><?= e((string) $mp['Samenvatting']) ?></small></span></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><?php endif; ?>
            </div>
            <div class="mi"><a class="mi-link<?= $is('/portfolio') ? ' active' : '' ?>" href="<?= e(url('/portfolio')) ?>">Portfolio</a></div>
            <div class="mi"><a class="mi-link<?= $is('/contact') ? ' active' : '' ?>" href="<?= e(url('/contact')) ?>">Contact</a></div>
        </nav>
        <div class="shead-actions">
            <?php if ($user): ?>
                <a class="btn" href="<?= e(url(\App\Core\Auth::homeFor($user))) ?>"><?= icon('user', 'icon icon-sm') ?> Mijn account</a>
            <?php else: ?>
                <a class="btn" href="<?= e(url('/login')) ?>"><?= icon('login', 'icon icon-sm') ?> Inloggen</a>
            <?php endif; ?>
            <button type="button" class="btn btn-ghost btn-icon mnav-btn" data-menu-toggle="#shead" aria-label="Menu"><?= icon('layout') ?></button>
        </div>
    </div>
</header>
<?php if (flash('success') || flash('error')): ?><div class="container" style="padding-top:16px"><?= $partial('partials.flash') ?></div><?php endif; ?>
<?= $yield('content') ?>
<footer class="sfoot">
    <div class="container">
        <div class="sf-top">
            <a class="sf-logo" href="<?= e(url('/')) ?>"><?= $partial('partials.brand', ['dark' => true]) ?></a>
            <p><?= e((string) setting('footer.tekst', (string) setting('website.intro', ''))) ?></p>
            <div class="sf-pills">
                <?php if ($tel !== ''): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $tel)) ?>"><?= icon('phone', 'icon icon-sm') ?> <?= e($tel) ?></a><?php endif; ?>
                <?php if ($mail !== ''): ?><a href="mailto:<?= e($mail) ?>"><?= icon('mail', 'icon icon-sm') ?> <?= e($mail) ?></a><?php endif; ?>
                <a href="<?= e(url('/klant/tickets/nieuw')) ?>"><?= icon('headset', 'icon icon-sm') ?> Support</a>
            </div>
            <div class="sf-social">
                <?php foreach (['linkedin', 'facebook', 'instagram'] as $net): $u = (string) setting('social.' . $net, ''); if ($u === '') { continue; } ?><a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($net)) ?>"><?= icon($net) ?></a><?php endforeach; ?>
            </div>
        </div>
        <div class="sf-cols">
            <div><h3>Cloudoplossingen</h3><?php foreach ($menu['cloud'] as $items): foreach ($items as $mp): ?><a href="<?= e(url('/oplossingen/' . $mp['Slug'])) ?>"><?= e($mp['Titel']) ?></a><?php endforeach; endforeach; ?></div>
            <div><h3>ICT &amp; Development</h3><?php foreach ($menu['ict'] as $items): foreach ($items as $mp): ?><a href="<?= e(url('/oplossingen/' . $mp['Slug'])) ?>"><?= e($mp['Titel']) ?></a><?php endforeach; endforeach; ?></div>
            <div><h3><?= e(company_name()) ?></h3><a href="<?= e(url('/over-ons')) ?>">Over ons</a><a href="<?= e(url('/portfolio')) ?>">Portfolio</a><a href="<?= e(url('/blog')) ?>">Blog</a><a href="<?= e(url('/kennisbank')) ?>">Kennisbank</a><a href="<?= e(url('/contact')) ?>">Contact</a></div>
            <div><h3>Klantenservice</h3><a href="<?= e(url('/login')) ?>">Inloggen klantportaal</a><?php if ($webmail !== ''): ?><a href="<?= e($webmail) ?>" target="_blank" rel="noopener">Webmail</a><?php endif; ?><?php if ($paneel !== ''): ?><a href="<?= e($paneel) ?>" target="_blank" rel="noopener">Hostingpaneel</a><?php endif; ?><a href="<?= e(url('/kennisbank')) ?>">Hulp &amp; handleidingen</a><a href="<?= e(url('/registreren')) ?>">Account aanmaken</a></div>
        </div>
        <div class="sf-bottom"><span>© <?= date('Y') ?> <?= e(company_name()) ?></span><span><?= e(implode(' · ', array_filter([trim(setting('bedrijf.adres', '') . ', ' . setting('bedrijf.plaats', ''), ', '), setting('bedrijf.kvk') ? 'KvK ' . setting('bedrijf.kvk') : '', setting('bedrijf.btw') ? 'Btw ' . setting('bedrijf.btw') : '']))) ?></span></div>
    </div>
</footer>
</body>
</html>
