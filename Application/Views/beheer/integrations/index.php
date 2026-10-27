<?php $extend('beheer', ['title' => 'Koppelingen']);
$cards = [
    ['realtimeregister', 'globe', 'Realtime Register', 'Domeinen importeren als dienst en extensies met inkoopprijs als product.'],
    ['qevanto', 'server', 'Qevanto hostingpaneel', 'Pakketten en abonnementen uit je hostingpaneel importeren, met SSO naar het paneel.'],
    ['mollie', 'wallet', 'Mollie', 'Klanten betalen facturen met iDEAL, kaart en andere methoden.'],
    ['cryptomus', 'zap', 'Cryptomus', 'Klanten betalen facturen met crypto via een webhook.'],
]; ?>
<div class="page-head"><div class="ph-icon"><?= icon('link') ?></div><div class="ph-text"><h1>Koppelingen</h1><div class="sub">Domeinen, betalingen en hosting koppelen aan je facturatie</div></div></div>
<div class="int-grid">
    <?php foreach ($cards as [$key, $ico, $label, $desc]): $on = $set[$key] ?? false; ?>
        <a class="card int-card" href="<?= e(url('/beheer/koppelingen/' . $key)) ?>">
            <span class="int-ic"><?= icon($ico) ?></span>
            <span class="int-t">
                <strong><?= e($label) ?></strong>
                <small><?= e($desc) ?></small>
            </span>
            <span class="int-r">
                <?= badge($on ? 'actief' : 'concept', $on ? 'Ingesteld' : 'Niet ingesteld') ?>
                <span class="int-arrow"><?= icon('chevron-right', 'icon') ?></span>
            </span>
        </a>
    <?php endforeach; ?>
</div>
