<?php $extend('frontend', ['title' => $group !== '' ? $group : 'Diensten']); $i = 0; $one = count($kinds) === 1 ? array_key_first($kinds) : null; ?>
<?= $partial('frontend._hero', ['title' => $group !== '' ? $group : 'Onze diensten', 'intro' => $group !== '' ? 'Alle ' . mb_strtolower($group) . '-pakketten op een rij, met heldere prijzen exclusief btw.' : 'Hosting, domeinen, servers en ICT-diensten onder één dak. Alle prijzen zijn exclusief btw.', 'icon' => \App\Models\Product::GROUPS[$group] ?? 'server', 'crumbs' => [['/diensten', 'Diensten']]]) ?>
<?php if ($kinds === [] && $domains === []): ?><section class="sec"><div class="container"><div class="empty">Geen diensten gevonden.</div></div></section><?php endif; ?>
<?php if ($groups !== []): ?>
<div class="container"><nav class="svc-nav"><a<?= $group === '' ? ' class="active"' : '' ?> href="<?= e(url('/diensten')) ?>"><?= icon('box', 'icon icon-sm') ?> Alles</a><?php foreach ($groups as $g): ?><a<?= $group === $g ? ' class="active"' : '' ?> href="<?= e(url('/diensten?groep=' . urlencode($g))) ?>"><?= icon(\App\Models\Product::GROUPS[$g] ?? 'box', 'icon icon-sm') ?> <?= e($g) ?></a><?php endforeach; ?></nav></div>
<?php endif; ?>
<?php if (count($kinds) + ($domains !== [] ? 1 : 0) > 1): ?>
<div class="container"><nav class="svc-nav"><?php foreach (array_keys($kinds) as $kind): ?><a href="#<?= e(slugify($kind)) ?>"><?= icon(\App\Models\Product::soortIcon($kind), 'icon icon-sm') ?> <?= e($kind) ?></a><?php endforeach; ?><?php if ($domains !== []): ?><a href="#domeinextensies"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</a><?php endif; ?></nav></div>
<?php endif; ?>
<section class="sec sec-tight"><div class="container"><?= $partial('frontend._trust') ?></div></section>
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
<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Waarom <?= e(company_name()) ?></span><h2>Alles onder één dak, altijd dezelfde kwaliteit</h2><p>Eén partner voor hosting, domeinen, servers en ICT — met één aanspreekpunt en één factuur.</p></div></div>
    <?= $partial('frontend._benefits', ['items' => \App\Services\PageContent::benefits($one, $group !== '' ? $group : '')]) ?>
</div></section>
<section class="sec sec-dark"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.</p></div></div>
    <?= $partial('frontend._steps', ['items' => \App\Services\PageContent::steps($one, true)]) ?>
</div></section>
<section class="sec"><div class="container faq-split">
    <div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Vragen over onze diensten?</h2><p class="muted">Staat je vraag er niet bij? Onze specialisten helpen je graag persoonlijk verder.</p><a class="btn btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Stel je vraag</a></div>
    <?= $partial('frontend._faq', ['items' => \App\Services\PageContent::faq($one, $group !== '' ? $group : 'onze diensten', true)]) ?>
</div></section>
<?= $partial('frontend._cta', ['title' => 'Hulp nodig bij het kiezen?', 'text' => 'Onze specialisten adviseren je vrijblijvend over het juiste pakket en helpen bij de overstap.']) ?>
