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
$handover = is_array($geo['handover'] ?? null) ? $geo['handover'] : null;
$epp = $info['authcode'] !== '' ? $info['authcode'] : (string) ($handover['code'] ?? '');
$nsList = array_values($info['nameservers']);
while (count($nsList) < 4) {
    $nsList[] = '';
}
$dial = (string) ($_GET['dial'] ?? '');
$opgezegd = (string) $service->Status === 'opgezegd';
?>
<div class="card">
    <div class="card-head"><h3><?= icon('globe', 'icon icon-sm') ?> Domeinbeheer</h3><?= badge($info['autoRenew'] ? 'actief' : 'opgezegd', $info['autoRenew'] ? 'Verlengt automatisch' : 'Verlengt niet') ?></div>
    <div class="card-body">
        <div class="form-grid">
            <div><small class="muted">Domeinnaam</small><div><strong><?= e($domain) ?></strong></div></div>
            <?php if ($info['status'] !== ''): ?><div><small class="muted">Status bij registrar</small><div><?= e($info['status']) ?></div></div><?php endif; ?>
            <?php if ($info['expiry']): ?><div><small class="muted">Verloopdatum</small><div><?= e(nl_date($info['expiry'])) ?></div></div><?php endif; ?>
            <?php if ($info['registrant'] !== ''): ?><div><small class="muted">Houder</small><div><?= e($info['registrant']) ?></div></div><?php endif; ?>
            <?php if ($info['nameservers'] !== []): ?><div><small class="muted">Nameservers</small><div class="mono"><?= e(implode(', ', $info['nameservers'])) ?></div></div><?php endif; ?>
            <?php if ($handover !== null): ?><div><small class="muted">Handover gestart</small><div><?= e((string) ($handover['op'] ?? '')) ?></div></div><?php endif; ?>
        </div>
    </div>
    <div class="card-foot" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <button type="button" class="btn btn-sm" data-open="dom-ns-<?= (int) $service->id() ?>"><?= icon('server', 'icon icon-sm') ?> Nameservers</button>
        <button type="button" class="btn btn-sm btn-ghost" data-open="dom-epp-<?= (int) $service->id() ?>"><?= icon('lock', 'icon icon-sm') ?> EPP-code</button>
        <button type="button" class="btn btn-sm btn-ghost" data-open="dom-handover-<?= (int) $service->id() ?>"><?= icon('handshake', 'icon icon-sm') ?> Handover</button>
        <span style="flex:1"></span>
        <?php if (!$opgezegd): ?>
            <form method="post" action="<?= $act('opzeggen') ?>" style="display:inline" data-confirm="Domein opzeggen? Het domein loopt af op de verloopdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
        <?php else: ?>
            <form method="post" action="<?= $act('hervatten') ?>" style="display:inline" data-confirm="Opzegging terugdraaien en automatisch verlengen weer aanzetten?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
        <?php endif; ?>
    </div>
</div>

<dialog class="confirm" id="dom-ns-<?= (int) $service->id() ?>">
    <div class="modal-h"><?= icon('server', 'icon icon-sm') ?> Nameservers — <?= e($domain) ?></div>
    <form method="post" action="<?= $act('ns') ?>" style="padding:18px 18px 0"><?= csrf_field() ?>
        <div class="form-grid" style="gap:10px 16px">
            <?php foreach ($nsList as $i => $ns): ?>
                <?= field('ns' . ($i + 1), 'ns' . ($i + 1), 'server', (string) $ns) ?>
            <?php endforeach; ?>
        </div>
        <small class="muted" style="display:block;margin-top:8px">Minimaal 2 nameservers; lege velden worden genegeerd.</small>
        <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
    </form>
</dialog>

<dialog class="confirm" id="dom-epp-<?= (int) $service->id() ?>">
    <div class="modal-h"><?= icon('lock', 'icon icon-sm') ?> Verhuiskode (EPP) — <?= e($domain) ?></div>
    <?php if ($epp !== ''): ?>
        <p>De verhuiskode voor <strong><?= e($domain) ?></strong>:</p>
        <p class="mono" style="font-size:15px;font-weight:700;padding-top:10px"><?= e($epp) ?></p>
        <form method="post" action="<?= $act('epp') ?>"><?= csrf_field() ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Sluiten</button><button class="btn btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Opnieuw ophalen</button></div>
        </form>
    <?php else: ?>
        <p>Haal de verhuiskode (EPP/auth-code) op bij de registrar. Deze heb je nodig om het domein naar een andere provider te verhuizen.</p>
        <form method="post" action="<?= $act('epp') ?>"><?= csrf_field() ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn"><?= icon('lock', 'icon icon-sm') ?> Code opvragen</button></div>
        </form>
    <?php endif; ?>
</dialog>

<dialog class="confirm" id="dom-handover-<?= (int) $service->id() ?>">
    <div class="modal-h"><?= icon('handshake', 'icon icon-sm') ?> Handover — <?= e($domain) ?></div>
    <?php if ($handover !== null): ?>
        <p>De handover is gestart. Geef deze verhuiskode aan de nieuwe provider:</p>
        <?php if ((string) ($handover['code'] ?? '') !== ''): ?><p class="mono" style="font-size:15px;font-weight:700;padding-top:10px"><?= e((string) $handover['code']) ?></p><?php endif; ?>
        <p><small class="muted">Automatisch verlengen staat uit<?= $info['expiry'] ? '; het domein loopt af op ' . e(nl_date($info['expiry'])) : '' ?>.</small></p>
        <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Sluiten</button></div>
    <?php else: ?>
        <p>Een handover verhuist het domein naar een andere provider. De verhuiskode wordt opgehaald en automatisch verlengen gaat uit.</p>
        <form method="post" action="<?= $act('handover') ?>"><?= csrf_field() ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn btn-danger"><?= icon('handshake', 'icon icon-sm') ?> Handover starten</button></div>
        </form>
    <?php endif; ?>
</dialog>
<?php if (in_array($dial, ['epp', 'handover'], true)): ?>
<script>document.getElementById('dom-<?= e($dial) ?>-<?= (int) $service->id() ?>')?.showModal();</script>
<?php endif; ?>
