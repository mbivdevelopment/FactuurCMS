<?php $extend('beheer', ['title' => 'Werkplek beheer — ' . $service->Naam]); ?>
<div class="page-head"><div class="ph-icon"><?= icon('headset') ?></div><div class="ph-text"><h1><?= e($service->Naam) ?></h1><div class="sub"><?= e($service->Nummer) ?> · <?= e($service->customer()?->display() ?? '') ?></div></div>
    <div class="row"><?php if ((string) $service->Bron === 'qevanto'): ?><form method="post" action="<?= e(url('/beheer/diensten/' . $service->id() . '/paneel')) ?>" style="display:inline"><?= csrf_field() ?><button class="btn btn-sm"><?= icon('login', 'icon icon-sm') ?> Beheer in paneel</button></form><?php endif; ?><a class="btn btn-ghost" href="<?= e(url('/beheer/diensten/' . $service->id())) ?>">Terug</a></div></div>
<div class="svc-split">
    <aside class="stack">
        <div class="card"><div class="card-body">
            <?= $partial('beheer.partials.catnav', ['link' => static fn (string $k): string => '/beheer/diensten?cat=' . $k, 'cat' => $cat, 'catCounts' => $catCounts, 'totalDiensten' => $totalDiensten]) ?>
        </div></div>
    </aside>
    <div class="stack">
        <?php $base = '/beheer'; require __DIR__ . '/../../shared/workplace-card.php'; ?>
    </div>
</div>
