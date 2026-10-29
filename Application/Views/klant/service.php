<?php $extend('klant', ['title' => dienst_naam($service)]); ?>
<?php $product = $service->product(); $geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []); $qv = (array) ($geo['qevanto'] ?? []); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= e(dienst_naam($service)) ?></h1><div class="sub"><?= e($service->Nummer) ?> · <?= badge((string) $service->Status, \App\Models\Service::STATUSES[$service->Status] ?? '') ?></div></div><div class="row" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center"><?php if ($service->isDomain()): $base = '/klant'; require __DIR__ . '/../shared/domain-actions.php'; endif; ?><?php if ($service->isHosting()): ?><a class="btn btn-sm" href="<?= e(url('/klant/diensten/' . $service->id() . '/hosting')) ?>"><?= icon('server', 'icon icon-sm') ?> Webhosting beheer</a><?php endif; ?></div></div>
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
            <?php if (($service->Bron ?? '') === 'interserver'): ?>
                <?php
                $is = (array) ($geo['interserver'] ?? []);
                $vps = null;
                try {
                    $vps = \App\Services\Interserver::vps((int) $service->ExternId);
                } catch (\Throwable) {
                }
                $power = (string) ($vps['powerstatus'] ?? $vps['status'] ?? '');
                $rootpass = '';
                try {
                    $rootpass = \App\Core\Crypt::decrypt((string) ($is['rootpass'] ?? ''));
                } catch (\Throwable) {
                }
                ?>
                <?php if ($vps !== null): ?>
                    <div><small class="muted">Status</small><div><?= badge($power === 'running' || $power === 'up' || $power === 'actief' ? 'actief' : 'gesloten', $power !== '' ? ucfirst($power) : 'Onbekend') ?></div></div>
                    <?php foreach (['ip' => 'IP-adres', 'hostname' => 'Hostname', 'os' => 'Besturingssysteem', 'location' => 'Locatie', 'slices' => 'Slices'] as $vk => $vl): $vv = $vps[$vk] ?? ($is[$vk] ?? ''); ?>
                        <?php if ((string) $vv !== ''): ?><div><small class="muted"><?= e($vl) ?></small><div><?= e(is_array($vv) ? implode(', ', $vv) : (string) $vv) ?></div></div><?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">De VPS wordt geïnstalleerd. Zodra InterServer klaar is, zie je hier de status en het IP-adres.</p>
                <?php endif; ?>
                <?php if ($rootpass !== ''): ?><div><small class="muted">Root-wachtwoord</small><div class="mono"><?= e($rootpass) ?></div></div><?php endif; ?>
                <div class="row" style="display:flex;gap:8px;flex-wrap:wrap">
                    <form method="post" action="<?= e(url('/klant/diensten/' . $service->id() . '/vps/start')) ?>"><?= csrf_field() ?><button class="btn btn-sm"><?= icon('zap', 'icon icon-sm') ?> Start</button></form>
                    <form method="post" action="<?= e(url('/klant/diensten/' . $service->id() . '/vps/restart')) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" data-confirm="VPS herstarten?"><?= icon('refresh', 'icon icon-sm') ?> Herstart</button></form>
                    <form method="post" action="<?= e(url('/klant/diensten/' . $service->id() . '/vps/stop')) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost" data-confirm="VPS uitschakelen?"><?= icon('x', 'icon icon-sm') ?> Stop</button></form>
                </div>
            <?php elseif (!$service->isHosting() && !$service->isDomain()): ?>
                <p class="muted">Deze dienst heeft geen gekoppeld beheerpaneel.</p>
            <?php endif; ?>
        </div>
    </div></div>
</div>
<?php $base = '/klant'; require __DIR__ . '/../shared/domain-card.php'; ?>
