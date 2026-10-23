<?php $extend('auth', ['title' => 'Nieuw wachtwoord']); ?>
<h1>Nieuw wachtwoord</h1>
<form method="post" action="<?= e(url('/wachtwoord-herstellen/' . $token)) ?>" class="stack">
    <?= csrf_field() ?>
    <?= field('Wachtwoord', 'Nieuw wachtwoord', 'lock', '', 'password', [], 'required minlength="8"') ?>
    <?= field('Wachtwoord_confirmation', 'Herhaal wachtwoord', 'lock', '', 'password', [], 'required') ?>
    <button class="btn" type="submit"><?= icon('check', 'icon icon-sm') ?> Opslaan</button>
</form>
