<?php /** @var \App\Models\Product $p */
$per = ['maand' => 'per maand', 'kwartaal' => 'per kwartaal', 'jaar' => 'per jaar'][(string) $p->Periode] ?? 'eenmalig';
$price = (string) preg_replace('/,(\d\d)$/', '<sup>,$1</sup>', e(money((int) $p->Prijs)));
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$feats = $p->featureItems();
$max = (int) ($max ?? 0);
$extra = 0;
if ($max > 0 && count($feats) > $max) {
    $extra = count($feats) - $max;
    $feats = array_slice($feats, 0, $max);
} ?>
<article class="pk pk2<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="pk-badge"><?= icon('star', 'icon icon-sm') ?> Meest gekozen</span><?php endif; ?>
    <header class="pk-head">
        <span class="pk-i"><?= icon(\App\Models\Product::soortIcon($p->Dienstsoort, $p->Groep)) ?></span>
        <span class="pk-kind"><?= e($kind) ?></span>
        <h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a></h3>
        <?php if ((string) $p->Omschrijving !== ''): ?><p class="pk-desc"><?= e($p->Omschrijving) ?></p><?php endif; ?>
    </header>
    <div class="pk-price">
        <div class="pk-amount"><span class="pk-eur"><?= $price ?></span><span class="pk-per"><?= e($per) ?></span></div>
        <div class="pk-vat">excl. btw<?= (int) $p->Instelkosten > 0 ? ' · ' . e(money((int) $p->Instelkosten)) . ' instelkosten' : '' ?></div>
    </div>
    <?php if ($feats !== [] || $extra > 0): ?>
    <ul class="pk-feats">
        <?php foreach ($feats as $f): ?><li><span class="pk-fi"><?= icon('check', 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?>
        <?php if ($extra > 0): ?><li class="pk-plus"><a href="<?= e(url($p->path())) ?>">+ <?= $extra ?> meer kenmerken</a></li><?php endif; ?>
    </ul>
    <?php endif; ?>
    <div class="pk-actions">
        <a class="btn btn-lg pk-btn" href="<?= e(url($p->path() . '/bestellen')) ?>"><?= icon('cart', 'icon icon-sm') ?> Bestellen</a>
        <a class="pk-more" href="<?= e(url($p->path())) ?>">Meer informatie <?= icon('arrow-right', 'icon icon-sm') ?></a>
    </div>
</article>
