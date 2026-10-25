<?php $extend('frontend', ['title' => $group !== '' ? $group : 'Diensten']); ?>
<?= $partial('frontend._hero', ['title' => $group !== '' ? $group : 'Onze diensten', 'intro' => 'Alle prijzen zijn exclusief btw.', 'icon' => \App\Models\Product::GROUPS[$group] ?? 'server', 'crumbs' => [['/diensten', 'Diensten']]]) ?>
<section class="sec">
    <div class="container">
        <div class="filters">
            <a href="<?= e(url('/diensten')) ?>" class="<?= $group === '' ? 'active' : '' ?>">Alles</a>
            <?php foreach ($groups as $g): ?><a href="<?= e(url('/diensten?groep=' . urlencode($g))) ?>" class="<?= $group === $g ? 'active' : '' ?>"><?= e($g) ?></a><?php endforeach; ?>
        </div>
        <?php if ($products === []): ?><div class="empty">Geen diensten gevonden.</div><?php endif; ?>
        <div class="grid grid-3"><?php foreach ($products as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
    </div>
</section>
