<?php /** @var \App\Models\Product $p */
$periode = (string) $p->Periode;
$per = ['maand' => 'p/m', 'kwartaal' => 'p/kw', 'jaar' => 'p/j'][$periode] ?? 'eenmalig';
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$feats = $p->featureItems(); ?>
<article class="pk pk5<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <div class="pk5-row">
        <div class="pk5-title">
            <span class="pk5-kind"><?= e($kind) ?></span>
            <h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a><?php if ($p->Uitgelicht): ?><span class="pk5-hot">Populair</span><?php endif; ?></h3>
        </div>
        <div class="pk5-price"><b><?= e(money((int) $p->Prijs)) ?></b><span><?= e($per) ?><?= $periode !== '' && $per !== 'eenmalig' ? ' excl. btw' : '' ?></span><?php if ((int) $p->Instelkosten > 0): ?><small>+ <?= e(money((int) $p->Instelkosten)) ?> instelkosten</small><?php endif; ?></div>
    </div>
    <?php if ((string) $p->Omschrijving !== ''): ?><p class="pk5-desc"><?= e($p->Omschrijving) ?></p><?php endif; ?>
    <?php if ($feats !== []): ?>
    <ul class="pk5-feats">
        <?php foreach ($feats as $f): ?><li><span class="pk5-ck"><?= icon('check', 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <a class="btn btn-lg pk-btn" href="<?= e(url($p->path() . '/bestellen')) ?>">Bestellen</a>
    <a class="pk5-more" href="<?= e(url($p->path())) ?>">Alle details <?= icon('arrow-right', 'icon icon-sm') ?></a>
</article>
