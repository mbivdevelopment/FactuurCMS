<?php $extend('klant', ['title' => 'Mijn diensten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1>Mijn diensten</h1></div><a class="btn" href="<?= e(url('/diensten')) ?>"><?= icon('plus', 'icon icon-sm') ?> Bestellen</a></div>
<div class="card"><?= $partial('klant._services', ['services' => $services]) ?></div>
