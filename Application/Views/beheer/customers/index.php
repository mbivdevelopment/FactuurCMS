<?php $extend('beheer', ['title' => 'Klanten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('users') ?></div><div class="ph-text"><h1>Klanten</h1><div class="sub"><?= (int) $page['total'] ?> klanten</div></div>
    <div class="row"><form class="search" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Zoeken…"></form><a class="btn" href="<?= e(url('/beheer/klanten/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe klant</a></div></div>
<div class="card"><?php if ($page['rows'] === []): ?><div class="empty">Geen klanten gevonden.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Klant</th><th>E-mail</th><th>Plaats</th><th>Diensten</th><th class="right">Openstaand</th><th>Actief</th><th></th></tr></thead><tbody>
<?php foreach ($page['rows'] as $c): ?><tr>
    <td class="mono"><?= e($c['Nummer']) ?></td>
    <td><a href="<?= e(url('/beheer/klanten/' . $c['Id'])) ?>"><strong><?= e($c['Bedrijfsnaam'] ?: trim($c['Voornaam'] . ' ' . $c['Achternaam'])) ?></strong></a><?php if ($c['Bedrijfsnaam']): ?><div class="small muted"><?= e(trim($c['Voornaam'] . ' ' . $c['Achternaam'])) ?></div><?php endif; ?></td>
    <td><?= e($c['Email']) ?></td><td><?= e($c['Plaats']) ?></td><td><?= (int) $c['Diensten'] ?></td>
    <td class="right nowrap"><?= (int) $c['Openstaand'] > 0 ? '<strong>' . e(money((int) $c['Openstaand'])) . '</strong>' : '<span class="muted">-</span>' ?></td>
    <td><?= check_icon((bool) $c['Actief']) ?></td>
    <td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/klanten/' . $c['Id'])) ?>"><?= icon('eye', 'icon icon-sm') ?> Bekijken</a></td>
</tr><?php endforeach; ?></tbody></table></div><?= pagination($page) ?><?php endif; ?></div>
