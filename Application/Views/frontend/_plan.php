<?php /** @var \App\Models\Product $p */ ?>
<div class="card plan<?= $p->Uitgelicht ? ' featured' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="ribbon">Populair</span><?php endif; ?>
    <div class="group"><?= icon(\App\Models\Product::GROUPS[$p->Groep] ?? 'box', 'icon icon-sm') ?> <?= e($p->Groep) ?></div>
    <h3 style="margin-top:6px"><?= e($p->Naam) ?></h3>
    <p class="muted" style="margin:0"><?= e($p->Omschrijving) ?></p>
    <div class="price"><?= e(money((int) $p->Prijs)) ?> <small><?= e(period_label((string) $p->Periode)) ?> excl. btw</small></div>
    <?php if ((int) $p->Instelkosten > 0): ?><div class="small muted">+ <?= e(money((int) $p->Instelkosten)) ?> eenmalige instelkosten</div><?php endif; ?>
    <ul><?php foreach ($p->features() as $f): ?><li><?= icon('check', 'icon icon-sm') ?> <?= e($f) ?></li><?php endforeach; ?></ul>
    <a class="btn<?= $p->Uitgelicht ? '' : ' btn-ghost' ?>" style="justify-content:center" href="<?= e(url($p->path())) ?>"><?= icon('cart', 'icon icon-sm') ?> Bestellen</a>
</div>
