<div class="dc">
    <div class="dc-map"><div class="dc-canvas">
        <img src="<?= e(asset('img/wereldkaart.svg')) ?>" alt="Wereldkaart met onze datacenters">
        <?php foreach ($datacenters as $dc): $x = ((float) $dc['Longitude'] + 180) / 360 * 100; $y = (85 - (float) $dc['Latitude']) / 145 * 100; ?>
            <span class="dc-pin<?= $dc['Status'] === 'binnenkort' ? ' soon' : '' ?>" style="left:<?= round($x, 2) ?>%;top:<?= round($y, 2) ?>%"><span class="dc-dot"></span><span class="dc-label"><strong><?= e($dc['Stad']) ?></strong><small><?= e($dc['Naam']) ?></small></span></span>
        <?php endforeach; ?>
    </div></div>
    <div class="dc-list">
        <?php foreach ($datacenters as $dc): ?>
            <div class="dc-item"><span class="dc-ic"><?= icon('server') ?></span><div><strong><?= e($dc['Stad']) ?>, <?= e($dc['Land']) ?></strong> <span class="badge <?= $dc['Status'] === 'actief' ? 'is-ok' : 'is-warn' ?>"><?= $dc['Status'] === 'actief' ? 'Actief' : 'Binnenkort' ?></span><div class="small muted"><?= e($dc['Naam']) ?> · <?= e((string) $dc['Omschrijving']) ?></div></div></div>
        <?php endforeach; ?>
    </div>
</div>
