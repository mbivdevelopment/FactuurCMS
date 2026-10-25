<?php $extend('frontend', ['title' => 'Portfolio']); ?>
<?= $partial('frontend._hero', ['title' => 'Portfolio', 'intro' => 'Websites, webshops, hosting en ICT-projecten die we met trots hebben gerealiseerd.', 'icon' => 'image', 'crumbs' => [[null, 'Portfolio']]]) ?>
<section class="sec"><div class="container">
    <?php if ($categories !== []): ?><div class="filters"><a href="<?= e(url('/portfolio')) ?>" class="<?= $category === '' ? 'active' : '' ?>">Alles</a><?php foreach ($categories as $c): ?><a href="<?= e(url('/portfolio?categorie=' . urlencode($c))) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a><?php endforeach; ?></div><?php endif; ?>
    <?php if ($projects === []): ?><div class="empty">Nog geen projecten.</div><?php endif; ?>
    <div class="grid grid-3"><?php foreach ($projects as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project']) ?><?php endforeach; ?></div>
</div></section>
