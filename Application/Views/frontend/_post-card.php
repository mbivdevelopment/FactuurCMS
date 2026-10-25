<?php $img = media_url((string) ($p['Afbeelding'] ?? '')); ?>
<a class="pcard" href="<?= e(url($base . $p['Slug'])) ?>">
    <span class="pc-img<?= $img === '' ? ' is-empty' : '' ?>"><?php if ($img !== ''): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php else: ?><?= icon($icon ?? 'note') ?><?php endif; ?><span class="pc-tag"><?= e((string) $p['Categorie']) ?></span></span>
    <span class="pc-body">
        <span class="pc-meta"><?php if (!empty($p['PublicatieDatum'])): ?><?= icon('calendar', 'icon icon-sm') ?> <?= e(nl_date((string) $p['PublicatieDatum'])) ?><?php elseif (!empty($p['Klant'])): ?><?= icon('building', 'icon icon-sm') ?> <?= e((string) $p['Klant']) ?><?php endif; ?></span>
        <strong><?= e((string) $p['Titel']) ?></strong>
        <span class="pc-text"><?= e((string) $p['Samenvatting']) ?></span>
        <span class="pc-more"><?= e($more ?? 'Lees meer') ?> <?= icon('arrow-right', 'icon icon-sm') ?></span>
    </span>
</a>
