<?php $new = !$member->id(); $extend('beheer', ['title' => $new ? 'Nieuwe medewerker' : $member->name()]); ?>
<div class="page-head"><h1><?= icon('shield') ?> <?= e($new ? 'Nieuwe medewerker' : $member->name()) ?></h1><a class="btn btn-ghost" href="<?= e(url('/beheer/medewerkers')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url($new ? '/beheer/medewerkers' : '/beheer/medewerkers/' . $member->id())) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Voornaam', 'Voornaam', 'user', $member->Voornaam, 'text', [], 'required') ?>
        <?= field('Achternaam', 'Achternaam', 'user', $member->Achternaam) ?>
        <?= field('Email', 'E-mailadres', 'mail', $member->Email, 'email', [], 'required') ?>
        <?= field('Telefoon', 'Telefoon', 'phone', $member->Telefoon) ?>
        <?= field('Rol', 'Rol', 'shield', $member->Rol, 'select', ['medewerker' => 'Medewerker', 'beheerder' => 'Beheerder']) ?>
        <?= field('Wachtwoord', $new ? 'Wachtwoord' : 'Nieuw wachtwoord (optioneel)', 'lock', '', 'password', [], ($new ? 'required ' : '') . 'autocomplete="new-password"') ?>
        <?php if (!$new): ?><label class="switch"><input type="checkbox" name="Actief" value="1" <?= $member->Actief ? 'checked' : '' ?>> Actief</label><?php endif; ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
<?php if (!$new && $member->id() !== auth_user()?->id()): ?><div style="margin-top:16px"><?= delete_button(url('/beheer/medewerkers/' . $member->id()), 'Medewerker ' . $member->name() . ' verwijderen?') ?></div><?php endif; ?>
