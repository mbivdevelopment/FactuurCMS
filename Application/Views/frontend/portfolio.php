<?php $extend('frontend', ['title' => 'Portfolio']); ?>
<?= $partial('frontend._hero', ['title' => 'Portfolio', 'intro' => 'Websites, webshops, hosting en ICT-projecten die we met trots hebben gerealiseerd.', 'icon' => 'image', 'crumbs' => [[null, 'Portfolio']]]) ?>
<section class="sec"><div class="container side-split">
    <aside class="side-card">
        <h3>Categorieën</h3>
        <a class="side-link<?= $category === '' ? ' active' : '' ?>" href="<?= e(url('/portfolio')) ?>"><strong>Alle projecten</strong></a>
        <?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/portfolio?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> project(en)</small></a><?php endforeach; ?>
        <div class="side-help"><?= icon('chat') ?><div><strong>Ook zo'n project?</strong><small>Vraag vrijblijvend een offerte.</small></div></div>
        <a class="btn" href="<?= e(url('/contact')) ?>">Neem contact op</a>
    </aside>
    <div>
        <?php if ($projects === []): ?><div class="empty">Nog geen projecten<?= $category !== '' ? ' in deze categorie' : '' ?>.</div><?php endif; ?>
        <div class="grid grid-3"><?php foreach ($projects as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project']) ?><?php endforeach; ?></div>
    </div>
</div></section>
<section class="sec"><div class="container">
    <div class="tiles">
        <a class="mega-tile tile-card" href="<?= e(url('/oplossingen/websites')) ?>"><span class="mt-i"><?= icon('layout') ?></span><span><strong>Website laten maken</strong><small>Een website of webshop op maat, zoals deze projecten.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/diensten')) ?>"><span class="mt-i"><?= icon('server') ?></span><span><strong>Hosting erbij</strong><small>Snelle hosting, domein en e-mail voor je nieuwe project.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/kennisbank')) ?>"><span class="mt-i"><?= icon('book') ?></span><span><strong>Kennisbank</strong><small>Handleidingen en antwoorden op veelgestelde vragen.</small></span></a>
    </div>
    <div class="cta" style="margin-top:24px"><div><h2>Ook een project starten?</h2><p>Vertel ons je idee; we denken mee en maken een vrijblijvende offerte.</p></div><a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Start je project <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
</div></section>
