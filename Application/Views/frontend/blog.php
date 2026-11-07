<?php $extend('frontend', ['title' => 'Blog']); ?>
<?= $partial('frontend._hero', ['title' => 'Blog', 'intro' => 'Nieuws, updates en handige tips van ons team.', 'icon' => 'note', 'crumbs' => [[null, 'Blog']]]) ?>
<section class="sec"><div class="container side-split">
    <aside class="side-card">
        <h3>Categorieën</h3>
        <?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/blog?categorie=' . urlencode($c['Categorie']))) ?>"><?= icon('note', 'icon icon-sm') ?><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?></small></a><?php endforeach; ?>
        <div class="side-start"><strong>Vragen over een onderwerp?</strong><p>Laat het ons weten — we denken graag mee of schrijven er een artikel over.</p><a class="btn" href="<?= e(url('/contact')) ?>">Neem contact op <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
    </aside>
    <div>
        <?php if ($posts === []): ?><div class="empty">Nog geen berichten<?= $category !== '' ? ' in deze categorie' : '' ?>.</div><?php endif; ?>
        <div class="grid grid-2"><?php foreach ($posts as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/blog/', 'icon' => 'note']) ?><?php endforeach; ?></div>
    </div>
</div></section>
