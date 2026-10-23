<?php $extend('beheer', ['title' => 'Medewerkers']); ?>
<div class="page-head"><h1><?= icon('shield') ?> Medewerkers</h1><a class="btn" href="<?= e(url('/beheer/medewerkers/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe medewerker</a></div>
<div class="card"><div class="table-wrap"><table class="table"><thead><tr><th>Naam</th><th>E-mail</th><th>Rol</th><th>Laatste login</th><th>Actief</th><th></th></tr></thead><tbody>
<?php foreach ($staff as $m): ?><tr><td><strong><?= e($m->name()) ?></strong></td><td><?= e($m->Email) ?></td><td><?= badge($m->isAdmin() ? 'actief' : 'off', \App\Models\User::ROLES[$m->Rol]) ?></td><td><?= e(nl_date($m->LaatsteLogin, true)) ?></td><td><?= check_icon((bool) $m->Actief) ?></td>
<td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/medewerkers/' . $m->id())) ?>"><?= icon('edit', 'icon icon-sm') ?> Bewerken</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<p class="small muted">Beheerders hebben toegang tot alles; medewerkers niet tot medewerkers, instellingen en updates.</p>
