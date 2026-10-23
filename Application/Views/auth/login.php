<?php $extend('auth', ['title' => 'Inloggen']); ?>
<h1>Inloggen</h1>
<p class="muted">Log in op je klantportaal of het beheer.</p>
<form method="post" action="<?= e(url('/login')) ?>" class="stack">
    <?= csrf_field() ?>
    <?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required autofocus autocomplete="username"') ?>
    <?= field('Wachtwoord', 'Wachtwoord', 'lock', '', 'password', [], 'required autocomplete="current-password"') ?>
    <div class="row between"><a href="<?= e(url('/wachtwoord-vergeten')) ?>" class="small">Wachtwoord vergeten?</a><button class="btn" type="submit"><?= icon('login', 'icon icon-sm') ?> Inloggen</button></div>
</form>
<p class="small muted" style="margin-top:20px;text-align:center">Nog geen account? <a href="<?= e(url('/registreren')) ?>">Account aanmaken</a></p>
