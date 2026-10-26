<?php $extend('klant', ['title' => 'Mijn diensten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= ($cat ?? '') !== '' ? e($cat) : 'Mijn diensten' ?></h1></div><a class="btn" href="<?= e(url('/oplossingen/webhosting')) ?>"><?= icon('plus', 'icon icon-sm') ?> Bestellen</a></div>
<?php if (($cat ?? '') !== ''): ?><p class="small muted" style="margin-top:-8px"><a href="<?= e(url('/klant/diensten')) ?>">← Alle diensten</a></p><?php endif; ?>
<div class="card"><?= $partial('klant._services', ['services' => $services]) ?></div>
