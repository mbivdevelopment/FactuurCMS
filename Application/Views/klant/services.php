<?php $extend('klant', ['title' => 'Mijn diensten']); ?>
<div class="page-head"><h1><?= icon('server') ?> Mijn diensten</h1><a class="btn" href="<?= e(url('/diensten')) ?>"><?= icon('plus', 'icon icon-sm') ?> Bestellen</a></div>
<div class="card"><?= $partial('klant._services', ['services' => $services]) ?></div>
