<?php
$extend('beheer', ['title' => $project['Naam']]);
$pid = (int) $project['Id'];
$base = '/beheer/projecten/' . $pid;
$def = \App\Services\Content::type('projecten')['fields'];
$hours = static fn (float $v): string => rtrim(rtrim(number_format($v, 2, ',', '.'), '0'), ',');
$rate = (int) $project['Uurtarief'];
$budget = (int) $project['Budget'];
$hourly = $project['Facturatie'] === 'uurloon';
$count = count($tasks);
$used = $budget > 0 ? (int) round($totals['workedValue'] / $budget * 100) : null;
$over = $budget > 0 && $totals['workedValue'] > $budget;
?>
<div class="page-head"><div class="ph-icon"><?= icon('layout') ?></div><div class="ph-text"><h1><?= e($project['Naam']) ?></h1>
    <div class="sub"><?php if ($customer): ?><a href="<?= e(url('/beheer/klanten/' . (int) $project['KlantId'])) ?>"><?= e($customer) ?></a> · <?php endif; ?><?= e($def['Status'][4][$project['Status']] ?? $project['Status']) ?> · <?= e($def['Facturatie'][4][$project['Facturatie']] ?? '') ?><?= $rate > 0 ? ' · ' . e(money($rate)) . ' per uur' : '' ?><?php if ($project['EindDatum']): ?> · oplevering <?= e(nl_date((string) $project['EindDatum'])) ?><?php endif; ?></div></div>
    <div class="row"><a class="btn btn-ghost" href="<?= e(url('/beheer/projecten')) ?>">Terug</a><a class="btn" href="<?= e(url($base)) ?>"><?= icon('edit', 'icon icon-sm') ?> Project bewerken</a></div></div>

<div class="grid grid-4" style="margin-bottom:16px">
    <div class="card stat"><div class="stat-icon"><?= icon('check') ?></div><div style="flex:1"><div class="stat-value"><?= $totals['done'] ?> / <?= $count ?></div><div class="stat-label">Taken klaar</div><div class="progress"><span style="width:<?= $count ? (int) round($totals['done'] / $count * 100) : 0 ?>%"></span></div></div></div>
    <div class="card stat"><div class="stat-icon"><?= icon('clock') ?></div><div style="flex:1"><div class="stat-value"><?= e($hours($totals['worked'])) ?> / <?= e($hours($totals['estimated'])) ?> uur</div><div class="stat-label">Gewerkt / geschat</div><div class="progress<?= $totals['worked'] > $totals['estimated'] && $totals['estimated'] > 0 ? ' is-bad' : '' ?>"><span style="width:<?= $totals['estimated'] > 0 ? min(100, (int) round($totals['worked'] / $totals['estimated'] * 100)) : 0 ?>%"></span></div></div></div>
    <div class="card stat"><div class="stat-icon"><?= icon('euro') ?></div><div><div class="stat-value"><?= $rate > 0 ? e(money($totals['workedValue'])) : '-' ?></div><div class="stat-label"><?= $hourly ? 'Te factureren (gewerkte uren × tarief)' : 'Waarde gewerkte uren' ?><?= $rate > 0 && $totals['estimatedValue'] > 0 ? ' · geschat ' . e(money($totals['estimatedValue'])) : '' ?></div></div></div>
    <div class="card stat<?= $over ? ' is-bad' : '' ?>"><div class="stat-icon"><?= icon('wallet') ?></div><div style="flex:1"><?php if ($budget > 0): ?><div class="stat-value"><?= e(money($budget - $totals['workedValue'])) ?></div><div class="stat-label"><?= $over ? 'Over budget' : 'Budget over' ?> van <?= e(money($budget)) ?><?= $rate > 0 && !$over ? ' · nog ' . e($hours(($budget - $totals['workedValue']) / $rate)) . ' uur' : '' ?></div><div class="progress<?= $over ? ' is-bad' : '' ?>"><span style="width:<?= min(100, (int) $used) ?>%"></span></div><?php else: ?><div class="stat-value">-</div><div class="stat-label">Geen budget ingesteld</div><?php endif; ?></div></div>
</div>
<?php if ($rate === 0): ?><div class="alert alert-info" style="margin-bottom:16px">Stel bij <a href="<?= e(url($base)) ?>">Project bewerken</a> een uurtarief in, dan rekent het overzicht de waarde van de uren en het resterende budget uit.</div><?php endif; ?>

