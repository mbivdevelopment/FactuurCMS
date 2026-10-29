<?php
// Verwacht: $service (App\Models\Service) en $base ('/klant' of '/beheer')
$domain = $service->domainName();
if ($domain === '') {
    return;
}
$geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
$info = ['nameservers' => (array) ($geo['nameservers'] ?? []), 'authcode' => (string) ($geo['epp'] ?? ''), 'status' => '', 'expiry' => null, 'autoRenew' => (bool) ($geo['realtimeregister']['autoRenew'] ?? true), 'registrant' => ''];
try {
    if (\App\Services\RealtimeRegister::configured()) {
        $info = array_merge($info, \App\Services\RealtimeRegister::info($domain));
    }
} catch (\Throwable) {
}
$act = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/domein/' . $a));
$nsList = array_values($info['nameservers']);
while (count($nsList) < 4) {
    $nsList[] = '';
}
?>
<div class="card">
    <div class="card-head"><h3><?= icon('globe', 'icon icon-sm') ?> Domeinbeheer</h3><?= badge($info['autoRenew'] ? 'actief' : 'opgezegd', $info['autoRenew'] ? 'Verlengt automatisch' : 'Verlengt niet') ?></div>
    <div class="card-body">
        <div class="form-grid">
            <div><small class="muted">Domeinnaam</small><div><strong><?= e($domain) ?></strong></div></div>
            <?php if ($info['status'] !== ''): ?><div><small class="muted">Status bij registrar</small><div><?= e($info['status']) ?></div></div><?php endif; ?>
            <?php if ($info['expiry']): ?><div><small class="muted">Verloopdatum</small><div><?= e(nl_date($info['expiry'])) ?></div></div><?php endif; ?>
            <?php if ($info['registrant'] !== ''): ?><div><small class="muted">Houder</small><div><?= e($info['registrant']) ?></div></div><?php endif; ?>
            <?php if (isset($geo['handover']) && is_array($geo['handover'])): ?><div><small class="muted">Handover gestart</small><div><?= e((string) ($geo['handover']['op'] ?? '')) ?></div></div><?php endif; ?>
            <?php if ($info['authcode'] !== ''): ?><div><small class="muted">Verhuiskode (EPP)</small><div class="mono"><?= e($info['authcode']) ?></div></div><?php endif; ?>
        </div>
    </div>
    <form method="post" action="<?= $act('ns') ?>"><?= csrf_field() ?>
        <div class="card-body" style="border-top:1px solid var(--line)">
            <small class="muted" style="display:block;margin-bottom:8px">Nameservers (minimaal 2)</small>
            <div class="form-grid">
                <?php foreach ($nsList as $i => $ns): ?>
                    <?= field('ns' . ($i + 1), 'ns' . ($i + 1), 'server', (string) $ns) ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-foot" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
            <button class="btn btn-sm"><?= icon('check', 'icon icon-sm') ?> Nameservers opslaan</button>
            <span style="flex:1"></span>
    </form>
            <form method="post" action="<?= $act('epp') ?>" style="display:inline"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('lock', 'icon icon-sm') ?> EPP-code opvragen</button></form>
            <form method="post" action="<?= $act('handover') ?>" style="display:inline" data-confirm="Handover starten? De verhuiskode wordt opgehaald en automatisch verlengen gaat uit."><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('handshake', 'icon icon-sm') ?> Handover</button></form>
            <?php if ((string) $service->Status !== 'opgezegd'): ?>
                <form method="post" action="<?= $act('opzeggen') ?>" style="display:inline" data-confirm="Domein opzeggen? Het domein loopt af op de verloopdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
            <?php else: ?>
                <form method="post" action="<?= $act('hervatten') ?>" style="display:inline" data-confirm="Opzegging terugdraaien en automatisch verlengen weer aanzetten?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
            <?php endif; ?>
        </div>
</div>
