<?php $t = $kind === 'offerte' ? 'Offertes' : 'Facturen'; $extend('klant', ['title' => $t]); ?>
<div class="page-head"><h1><?= icon($kind === 'offerte' ? 'quote' : 'file') ?> <?= e($t) ?></h1></div>
<div class="card"><?= $partial('klant._documents', ['docs' => $docs]) ?></div>
