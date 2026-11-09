<!doctype html>
<html lang="nl">
<head>
    <?= $partial('partials.head', ['title' => 'Onderhoud']) ?>
</head>
<body class="auth-page">
<main class="login-wrap">
    <section class="login-box card" style="text-align:center">
        <?php $logo = branding_url('logo'); ?>
        <?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e($company) ?>" class="login-logo"><?php endif; ?>
        <div class="ph-icon" style="margin:0 auto 18px"><?= icon('server') ?></div>
        <h1>Deze website is niet bereikbaar</h1>
        <p class="muted">We voeren momenteel onderhoud uit. Probeer het later opnieuw.</p>
    </section>
</main>
</body>
</html>
