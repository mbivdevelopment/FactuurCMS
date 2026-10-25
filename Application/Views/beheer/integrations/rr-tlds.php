<?php $extend('beheer', ['title' => 'Extensies importeren']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('tag') ?></div><div class="ph-text"><h1>Extensies importeren</h1><div class="sub"><?= count($rows) ?> extensies in je Realtime Register-prijslijst · voorstel verkoopprijs = inkoop + <?= (int) $margin ?>%</div></div>
    <div class="row"><input type="search" class="import-filter" placeholder="Filter op extensie…" data-filter><a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen#realtimeregister')) ?>"><?= icon('link', 'icon icon-sm') ?> Koppelingen</a></div></div>
<form class="card" method="post" action="<?= e(url('/beheer/koppelingen/realtimeregister/extensies')) ?>"><?= csrf_field() ?>
    <div class="table-wrap"><table class="table import-table" data-filter-table>
        <thead><tr><th><input type="checkbox" data-check-all aria-label="Alles selecteren"></th><th>Extensie</th><th class="right">Registratie</th><th class="right">Verlenging</th><th class="right">Verhuizing</th><th>Verkoopprijs per jaar (€)</th><th>Product</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><input type="checkbox" name="tld[]" value="<?= e($r['tld']) ?>"></td>
                <td><strong>.<?= e($r['tld']) ?></strong></td>
                <td class="right"><?= e(money($r['create'])) ?></td>
                <td class="right"><?= e(money($r['renew'])) ?></td>
                <td class="right"><?= e(money($r['transfer'])) ?></td>
                <td><input class="cell-input cell-num" name="prijs[<?= e($r['tld']) ?>]" value="<?= e(cents_input($r['sale'])) ?>" inputmode="decimal"></td>
                <td><?= $r['product'] ? badge('actief', 'Bestaat: ' . $r['product']['Naam']) : '<span class="muted small">Nieuw product</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($rows === []): ?><tr><td colspan="7" class="muted">Geen domeinprijzen gevonden.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-foot row between"><span class="small muted">Nieuwe extensies worden een zichtbaar product onder Domeinnamen. Bij bestaande producten worden de verkoop- en inkoopprijs bijgewerkt.</span><button class="btn"><?= icon('download', 'icon icon-sm') ?> Geselecteerde importeren</button></div>
</form>
