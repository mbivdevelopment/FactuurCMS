<?php $extend('frontend', ['title' => $group !== '' ? $group : 'Diensten']); $i = 0; ?>
<?= $partial('frontend._hero', ['title' => $group !== '' ? $group : 'Onze diensten', 'intro' => 'Alle pakketten op een rij. Alle prijzen zijn exclusief btw.', 'icon' => \App\Models\Product::GROUPS[$group] ?? 'server', 'crumbs' => [['/diensten', 'Diensten']]]) ?>
<?php if ($kinds === [] && $domains === []): ?><section class="sec"><div class="container"><div class="empty">Geen diensten gevonden.</div></div></section><?php endif; ?>
<?php if (count($kinds) + ($domains !== [] ? 1 : 0) > 1): ?>
<div class="container"><nav class="svc-nav"><?php foreach (array_keys($kinds) as $kind): ?><a href="#<?= e(slugify($kind)) ?>"><?= icon(\App\Models\Product::soortIcon($kind), 'icon icon-sm') ?> <?= e($kind) ?></a><?php endforeach; ?><?php if ($domains !== []): ?><a href="#domeinextensies"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</a><?php endif; ?></nav></div>
<?php endif; ?>
<?php foreach ($kinds as $kind => $list): ?>
<section class="sec svc-sec<?= $i++ % 2 ? ' sec-alt' : '' ?>" id="<?= e(slugify($kind)) ?>"><div class="container">
    <div class="sec-head"><div class="svc-head"><span class="pk-i"><?= icon(\App\Models\Product::soortIcon($kind)) ?></span><div><h2><?= e($kind) ?></h2><p><?= e(\App\Models\Product::soortText($kind) ?: 'Pakketten en diensten') ?> · <?= count($list) ?> pakket<?= count($list) === 1 ? '' : 'ten' ?></p></div></div>
        <?php if (isset($soortPages[$kind])): ?><a class="btn btn-ghost" href="<?= e(url('/oplossingen/' . $soortPages[$kind])) ?>">Meer over <?= e(mb_strtolower($kind)) ?> <?= icon('arrow-right', 'icon icon-sm') ?></a><?php endif; ?></div>
    <?= $partial('frontend._plans', ['products' => $list]) ?>
</div></section>
<?php endforeach; ?>
<?php if ($domains !== []): ?>
<section class="sec svc-sec<?= $i % 2 ? ' sec-alt' : '' ?>" id="domeinextensies"><div class="container">
    <div class="sec-head"><div class="svc-head"><span class="pk-i"><?= icon('globe') ?></span><div><h2>Domeinextensies</h2><p>Prijzen per jaar, exclusief btw · <?= count($domains) ?> extensies</p></div></div><a class="btn btn-ghost" href="<?= e(url('/domeinen')) ?>"><?= icon('search', 'icon icon-sm') ?> Domein zoeken</a></div>
    <?= $partial('frontend._extensions', ['domains' => $domains, 'max' => 500]) ?>
</div></section>
<?php endif; ?>
<section class="sec sec-usps"><div class="container"><?= $partial('frontend._usps') ?></div></section>
