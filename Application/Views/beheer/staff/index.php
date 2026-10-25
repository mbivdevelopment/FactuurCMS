<?php $extend('beheer', ['title' => 'Medewerkers']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('shield') ?></div><div class="ph-text"><h1>Medewerkers</h1></div><a class="btn" href="<?= e(url('/beheer/medewerkers/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe medewerker</a></div>
<div class="card"><div class="table-wrap"><table class="table"><thead><tr><th>Naam</th><th>E-mail</th><th>Rol</th><th>Laatste login</th><th>Actief</th></tr></thead><tbody>
<?php foreach ($staff as $m): ?><tr><td><a href="<?= e(url('/beheer/medewerkers/' . $m->id())) ?>"><strong><?= e($m->name()) ?></strong></a></td><td><?= e($m->Email) ?></td><td><?= badge($m->isAdmin() ? 'actief' : 'off', \App\Models\User::ROLES[$m->Rol]) ?></td><td><?= e(nl_date($m->LaatsteLogin, true)) ?></td><td><?= check_icon((bool) $m->Actief) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<p class="small muted">Beheerders hebben toegang tot alles; medewerkers niet tot medewerkers, instellingen en updates.</p>
