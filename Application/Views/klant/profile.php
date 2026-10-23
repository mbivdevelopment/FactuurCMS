<?php $extend('klant', ['title' => 'Mijn gegevens']); ?>
<div class="page-head"><h1><?= icon('user') ?> Mijn gegevens</h1></div>
<form class="card" method="post" action="<?= e(url('/klant/profiel')) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Voornaam', 'Voornaam', 'user', $user->Voornaam, 'text', [], 'required') ?>
        <?= field('Achternaam', 'Achternaam', 'user', $user->Achternaam, 'text', [], 'required') ?>
        <?= field('Bedrijfsnaam', 'Bedrijfsnaam', 'building', $user->Bedrijfsnaam) ?>
        <?= field('Email', 'E-mailadres', 'mail', $user->Email, 'email', [], 'required') ?>
        <?= field('Telefoon', 'Telefoon', 'phone', $user->Telefoon) ?>
        <?= field('Adres', 'Adres', 'map', $user->Adres) ?>
        <?= field('Postcode', 'Postcode', 'map', $user->Postcode) ?>
        <?= field('Plaats', 'Plaats', 'map', $user->Plaats) ?>
        <?= field('KvkNummer', 'KvK-nummer', 'hash', $user->KvkNummer) ?>
        <?= field('BtwNummer', 'Btw-nummer', 'hash', $user->BtwNummer) ?>
        <?= field('Wachtwoord', 'Nieuw wachtwoord (optioneel)', 'lock', '', 'password', [], 'autocomplete="new-password"') ?>
        <?= field('Wachtwoord_confirmation', 'Herhaal wachtwoord', 'lock', '', 'password') ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
