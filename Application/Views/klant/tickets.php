<?php $extend('klant', ['title' => 'Support']); ?>
<div class="page-head"><h1><?= icon('ticket') ?> Support</h1><a class="btn" href="<?= e(url('/klant/tickets/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuw ticket</a></div>
<div class="card"><?php if ($tickets === []): ?><div class="empty">Je hebt nog geen tickets.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Onderwerp</th><th>Afdeling</th><th>Laatste reactie</th><th>Status</th></tr></thead><tbody>
<?php foreach ($tickets as $t): ?><tr><td><a href="<?= e(url('/klant/tickets/' . $t->id())) ?>"><strong><?= e($t->Nummer) ?></strong></a></td><td><?= e($t->Onderwerp) ?></td><td><?= e($t->Afdeling) ?></td><td><?= e(nl_date($t->LaatsteReactie, true)) ?></td><td><?= badge((string) $t->Status, \App\Models\Ticket::STATUSES[$t->Status] ?? $t->Status) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></div>
