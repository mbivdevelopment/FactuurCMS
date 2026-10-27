<?php $extend('klant', ['title' => dienst_naam($service)]); ?>
<?php $product = $service->product(); $geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []); $qv = (array) ($geo['qevanto'] ?? []); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= e(dienst_naam($service)) ?></h1><div class="sub"><?= e($service->Nummer) ?> · <?= badge((string) $service->Status, \App\Models\Service::STATUSES[$service->Status] ?? '') ?></div></div><a class="btn btn-ghost" href="<?= e(url('/klant/diensten')) ?>">Terug</a></div>
<div class="grid grid-2">
    <div class="card"><div class="card-body">
        <h3 style="margin-top:0">Details</h3>
        <div class="stack">
            <?php if ($service->Omschrijving): ?><div><small class="muted">Omschrijving</small><div><?= e($service->Omschrijving) ?></div></div><?php endif; ?>
            <?php if ($product !== null): ?><div><small class="muted">Product</small><div><?= e($product->Naam) ?></div></div><?php endif; ?>
            <div><small class="muted">Prijs</small><div><?= e(money((int) $service->Prijs)) ?> <span class="muted">per <?= e(period_label((string) $service->Periode)) ?></span></div></div>
            <div><small class="muted">Gestart op</small><div><?= e(nl_date($service->StartDatum)) ?></div></div>
            <div><small class="muted">Volgende factuur</small><div><?= e(nl_date($service->VolgendeFactuur)) ?></div></div>
            <?php if ($service->EindDatum): ?><div><small class="muted">Einddatum</small><div><?= e(nl_date($service->EindDatum)) ?></div></div><?php endif; ?>
            <?php if (($geo['domein'] ?? '') !== ''): ?><div><small class="muted">Domeinnaam</small><div><?= e($geo['domein']) ?></div></div><?php endif; ?>
        </div>
    </div></div>
    <div class="card"><div class="card-body">
        <h3 style="margin-top:0">Beheer</h3>
        <div class="stack">
            <?php if (($service->Bron ?? '') === 'qevanto'): ?>
                <?php if (($qv['nummer'] ?? '') !== ''): ?><div><small class="muted">Abonnement</small><div><?= e($qv['nummer']) ?><?= ($qv['systeemgebruiker'] ?? '') !== '' ? ' · ' . e($qv['systeemgebruiker']) : '' ?></div></div><?php endif; ?>
                <p class="muted">Beheer deze dienst direct in het hostingpaneel — je wordt automatisch ingelogd.</p>
                <form method="post" action="<?= e(url('/klant/diensten/' . $service->id() . '/paneel')) ?>"><?= csrf_field() ?><button class="btn"><?= icon('login', 'icon icon-sm') ?> Beheer in paneel</button></form>
            <?php else: ?>
                <p class="muted">Deze dienst heeft geen gekoppeld beheerpaneel.</p>
            <?php endif; ?>
        </div>
    </div></div>
</div>
