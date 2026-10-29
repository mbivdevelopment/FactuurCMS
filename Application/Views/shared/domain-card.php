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
        <p>De handover is gestart voor <strong><?= e((string) ($handover['email'] ?? '')) ?></strong>. Die klant heeft een mail met overnamecode gekregen; zodra die bevestigt, staat het domein in zijn account.</p>
        <p><small class="muted">Gestart op <?= e((string) ($handover['op'] ?? '')) ?> · code <?= e((string) ($handover['code'] ?? '')) ?></small></p>
        <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Sluiten</button></div>
    <?php else: ?>
        <p>Plaats het domein over naar een andere klant. Die klant krijgt een e-mail met een overnamecode om het domein in zijn account te zetten.</p>
        <form method="post" action="<?= $act('handover') ?>" style="padding:0 18px"><?= csrf_field() ?>
            <?= field('email', 'E-mailadres nieuwe klant', 'mail', '', 'email', [], 'required') ?>
            <div class="confirm-actions"><button type="button" class="btn btn-ghost" data-close>Annuleren</button><button class="btn"><?= icon('handshake', 'icon icon-sm') ?> Handover starten</button></div>
        </form>
    <?php endif; ?>
</dialog>
<?php if (in_array($dial, ['epp', 'handover'], true)): ?>
<script>document.getElementById('dom-<?= e($dial) ?>-<?= (int) $service->id() ?>')?.showModal();</script>
<?php endif; ?>
