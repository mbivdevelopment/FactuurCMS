<?php $t = $kind === 'offerte' ? 'Offertes' : 'Facturen'; $extend('klant', ['title' => $t]); ?>
<div class="page-head"><div class="ph-icon"><?= icon($kind === 'offerte' ? 'quote' : 'file') ?></div><div class="ph-text"><h1><?= e($t) ?></h1></div></div>
<div class="card"><?= $partial('klant._documents', ['docs' => $docs]) ?></div>
