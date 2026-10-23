<?php $extend('auth', ['title' => 'Account aanmaken']); ?>
<h1>Account aanmaken</h1>
<form method="post" action="<?= e(url('/registreren')) ?>" class="stack">
    <?= csrf_field() ?>
    <div class="form-grid">
        <?= field('Voornaam', 'Voornaam', 'user', '', 'text', [], 'required') ?>
        <?= field('Achternaam', 'Achternaam', 'user', '', 'text', [], 'required') ?>
    </div>
    <?= field('Bedrijfsnaam', 'Bedrijfsnaam (optioneel)', 'building') ?>
    <?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required') ?>
    <?= field('Telefoon', 'Telefoon', 'phone') ?>
    <div class="form-grid">
        <?= field('Wachtwoord', 'Wachtwoord', 'lock', '', 'password', [], 'required minlength="8"') ?>
        <?= field('Wachtwoord_confirmation', 'Herhaal wachtwoord', 'lock', '', 'password', [], 'required') ?>
    </div>
    <button class="btn" type="submit" style="width:100%;justify-content:center"><?= icon('check', 'icon icon-sm') ?> Account aanmaken</button>
</form>
<p class="small muted" style="margin-top:20px;text-align:center">Al een account? <a href="<?= e(url('/login')) ?>">Inloggen</a></p>
