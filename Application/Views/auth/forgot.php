<?php $extend('auth', ['title' => 'Wachtwoord vergeten']); ?>
<h1>Wachtwoord vergeten</h1>
<p class="muted">Vul je e-mailadres in; je ontvangt een link om een nieuw wachtwoord in te stellen.</p>
<form method="post" action="<?= e(url('/wachtwoord-vergeten')) ?>" class="stack">
    <?= csrf_field() ?>
    <?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required') ?>
    <div class="row between"><a href="<?= e(url('/login')) ?>" class="small">Terug naar inloggen</a><button class="btn" type="submit"><?= icon('send', 'icon icon-sm') ?> Versturen</button></div>
</form>
