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
