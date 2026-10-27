<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head', ['sheet' => 'beheer']) ?></head>
<body>
<div class="login-wrap">
    <div class="login-box card">
        <?php $logo = branding_url(branding_url('logo') !== '' ? 'logo' : 'logo_licht'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e(company_name()) ?>" class="login-logo"><?php endif; ?>
        <?= $partial('partials.flash') ?>
        <?= $yield('content') ?>
        <a href="<?= e(url('/')) ?>" class="small"><?= icon('globe', 'icon icon-sm') ?> Naar de website</a>
    </div>
</div>
</body>
</html>
