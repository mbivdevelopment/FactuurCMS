<?php $extend('frontend', ['title' => $article['Titel']]); ?>
<?= $partial('frontend._hero', ['title' => $article['Titel'], 'intro' => $article['Samenvatting'], 'icon' => 'book', 'crumbs' => [['/kennisbank', 'Kennisbank'], ['/kennisbank?categorie=' . urlencode($article['Categorie']), $article['Categorie']]]]) ?>
<section class="sec"><div class="container read-split">
    <aside class="side-card"><h3>Categorieën</h3><?php foreach ($categories as $c): ?><a class="side-link<?= $article['Categorie'] === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/kennisbank?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> artikel(en)</small></a><?php endforeach; ?>
        <div class="side-start"><strong>Kom je er niet uit?</strong><p>Maak een supportticket aan — ons team helpt je graag verder.</p><a class="btn" href="<?= e(url('/klant/tickets/nieuw')) ?>">Ticket aanmaken <?= icon('arrow-right', 'icon icon-sm') ?></a></div></aside>
    <article class="read read-card"><div class="prose"><?= rich_text((string) $article['Inhoud']) ?></div><a class="btn btn-ghost" href="<?= e(url('/kennisbank')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug naar de kennisbank</a></article>
</div></section>
