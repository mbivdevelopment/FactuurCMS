<?php $extend('beheer', ['title' => 'Tickets']); $tabs = ['actief' => 'Actief', 'open' => 'Open', 'beantwoord' => 'Beantwoord', 'gesloten' => 'Gesloten', 'alle' => 'Alle']; ?>
<div class="page-head"><div class="ph-icon"><?= icon('ticket') ?></div><div class="ph-text"><h1>Tickets</h1><div class="sub"><?= (int) $page['total'] ?> tickets</div></div>
    <div class="filters ph-filters"><?php foreach ($tabs as $k => $v): ?><a href="<?= e(url('/beheer/tickets?status=' . $k)) ?>" class="<?= $status === $k ? 'active' : '' ?>"><?= e($v) ?></a><?php endforeach; ?></div>
    <a class="btn" href="<?= e(url('/beheer/tickets/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuw ticket</a></div>
<div class="card"><?php if ($page['rows'] === []): ?><div class="empty">Geen tickets.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Onderwerp</th><th>Klant</th><th>Afdeling</th><th>Prioriteit</th><th>Laatste reactie</th><th>Status</th></tr></thead><tbody>
<?php foreach ($page['rows'] as $t): ?><tr><td class="mono"><?= e($t['Nummer']) ?></td><td><a href="<?= e(url('/beheer/tickets/' . $t['Id'])) ?>"><strong><?= e($t['Onderwerp']) ?></strong></a></td>
<td><a href="<?= e(url('/beheer/klanten/' . $t['KlantId'])) ?>"><?= e($t['Bedrijfsnaam'] ?: trim($t['Voornaam'] . ' ' . $t['Achternaam'])) ?></a></td><td><?= e($t['Afdeling']) ?></td>
<td><?= badge($t['Prioriteit'], \App\Models\Ticket::PRIORITIES[$t['Prioriteit']] ?? '') ?></td><td><?= e(nl_date($t['LaatsteReactie'], true)) ?></td><td><?= badge($t['Status'], \App\Models\Ticket::STATUSES[$t['Status']] ?? '') ?></td></tr><?php endforeach; ?>
</tbody></table></div><?= pagination($page) ?><?php endif; ?></div>
