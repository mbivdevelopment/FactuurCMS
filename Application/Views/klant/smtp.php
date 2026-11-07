<?php $extend('klant', ['title' => dienst_naam($service)]); ?>
<?php $geo = is_array($service->Gegevens) ? $service->Gegevens : (json_decode((string) $service->Gegevens, true) ?: []); $smtp = (array) ($geo['smtp'] ?? []);
$host = $smtp['host'] ?? setting('smtp.relay_host', 'smtp.qevanto.nl'); $poort = (int) ($smtp['poort'] ?? 587); $user = $smtp['gebruiker'] ?? ($geo['domein'] ?? 'relay-' . strtolower((string) $service->Nummer)); $pass = (string) ($smtp['wachtwoord'] ?? ''); $limiet = (int) ($smtp['limiet'] ?? 10000); ?>
<div class="page-head"><div class="ph-icon"><?= icon('send') ?></div><div class="ph-text"><h1><?= e(dienst_naam($service)) ?></h1><div class="sub"><?= e($service->Nummer) ?> · <?= badge((string) $service->Status, \App\Models\Service::STATUSES[$service->Status] ?? '') ?></div></div><div class="row" style="display:flex;gap:8px;align-items:center"><a class="btn btn-ghost" href="<?= e(url('/klant/diensten/' . $service->id())) ?>">Terug</a></div></div>
<div class="grid grid-2">
    <div class="card"><div class="card-body">
        <h3 style="margin-top:0">Verzendgegevens</h3>
        <div class="stack">
            <div><small class="muted">SMTP-host</small><div class="mono"><?= e((string) $host) ?></div></div>
            <div><small class="muted">Poort</small><div class="mono"><?= $poort ?> <span class="muted">(STARTTLS · 465 voor SSL)</span></div></div>
            <div><small class="muted">Gebruikersnaam</small><div class="mono"><?= e((string) $user) ?></div></div>
            <div><small class="muted">Wachtwoord</small><div class="mono"><?= $pass !== '' ? e($pass) : '<span class="muted">Nog niet ingesteld</span>' ?></div></div>
            <div><small class="muted">Limiet</small><div><?= number_format($limiet, 0, ',', '.') ?> e-mails per maand</div></div>
            <?php if (($geo['domein'] ?? '') !== ''): ?><div><small class="muted">Domeinnaam</small><div><?= e($geo['domein']) ?></div></div><?php endif; ?>
        </div>
        <form method="post" action="<?= e(url('/klant/diensten/' . $service->id() . '/smtp/wachtwoord')) ?>" style="margin-top:14px"><?= csrf_field() ?><button class="btn btn-sm"><?= icon('refresh', 'icon icon-sm') ?> Nieuw wachtwoord genereren</button></form>
    </div></div>
    <div class="card"><div class="card-body">
        <h3 style="margin-top:0">Instellen &amp; DNS</h3>
        <div class="stack">
            <p class="muted" style="margin:0">Gebruik deze gegevens in je website, applicatie of e-mailprogramma om via de relay te versturen. Authenticatie is verplicht; versturen zonder inloggen wordt geweigerd.</p>
            <div><small class="muted">SPF-record</small><div class="mono small">v=spf1 include:spf.qevanto.nl ~all</div></div>
            <div><small class="muted">DKIM</small><div>Zet onze DKIM-selector op je domein; activering gaat via een ticket.</div></div>
            <div><small class="muted">DMARC</small><div class="mono small">v=DMARC1; p=quarantine</div></div>
            <p class="muted" style="margin:0">Hulp nodig bij de DNS-instellingen? Open een ticket, dan regelen wij het.</p>
            <div class="row"><a class="btn btn-sm btn-ghost" href="<?= e(url('/klant/tickets/nieuw')) ?>"><?= icon('ticket', 'icon icon-sm') ?> Ticket openen</a></div>
        </div>
    </div></div>
</div>
