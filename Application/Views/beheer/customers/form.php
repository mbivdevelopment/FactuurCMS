<?php $extend('beheer', ['title' => 'Nieuwe klant']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('user') ?></div><div class="ph-text"><h1>Nieuwe klant</h1></div><a class="btn btn-ghost" href="<?= e(url('/beheer/klanten')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url('/beheer/klanten')) ?>"><?= csrf_field() ?>
    <div class="card-body"><?= $partial('beheer.customers._fields', ['customer' => $customer]) ?></div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Klant aanmaken</button></div>
</form>
