<?php $extend('klant', ['title' => 'Mijn diensten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= ($cat ?? '') !== '' ? e($cat) : 'Mijn diensten' ?></h1></div><a class="btn" href="<?= e(url('/oplossingen/webhosting')) ?>"><?= icon('plus', 'icon icon-sm') ?> Bestellen</a></div>
<?php if (($cat ?? '') !== ''): ?><p class="small muted" style="margin-top:-8px"><a href="<?= e(url('/klant/diensten')) ?>">← Alle diensten</a></p><?php endif; ?>
<?php if (($overname ?? '') !== ''): ?>
<div class="card" style="margin-bottom:16px"><div class="card-body">
    <h3 style="margin-top:0">Dienst overnemen</h3>
    <p class="muted">Bevestig de overname met de code uit de e-mail.</p>
    <form method="post" action="<?= e(url('/klant/diensten')) ?>" style="display:flex;gap:8px;align-items:end;flex-wrap:wrap"><?= csrf_field() ?>
        <?= field('code', 'Overnamecode', 'lock', (string) $overname) ?>
        <button class="btn"><?= icon('check', 'icon icon-sm') ?> Overnemen</button>
    </form>
</div></div>
<?php endif; ?>
<div class="card"><?= $partial('klant._services', ['services' => $services]) ?></div>
