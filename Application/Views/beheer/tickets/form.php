<?php $extend('beheer', ['title' => 'Nieuw ticket']); $opts = ['' => 'Kies een klant…']; foreach ($customers as $c) { $opts[$c->id()] = $c->display() . ' (' . $c->Nummer . ')' . ($c->Rol !== 'klant' ? ' · ' . \App\Models\User::ROLES[$c->Rol] : ''); } ?>
<div class="page-head"><div class="ph-icon"><?= icon('ticket') ?></div><div class="ph-text"><h1>Nieuw ticket</h1></div><a class="btn btn-ghost" href="<?= e(url('/beheer/tickets')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url('/beheer/tickets')) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('KlantId', 'Klant', 'user', $klantId ?: '', 'select', $opts, 'required') ?>
        <?= field('Onderwerp', 'Onderwerp', 'tag', '', 'text', [], 'required') ?>
        <?= field('Afdeling', 'Afdeling', 'users', 'Support', 'select', array_combine(\App\Models\Ticket::DEPARTMENTS, \App\Models\Ticket::DEPARTMENTS)) ?>
        <?= field('Prioriteit', 'Prioriteit', 'zap', 'normaal', 'select', \App\Models\Ticket::PRIORITIES) ?>
        <?= field('Bericht', 'Bericht aan klant', 'note', '', 'textarea', [], 'required rows="6"') ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('send', 'icon icon-sm') ?> Aanmaken &amp; versturen</button></div>
</form>
