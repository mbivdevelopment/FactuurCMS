<?php $extend('beheer', ['title' => 'Domeinen importeren']);
$open = count(array_filter($rows, static fn (array $r): bool => !$r['imported'])); ?>
<div class="page-head"><div class="ph-icon"><?= icon('globe') ?></div><div class="ph-text"><h1>Domeinen importeren</h1><div class="sub"><?= count($rows) ?> domeinen bij Realtime Register · <?= $open ?> nog niet als dienst</div></div>
    <div class="row"><input type="search" class="import-filter" placeholder="Filter op domein…" data-filter><a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen#realtimeregister')) ?>"><?= icon('link', 'icon icon-sm') ?> Koppelingen</a></div></div>
<form class="card" method="post" action="<?= e(url('/beheer/koppelingen/realtimeregister/domeinen')) ?>"><?= csrf_field() ?>
    <div class="table-wrap"><table class="table import-table" data-filter-table>
        <thead><tr><th><input type="checkbox" data-check-all aria-label="Alles selecteren"></th><th>Domein</th><th>Verloopt</th><th>Product</th><th>Klant</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?php if (!$r['imported']): ?><input type="checkbox" name="domein[]" value="<?= e($r['domain']) ?>" <?= $r['klant'] ? 'checked' : '' ?>><?php endif; ?></td>
                <td><strong><?= e($r['domain']) ?></strong><?php if ($r['autoRenew']): ?><div class="small muted">Automatisch verlengen</div><?php endif; ?></td>
                <td><?= $r['expiry'] ? e(date('d-m-Y', (int) strtotime($r['expiry']))) : '-' ?></td>
                <td><?= $r['product'] ? e($r['product']['Naam']) . '<div class="small muted">' . e(money((int) $r['product']['Prijs'])) . ' per jaar</div>' : '<span class="muted">Geen product voor deze extensie</span>' ?></td>
                <td><?php if ($r['imported']): ?><?= badge('actief', 'Al als dienst aanwezig') ?><?php else: ?>
                    <select class="cell-input" name="klant[<?= e($r['domain']) ?>]"><option value="">- Kies klant -</option><?php foreach ($customers as $id => $label): ?><option value="<?= (int) $id ?>"<?= (int) $r['klant'] === (int) $id ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select>
                <?php endif; ?></td>
                <td class="small muted"><?= e($r['status']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($rows === []): ?><tr><td colspan="6" class="muted">Geen domeinen gevonden.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-foot row between"><span class="small muted">Een domein wordt een actieve jaardienst bij de gekozen klant. De volgende factuur valt op de verloopdatum.</span><button class="btn"><?= icon('download', 'icon icon-sm') ?> Geselecteerde importeren</button></div>
</form>
