<?php $extend('klant', ['title' => 'Nieuw ticket']); $opts = ['' => 'Geen specifieke dienst']; foreach ($services as $s) { $opts[$s->id()] = $s->Naam . ($s->Omschrijving ? ' - ' . $s->Omschrijving : ''); } ?>
<div class="page-head"><div class="ph-icon"><?= icon('ticket') ?></div><div class="ph-text"><h1>Nieuw ticket</h1></div></div>
<form class="card" method="post" action="<?= e(url('/klant/tickets')) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Onderwerp', 'Onderwerp', 'tag', '', 'text', [], 'required') ?>
        <?= field('Afdeling', 'Afdeling', 'users', 'Support', 'select', array_combine(\App\Models\Ticket::DEPARTMENTS, \App\Models\Ticket::DEPARTMENTS)) ?>
        <?= field('DienstId', 'Dienst', 'server', '', 'select', $opts) ?>
        <?= field('Prioriteit', 'Prioriteit', 'zap', 'normaal', 'select', \App\Models\Ticket::PRIORITIES) ?>
        <?= field('Bericht', 'Bericht', 'note', '', 'textarea', [], 'required rows="6"') ?>
    </div>
    <div class="card-foot"><a class="btn btn-ghost" href="<?= e(url('/klant/tickets')) ?>">Annuleren</a><button class="btn"><?= icon('send', 'icon icon-sm') ?> Versturen</button></div>
</form>
