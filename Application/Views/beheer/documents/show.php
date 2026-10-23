<?php $isQ = $doc->isQuote(); $id = $doc->id(); $extend('beheer', ['title' => $doc->label() . ' ' . $doc->Nummer]); $statuses = $isQ ? \App\Models\Document::QUOTE_STATUSES : \App\Models\Document::INVOICE_STATUSES; ?>
<div class="page-head"><div><h1><?= icon($isQ ? 'quote' : 'file') ?> <?= e($doc->label() . ' ' . $doc->Nummer) ?></h1><div class="sub"><a href="<?= e(url('/beheer/klanten/' . $doc->KlantId)) ?>"><?= e($doc->customer()?->display()) ?></a><?= $doc->VerzondenOp ? ' · verzonden ' . e(nl_date($doc->VerzondenOp, true)) : '' ?></div></div>
    <div class="row">
        <a class="btn btn-ghost" href="<?= e(url('/beheer/documenten/' . $id . '/bewerken')) ?>"><?= icon('edit', 'icon icon-sm') ?> Bewerken</a>
        <a class="btn btn-ghost" target="_blank" href="<?= e(url('/beheer/documenten/' . $id . '/pdf')) ?>"><?= icon('download', 'icon icon-sm') ?> PDF</a>
        <form method="post" action="<?= e(url('/beheer/documenten/' . $id . '/kopie')) ?>"><?= csrf_field() ?><button class="btn btn-ghost"><?= icon('copy', 'icon icon-sm') ?> Kopie</button></form>
        <?php if ($isQ): ?><form method="post" action="<?= e(url('/beheer/documenten/' . $id . '/naar-factuur')) ?>" data-confirm="Factuur aanmaken op basis van deze offerte?"><?= csrf_field() ?><button class="btn btn-ghost"><?= icon('file', 'icon icon-sm') ?> Omzetten naar factuur</button></form>
        <?php elseif ((int) $doc->Totaal > 0 && $doc->Status !== 'gecrediteerd'): ?><form method="post" action="<?= e(url('/beheer/documenten/' . $id . '/crediteren')) ?>" data-confirm="Creditfactuur aanmaken voor deze factuur?"><?= csrf_field() ?><button class="btn btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Crediteren</button></form><?php endif; ?>
        <form method="post" action="<?= e(url('/beheer/documenten/' . $id . '/versturen')) ?>" data-confirm="<?= e($doc->label()) ?> per e-mail versturen naar <?= e($doc->customer()?->Email) ?>?"><?= csrf_field() ?><button class="btn"><?= icon('send', 'icon icon-sm') ?> Versturen</button></form>
    </div></div>
<div class="grid grid-side">
    <div><?= $partial('shared.document', ['doc' => $doc]) ?>
        <?php if ($credits !== []): ?><div class="alert alert-info" style="margin-top:16px">Gecrediteerd via <?php foreach ($credits as $c): ?><a href="<?= e(url('/beheer/documenten/' . $c->id())) ?>"><?= e($c->Nummer) ?></a> <?php endforeach; ?></div><?php endif; ?></div>
    <div class="stack">
        <form class="card" method="post" action="<?= e(url('/beheer/documenten/' . $id . '/status')) ?>"><?= csrf_field() ?>
            <div class="card-head"><h2><?= icon('check') ?> Status</h2></div>
            <div class="card-body"><?= field('Status', 'Status', 'check', $doc->Status, 'select', array_diff_key($statuses, ['vervallen' => 1, 'verlopen' => 1])) ?></div>
            <div class="card-foot"><button class="btn btn-sm">Wijzigen</button></div></form>
        <?php if (!$isQ && $doc->outstanding() > 0): ?>
        <form class="card" method="post" action="<?= e(url('/beheer/documenten/' . $id . '/betaling')) ?>"><?= csrf_field() ?>
            <div class="card-head"><h2><?= icon('wallet') ?> Betaling registreren</h2></div>
            <div class="card-body stack">
                <?= field('Bedrag', 'Bedrag (€)', 'euro', cents_input($doc->outstanding())) ?>
                <?= field('Datum', 'Datum', 'calendar', date('Y-m-d'), 'date') ?>
                <?= field('Notitie', 'Notitie', 'note', '', 'text', [], 'placeholder="bijv. bankoverschrijving"') ?>
            </div>
            <div class="card-foot"><button class="btn btn-sm"><?= icon('check', 'icon icon-sm') ?> Registreren</button></div></form>
        <?php endif; ?>
        <?php if ($doc->Status === 'concept' || auth_user()?->isAdmin()): ?><div><?= delete_button(url('/beheer/documenten/' . $id), $doc->label() . ' ' . $doc->Nummer . ' definitief verwijderen?') ?></div><?php endif; ?>
    </div>
</div>
