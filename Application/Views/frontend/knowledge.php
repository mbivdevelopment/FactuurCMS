<?php $extend('frontend', ['title' => 'Kennisbank']); ?>
<?= $partial('frontend._hero', ['title' => 'Kennisbank', 'intro' => 'Handleidingen en antwoorden op veelgestelde vragen.', 'icon' => 'book', 'crumbs' => [[null, 'Kennisbank']], 'search' => true, 'searchAction' => '/kennisbank', 'searchName' => 'q', 'searchValue' => $q ?? '', 'searchPlaceholder' => 'Waar kunnen we je mee helpen?']) ?>
<section class="sec"><div class="container side-split">
    <aside class="side-card"><h3>Categorieën</h3><?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/kennisbank?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> artikel(en)</small></a><?php endforeach; ?>
        <div class="side-start"><strong>Kom je er niet uit?</strong><p>Maak een supportticket aan — ons team helpt je graag verder.</p><a class="btn" href="<?= e(url('/klant/tickets/nieuw')) ?>">Ticket aanmaken <?= icon('arrow-right', 'icon icon-sm') ?></a></div></aside>
    <div>
        <?php if ($articles === []): ?><div class="empty">Geen artikelen gevonden<?= $q !== '' ? ' voor "' . e($q) . '"' : '' ?>.</div><?php endif; ?>
        <?php foreach ($articles as $cat => $items): ?>
            <h2 class="kb-cat"><?= icon('book', 'icon icon-sm') ?> <?= e($cat) ?></h2>
            <div class="kb-list"><?php foreach ($items as $a): ?><a class="kb-item" href="<?= e(url('/kennisbank/' . $a['Slug'])) ?>"><span class="kb-ic"><?= icon('note', 'icon icon-sm') ?></span><span><strong><?= e($a['Titel']) ?></strong><small><?= e((string) $a['Samenvatting']) ?></small></span><?= icon('chevron-right', 'icon icon-sm') ?></a><?php endforeach; ?></div>
        <?php endforeach; ?>
    </div>
</div></section>
