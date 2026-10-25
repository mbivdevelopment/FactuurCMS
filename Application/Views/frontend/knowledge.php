<?php $extend('frontend', ['title' => 'Kennisbank']); ?>
<?= $partial('frontend._hero', ['title' => 'Kennisbank', 'intro' => 'Handleidingen en antwoorden op veelgestelde vragen.', 'icon' => 'book', 'crumbs' => [[null, 'Kennisbank']]]) ?>
<div class="container phero-search"><form class="kb-search big" action="<?= e(url('/kennisbank')) ?>" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Waar kunnen we je mee helpen?" aria-label="Zoeken"><button class="btn">Zoeken</button></form></div>
<section class="sec"><div class="container read-split">
    <div>
        <?php if ($articles === []): ?><div class="empty">Geen artikelen gevonden<?= $q !== '' ? ' voor "' . e($q) . '"' : '' ?>.</div><?php endif; ?>
        <?php foreach ($articles as $cat => $items): ?>
            <h2 class="kb-cat"><?= icon('book', 'icon icon-sm') ?> <?= e($cat) ?></h2>
            <div class="kb-list"><?php foreach ($items as $a): ?><a class="kb-item" href="<?= e(url('/kennisbank/' . $a['Slug'])) ?>"><span class="kb-ic"><?= icon('note', 'icon icon-sm') ?></span><span><strong><?= e($a['Titel']) ?></strong><small><?= e((string) $a['Samenvatting']) ?></small></span><?= icon('chevron-right', 'icon icon-sm') ?></a><?php endforeach; ?></div>
        <?php endforeach; ?>
    </div>
    <aside class="side-card"><h3>Categorieën</h3><a class="side-link<?= $category === '' ? ' active' : '' ?>" href="<?= e(url('/kennisbank')) ?>"><strong>Alle artikelen</strong></a><?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/kennisbank?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> artikel(en)</small></a><?php endforeach; ?>
        <div class="side-help"><?= icon('headset') ?><div><strong>Kom je er niet uit?</strong><small>Maak een supportticket aan.</small></div></div><a class="btn" href="<?= e(url('/klant/tickets/nieuw')) ?>">Ticket aanmaken</a></aside>
</div></section>
