<?php $extend('frontend', ['title' => $page['Titel']]); $img = media_url((string) $page['Afbeelding']); $catLabel = ['cloud' => 'Cloudoplossingen', 'ict' => 'ICT & Development', 'overig' => 'Oplossingen'][$page['Categorie']] ?? 'Oplossingen'; ?>
<?= $partial('frontend._hero', ['title' => $page['Titel'], 'intro' => $page['Samenvatting'], 'icon' => $page['Icoon'], 'crumbs' => [[null, $catLabel], [null, $page['Titel']]]]) ?>
<?php if ($page['ProductGroep'] === 'Domeinen' || $page['Groep'] === 'Domeinen'): ?><div class="container phero-search"><?= $partial('frontend._domain-search', ['extensions' => $extensions]) ?></div><?php endif; ?>
<section class="sec">
    <div class="container page-split<?= $img === '' ? ' no-img' : '' ?>">
        <div class="prose"><?= rich_text((string) $page['Inhoud']) ?></div>
        <?php if ($img !== ''): ?><img class="page-img" src="<?= e($img) ?>" alt="<?= e($page['Titel']) ?>"><?php else: ?>
            <aside class="page-aside"><span class="pa-i"><?= icon($page['Icoon']) ?></span><strong>Advies op maat?</strong><p>Onze specialisten denken graag met je mee over <?= e(mb_strtolower($page['Titel'])) ?>.</p><a class="btn" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Neem contact op</a></aside>
        <?php endif; ?>
    </div>
</section>
<?php if ($products !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies je pakket</h2><p>Alle prijzen zijn exclusief btw.</p></div></div>
    <div class="grid grid-3"><?php foreach ($products as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
</div></section>
<?php endif; ?>
<?php if ($related !== []): ?>
<section class="sec"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('layout', 'icon icon-sm') ?> <?= e($catLabel) ?></span><h2>Bekijk ook</h2></div></div>
    <div class="tiles"><?php foreach ($related as $r): ?><a class="mega-tile tile-card" href="<?= e(url('/oplossingen/' . $r['Slug'])) ?>"><span class="mt-i"><?= icon($r['Icoon']) ?></span><span><strong><?= e($r['Titel']) ?></strong><small><?= e((string) $r['Samenvatting']) ?></small></span></a><?php endforeach; ?></div>
</div></section>
<?php endif; ?>
