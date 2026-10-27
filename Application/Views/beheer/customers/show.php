<?php $extend('beheer', ['title' => $customer->display()]); $cid = $customer->id(); ?>
<div class="page-head"><div class="ph-icon"><?= icon('user') ?></div><div class="ph-text"><h1><?= e($customer->display()) ?></h1><div class="sub"><?= e($customer->Nummer) ?> · <?= e($customer->Email) ?> · klant sinds <?= e(nl_date((string) $customer->AangemaaktOp)) ?><?= ($customer->Type ?? 'klant') !== 'klant' ? ' · ' . e(\App\Models\User::TYPES[$customer->Type] ?? $customer->Type) : '' ?></div></div>
    <div class="row">
        <form method="post" action="<?= e(url('/beheer/klanten/' . $cid . '/inloggen')) ?>"><?= csrf_field() ?><button class="btn btn-ghost"><?= icon('login', 'icon icon-sm') ?> Inloggen als klant</button></form>
        <a class="btn btn-ghost" href="<?= e(url('/beheer/diensten/nieuw?klant=' . $cid)) ?>"><?= icon('server', 'icon icon-sm') ?> Dienst</a>
        <a class="btn btn-ghost" href="<?= e(url('/beheer/offertes/nieuw?klant=' . $cid)) ?>"><?= icon('quote', 'icon icon-sm') ?> Offerte</a>
        <a class="btn" href="<?= e(url('/beheer/facturen/nieuw?klant=' . $cid)) ?>"><?= icon('file', 'icon icon-sm') ?> Factuur</a>
    </div></div>
<div class="grid grid-4" style="margin-bottom:16px">
    <div class="card stat is-ok"><div class="stat-icon"><?= icon('server') ?></div><div><div class="stat-value"><?= count(array_filter($allServices, static fn ($s) => $s->Status === 'actief')) ?></div><div class="stat-label">Actieve diensten</div></div></div>
    <div class="card stat <?= $open > 0 ? 'is-warn' : '' ?>"><div class="stat-icon"><?= icon('wallet') ?></div><div><div class="stat-value"><?= e(money($open)) ?></div><div class="stat-label">Openstaand</div></div></div>
    <div class="card stat"><div class="stat-icon"><?= icon('euro') ?></div><div><div class="stat-value"><?= e(money($revenue)) ?></div><div class="stat-label">Totaal betaald</div></div></div>
    <div class="card stat"><div class="stat-icon"><?= icon('ticket') ?></div><div><div class="stat-value"><?= count($tickets) ?></div><div class="stat-label">Tickets</div></div></div>
