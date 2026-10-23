<!doctype html>
<html lang="nl">
<head><?= $partial('partials.head') ?></head>
<body>
<div class="auth">
    <div class="auth-box">
        <a class="auth-brand" href="<?= e(url('/')) ?>"><?php $logo = branding_url('logo'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e(company_name()) ?>"><?php else: ?><?= e(company_name()) ?><?php endif; ?></a>
        <div class="card"><div class="card-body">
            <?= $partial('partials.flash') ?>
            <?= $yield('content') ?>
        </div></div>
    </div>
</div>
</body>
</html>
