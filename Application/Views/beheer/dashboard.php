<?php $extend('beheer', ['title' => 'Dashboard']); $max = max(1, ...array_values($months)); $cust = static fn (array|object $r): string => (string) (is_array($r) ? ($r['Bedrijfsnaam'] ?: trim($r['Voornaam'] . ' ' . $r['Achternaam'])) : ($r->Bedrijfsnaam ?: trim($r->Voornaam . ' ' . $r->Achternaam))); ?>
<div class="page-head"><div><h1><?= icon('dashboard') ?> Dashboard</h1><div class="sub">Overzicht van <?= e(company_name()) ?></div></div>
    <div class="row"><a class="btn btn-ghost" href="<?= e(url('/beheer/offertes/nieuw')) ?>"><?= icon('quote', 'icon icon-sm') ?> Nieuwe offerte</a><a class="btn" href="<?= e(url('/beheer/facturen/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe factuur</a></div></div>
<div class="grid grid-4" style="margin-bottom:16px">
    <div class="card stat"><div class="stat-icon"><?= icon('users') ?></div><div><div class="stat-value"><?= $stats['customers'] ?></div><div class="stat-label">Klanten</div></div></div>
    <div class="card stat is-ok"><div class="stat-icon"><?= icon('server') ?></div><div><div class="stat-value"><?= $stats['services'] ?></div><div class="stat-label">Actieve diensten · <?= $stats['pending'] ?> in afwachting</div></div></div>
    <div class="card stat"><div class="stat-icon"><?= icon('euro') ?></div><div><div class="stat-value"><?= e(money($stats['mrr'])) ?></div><div class="stat-label">Terugkerende omzet p/m</div></div></div>
    <div class="card stat <?= $stats['overdue'] > 0 ? 'is-bad' : 'is-warn' ?>"><div class="stat-icon"><?= icon('wallet') ?></div><div><div class="stat-value"><?= e(money($stats['open'])) ?></div><div class="stat-label">Openstaand · <?= e(money($stats['overdue'])) ?> vervallen</div></div></div>
</div>
<div class="grid grid-side" style="margin-bottom:16px">
    <div class="card"><div class="card-head"><h2><?= icon('euro') ?> Omzet laatste 6 maanden (excl. btw)</h2></div><div class="card-body">
        <div class="chart"><?php foreach ($months as $m => $v): ?><div class="chart-bar" data-tip="<?= e(money($v)) ?>" style="position:relative"><span style="height:<?= max(1, (int) round($v / $max * 100)) ?>%"></span><small><?= e(nl_month($m)) ?></small></div><?php endforeach; ?></div>
    </div></div>
    <div class="card"><div class="card-head"><h2><?= icon('zap') ?> Snel naar</h2></div><div class="card-body stack">
        <a class="row between" href="<?= e(url('/beheer/tickets')) ?>"><span><?= icon('ticket') ?> Open tickets</span><?= badge($stats['tickets'] ? 'open' : 'off', (string) $stats['tickets']) ?></a>
        <a class="row between" href="<?= e(url('/beheer/offertes?status=verzonden')) ?>"><span><?= icon('quote') ?> Offertes in afwachting</span><?= badge('off', (string) $stats['quotes']) ?></a>
        <a class="row between" href="<?= e(url('/beheer/diensten?status=in_afwachting')) ?>"><span><?= icon('server') ?> Diensten in afwachting</span><?= badge($stats['pending'] ? 'open' : 'off', (string) $stats['pending']) ?></a>
        <a class="row between" href="<?= e(url('/beheer/klanten/nieuw')) ?>"><span><?= icon('user') ?> Klant toevoegen</span><?= icon('arrow-right', 'icon icon-sm') ?></a>
    </div></div>
</div>
<div class="grid grid-3">
    <div class="card"><div class="card-head"><h2><?= icon('file') ?> Openstaande facturen</h2><a class="small" href="<?= e(url('/beheer/facturen?status=open')) ?>">Alle</a></div>
        <?php if ($openInvoices === []): ?><div class="empty">Alles betaald.</div><?php else: ?><table class="table"><tbody><?php foreach ($openInvoices as $d): ?><tr><td><a href="<?= e(url('/beheer/documenten/' . $d->id())) ?>"><strong><?= e($d->Nummer) ?></strong></a><div class="small muted"><?= e($cust($d)) ?></div></td><td class="right nowrap"><?= e(money($d->outstanding())) ?><div><?= badge($d->status(), $d->statusLabel()) ?></div></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div>
    <div class="card"><div class="card-head"><h2><?= icon('ticket') ?> Open tickets</h2><a class="small" href="<?= e(url('/beheer/tickets')) ?>">Alle</a></div>
        <?php if ($tickets === []): ?><div class="empty">Geen open tickets.</div><?php else: ?><table class="table"><tbody><?php foreach ($tickets as $t): ?><tr><td><a href="<?= e(url('/beheer/tickets/' . $t->id())) ?>"><strong><?= e($t->Onderwerp) ?></strong></a><div class="small muted"><?= e($t->Nummer) ?> · <?= e($cust($t)) ?></div></td><td class="right"><?= badge((string) $t->Prioriteit, \App\Models\Ticket::PRIORITIES[$t->Prioriteit] ?? '') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div>
    <div class="card"><div class="card-head"><h2><?= icon('calendar') ?> Komende facturatie</h2><a class="small" href="<?= e(url('/beheer/diensten')) ?>">Alle</a></div>
        <?php if ($upcoming === []): ?><div class="empty">Niets gepland.</div><?php else: ?><table class="table"><tbody><?php foreach ($upcoming as $s): ?><tr><td><a href="<?= e(url('/beheer/diensten/' . $s['Id'])) ?>"><strong><?= e($s['Naam']) ?></strong></a><div class="small muted"><?= e($cust($s)) ?></div></td><td class="right nowrap"><?= e(nl_date($s['VolgendeFactuur'])) ?><div class="small muted"><?= e(money((int) $s['Prijs'])) ?></div></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div>
</div>
