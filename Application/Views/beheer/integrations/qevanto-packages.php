<?php $extend('beheer', ['title' => 'Pakketten importeren uit Qevanto']);
$limitLabels = ['diskMb' => ['Opslag', 'MB'], 'websites' => ['Websites', ''], 'mailboxes' => ['Mailboxen', ''], 'databases' => ['Databases', ''], 'users' => ['Gebruikers', ''], 'devices' => ['Apparaten', ''], 'workplaces' => ['Werkplekken', '']]; ?>
<div class="page-head"><div class="ph-icon"><?= icon('box') ?></div><div class="ph-text"><h1>Pakketten importeren</h1><div class="sub"><?= count($rows) ?> pakketten in het Qevanto hostingpaneel<?php foreach (array_count_values(array_column($rows, 'kind')) as $k => $n): ?> · <?= (int) $n ?> <?= e(strtolower(\App\Services\QevantoPaneel::kindLabel((string) $k))) ?><?php endforeach; ?></div></div>
    <div class="row"><input type="search" class="import-filter" placeholder="Filter…" data-filter><a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen#qevanto')) ?>"><?= icon('link', 'icon icon-sm') ?> Koppelingen</a></div></div>
<form class="card" method="post" action="<?= e(url('/beheer/koppelingen/qevanto/pakketten')) ?>"><?= csrf_field() ?>
    <div class="table-wrap"><table class="table import-table" data-filter-table>
        <thead><tr><th><input type="checkbox" data-check-all aria-label="Alles selecteren"></th><th>Pakket</th><th>Soort</th><th>Kenmerken (uit het paneel)</th><th>Verkoopprijs (€)</th><th>Periode</th><th>Product</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): $kind = \App\Services\QevantoPaneel::KINDS[$r['kind']] ?? null;
            $limits = \App\Services\QevantoPaneel::features($r['limits']); ?>
            <tr>
                <td><input type="checkbox" name="pakket[]" value="<?= (int) $r['id'] ?>" <?= $r['product'] === null ? 'checked' : '' ?>></td>
                <td><strong><?= e($r['name']) ?></strong><?php if (!$r['active']): ?><div class="small muted">Niet actief in het paneel</div><?php endif; ?></td>
                <td><?= badge('concept', \App\Services\QevantoPaneel::kindLabel($r['kind'])) ?><div class="small muted"><?= e($kind ? \App\Models\Product::soortLabel($kind[2]) : 'Overig') ?></div></td>
                <td class="small muted"><?= e($limits ? implode(' · ', array_slice($limits, 0, 6)) : '-') ?></td>
                <td><input class="cell-input cell-num" name="prijs[<?= (int) $r['id'] ?>]" value="<?= e(cents_input((int) ($r['product']['Prijs'] ?? 0))) ?>" inputmode="decimal"></td>
                <td><select class="cell-input" name="periode[<?= (int) $r['id'] ?>]" style="min-width:110px"><?php foreach (['maand' => 'Per maand', 'kwartaal' => 'Per kwartaal', 'jaar' => 'Per jaar'] as $k => $v): ?><option value="<?= e($k) ?>"<?= ($r['product']['Periode'] ?? 'maand') === $k ? ' selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></td>
                <td><?= $r['product'] ? badge('actief', ($r['linked'] ? 'Gekoppeld: ' : 'Bestaat: ') . $r['product']['Naam']) : '<span class="muted small">Nieuw product</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($rows === []): ?><tr><td colspan="7" class="muted">Geen pakketten gevonden.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-foot row between"><span class="small muted">Nieuwe pakketten worden een product (niet zichtbaar op de website) onder de juiste dienstsoort. Bij een bestaand product worden prijs en periode bijgewerkt en wordt het aan het pakket gekoppeld.</span><button class="btn"><?= icon('download', 'icon icon-sm') ?> Geselecteerde importeren</button></div>
</form>
