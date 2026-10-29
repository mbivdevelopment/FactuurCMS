<?php $extend('beheer', ['title' => 'Webhosting — ' . $service->Naam]); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= e($service->Naam) ?></h1><div class="sub"><?= e($service->Nummer) ?> · <?= e($service->customer()?->display() ?? '') ?></div></div>
    <div class="row"><?php $base = '/beheer'; require __DIR__ . '/../../shared/hosting-actions.php'; ?><a class="btn btn-ghost" href="<?= e(url('/beheer/diensten/' . $service->id())) ?>">Terug</a></div></div>
<?php $base = '/beheer'; require __DIR__ . '/../../shared/hosting-card.php'; ?>
