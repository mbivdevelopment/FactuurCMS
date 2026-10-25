<?php $extend('frontend', ['title' => 'Blog']); ?>
<?= $partial('frontend._hero', ['title' => 'Blog', 'intro' => 'Nieuws, updates en handige tips van ons team.', 'icon' => 'note', 'crumbs' => [[null, 'Blog']]]) ?>
<section class="sec"><div class="container">
    <?php if ($categories !== []): ?><div class="filters"><a href="<?= e(url('/blog')) ?>" class="<?= $category === '' ? 'active' : '' ?>">Alles</a><?php foreach ($categories as $c): ?><a href="<?= e(url('/blog?categorie=' . urlencode($c))) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a><?php endforeach; ?></div><?php endif; ?>
    <?php if ($posts === []): ?><div class="empty">Nog geen berichten.</div><?php endif; ?>
    <div class="grid grid-3"><?php foreach ($posts as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/blog/', 'icon' => 'note']) ?><?php endforeach; ?></div>
</div></section>
