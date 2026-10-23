<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(base_path_url())) ?: '/';
$user = auth_user();
$groups = array_column(\App\Core\Database::all('SELECT DISTINCT Groep FROM Producten WHERE Zichtbaar = 1 ORDER BY Groep'), 'Groep');
?>
<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head') ?></head>
<body class="site">
<header class="site-header">
    <div class="container">
        <a class="site-logo" href="<?= e(url('/')) ?>"><?= $partial('partials.brand') ?></a>
        <nav class="site-nav" id="site-nav">
            <a href="<?= e(url('/')) ?>" class="<?= $path === '/' ? 'active' : '' ?>">Home</a>
            <?php foreach ($groups as $g): ?>
                <a href="<?= e(url('/diensten?groep=' . urlencode($g))) ?>" class="<?= ($_GET['groep'] ?? '') === $g ? 'active' : '' ?>"><?= e($g) ?></a>
            <?php endforeach; ?>
            <a href="<?= e(url('/contact')) ?>" class="<?= $path === '/contact' ? 'active' : '' ?>">Contact</a>
            <?php if ($user): ?>
                <a class="btn" href="<?= e(url(\App\Core\Auth::homeFor($user))) ?>"><?= icon('user', 'icon icon-sm') ?> Mijn account</a>
            <?php else: ?>
                <a class="btn btn-ghost" href="<?= e(url('/login')) ?>"><?= icon('login', 'icon icon-sm') ?> Inloggen</a>
            <?php endif; ?>
        </nav>
        <button type="button" class="btn btn-ghost btn-icon menu-btn" style="display:none" data-menu-toggle="#site-nav"><?= icon('layout') ?></button>
    </div>
</header>
<?php if (flash('success') || flash('error')): ?><div class="container" style="padding-top:16px"><?= $partial('partials.flash') ?></div><?php endif; ?>
<?= $yield('content') ?>
<footer class="site-footer">
    <div class="container">
        <div class="grid grid-4">
            <div><h3><?= e(company_name()) ?></h3><p><?= e((string) setting('website.intro', '')) ?></p></div>
            <div><h3>Diensten</h3><?php foreach ($groups as $g): ?><a href="<?= e(url('/diensten?groep=' . urlencode($g))) ?>"><?= e($g) ?></a><?php endforeach; ?></div>
            <div><h3>Account</h3><a href="<?= e(url('/login')) ?>">Inloggen</a><a href="<?= e(url('/registreren')) ?>">Account aanmaken</a><a href="<?= e(url('/klant/tickets')) ?>">Support</a></div>
            <div><h3>Contact</h3>
                <?php foreach (array_filter([setting('bedrijf.adres'), trim(setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', ''))]) as $l): ?><div><?= e($l) ?></div><?php endforeach; ?>
                <?php if (setting('bedrijf.email')): ?><a href="mailto:<?= e(setting('bedrijf.email')) ?>"><?= e(setting('bedrijf.email')) ?></a><?php endif; ?>
                <?php if (setting('bedrijf.telefoon')): ?><a href="tel:<?= e(setting('bedrijf.telefoon')) ?>"><?= e(setting('bedrijf.telefoon')) ?></a><?php endif; ?>
            </div>
        </div>
        <div class="bottom"><span>© <?= date('Y') ?> <?= e(company_name()) ?></span><span><?= e(implode(' · ', array_filter([setting('bedrijf.kvk') ? 'KvK ' . setting('bedrijf.kvk') : '', setting('bedrijf.btw') ? 'Btw ' . setting('bedrijf.btw') : '']))) ?></span></div>
    </div>
</footer>
</body>
</html>
