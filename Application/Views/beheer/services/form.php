<?php $new = !$service->id(); $extend('beheer', ['title' => $new ? 'Nieuwe dienst' : $service->Naam]);
$custOpts = ['' => 'Kies een klant…']; foreach ($customers as $c) { $custOpts[$c->id()] = $c->display() . ' (' . $c->Nummer . ')' . ($c->Rol !== 'klant' ? ' · ' . \App\Models\User::ROLES[$c->Rol] : ''); }
$prodOpts = ['' => 'Geen / handmatig']; foreach ($products as $p) { $prodOpts[$p->id()] = $p->Groep . ' · ' . $p->Naam; } ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= e($new ? 'Nieuwe dienst' : $service->Naam) ?></h1><?php if (!$new): ?><div class="sub"><?= e($service->Nummer) ?></div><?php endif; ?></div>
    <div class="row"><?php if (!$new && $service->isDomain()): $base = '/beheer'; require __DIR__ . '/../../shared/domain-actions.php'; endif; ?><?php if (!$new): ?><form method="post" action="<?= e(url('/beheer/diensten/' . $service->id() . '/factureren')) ?>" data-confirm="Nu een factuur maken voor de volgende periode?"><?= csrf_field() ?><button class="btn btn-ghost"><?= icon('file', 'icon icon-sm') ?> Nu factureren</button></form><?php endif; ?></div></div>
<div class="svc-split">
    <aside class="stack">
        <div class="card"><div class="card-body">
            <?= $partial('beheer.partials.catnav', ['link' => static fn (string $k): string => '/beheer/diensten?cat=' . $k, 'cat' => $cat, 'catCounts' => $catCounts, 'totalDiensten' => $totalDiensten]) ?>
        </div></div>
    </aside>
    <div class="stack">
<form class="card" method="post" action="<?= e(url($new ? '/beheer/diensten' : '/beheer/diensten/' . $service->id())) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('KlantId', 'Klant', 'user', $service->KlantId, 'select', $custOpts, 'required') ?>
        <?= field('ProductId', 'Product', 'box', $service->ProductId, 'select', $prodOpts) ?>
        <?= field('Naam', 'Naam', 'tag', $service->Naam, 'text', [], 'required') ?>
        <?= field('Omschrijving', 'Omschrijving / domein', 'globe', $service->Omschrijving) ?>
        <?= field('Prijs', 'Prijs excl. btw (€)', 'euro', cents_input((int) $service->Prijs)) ?>
        <?= field('BtwTarief', 'Btw-tarief (%)', 'hash', (string) (float) $service->BtwTarief) ?>
        <?= field('Periode', 'Factuurperiode', 'refresh', $service->Periode, 'select', \App\Models\Product::PERIODS) ?>
        <?= field('Status', 'Status', 'check', $service->Status, 'select', \App\Models\Service::STATUSES) ?>
        <?= field('StartDatum', 'Startdatum', 'calendar', $service->StartDatum, 'date') ?>
        <?= field('VolgendeFactuur', 'Volgende factuurdatum', 'calendar', $service->VolgendeFactuur, 'date') ?>
        <?= field('EindDatum', 'Einddatum', 'calendar', $service->EindDatum, 'date') ?>
    </div>
    <div class="card-foot"><?php if (!$new): ?><?= delete_submit() ?><span style="margin-left:auto"></span><?php endif; ?><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
<?php if (!$new): ?><?= delete_form(url('/beheer/diensten/' . $service->id()), 'Dienst ' . $service->Naam . ' verwijderen?') ?><?php endif; ?>
<?php if (!$new && $service->isDomain()): $base = '/beheer'; require __DIR__ . '/../../shared/domain-card.php'; endif; ?>
    </div>
</div>
