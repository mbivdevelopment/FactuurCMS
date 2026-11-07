<?php $img = media_url((string) ($p['Afbeelding'] ?? '')); $summary = (string) ($p['Samenvatting'] ?? '') !== '' ? (string) $p['Samenvatting'] : mb_strimwidth(trim((string) preg_replace('/\s+/', ' ', preg_replace('/^## .*$/m', '', (string) ($p['Inhoud'] ?? '')))), 0, 150, '…'); ?>
<a class="pcard" href="<?= e(url($base . $p['Slug'])) ?>">
    <span class="pc-img<?= $img === '' ? ' is-empty' : '' ?>"><?php if ($img !== ''): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php else: ?><?= icon($icon ?? 'note') ?><?php endif; ?><span class="pc-tag"><?= e((string) $p['Categorie']) ?></span></span>
    <span class="pc-body">
        <span class="pc-meta"><?php if (!empty($p['PublicatieDatum'])): ?><?= icon('calendar', 'icon icon-sm') ?> <?= e(nl_date((string) $p['PublicatieDatum'])) ?><?php elseif (!empty($p['Klant'])): ?><?= icon('building', 'icon icon-sm') ?> <?= e((string) $p['Klant']) ?><?php endif; ?></span>
        <span class="pc-titlerow"><strong><?= e((string) $p['Titel']) ?></strong><i class="pc-arrow"><?= icon('arrow-right', 'icon icon-sm') ?></i></span>
        <span class="pc-text"><?= e($summary) ?></span>
        <?php if (($more ?? 'Lees meer') !== ''): ?><span class="pc-more<?= !empty($btn) ? ' pc-btn' : '' ?>"><?= e($more ?? 'Lees meer') ?> <?= icon('arrow-right', 'icon icon-sm') ?></span><?php endif; ?>
    </span>
</a>
