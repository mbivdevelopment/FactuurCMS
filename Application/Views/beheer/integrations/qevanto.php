<?php $extend('beheer', ['title' => 'Diensten importeren uit Qevanto']);
$statuses = ['active' => ['actief', 'Actief'], 'suspended' => ['open', 'Opgeschort'], 'ended' => ['vervallen', 'Beëindigd']]; ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1>Diensten importeren</h1><div class="sub"><?= count($rows) ?> abonnementen in het Qevanto hostingpaneel<?php foreach (array_count_values(array_column($rows, 'kind')) as $k => $n): ?> · <?= (int) $n ?> <?= e(strtolower(\App\Services\QevantoPaneel::kindLabel((string) $k))) ?><?php endforeach; ?></div></div>
    <div class="row"><input type="search" class="import-filter" placeholder="Filter…" data-filter><a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen#qevanto')) ?>"><?= icon('link', 'icon icon-sm') ?> Koppelingen</a></div></div>
<form class="card" method="post" action="<?= e(url('/beheer/koppelingen/qevanto/diensten')) ?>"><?= csrf_field() ?>
    <div class="card-body"><div class="form-grid"><?= field('eerste_factuur', 'Eerste factuurdatum voor actieve diensten', 'calendar', $firstInvoice, 'date') ?></div></div>
    <div class="table-wrap"><table class="table import-table" data-filter-table>
        <thead><tr><th><input type="checkbox" data-check-all aria-label="Alles selecteren"></th><th>Abonnement</th><th>Domeinen</th><th>Klant in paneel</th><th>Klant</th><th>Product</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): $st = $statuses[$r['status']] ?? ['concept', $r['status']]; ?>
            <tr>
                <td><?php if (!$r['imported']): ?><input type="checkbox" name="abonnement[]" value="<?= (int) $r['id'] ?>" <?= $r['klant'] !== '' && $r['status'] !== 'ended' ? 'checked' : '' ?>><?php endif; ?></td>
                <td><strong class="mono"><?= e($r['number']) ?></strong> <?= badge('concept', \App\Services\QevantoPaneel::kindLabel($r['kind'])) ?><div class="small muted"><?= e($r['package'] ?: '-') ?><?= $r['server'] !== '' ? ' · ' . e($r['server']) : '' ?></div></td>
                <td class="small"><?= $r['domains'] ? e(implode(', ', array_slice($r['domains'], 0, 3))) . (count($r['domains']) > 3 ? ' +' . (count($r['domains']) - 3) : '') : '<span class="muted">' . e($r['label'] ?: '-') . '</span>' ?></td>
                <td class="small"><?= e((string) (($r['user']['company'] ?? '') ?: ($r['user']['name'] ?? ''))) ?><div class="muted"><?= e((string) ($r['user']['email'] ?? '')) ?></div></td>
                <td><?php if ($r['imported']): ?><?= badge('actief', 'Al geïmporteerd') ?><?php else: ?>
                    <select class="cell-input" name="klant[<?= (int) $r['id'] ?>]"><option value="">- Kies klant -</option><?php if (($r['user']['email'] ?? '') !== ''): ?><option value="nieuw"<?= $r['klant'] === 'nieuw' ? ' selected' : '' ?>>+ Nieuwe klant aanmaken</option><?php endif; ?><?php foreach ($customers as $id => $label): ?><option value="<?= (int) $id ?>"<?= (string) $r['klant'] === (string) $id ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select>
                <?php endif; ?></td>
                <td><?php if (!$r['imported']): ?><select class="cell-input" name="product[<?= (int) $r['id'] ?>]"><option value="">- Geen product -</option><?php foreach ($products as $id => $label): ?><option value="<?= (int) $id ?>"<?= (int) $r['product'] === (int) $id ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><?php endif; ?></td>
                <td><?= badge($st[0], $st[1]) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($rows === []): ?><tr><td colspan="7" class="muted">Geen abonnementen gevonden.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-foot row between"><span class="small muted">De prijs en periode komen van het gekozen product. Zonder product komt de dienst op € 0,00 per maand.</span><button class="btn"><?= icon('download', 'icon icon-sm') ?> Geselecteerde importeren</button></div>
</form>
