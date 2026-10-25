<?php $extend('beheer', ['title' => 'BTW-overzicht']);
$euro = static fn (int $c): string => money($c);
$whole = static fn (int $c): string => '€ ' . number_format(intdiv($c, 100), 0, ',', '.');
$rows = [
    ['1', 'Prestaties binnenland', null],
    ['1a', 'Leveringen/diensten belast met hoog tarief (21%)', $sales['hoog']],
    ['1b', 'Leveringen/diensten belast met laag tarief (9%)', $sales['laag']],
    ['1c', 'Leveringen/diensten belast met overige tarieven, behalve 0%', $sales['overig']],
    ['1d', 'Privégebruik', [0, 0]],
    ['1e', 'Leveringen/diensten belast met 0% of niet bij u belast', [$sales['nul'][0], null]],
    ['2', 'Verleggingsregelingen binnenland', null],
    ['2a', 'Leveringen/diensten waarbij de omzetbelasting naar u is verlegd', [0, 0]],
    ['3', 'Prestaties naar of in het buitenland', null],
    ['3a', 'Leveringen naar landen buiten de EU (uitvoer)', [0, null]],
    ['3b', 'Leveringen naar/diensten in landen binnen de EU', [0, null]],
    ['3c', 'Installatie/afstandsverkopen binnen de EU', [0, null]],
    ['4', 'Prestaties vanuit het buitenland aan u verricht', null],
    ['4a', 'Leveringen/diensten uit landen buiten de EU', [0, 0]],
    ['4b', 'Leveringen/diensten uit landen binnen de EU', [0, 0]],
];
$total = $owed - $input; ?>
<div class="page-head"><div class="ph-icon"><?= icon('euro') ?></div><div class="ph-text"><h1>BTW-overzicht</h1><div class="sub">Kwartaal <?= (int) $quarter ?> <?= (int) $year ?> · <?= e(date('d-m-Y', strtotime($from))) ?> t/m <?= e(date('d-m-Y', strtotime($to))) ?> · volgens de Nederlandse btw-aangifte</div></div>
    <form method="get" class="btw-period"><select name="kwartaal" aria-label="Kwartaal"><?php for ($q = 1; $q <= 4; $q++): ?><option value="<?= $q ?>"<?= $q === $quarter ? ' selected' : '' ?>>Kwartaal <?= $q ?></option><?php endfor; ?></select><select name="jaar" aria-label="Jaar"><?php for ($y = (int) date('Y') + 1; $y >= (int) date('Y') - 6; $y--): ?><option value="<?= $y ?>"<?= $y === $year ? ' selected' : '' ?>><?= $y ?></option><?php endfor; ?></select><button class="btn btn-sm" type="submit">Tonen</button></form>
    <button class="btn btn-ghost" type="button" onclick="window.print()"><?= icon('file', 'icon icon-sm') ?> Afdrukken</button></div>
<div class="grid grid-3" style="margin-bottom:20px">
    <div class="card stat"><div class="card-body"><div class="stat-label">Verschuldigde btw (5a)</div><div class="stat-value"><?= e($euro($owed)) ?></div><div class="small muted"><?= (int) $invoices ?> verkoopfactu<?= $invoices === 1 ? 'ur' : 'ren' ?></div></div></div>
    <div class="card stat"><div class="card-body"><div class="stat-label">Voorbelasting (5b)</div><div class="stat-value"><?= e($euro($input)) ?></div><div class="small muted"><?= (int) $purchase['Aantal'] ?> inkoopfactu<?= (int) $purchase['Aantal'] === 1 ? 'ur' : 'ren' ?></div></div></div>
    <div class="card stat"><div class="card-body"><div class="stat-label"><?= $total >= 0 ? "Te betalen" : "Terug te vragen" ?> (5g)</div><div class="stat-value" style="color:<?= $total >= 0 ? 'var(--accent)' : 'var(--ok, #16a34a)' ?>"><?= e($euro(abs($total))) ?></div><div class="small muted">Aangifte afgerond: <?= e($whole(abs($total))) ?></div></div></div>
</div>
<div class="card"><div class="table-wrap"><table class="table btw-table"><thead><tr><th style="width:70px">Rubriek</th><th>Omschrijving</th><th class="num">Omzet</th><th class="num">Omzetbelasting</th></tr></thead><tbody>
<?php foreach ($rows as [$code, $label, $v]): ?>
    <?php if ($v === null): ?><tr class="btw-group"><td><strong><?= e($code) ?></strong></td><td colspan="3"><strong><?= e($label) ?></strong></td></tr>
    <?php else: ?><tr><td><?= e($code) ?></td><td><?= e($label) ?></td><td class="num"><?= e($euro((int) $v[0])) ?></td><td class="num"><?= $v[1] === null ? '' : e($euro((int) $v[1])) ?></td></tr><?php endif; ?>
<?php endforeach; ?>
<tr class="btw-group"><td><strong>5</strong></td><td colspan="3"><strong>Voorbelasting en totaal</strong></td></tr>
<tr><td>5a</td><td>Verschuldigde omzetbelasting (rubrieken 1a t/m 4b)</td><td></td><td class="num"><?= e($euro($owed)) ?></td></tr>
<tr><td>5b</td><td>Voorbelasting (uit inkoopfacturen)</td><td></td><td class="num"><?= e($euro($input)) ?></td></tr>
<tr><td>5c</td><td>Subtotaal (5a min 5b)</td><td></td><td class="num"><?= e($euro($total)) ?></td></tr>
<tr class="btw-total"><td>5g</td><td>Totaal <?= $total >= 0 ? 'te betalen' : 'terug te vragen' ?></td><td></td><td class="num"><strong><?= e($euro(abs($total))) ?></strong></td></tr>
</tbody></table></div>
<div class="card-body small muted" style="border-top:1px solid var(--line)">Berekend uit verkoopfacturen (behalve concepten, inclusief creditfacturen) en inkoopfacturen met een factuurdatum in dit kwartaal. Rubrieken 1d, 2a, 3 en 4 worden niet automatisch gevuld; controleer ze zelf. In de aangifte rondt de Belastingdienst af op hele euro's. Dit overzicht is een hulpmiddel en vervangt geen advies.</div></div>
