<?php $extend('beheer', ['title' => 'Referrals']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('users') ?></div><div class="ph-text"><h1>Referrals</h1><div class="sub"><?= e((string) $rate) ?>% tegoed na € <?= e(money((int) $threshold)) ?> besteding</div></div>
    <div class="row"><span class="wallet-balance"><?= e(money((int) $total)) ?> beloond</span></div></div>
<div class="card"><div class="table-wrap"><table class="table"><thead><tr><th>Aanbrenger</th><th>Aangebracht lid</th><th>Code</th><th>Voortgang</th><th class="right">Beloond</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td colspan="5" class="muted">Nog geen referrals.</td></tr><?php endif; ?>
<?php foreach ($rows as $row): ?><tr>
    <td><?= e($row['AanbrengerBedrijf'] ?: trim($row['AanbrengerVoornaam'] . ' ' . $row['AanbrengerAchternaam'])) ?></td>
    <td><?= e($row['LidBedrijf'] ?: trim($row['LidVoornaam'] . ' ' . $row['LidAchternaam'])) ?><div class="small muted"><?= e($row['LidEmail']) ?></div></td>
    <td><span class="mono"><?= e($row['Code']) ?></span></td>
    <td><?= $row['GekwalificeerdOp'] ? '<span class="badge badge-green">Gekwalificeerd</span>' : '<span class="muted">Nog niet gekwalificeerd</span>' ?></td>
    <td class="right nowrap"><?= e(money((int) $row['Beloond'])) ?></td>
</tr><?php endforeach; ?></tbody></table></div></div>