</div>
<div class="card" data-tabs>
    <nav class="tab-nav">
        <a href="#diensten" class="tab-link" data-tab="diensten"><?= icon('server', 'icon icon-sm') ?> Diensten (<?= (int) $totalDiensten ?>)</a>
        <a href="#facturen" class="tab-link" data-tab="facturen"><?= icon('file', 'icon icon-sm') ?> Facturen (<?= count($invoices) ?>)</a>
        <a href="#offertes" class="tab-link" data-tab="offertes"><?= icon('quote', 'icon icon-sm') ?> Offertes (<?= count($quotes) ?>)</a>
        <a href="#tickets" class="tab-link" data-tab="tickets"><?= icon('ticket', 'icon icon-sm') ?> Tickets (<?= count($tickets) ?>)</a>
        <a href="#gegevens" class="tab-link" data-tab="gegevens"><?= icon('edit', 'icon icon-sm') ?> Gegevens</a>
    </nav>
    <div class="tab-pane" data-pane="diensten">
        <div class="card-body svc-split">
            <aside>
                <h3 class="side-title">Categorieën</h3>
                <nav class="sidecat">
                    <a href="<?= e(url('/beheer/klanten/' . $cid)) ?>#diensten"<?= $cat === '' ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon('layout', 'icon icon-sm') ?></span><span>Alle diensten</span><small><?= (int) $totalDiensten ?></small></a>
                    <?php foreach (\App\Models\Service::CATEGORIES as $key => $def): ?>
                        <a href="<?= e(url('/beheer/klanten/' . $cid . '?cat=' . $key)) ?>#diensten"<?= $cat === $key ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon($def[2], 'icon icon-sm') ?></span><span><?= e($def[0]) ?></span><small><?= (int) ($catCounts[$key] ?? 0) ?></small></a>
                    <?php endforeach; ?>
                </nav>
            </aside>
            <div>
        <?php if ($services === []): ?><div class="empty">Geen diensten<?= $catDef !== null ? ' in ' . e($catDef[0]) : '' ?>.</div><?php else: ?><div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Dienst</th><th>Prijs</th><th>Volgende factuur</th><th>Status</th><th></th></tr></thead><tbody>
        <?php foreach ($services as $s): ?><tr><td class="mono"><?= e($s->Nummer) ?></td><td><strong><?= dienst_naam($s) !== (string) $s->Naam ? icon('globe', 'icon icon-sm') . ' ' : '' ?><?= e(dienst_naam($s)) ?></strong><?php if (dienst_naam($s) === (string) $s->Naam && $s->Omschrijving): ?><div class="small muted"><?= e($s->Omschrijving) ?></div><?php endif; ?></td><td class="nowrap"><?= e(money((int) $s->Prijs)) ?> <span class="small muted"><?= e(period_label((string) $s->Periode)) ?></span></td><td><?= e(nl_date($s->VolgendeFactuur)) ?></td><td><?= badge((string) $s->Status, \App\Models\Service::STATUSES[$s->Status] ?? '') ?></td><td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/diensten/' . $s->id())) ?>"><?= icon('edit', 'icon icon-sm') ?></a></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
            </div>
        </div>
    </div>
    <?php foreach (['facturen' => $invoices, 'offertes' => $quotes] as $pane => $docs): ?>
    <div class="tab-pane" data-pane="<?= $pane ?>">
        <?php if ($docs === []): ?><div class="empty">Geen <?= $pane ?>.</div><?php else: ?><div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Datum</th><th>Onderwerp</th><th class="right">Totaal</th><th>Status</th><th></th></tr></thead><tbody>
        <?php foreach ($docs as $d): ?><tr><td><a href="<?= e(url('/beheer/documenten/' . $d->id())) ?>"><strong><?= e($d->Nummer) ?></strong></a></td><td><?= e(nl_date((string) $d->Datum)) ?></td><td><?= e($d->Onderwerp) ?></td><td class="right nowrap"><?= e(money((int) $d->Totaal)) ?></td><td><?= badge($d->status(), $d->statusLabel()) ?></td><td class="actions"><a class="btn btn-ghost btn-sm" target="_blank" href="<?= e(url('/beheer/documenten/' . $d->id() . '/pdf')) ?>"><?= icon('download', 'icon icon-sm') ?> PDF</a></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div class="tab-pane" data-pane="tickets">
        <div class="card-body" style="padding-bottom:0"><a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/tickets/nieuw?klant=' . $cid)) ?>"><?= icon('plus', 'icon icon-sm') ?> Ticket openen</a></div>
        <?php if ($tickets === []): ?><div class="empty">Geen tickets.</div><?php else: ?><table class="table"><tbody><?php foreach ($tickets as $t): ?><tr><td><a href="<?= e(url('/beheer/tickets/' . $t->id())) ?>"><strong><?= e($t->Nummer) ?></strong></a></td><td><?= e($t->Onderwerp) ?></td><td><?= e(nl_date($t->LaatsteReactie, true)) ?></td><td><?= badge((string) $t->Status, \App\Models\Ticket::STATUSES[$t->Status] ?? '') ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>
    </div>
    <div class="tab-pane" data-pane="gegevens">
        <form method="post" action="<?= e(url('/beheer/klanten/' . $cid)) ?>"><?= csrf_field() ?>
            <div class="card-body"><?= $partial('beheer.customers._fields', ['customer' => $customer]) ?></div>
            <div class="card-foot"><?php if (auth_user()?->isAdmin()): ?><span style="margin-right:auto"></span><?php endif; ?><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
        </form>
        <?php if (auth_user()?->isAdmin()): ?><div class="card-body" style="border-top:1px solid var(--line)"><?= delete_button(url('/beheer/klanten/' . $cid), 'Klant ' . $customer->display() . ' met alle diensten, facturen en tickets definitief verwijderen?', 'Klant verwijderen') ?></div><?php endif; ?>
    </div>
</div>
