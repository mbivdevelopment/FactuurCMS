<?php $extend('frontend', ['title' => $page['Titel']]); $img = media_url((string) $page['Afbeelding']); $catLabel = ['cloud' => 'Cloudoplossingen', 'ict' => 'ICT & Development', 'overig' => 'Oplossingen'][$page['Categorie']] ?? 'Oplossingen'; $isDomain = $page['ProductGroep'] === 'Domeinen' || $page['Groep'] === 'Domeinen';
$orderable = $products !== [] || $domains !== []; $lower = mb_strtolower($page['Titel']);
$benefits = \App\Services\PageContent::benefits($soort, $page['Titel']); $steps = \App\Services\PageContent::steps($soort, $orderable); $faq = \App\Services\PageContent::faq($soort, $page['Titel'], $orderable); ?>
<?= $partial('frontend._hero', ['title' => $page['Titel'], 'intro' => $page['Samenvatting'], 'icon' => $page['Icoon'], 'trust' => true, 'crumbs' => [[null, $catLabel], [null, $page['Titel']]]]) ?>
<section class="sec sec-trust"><div class="container"><?= $partial('frontend._trust') ?></div></section>
<section class="sec">
    <div class="container intro-split">
        <div>
            <span class="eyebrow"><?= icon($page['Icoon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?></span>
            <h2 class="h-xl"><?= e($orderable ? $page['Titel'] . ' die gewoon werkt' : $page['Titel'] . ' zonder zorgen') ?></h2>
            <div class="prose"><?= rich_text((string) $page['Inhoud']) ?></div>
            <?php if ($img !== ''): ?><img class="page-img" src="<?= e($img) ?>" alt="<?= e($page['Titel']) ?>" style="margin-top:22px"><?php endif; ?>
        </div>
        <aside class="intro-aside">
            <div class="ia-card dark"><span class="ia-i"><?= icon($orderable ? 'cart' : 'chat') ?></span><div><strong><?= $orderable ? 'Direct online regelen' : 'Advies op maat' ?></strong><small><?= $orderable ? 'Kies hieronder een pakket en bestel in een paar stappen. Na betaling is het automatisch geregeld.' : 'Onze specialisten denken graag met je mee over ' . e($lower) . '. Je ontvangt binnen één werkdag een voorstel.' ?></small></div><a class="btn" href="<?= e(url($orderable ? '#pakketten' : '/contact')) ?>" style="margin-top:12px"><?= icon($orderable ? 'box' : 'send', 'icon icon-sm') ?> <?= $orderable ? 'Bekijk pakketten' : 'Vraag een offerte aan' ?></a></div>
            <div class="ia-card"><span class="ia-i"><?= icon('headset') ?></span><div><strong>Persoonlijke hulp</strong><small>Bel of mail met onze specialisten; we helpen je bij de keuze en de overstap.</small></div></div>
            <div class="ia-card"><span class="ia-i"><?= icon('shield') ?></span><div><strong>Nederlandse infrastructuur</strong><small>Je data staat in Nederlandse datacenters, met dagelijkse back-ups en 24/7 monitoring.</small></div></div>
            <div class="ia-card"><span class="ia-i"><?= icon('euro') ?></span><div><strong>Heldere prijzen</strong><small>Alle prijzen zijn exclusief btw; geen verborgen kosten of verrassingen achteraf.</small></div></div>
        </aside>
    </div>
</section>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom <?= e($lower) ?> bij <?= e(company_name()) ?></h2><p>Alles wat je nodig hebt zit erin — en wij houden het draaiend.</p></div></div>
    <?= $partial('frontend._benefits', ['items' => $benefits]) ?>
</div></section>
<?php if ($products !== []): ?>
<section class="sec sec-plans" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies het pakket dat bij je past</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; na betaling is het geregeld. Upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section>
<?php if (count($products) > 1): ?>
<section class="sec sec-tight"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('layout', 'icon icon-sm') ?> Vergelijken</span><h2>Alle pakketten naast elkaar</h2><p>Zo zie je in één oogopslag wat je per pakket krijgt.</p></div></div>
    <?= $partial('frontend._compare', ['products' => $products]) ?>
</div></section>
<?php endif; endif; ?>
<?php if ($domains !== []): ?>
<section class="sec<?= $products !== [] ? ' sec-alt' : '' ?>" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Populaire extensies</h2><p>Prijzen per jaar, exclusief btw. Inclusief gratis DNS-beheer en automatische verlenging.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => $domains]) ?>
</div></section>
<?php endif; ?>
<section class="sec sec-dark"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2><?= $orderable ? 'In vier stappen geregeld' : 'Zo pakken we het aan' ?></h2><p><?= $orderable ? 'Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.' : 'Van kennismaking tot oplevering: helder, persoonlijk en met een vaste prijs.' ?></p></div></div>
    <?= $partial('frontend._steps', ['items' => $steps]) ?>
</div></section>
<section class="sec"><div class="container faq-split">
    <div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Vragen over <?= e($lower) ?>?</h2><p class="muted">Staat je vraag er niet bij? Onze specialisten helpen je graag persoonlijk verder.</p><a class="btn btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Stel je vraag</a></div>
    <?= $partial('frontend._faq', ['items' => $faq]) ?>
</div></section>
<?php if ($related !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('layout', 'icon icon-sm') ?> <?= e($catLabel) ?></span><h2>Bekijk ook</h2><p>Andere oplossingen die hier goed bij passen.</p></div></div>
    <div class="tiles"><?php foreach ($related as $r): ?><a class="mega-tile tile-card" href="<?= e(url('/oplossingen/' . $r['Slug'])) ?>"><span class="mt-i"><?= icon($r['Icoon']) ?></span><span><strong><?= e($r['Titel']) ?></strong><small><?= e((string) $r['Samenvatting']) ?></small></span></a><?php endforeach; ?></div>
</div></section>
<?php endif; ?>
<?= $partial('frontend._cta', ['title' => $orderable ? 'Twijfel je welk pakket past?' : 'Benieuwd wat ' . $lower . ' voor jou kan betekenen?', 'text' => $orderable ? 'Onze specialisten adviseren je vrijblijvend over het juiste pakket en helpen bij de overstap.' : 'Plan een vrijblijvende kennismaking; je ontvangt binnen één werkdag een helder voorstel.']) ?>
