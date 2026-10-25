<?php $extend('frontend', ['title' => $page['Titel']]); $img = media_url((string) $page['Afbeelding']); $catLabel = ['cloud' => 'Cloudoplossingen', 'ict' => 'ICT & Development', 'overig' => 'Oplossingen'][$page['Categorie']] ?? 'Oplossingen'; $isDomain = $page['ProductGroep'] === 'Domeinen' || $page['Groep'] === 'Domeinen'; ?>
<?= $partial('frontend._hero', ['title' => $page['Titel'], 'intro' => $page['Samenvatting'], 'icon' => $page['Icoon'], 'crumbs' => [[null, $catLabel], [null, $page['Titel']]]]) ?>
<?php if ($isDomain): ?><div class="container phero-search"><?= $partial('frontend._domain-search', ['extensions' => $extensions]) ?></div><?php endif; ?>
<?php if ($products !== []): ?>
<section class="sec sec-plans"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies het pakket dat bij je past</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; na betaling is het geregeld.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section>
<?php endif; ?>
<?php if ($domains !== []): ?>
<section class="sec<?= $products !== [] ? ' sec-alt' : '' ?>"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Populaire extensies</h2><p>Prijzen per jaar, exclusief btw. Inclusief gratis DNS-beheer.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => $domains]) ?>
</div></section>
<?php endif; ?>
<section class="sec<?= $products !== [] || $domains !== [] ? ' sec-alt' : '' ?>">
    <div class="container page-split<?= $img === '' ? ' no-img' : '' ?>">
        <div><span class="eyebrow"><?= icon($page['Icoon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?></span><div class="prose"><?= rich_text((string) $page['Inhoud']) ?></div></div>
        <?php if ($img !== ''): ?><img class="page-img" src="<?= e($img) ?>" alt="<?= e($page['Titel']) ?>"><?php else: ?>
            <aside class="page-aside"><span class="pa-i"><?= icon($page['Icoon']) ?></span><strong><?= $products === [] && $domains === [] ? 'Een oplossing op maat' : 'Advies op maat?' ?></strong><p>Onze specialisten denken graag met je mee over <?= e(mb_strtolower($page['Titel'])) ?>.<?= $products === [] && $domains === [] ? ' Je ontvangt binnen één werkdag een voorstel.' : '' ?></p><a class="btn" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> <?= $products === [] && $domains === [] ? 'Vraag een offerte aan' : 'Neem contact op' ?></a></aside>
        <?php endif; ?>
    </div>
</section>
<section class="sec sec-usps"><div class="container"><?= $partial('frontend._usps') ?></div></section>
<?php if ($related !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('layout', 'icon icon-sm') ?> <?= e($catLabel) ?></span><h2>Bekijk ook</h2></div><a class="btn btn-ghost" href="<?= e(url('/diensten')) ?>">Alle diensten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
    <div class="tiles"><?php foreach ($related as $r): ?><a class="mega-tile tile-card" href="<?= e(url('/oplossingen/' . $r['Slug'])) ?>"><span class="mt-i"><?= icon($r['Icoon']) ?></span><span><strong><?= e($r['Titel']) ?></strong><small><?= e((string) $r['Samenvatting']) ?></small></span></a><?php endforeach; ?></div>
</div></section>
<?php endif; ?>
