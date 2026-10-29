<?php
// Verwacht: $service (App\Models\Service) en $base ('/klant' of '/beheer')
$domain = $service->domainName();
if ($domain === '') {
    return;
}
$geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
$info = ['nameservers' => (array) ($geo['nameservers'] ?? []), 'authcode' => (string) ($geo['epp'] ?? ''), 'status' => '', 'expiry' => null, 'autoRenew' => (bool) ($geo['realtimeregister']['autoRenew'] ?? true)];
$live = false;
try {
    if (\App\Services\RealtimeRegister::configured()) {
        $live = \App\Services\RealtimeRegister::info($domain);
        $info = array_merge($info, $live);
    }
} catch (\Throwable) {
}
$act = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/domein/' . $a));
$nsValue = implode("\n", $info['nameservers']);
?>
<div class="card"><div class="card-body">
    <h3 style="margin-top:0"><?= icon('globe', 'icon icon-sm') ?> Domeinbeheer</h3>
    <div class="stack">
        <div><small class="muted">Domeinnaam</small><div><?= e($domain) ?></div></div>
        <?php if ($info['status'] !== ''): ?><div><small class="muted">Status bij registrar</small><div><?= e($info['status']) ?></div></div><?php endif; ?>
        <?php if ($info['expiry']): ?><div><small class="muted">Verloopdatum</small><div><?= e(nl_date($info['expiry'])) ?></div></div><?php endif; ?>
        <div><small class="muted">Automatisch verlengen</small><div><?= badge($info['autoRenew'] ? 'actief' : 'opgezegd', $info['autoRenew'] ? 'Aan' : 'Uit') ?></div></div>
        <?php if (isset($geo['handover']) && is_array($geo['handover'])): ?><div><small class="muted">Handover gestart</small><div><?= e((string) ($geo['handover']['op'] ?? '')) ?></div></div><?php endif; ?>
    </div>
    <form method="post" action="<?= $act('ns') ?>" style="margin-top:12px"><?= csrf_field() ?>
        <label class="lbl"><small class="muted">Nameservers (één per regel, min. 2)</small>
            <textarea name="ns" class="inp mono" rows="4" placeholder="ns1.voorbeeld.nl&#10;ns2.voorbeeld.nl"><?= e($nsValue) ?></textarea>
        </label>
        <div class="row" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
            <button class="btn btn-sm"><?= icon('check', 'icon icon-sm') ?> Nameservers opslaan</button>
        </div>
    </form>
    <?php if ($info['authcode'] !== ''): ?>
        <div style="margin-top:12px"><small class="muted">Verhuiskode (EPP)</small><div class="mono"><?= e($info['authcode']) ?></div></div>
    <?php endif; ?>
    <div class="row" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
        <form method="post" action="<?= $act('epp') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('lock', 'icon icon-sm') ?> EPP-code opvragen</button></form>
        <form method="post" action="<?= $act('handover') ?>" data-confirm="Handover starten? De verhuiskode wordt opgehaald en automatisch verlengen gaat uit."><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Handover</button></form>
        <?php if ((string) $service->Status !== 'opgezegd'): ?>
            <form method="post" action="<?= $act('opzeggen') ?>" data-confirm="Domein opzeggen? Het domein loopt af op de verloopdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
        <?php else: ?>
            <form method="post" action="<?= $act('hervatten') ?>" data-confirm="Opzegging terugdraaien en automatisch verlengen weer aanzetten?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
        <?php endif; ?>
    </div>
</div></div>
