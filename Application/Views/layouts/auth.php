<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head', ['sheet' => 'beheer']) ?></head>
<body>
<div class="login-wrap">
    <div class="login-side">
        <?php $logo = branding_url(branding_url('logo_licht') !== '' ? 'logo_licht' : 'logo'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e(company_name()) ?>" class="login-logo"><?php endif; ?>
        <h1><?= e(company_name()) ?></h1>
        <p>Klantportaal en beheer voor je hosting, domeinen en facturen.</p>
        <ul>
            <li><?= icon('check') ?> Facturen en offertes inzien en downloaden</li>
            <li><?= icon('check') ?> Diensten en abonnementen beheren</li>
            <li><?= icon('check') ?> Support via tickets</li>
        </ul>
    </div>
    <div class="login-form">
        <div class="login-box card">
            <?= $partial('partials.flash') ?>
            <?= $yield('content') ?>
            <a href="<?= e(url('/')) ?>" class="small"><?= icon('globe', 'icon icon-sm') ?> Naar de website</a>
        </div>
    </div>
</div>
</body>
</html>
