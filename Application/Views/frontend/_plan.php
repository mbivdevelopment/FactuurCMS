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
        <h3 class="pk7-t"><a href="<?= e(url($p->path())) ?>"><?= e($short) ?></a></h3>
    </div>
    <div class="pk7-pr"><b><?= e(money((int) $p->Prijs)) ?></b><span><?= e($per) ?></span></div>
    <form method="post" action="<?= e(url($p->orderPath())) ?>" style="margin:0"><input type="hidden" name="_token" value="<?= e(\App\Core\Session::token()) ?>"><input type="hidden" name="KiesProduct" value="1"><input type="hidden" name="product" value="<?= e((string) $p->Slug) ?>"><button class="btn pk7-btn" type="submit"><?= icon('cart', 'icon icon-sm') ?> Direct bestellen</button></form>
    <ul class="pk7-f"><?php foreach ($p->featureItems() as $f): ?><li><i class="pk7-ck"><?= icon('check', 'icon icon-sm') ?></i><?= e($f['tekst']) ?></li><?php endforeach; ?></ul>
</article>
