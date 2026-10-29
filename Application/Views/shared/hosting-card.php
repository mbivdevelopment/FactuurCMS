<?php
// Webhosting-beheerkaart: abonnementsinfo, verbruik (Qevanto stats) en de handover-pop-up.
// Verwacht: $service (App\Models\Service) en $base ('/klant' of '/beheer').
if (!$service->isHosting()) {
    return;
}
$geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
$qv = (array) ($geo['qevanto'] ?? []);
$handover = is_array($geo['handover'] ?? null) ? $geo['handover'] : null;
$sub = null;
$stats = null;
if ((string) $service->Bron === 'qevanto' && (int) $service->ExternId > 0) {
    try {
        $sub = \App\Services\QevantoPaneel::subscription((int) $service->ExternId);
        $stats = \App\Services\QevantoPaneel::stats((int) $service->ExternId, '30d');
    } catch (\Throwable) {
    }
}
$limits = (array) ($stats['limits'] ?? []);
$current = (array) ($stats['current'] ?? []);
$totals = (array) ($stats['totals'] ?? []);
$fmtMb = static function (?float $mb): string {
    if ($mb === null) {
        return '—';
    }

    return $mb >= 1024 ? number_format($mb / 1024, 1, ',', '') . ' GB' : number_format($mb, 0, ',', '') . ' MB';
};
$meters = [];
if ($current !== [] || $limits !== []) {
    $disk = isset($current['disk']) ? (float) $current['disk'] : null;
    $diskMax = (float) ($limits['diskGb'] ?? 0) * 1024;
    $net = isset($totals['netMonth']) ? (float) $totals['netMonth'] / 1048576 : null;
    $netMax = (float) ($limits['bandwidthGb'] ?? 0) * 1024;
    $mem = isset($current['mem']) ? (float) $current['mem'] : null;
    $memMax = (float) ($limits['memoryMb'] ?? 0);
    $cpu = isset($current['cpu']) ? (float) $current['cpu'] : null;
    $cpuMax = (float) ($limits['cpuPercent'] ?? 0);
    $meters = [
        ['box', 'Opslag', $disk, $diskMax > 0 ? $diskMax : null, $fmtMb],
        ['zap', 'Dataverkeer deze maand', $net, $netMax > 0 ? $netMax : null, $fmtMb],
        ['cpu', 'Geheugen', $mem, $memMax > 0 ? $memMax : null, $fmtMb],
        ['cpu', 'CPU', $cpu, $cpuMax > 0 ? $cpuMax : null, static fn (?float $v): string => $v === null ? '—' : number_format($v, 1, ',', '') . '%'],
    ];
}
$sites = array_values(array_filter(array_map(static fn ($w): string => (string) ($w['domain'] ?? ''), (array) ($sub['websites'] ?? [])), static fn (string $d): bool => $d !== ''));
$dial = (string) ($_GET['dial'] ?? '');
?>
<div class="card">
    <div class="card-head"><h3><?= icon('server', 'icon icon-sm') ?> Webhosting</h3><?php if ($sub !== null): ?><?= badge((string) ($sub['status'] ?? 'actief') === 'active' ? 'actief' : 'gesloten', (string) ($sub['status'] ?? '')) ?><?php endif; ?></div>
    <div class="card-body">
        <div class="form-grid">
            <?php if (($qv['nummer'] ?? $sub['number'] ?? '') !== ''): ?><div><small class="muted">Abonnement</small><div><strong><?= e((string) ($qv['nummer'] ?? $sub['number'])) ?></strong><?= (($qv['systeemgebruiker'] ?? $sub['unixUser'] ?? '') !== '') ? ' · ' . e((string) ($qv['systeemgebruiker'] ?? $sub['unixUser'])) : '' ?></div></div><?php endif; ?>
            <?php if (($qv['pakket'] ?? $sub['package']['name'] ?? '') !== ''): ?><div><small class="muted">Pakket</small><div><?= e((string) ($qv['pakket'] ?? $sub['package']['name'])) ?></div></div><?php endif; ?>
            <?php if (($qv['server'] ?? $sub['server']['name'] ?? '') !== ''): ?><div><small class="muted">Server</small><div><?= e((string) ($qv['server'] ?? $sub['server']['name'])) ?></div></div><?php endif; ?>
            <?php if ($sites !== [] || ($qv['domeinen'] ?? []) !== []): ?><div><small class="muted">Websites</small><div class="mono"><?= e(implode(', ', $sites !== [] ? $sites : (array) $qv['domeinen'])) ?></div></div><?php endif; ?>
        </div>
        <?php if ($meters !== []): ?>
            <div class="form-grid" style="margin-top:14px">
                <?php foreach ($meters as [$ic, $label, $val, $max, $fmt]): $pct = $val !== null && $max ? min(100, (int) round($val / $max * 100)) : null; ?>
                    <div class="card stat" style="margin:0"><div class="stat-icon"><?= icon($ic) ?></div><div style="flex:1">
                        <div class="stat-value"><?= e($fmt($val)) ?></div>
                        <div class="stat-label"><?= e($label) ?><?= $max ? ' · limiet ' . e($fmt($max)) : ' · onbeperkt' ?></div>
                        <?php if ($pct !== null): ?><div class="progress<?= $pct > 85 ? ' is-bad' : '' ?>"><span style="width:<?= $pct ?>%"></span></div><?php endif; ?>
                    </div></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ((string) $service->Bron === 'qevanto' && $stats === null): ?>
            <p class="muted" style="margin-top:10px">Verbruiksgegevens konden niet bij het paneel worden opgehaald.</p>
        <?php endif; ?>
    </div>
</div>

<dialog class="confirm" id="host-handover-<?= (int) $service->id() ?>">
    <div class="modal-h"><?= icon('handshake', 'icon icon-sm') ?> Handover — <?= e((string) $service->Naam) ?></div>
    <?php if ($handover !== null): ?>
        <p>De handover is gestart voor <strong><?= e((string) ($handover['email'] ?? '')) ?></strong>. Die klant heeft een mail met overnamecode gekregen; zodra die bevestigt, staat de dienst in zijn account.</p>
        <p><small class="muted">Gestart op <?= e((string) ($handover['op'] ?? '')) ?> · code <?= e((string) ($handover['code'] ?? '')) ?></small></p>
        <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Sluiten</button></div>
    <?php else: ?>
        <p>Plaats deze dienst over naar een andere klant. Die klant krijgt een e-mail met een overnamecode om de dienst in zijn account te zetten.</p>
        <form method="post" action="<?= e(url($base . '/diensten/' . $service->id() . '/hosting/handover')) ?>" style="padding:0 18px"><?= csrf_field() ?>
            <?= field('email', 'E-mailadres nieuwe klant', 'mail', '', 'email', [], 'required') ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn"><?= icon('handshake', 'icon icon-sm') ?> Handover starten</button></div>
        </form>
    <?php endif; ?>
</dialog>
<?php if ($dial === 'handover'): ?>
<script>document.getElementById('host-handover-<?= (int) $service->id() ?>')?.showModal();</script>
<?php endif; ?>
