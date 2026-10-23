<?php $extend('klant', ['title' => 'Overzicht']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('dashboard') ?></div><div class="ph-text"><h1>Welkom, <?= e(auth_user()?->Voornaam) ?></h1><div class="sub">Hier zie je je diensten, facturen en supportvragen.</div></div><a class="btn" href="<?= e(url('/diensten')) ?>"><?= icon('cart', 'icon icon-sm') ?> Nieuwe dienst bestellen</a></div>
<div class="grid grid-4" style="margin-bottom:16px">
    <a class="card stat is-ok" href="<?= e(url('/klant/diensten')) ?>" style="color:inherit"><div class="stat-icon"><?= icon('server') ?></div><div><div class="stat-value"><?= $activeCount ?></div><div class="stat-label">Actieve diensten</div></div></a>
    <a class="card stat <?= $openAmount > 0 ? 'is-warn' : '' ?>" href="<?= e(url('/klant/facturen')) ?>" style="color:inherit"><div class="stat-icon"><?= icon('wallet') ?></div><div><div class="stat-value"><?= e(money($openAmount)) ?></div><div class="stat-label">Openstaand</div></div></a>
    <a class="card stat" href="<?= e(url('/klant/offertes')) ?>" style="color:inherit"><div class="stat-icon"><?= icon('quote') ?></div><div><div class="stat-value"><?= $quoteCount ?></div><div class="stat-label">Openstaande offertes</div></div></a>
    <a class="card stat" href="<?= e(url('/klant/tickets')) ?>" style="color:inherit"><div class="stat-icon"><?= icon('ticket') ?></div><div><div class="stat-value"><?= $ticketCount ?></div><div class="stat-label">Open tickets</div></div></a>
</div>
<div class="grid grid-2">
    <div class="card"><div class="card-head"><h2><?= icon('server') ?> Mijn diensten</h2><a href="<?= e(url('/klant/diensten')) ?>" class="small">Alles bekijken</a></div>
        <?= $partial('klant._services', ['services' => $services]) ?></div>
    <div class="card"><div class="card-head"><h2><?= icon('file') ?> Recente facturen</h2><a href="<?= e(url('/klant/facturen')) ?>" class="small">Alles bekijken</a></div>
        <?= $partial('klant._documents', ['docs' => $invoices]) ?></div>
</div>