<form class="card" method="post" action="<?= e(url($base . '/taken')) ?>" style="margin-bottom:16px"><?= csrf_field() ?>
    <div class="card-head"><h2>Taak toevoegen</h2></div>
    <div class="card-body form-grid">
        <?= field('Titel', 'Taak', 'tag', '', 'text', [], 'required') ?>
        <?= field('MedewerkerId', 'Medewerker', 'user', '', 'select', ['' => '- Niemand -'] + $staff) ?>
        <?= field('Deadline', 'Deadline', 'calendar', '', 'date') ?>
        <?= field('GeschatteUren', 'Geschatte uren', 'clock', '', 'text', [], 'inputmode="decimal" placeholder="bv. 2,5"') ?>
        <?= field('Status', 'Status', 'flag', 'open', 'select', \App\Controllers\Beheer\ProjectController::TASK_STATUSES) ?>
        <?= field('GewerkteUren', 'Al gewerkte uren', 'clock', '', 'text', [], 'inputmode="decimal"') ?>
        <?= field('Omschrijving', 'Omschrijving', 'note', '', 'textarea', [], 'rows="2"') ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('plus', 'icon icon-sm') ?> Taak toevoegen</button></div>
</form>

<div class="card"><div class="card-head"><h2>Taken (<?= $count ?>)</h2></div>
<?php if ($tasks === []): ?><div class="empty">Nog geen taken. Voeg hierboven de eerste taak toe.</div><?php else: ?>
<div class="table-wrap"><table class="table task-table"><thead><tr><th>Taak</th><th>Medewerker</th><th>Deadline</th><th>Geschat</th><th>Gewerkt</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($tasks as $t): $f = 'taak-' . (int) $t['Id']; $late = $t['Status'] !== 'klaar' && $t['Deadline'] && $t['Deadline'] < date('Y-m-d'); ?>
<tr class="<?= $t['Status'] === 'klaar' ? 'is-done' : '' ?>">
    <td><input class="cell-input" form="<?= $f ?>" name="Titel" value="<?= e($t['Titel']) ?>" required><?php if ($t['Omschrijving']): ?><div class="small muted"><?= e(mb_strimwidth((string) $t['Omschrijving'], 0, 90, '…')) ?></div><?php endif; ?><input type="hidden" form="<?= $f ?>" name="Omschrijving" value="<?= e((string) $t['Omschrijving']) ?>"></td>
    <td><select class="cell-input" form="<?= $f ?>" name="MedewerkerId"><option value="">- Niemand -</option><?php foreach ($staff as $sid => $sname): ?><option value="<?= $sid ?>"<?= (int) $t['MedewerkerId'] === $sid ? ' selected' : '' ?>><?= e($sname) ?></option><?php endforeach; ?></select></td>
    <td><input class="cell-input<?= $late ? ' is-late' : '' ?>" type="date" form="<?= $f ?>" name="Deadline" value="<?= e((string) $t['Deadline']) ?>"<?= $late ? ' title="Deadline verstreken"' : '' ?>></td>
    <td><input class="cell-input cell-num" form="<?= $f ?>" name="GeschatteUren" inputmode="decimal" value="<?= e($hours((float) $t['GeschatteUren'])) ?>"></td>
    <td><input class="cell-input cell-num" form="<?= $f ?>" name="GewerkteUren" inputmode="decimal" value="<?= e($hours((float) $t['GewerkteUren'])) ?>"></td>
    <td><select class="cell-input" form="<?= $f ?>" name="Status"><?php foreach (\App\Controllers\Beheer\ProjectController::TASK_STATUSES as $sk => $sl): ?><option value="<?= $sk ?>"<?= $t['Status'] === $sk ? ' selected' : '' ?>><?= e($sl) ?></option><?php endforeach; ?></select></td>
    <td class="actions nowrap"><form id="<?= $f ?>" class="inline" method="post" action="<?= e(url($base . '/taken/' . (int) $t['Id'])) ?>"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" title="Opslaan"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></form> <?= delete_button(url($base . '/taken/' . (int) $t['Id']), 'Taak "' . $t['Titel'] . '" verwijderen?', '') ?></td>
</tr>
<?php endforeach; ?>
</tbody><tfoot><tr><th colspan="3">Totaal</th><th><?= e($hours($totals['estimated'])) ?> uur</th><th><?= e($hours($totals['worked'])) ?> uur</th><th colspan="2"><?= $rate > 0 ? e(money($totals['workedValue'])) : '' ?></th></tr></tfoot></table></div>
<?php endif; ?></div>
