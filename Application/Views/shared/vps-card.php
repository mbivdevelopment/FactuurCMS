<?php
// VPS-beheerkaart: status, gegevens en powerknoppen (InterServer /vps API).
// Verwacht: $service (App\Models\Service) en $base ('/klant' of '/beheer').
if (!$service->isVps()) {
    return;
}
$geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
$is = (array) ($geo['interserver'] ?? []);
$handover = is_array($geo['handover'] ?? null) ? $geo['handover'] : null;
$vps = null;
if ((int) $service->ExternId > 0) {
    try {
        $vps = \App\Services\Interserver::vps((int) $service->ExternId);
    } catch (\Throwable) {
    }
}
$power = (string) ($vps['powerstatus'] ?? $vps['status'] ?? '');
$running = in_array(strtolower($power), ['running', 'up', 'actief', 'active'], true);
$rootpass = '';
try {
    $rootpass = \App\Core\Crypt::decrypt((string) ($is['rootpass'] ?? ''));
} catch (\Throwable) {
}
$vact = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/vps/' . $a));
$dial = (string) ($_GET['dial'] ?? '');
?>
<div class="card">
    <div class="card-head"><h3><?= icon('cloud', 'icon icon-sm') ?> CloudVPS</h3><?php if ($power !== ''): ?><?= badge($running ? 'actief' : 'gesloten', ucfirst($power)) ?><?php endif; ?></div>
    <div class="card-body">
        <?php if ($vps === null && (int) $service->ExternId === 0): ?>
            <p class="muted" style="margin:0">De VPS wordt geïnstalleerd. Zodra InterServer klaar is, zie je hier de status en het IP-adres.</p>
        <?php elseif ($vps === null): ?>
            <p class="muted" style="margin:0">De servergegevens konden niet bij InterServer worden opgehaald.</p>
        <?php endif; ?>
        <div class="form-grid" style="grid-template-columns:repeat(2,1fr)">
            <?php foreach ([
                'ip' => 'IP-adres', 'hostname' => 'Hostname', 'os' => 'Besturingssysteem',
                'location' => 'Locatie', 'slices' => 'Slices', 'platform' => 'Platform',
            ] as $vk => $vl): $vv = $vps[$vk] ?? ($is[$vk] ?? ''); ?>
                <?php if (is_string($vv) && $vv !== ''): ?><div><small class="muted"><?= e($vl) ?></small><div><?= e($vv) ?></div></div><?php endif; ?>
            <?php endforeach; ?>
            <?php if (($is['besteld'] ?? '') !== ''): ?><div><small class="muted">Besteld op</small><div><?= e((string) $is['besteld']) ?></div></div><?php endif; ?>
            <?php if ($rootpass !== ''): ?><div><small class="muted">Root-wachtwoord</small><div class="mono"><?= e($rootpass) ?></div></div><?php endif; ?>
        </div>
        <?php if ($vps !== null): ?>
        <div class="row" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px">
            <?php if (!$running): ?><form method="post" action="<?= $vact('start') ?>" style="display:inline"><?= csrf_field() ?><button class="btn btn-sm"><?= icon('zap', 'icon icon-sm') ?> Start</button></form><?php endif; ?>
            <form method="post" action="<?= $vact('restart') ?>" style="display:inline" data-confirm="VPS herstarten?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Herstart</button></form>
            <?php if ($running): ?><form method="post" action="<?= $vact('stop') ?>" style="display:inline" data-confirm="VPS uitschakelen?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('x', 'icon icon-sm') ?> Stop</button></form><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<dialog class="confirm" id="vps-handover-<?= (int) $service->id() ?>">
    <div class="modal-h"><?= icon('handshake', 'icon icon-sm') ?> Handover — <?= e((string) $service->Naam) ?></div>
    <?php if ($handover !== null): ?>
        <p>De handover is gestart voor <strong><?= e((string) ($handover['email'] ?? '')) ?></strong>. Die klant heeft een mail met overnamecode gekregen; zodra die bevestigt, staat de dienst in zijn account.</p>
        <p><small class="muted">Gestart op <?= e((string) ($handover['op'] ?? '')) ?> · code <?= e((string) ($handover['code'] ?? '')) ?></small></p>
        <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Sluiten</button></div>
    <?php else: ?>
        <p>Plaats deze server over naar een andere klant. Die klant krijgt een e-mail met een overnamecode om de dienst in zijn account te zetten.</p>
        <form method="post" action="<?= $vact('handover') ?>" style="padding:0 18px"><?= csrf_field() ?>
            <?= field('email', 'E-mailadres nieuwe klant', 'mail', '', 'email', [], 'required') ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn"><?= icon('handshake', 'icon icon-sm') ?> Handover starten</button></div>
        </form>
    <?php endif; ?>
</dialog>
<?php if ($dial === 'handover'): ?>
<script>document.getElementById('vps-handover-<?= (int) $service->id() ?>')?.showModal();</script>
<?php endif; ?>
