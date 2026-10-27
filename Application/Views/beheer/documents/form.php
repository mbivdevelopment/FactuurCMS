<?php $isQ = $doc->Soort === 'offerte'; $new = !$doc->id(); $label = $isQ ? 'offerte' : 'factuur';
$extend('beheer', ['title' => $new ? 'Nieuwe ' . $label : ucfirst($label) . ' ' . $doc->Nummer]);
$custOpts = ['' => 'Kies een klant…']; foreach ($customers as $c) { $custOpts[$c->id()] = $c->display() . ' (' . $c->Nummer . ')' . ($c->Rol !== 'klant' ? ' · ' . \App\Models\User::ROLES[$c->Rol] : ''); }
$action = $new ? ($isQ ? '/beheer/offertes' : '/beheer/facturen') : '/beheer/documenten/' . $doc->id(); ?>
<div class="page-head"><div class="ph-icon"><?= icon($isQ ? 'quote' : 'file') ?></div><div class="ph-text"><h1><?= e($new ? 'Nieuwe ' . $label : ucfirst($label) . ' ' . $doc->Nummer . ' bewerken') ?></h1></div><a class="btn btn-ghost" href="<?= e(url($new ? $action : $action)) ?>">Annuleren</a></div>
<form method="post" action="<?= e(url($action)) ?>" data-document><?= csrf_field() ?>
    <div class="card" style="margin-bottom:16px"><div class="card-body form-grid">
        <?= field('KlantId', 'Klant', 'user', $doc->KlantId, 'select', $custOpts, 'required') ?>
        <?= field('Onderwerp', 'Onderwerp', 'tag', $doc->Onderwerp) ?>
        <?= field('Datum', 'Datum', 'calendar', $doc->Datum, 'date', [], 'required') ?>
        <?= field('VervalDatum', $isQ ? 'Geldig tot' : 'Vervaldatum', 'calendar', $doc->VervalDatum, 'date') ?>
    </div></div>
    <div class="card" style="margin-bottom:16px">
        <div class="card-head"><h2><?= icon('list') ?> Regels</h2>
            <select data-product-pick><option value="">+ Product toevoegen…</option><?php foreach ($products as $p): ?><option value="<?= $p->id() ?>" data-name="<?= e($p->Naam . ($p->Periode !== 'eenmalig' ? ' (' . period_label((string) $p->Periode) . ')' : '')) ?>" data-price="<?= e(cents_input((int) $p->Prijs)) ?>" data-vat="<?= e((float) $p->BtwTarief) ?>"><?= e($p->Groep . ' · ' . $p->Naam . ' · ' . money((int) $p->Prijs)) ?></option><?php endforeach; ?></select></div>
        <?php if ($e = error('Regels')): ?><div class="alert alert-error" style="margin:12px"><?= e($e) ?></div><?php endif; ?>
        <div class="table-wrap"><table class="table lines"><thead><tr><th style="width:50%">Omschrijving</th><th>Aantal</th><th>Prijs (€)</th><th>Btw %</th><th class="right">Totaal</th><th></th></tr></thead>
            <tbody data-lines>
            <?php foreach ($lines as $i => $l): ?><tr data-line>
                <td><input name="Regels[<?= $i ?>][Omschrijving]" value="<?= e($l['Omschrijving']) ?>" placeholder="Omschrijving"></td>
                <td><input name="Regels[<?= $i ?>][Aantal]" value="<?= e(rtrim(rtrim(number_format((float) $l['Aantal'], 2, '.', ''), '0'), '.')) ?>" data-qty inputmode="decimal"></td>
                <td><input name="Regels[<?= $i ?>][Prijs]" value="<?= e(cents_input((int) $l['Prijs'])) ?>" data-price inputmode="decimal"></td>
                <td><input name="Regels[<?= $i ?>][BtwTarief]" value="<?= e((float) $l['BtwTarief']) ?>" data-vat inputmode="decimal"></td>
                <td class="right nowrap" data-line-total></td>
                <td><button type="button" class="btn btn-ghost btn-sm" data-remove-line><?= icon('trash', 'icon icon-sm') ?></button></td>
            </tr><?php endforeach; ?>
            </tbody></table></div>
        <div class="card-body row between"><button type="button" class="btn btn-ghost btn-sm" data-add-line><?= icon('plus', 'icon icon-sm') ?> Regel toevoegen</button>
            <div class="totals"><div><span>Subtotaal</span><span data-subtotal>€ 0,00</span></div><div><span>Btw</span><span data-vat-total>€ 0,00</span></div><div class="grand"><span>Totaal</span><span data-total>€ 0,00</span></div></div></div>
    </div>
    <div class="card"><div class="card-body"><?= field('Opmerking', 'Opmerking (komt op het document)', 'note', $doc->Opmerking, 'textarea') ?></div>
        <div class="card-foot"><button class="btn btn-ghost" name="opslaan" value="1"><?= icon('check', 'icon icon-sm') ?> Opslaan als concept</button><button class="btn" name="versturen" value="1"><?= icon('send', 'icon icon-sm') ?> Opslaan &amp; versturen</button></div></div>
</form>
