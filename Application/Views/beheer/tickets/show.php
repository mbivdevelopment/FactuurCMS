<?php $extend('beheer', ['title' => 'Ticket ' . $ticket->Nummer]); $id = $ticket->id(); ?>
<div class="page-head"><div class="ph-icon"><?= icon('ticket') ?></div><div class="ph-text"><h1><?= e($ticket->Onderwerp) ?></h1><div class="sub"><?= e($ticket->Nummer) ?> · <?= e($ticket->Afdeling) ?> · <?= badge((string) $ticket->Status, \App\Models\Ticket::STATUSES[$ticket->Status] ?? '') ?></div></div><a class="btn btn-ghost" href="<?= e(url('/beheer/tickets')) ?>">Terug</a></div>
<div class="grid grid-side">
    <div class="card"><div class="card-body"><?= $partial('shared.thread', ['messages' => $messages]) ?></div>
        <form method="post" action="<?= e(url('/beheer/tickets/' . $id)) ?>" class="card-body" style="border-top:1px solid var(--line)"><?= csrf_field() ?>
            <?php $quick = \App\Core\Database::all('SELECT Titel, Tekst FROM SnelleAntwoorden ORDER BY Volgorde, Titel'); ?>
            <div class="quick-replies"><?= icon('chat', 'icon icon-sm') ?><select data-quick-reply="#f-Bericht" aria-label="Snel antwoord invoegen"><option value="">Snel antwoord invoegen…</option><?php foreach ($quick as $q): ?><option value="<?= e($q['Tekst']) ?>"><?= e($q['Titel']) ?></option><?php endforeach; ?></select><a class="small" href="<?= e(url('/beheer/snelle-antwoorden')) ?>">Beheren</a></div>
            <?= field('Bericht', 'Reactie', 'chat', '', 'textarea', [], 'required rows="5"') ?>
            <div class="row between" style="margin-top:12px"><div class="row"><label class="switch"><input type="checkbox" name="Intern" value="1"> Interne notitie</label><label class="switch"><input type="checkbox" name="Sluiten" value="1"> Ticket sluiten na reactie</label></div>
                <button class="btn"><?= icon('send', 'icon icon-sm') ?> Versturen</button></div>
        </form></div>
    <div class="stack">
        <div class="card"><div class="card-head"><h2><?= icon('user') ?> Klant</h2></div><div class="card-body"><?php if ($customer): ?>
            <dl class="details"><dt>Naam</dt><dd><a href="<?= e(url('/beheer/klanten/' . $customer->id())) ?>"><?= e($customer->display()) ?></a></dd><dt>E-mail</dt><dd><?= e($customer->Email) ?></dd><dt>Telefoon</dt><dd><?= e($customer->Telefoon ?: '-') ?></dd><?php if ($service): ?><dt>Dienst</dt><dd><a href="<?= e(url('/beheer/diensten/' . $service->id())) ?>"><?= e(dienst_naam($service)) ?></a><div class="small muted"><?= e(dienst_naam($service) !== (string) $service->Naam ? $service->Naam : $service->Omschrijving) ?></div></dd><?php endif; ?></dl><?php endif; ?></div></div>
        <form class="card" method="post" action="<?= e(url('/beheer/tickets/' . $id . '/status')) ?>"><?= csrf_field() ?>
            <div class="card-head"><h2><?= icon('settings') ?> Ticket</h2></div>
            <div class="card-body stack"><?= field('Status', 'Status', 'check', $ticket->Status, 'select', \App\Models\Ticket::STATUSES) ?><?= field('Prioriteit', 'Prioriteit', 'zap', $ticket->Prioriteit, 'select', \App\Models\Ticket::PRIORITIES) ?></div>
            <div class="card-foot"><button class="btn btn-sm">Opslaan</button></div></form>
        <div><?= delete_button(url('/beheer/tickets/' . $id), 'Ticket ' . $ticket->Nummer . ' verwijderen?') ?></div>
    </div>
</div>
