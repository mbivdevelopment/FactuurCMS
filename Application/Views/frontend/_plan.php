<?php /** @var \App\Models\Product $p */
$periode = (string) $p->Periode;
$per = period_label($periode);
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$naam = (string) $p->Naam;
$short = preg_replace('/^' . preg_quote($kind, '/') . '\s+/iu', '', $naam) ?: $naam; ?>
<article class="pk pk7<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="pk7-hot">Populair</span><?php endif; ?>
    <div class="pk7-head">
        <span class="pk7-k"><?= icon(\App\Models\Product::soortIcon($p->Dienstsoort, $p->Groep), 'icon icon-sm') ?><?= e($kind) ?></span>
        <h3 class="pk7-t"><a href="<?= e(url($p->path() . '/bestellen')) ?>"><?= e($short) ?></a></h3>
    </div>
    <div class="pk7-pr"><b><?= e(money((int) $p->Prijs)) ?></b><span><?= e($per) ?></span></div>
    <a class="btn pk7-btn" href="<?= e(url($p->path() . '/bestellen')) ?>">Direct bestellen</a>
    <ul class="pk7-f"><?php foreach ($p->featureItems() as $f): ?><li><i class="pk7-ck"><?= icon('check', 'icon icon-sm') ?></i><?= e($f['tekst']) ?></li><?php endforeach; ?></ul>
</article>
