<?php $extend('klant', ['title' => 'Ticket ' . $ticket->Nummer]); ?>
<div class="page-head"><div><h1><?= icon('ticket') ?> <?= e($ticket->Onderwerp) ?></h1><div class="sub"><?= e($ticket->Nummer) ?> · <?= e($ticket->Afdeling) ?> · <?= badge((string) $ticket->Status, \App\Models\Ticket::STATUSES[$ticket->Status] ?? '') ?></div></div><a class="btn btn-ghost" href="<?= e(url('/klant/tickets')) ?>">Terug</a></div>
<div class="card"><div class="card-body"><?= $partial('shared.thread', ['messages' => $messages]) ?></div>
    <form method="post" action="<?= e(url('/klant/tickets/' . $ticket->id())) ?>" class="card-body" style="border-top:1px solid var(--line)"><?= csrf_field() ?>
        <?= field('Bericht', 'Jouw reactie', 'chat', '', 'textarea', [], 'required') ?>
        <div class="row" style="justify-content:flex-end;margin-top:12px"><button class="btn"><?= icon('send', 'icon icon-sm') ?> Reageren</button></div>
    </form></div>
