<?php $extend('beheer', ['title' => $label]);
$secret = static fn (string $name, string $label, bool $isSet): string => field($name, $label, 'lock', '', 'password', [], 'autocomplete="new-password"' . ($isSet ? ' placeholder="Opgeslagen; laat leeg om te behouden"' : ''));
$state = static fn (bool $on): string => badge($on ? 'actief' : 'concept', $on ? 'Ingesteld' : 'Niet ingesteld');
$test = static fn (string $name): string => '<button class="btn btn-ghost" formaction="' . e(url('/beheer/koppelingen/' . $name . '/test')) . '">' . icon('refresh', 'icon icon-sm') . ' Verbinding testen</button>'; ?>
<div class="page-head"><div class="ph-icon"><?= icon($icon) ?></div><div class="ph-text"><h1><?= e($label) ?></h1><div class="sub"><?= e($desc) ?></div></div>
    <a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen')) ?>"><?= icon('arrow-right', 'icon icon-sm') ?> Alle koppelingen</a></div>
<?php if ($name === 'realtimeregister'): ?>
    <form class="card" method="post" action="<?= e(url('/beheer/koppelingen/realtimeregister')) ?>"><?= csrf_field() ?>
        <div class="card-head"><h2><?= icon('globe') ?> Realtime Register</h2><?= $state($set['realtimeregister']) ?></div>
        <div class="card-body">
            <p class="integration-note">Importeer je domeinen als dienst bij een klant, en je extensies met inkoopprijs als product.</p>
            <div class="form-grid">
                <?= $secret('rr_apikey', 'API-sleutel', $set['realtimeregister']) ?>
                <?= field('rr_customer', 'Klanthandle (customer)', 'user', (string) setting('rr.customer', '')) ?>
                <?= field('rr_url', 'Omgeving', 'server', (string) setting('rr.url', \App\Services\RealtimeRegister::LIVE), 'select', [\App\Services\RealtimeRegister::LIVE => 'Live (api.yoursrs.com)', \App\Services\RealtimeRegister::OTE => 'Test / OT&E (api.yoursrs-ote.com)']) ?>
                <?= field('rr_marge', 'Marge op inkoopprijs (%)', 'trend', (string) setting('rr.marge', 30), 'number', [], 'min="0" max="500"') ?>
                <?= field('rr_contact', 'Eigen contacthandle (admin, tech, billing)', 'users', (string) setting('rr.contact', ''), 'text', [], 'placeholder="Leeg = de klant zelf"') ?>
                <?= field('rr_ns', 'Nameservers bij registratie', 'server', (string) setting('rr.ns', ''), 'text', [], 'placeholder="ns1.jouwdomein.nl, ns2.jouwdomein.nl"') ?>
                <label class="switch field-full"><input type="checkbox" name="rr_auto" value="1"<?= (string) setting('rr.auto', '1') === '1' ? ' checked' : '' ?>> Domeinen na betaling automatisch registreren of verhuizen</label>
            </div>
        </div>
        <div class="card-foot row">
            <button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button>
            <?php if ($set['realtimeregister']): ?><?= $test('realtimeregister') ?>
            <a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen/realtimeregister/domeinen')) ?>"><?= icon('download', 'icon icon-sm') ?> Domeinen importeren</a>
            <a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen/realtimeregister/extensies')) ?>"><?= icon('tag', 'icon icon-sm') ?> Extensies importeren</a><?php endif; ?>
        </div>
    </form>
<?php elseif ($name === 'qevanto'): ?>
    <form class="card" method="post" action="<?= e(url('/beheer/koppelingen/qevanto')) ?>"><?= csrf_field() ?>
        <div class="card-head"><h2><?= icon('server') ?> Qevanto hostingpaneel</h2><?= $state($set['qevanto']) ?></div>
        <div class="card-body">
            <p class="integration-note">Importeer de pakketten uit het paneel als producten, en de abonnementen (hosting, reseller, VPN en werkplekken) als diensten bij je klanten. Gebruik een API-sleutel (qv_…) van een beheerdersaccount.</p>
            <div class="form-grid">
                <?= field('qevanto_url', 'Adres van het paneel', 'globe', (string) setting('qevanto.url', ''), 'url', [], 'placeholder="https://hosting.qevanto.nl"') ?>
                <?= $secret('qevanto_token', 'API-sleutel', $set['qevanto']) ?>
            </div>
        </div>
        <div class="card-foot row">
            <button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button>
            <?php if ($set['qevanto']): ?><?= $test('qevanto') ?>
            <a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen/qevanto/pakketten')) ?>"><?= icon('box', 'icon icon-sm') ?> Pakketten importeren</a>
            <a class="btn btn-ghost" href="<?= e(url('/beheer/koppelingen/qevanto/diensten')) ?>"><?= icon('download', 'icon icon-sm') ?> Diensten importeren</a><?php endif; ?>
        </div>
    </form>
<?php elseif ($name === 'mollie'): ?>
    <form class="card" method="post" action="<?= e(url('/beheer/koppelingen/mollie')) ?>"><?= csrf_field() ?>
        <div class="card-head"><h2><?= icon('wallet') ?> Mollie</h2><?= $state($set['mollie']) ?></div>
        <div class="card-body">
            <p class="integration-note">Klanten betalen openstaande facturen in het klantportaal met iDEAL, kaart of een andere Mollie-methode. Is de betaling binnen, dan staat de factuur vanzelf op betaald. Met een sleutel die begint met <code>test_</code> test je zonder echt geld.</p>
            <div class="form-grid"><?= $secret('mollie_apikey', 'API-sleutel (live_… of test_…)', $set['mollie']) ?></div>
            <label class="switch" style="margin-top:12px"><input type="checkbox" name="mollie_actief" value="1" <?= setting('mollie.actief', false) ? 'checked' : '' ?>> Online betalen via Mollie aanzetten</label>
        </div>
        <div class="card-foot row"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button><?php if ($set['mollie']): ?><?= $test('mollie') ?><?php endif; ?></div>
    </form>
<?php elseif ($name === 'cryptomus'): ?>
    <form class="card" method="post" action="<?= e(url('/beheer/koppelingen/cryptomus')) ?>"><?= csrf_field() ?>
        <div class="card-head"><h2><?= icon('zap') ?> Cryptomus</h2><?= $state($set['cryptomus']) ?></div>
        <div class="card-body">
            <p class="integration-note">Klanten betalen facturen met crypto. De webhook wordt per betaling meegestuurd en met je API-sleutel gecontroleerd.</p>
            <div class="form-grid">
                <?= field('cryptomus_merchant', 'Merchant-ID', 'hash', (string) setting('cryptomus.merchant', '')) ?>
                <?= $secret('cryptomus_apikey', 'Payment API-sleutel', $set['cryptomus']) ?>
            </div>
            <label class="switch" style="margin-top:12px"><input type="checkbox" name="cryptomus_actief" value="1" <?= setting('cryptomus.actief', false) ? 'checked' : '' ?>> Betalen met crypto aanzetten</label>
        </div>
        <div class="card-foot row"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button><?php if ($set['cryptomus']): ?><?= $test('cryptomus') ?><?php endif; ?></div>
    </form>
<?php endif; ?>
