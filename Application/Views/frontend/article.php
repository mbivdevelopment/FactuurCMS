<?php $extend('frontend', ['title' => $article['Titel']]); ?>
<?= $partial('frontend._hero', ['title' => $article['Titel'], 'intro' => $article['Samenvatting'], 'icon' => 'book', 'crumbs' => [['/kennisbank', 'Kennisbank'], ['/kennisbank?categorie=' . urlencode($article['Categorie']), $article['Categorie']]]]) ?>
<section class="sec"><div class="container read-split">
    <article class="read"><div class="prose"><?= rich_text((string) $article['Inhoud']) ?></div><a class="btn btn-ghost" href="<?= e(url('/kennisbank')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug naar de kennisbank</a></article>
    <aside class="side-card"><h3>Meer over <?= e($article['Categorie']) ?></h3><?php foreach ($related as $r): ?><a class="side-link" href="<?= e(url('/kennisbank/' . $r['Slug'])) ?>"><strong><?= e($r['Titel']) ?></strong></a><?php endforeach; ?><?php if ($related === []): ?><p class="muted small">Geen andere artikelen in deze categorie.</p><?php endif; ?>
        <div class="side-help"><?= icon('headset') ?><div><strong>Hulp nodig?</strong><small>Ons team helpt je graag.</small></div></div><a class="btn" href="<?= e(url('/klant/tickets/nieuw')) ?>">Ticket aanmaken</a></aside>
</div></section>
