<?php $extend('beheer', ['title' => 'Klanten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('users') ?></div><div class="ph-text"><h1>Klanten</h1><div class="sub"><?= (int) $page['total'] ?> klanten</div></div>
    <div class="row"><form class="search" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Zoeken…"><select name="type" onchange="this.form.submit()"><option value="">Alle types</option><?php foreach (\App\Models\User::TYPES as $k => $v): ?><option value="<?= e($k) ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></form><a class="btn" href="<?= e(url('/beheer/klanten/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe klant</a></div></div>
<div class="card"><?php if ($page['rows'] === []): ?><div class="empty">Geen klanten gevonden.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th class="nowrap" style="width:1%">Nummer</th><th style="width:100%">Klant</th><th class="center">Type</th><th class="center">Diensten</th><th class="right">Openstaand</th><th class="center">Actief</th></tr></thead><tbody>
<?php foreach ($page['rows'] as $c): ?><tr>
    <td class="mono"><?= e($c['Nummer']) ?></td>
    <td><a href="<?= e(url('/beheer/klanten/' . $c['Id'])) ?>"><strong><?= e($c['Bedrijfsnaam'] ?: trim($c['Voornaam'] . ' ' . $c['Achternaam'])) ?></strong></a><?php if ($c['Bedrijfsnaam']): ?><div class="small muted"><?= e(trim($c['Voornaam'] . ' ' . $c['Achternaam'])) ?></div><?php endif; ?></td>
    <td class="center"><?php $typeLabel = $c['Rol'] !== 'klant' ? (\App\Models\User::ROLES[$c['Rol']] ?? $c['Rol']) : (\App\Models\User::TYPES[$c['Type'] ?? 'klant'] ?? 'Klant'); ?><?= $typeLabel !== 'Klant' ? badge((string) ($c['Rol'] !== 'klant' ? $c['Rol'] : ($c['Type'] ?? 'klant')), $typeLabel) : '<span class="muted">Klant</span>' ?></td>
    <td class="center"><?= (int) $c['Diensten'] ?></td>
    <td class="right nowrap"><?= (int) $c['Openstaand'] > 0 ? '<strong>' . e(money((int) $c['Openstaand'])) . '</strong>' : '<span class="muted">-</span>' ?></td>
    <td class="center"><?= check_icon((bool) $c['Actief']) ?></td>
</tr><?php endforeach; ?></tbody></table></div><?= pagination($page) ?><?php endif; ?></div>
