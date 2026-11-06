<?php /** @var \App\Models\Product $p */
$periode = (string) $p->Periode;
$per = ['maand' => 'per maand', 'kwartaal' => 'per kwartaal', 'jaar' => 'per jaar'][$periode] ?? 'eenmalig';
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep; ?>
<article class="pk pk6<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <div class="pk6-row">
        <div><span class="pk6-k"><?= e($kind) ?></span><h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a><?php if ($p->Uitgelicht): ?><span class="pk6-hot">Populair</span><?php endif; ?></h3></div>
        <div class="pk6-pr"><b><?= e(money((int) $p->Prijs)) ?></b><span><?= e($per) ?></span></div>
    </div>
    <?php if ((string) $p->Omschrijving !== ''): ?><p class="pk6-d"><?= e($p->Omschrijving) ?></p><?php endif; ?>
    <ul class="pk6-f"><?php foreach ($p->featureItems() as $f): ?><li><i class="pk6-ck"><?= icon('check', 'icon icon-sm') ?></i><?= e($f['tekst']) ?></li><?php endforeach; ?></ul>
    <a class="btn pk6-btn" href="<?= e(url($p->path() . '/bestellen')) ?>">Bestellen</a>
</article>
