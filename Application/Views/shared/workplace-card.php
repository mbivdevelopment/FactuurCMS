<?php
// Werkplekbeheer-kaart: pakketinfo, koppelcode en de werkplekken (Qevanto /workplaces API).
// Verwacht: $service (App\Models\Service) en $base ('/klant' of '/beheer').
if (!$service->isWorkplace()) {
    return;
}
$geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []);
$qv = (array) ($geo['qevanto'] ?? []);
$data = null;
if ((string) $service->Bron === 'qevanto' && (int) $service->ExternId > 0) {
    try {
        $data = \App\Services\QevantoPaneel::workplaces((int) $service->ExternId);
    } catch (\Throwable) {
    }
}
$wps = (array) ($data['workplaces'] ?? []);
$code = (string) ($data['code'] ?? '');
$install = (string) ($data['install'] ?? '');
$limit = $data['limit'] ?? null;
$online = count(array_filter($wps, static fn ($w): bool => !empty($w['online'])));
?>
<div class="card">
    <div class="card-head"><h3><?= icon('lock', 'icon icon-sm') ?> Koppelcode</h3><?php if ((string) $service->Bron === 'qevanto' && (int) $service->ExternId > 0): ?><form method="post" action="<?= e(url($base . '/diensten/' . $service->id() . '/werkplek/code')) ?>"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Nieuwe code</button></form><?php endif; ?></div>
    <div class="card-body">
        <?php if ($code !== ''): ?>
            <div class="form-grid">
                <div><small class="muted">Koppelcode</small><div class="mono" style="font-size:16px;font-weight:700"><?= e($code) ?></div></div>
                <?php if ($limit !== null): ?><div><small class="muted">Werkplekken</small><div><?= count($wps) ?> / <?= (int) $limit === 0 ? 'onbeperkt' : e((string) $limit) ?></div></div><?php endif; ?>
            </div>
            <?php if ($install !== ''): ?><div style="margin-top:12px"><small class="muted">Installeren op een Windows-werkplek (PowerShell)</small><div class="mono" style="background:#f6f7f9;border:1px solid var(--line);border-radius:8px;padding:10px 12px;font-size:12.5px;overflow-x:auto"><?= e($install) ?></div></div><?php endif; ?>
        <?php elseif ((string) $service->Bron === 'qevanto'): ?>
            <p class="muted" style="margin:0">De koppelcode kon niet bij het paneel worden opgehaald.</p>
        <?php else: ?>
            <p class="muted" style="margin:0">Deze dienst is niet aan het hostingpaneel gekoppeld.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-head"><h3><?= icon('headset', 'icon icon-sm') ?> Werkplekken</h3><span class="muted" style="font-size:12.5px"><?= count($wps) ?> werkplek(ken)<?= $wps !== [] ? ' · ' . $online . ' online' : '' ?></span></div>
    <?php if ($wps === []): ?>
        <div class="card-body"><p class="muted" style="margin:0">Nog geen werkplekken gekoppeld. Gebruik de koppelcode op een werkplek om hem te verbinden.</p></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Nummer</th><th>Werkplek</th><th>Besturingssysteem</th><th>Gebruiker</th><th>IP-adres</th><th>Laatst gezien</th><th style="text-align:right">Status</th></tr></thead>
            <tbody>
            <?php foreach ($wps as $w): ?>
                <tr>
                    <td><span class="mono"><?= e((string) ($w['number'] ?? '')) ?></span></td>
                    <td><strong><?= e((string) ($w['name'] ?: $w['hostname'] ?: '—')) ?></strong><?php if ((string) ($w['hostname'] ?? '') !== '' && (string) $w['hostname'] !== (string) $w['name']): ?><div><small class="muted"><?= e((string) $w['hostname']) ?></small></div><?php endif; ?></td>
                    <td><?= e((string) ($w['os'] ?? '—')) ?></td>
                    <td><?= e((string) ($w['user'] ?? '') ?: '—') ?></td>
                    <td><span class="mono"><?= e((string) ($w['publicIp'] ?? $w['ip'] ?? '—')) ?></span></td>
                    <td><small class="muted"><?= !empty($w['lastSeenAt']) ? e(date('d-m-Y H:i', strtotime((string) $w['lastSeenAt']))) : '—' ?></small></td>
                    <td style="text-align:right"><?= !empty($w['online']) ? badge('Online', 'is-good') : badge('Offline', 'is-bad') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head"><h3><?= icon('server', 'icon icon-sm') ?> Abonnement</h3></div>
    <div class="card-body">
        <div class="form-grid">
            <?php if ((string) ($qv['nummer'] ?? '') !== ''): ?><div><small class="muted">Abonnement</small><div><strong><?= e((string) $qv['nummer']) ?></strong></div></div><?php endif; ?>
            <?php if ((string) ($qv['pakket'] ?? '') !== ''): ?><div><small class="muted">Pakket</small><div><?= e((string) $qv['pakket']) ?></div></div><?php endif; ?>
            <?php if ((string) ($data['version'] ?? '') !== ''): ?><div><small class="muted">Agent-versie</small><div><?= e((string) $data['version']) ?></div></div><?php endif; ?>
        </div>
    </div>
</div>
