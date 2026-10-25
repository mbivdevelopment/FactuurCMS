<?php /** @var \App\Models\Product $p */
$per = ['maand' => '/ maand', 'kwartaal' => '/ kwartaal', 'jaar' => '/ jaar'][(string) $p->Periode] ?? 'eenmalig';
$price = (string) preg_replace('/,(\d\d)$/', '<sup>,$1</sup>', e(money((int) $p->Prijs)));
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep; ?>
<article class="pk<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="pk-badge"><?= icon('star', 'icon icon-sm') ?> Meest gekozen</span><?php endif; ?>
    <div class="pk-top">
        <span class="pk-i"><?= icon(\App\Models\Product::soortIcon($p->Dienstsoort, $p->Groep)) ?></span>
        <div><span class="pk-kind"><?= e($kind) ?></span><h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a></h3></div>
    </div>
    <?php if ((string) $p->Omschrijving !== ''): ?><p class="pk-desc"><?= e($p->Omschrijving) ?></p><?php endif; ?>
    <div class="pk-price"><span class="pk-eur"><?= $price ?></span><span class="pk-per"><?= e($per) ?></span></div>
    <div class="pk-vat">excl. btw<?php if ((int) $p->Instelkosten > 0): ?> · + <?= e(money((int) $p->Instelkosten)) ?> instelkosten<?php endif; ?></div>
    <?php $sr = $p->specItems(); $specs = $sr['specs']; $feats = $sr['rest']; $max = (int) ($max ?? 0); if ($max > 0 && count($feats) > $max) { $extra = count($feats) - $max; $feats = array_slice($feats, 0, $max); } else { $extra = 0; } ?>
    <?php if ($specs !== []): ?><div class="pk-specs"><?php foreach ($specs as $sp): ?><div class="pk-spec"><span class="pk-sl"><?= e($sp['label']) ?></span><span class="pk-sv"><?= e($sp['disp']) ?></span><span class="pk-sb"><span class="pk-sf" style="width:<?= (int) round(min(100, $sp['num'] / $sp['max'] * 100)) ?>%"></span></span></div><?php endforeach; ?></div><?php endif; ?>
    <ul class="pk-feats"><?php foreach ($feats as $f): ?><li><span class="pk-fi"><?= icon($f['icoon'], 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?><?php if ($extra > 0): ?><li class="pk-plus"><a href="<?= e(url($p->path())) ?>">+ <?= $extra ?> meer <?= icon('arrow-right', 'icon icon-sm') ?></a></li><?php endif; ?></ul>
    <div class="pk-actions">
        <a class="btn btn-lg pk-btn" href="<?= e(url($p->path())) ?>"><?= icon('cart', 'icon icon-sm') ?> Bestellen</a>
        <a class="pk-more" href="<?= e(url($p->path())) ?>">Meer informatie <?= icon('arrow-right', 'icon icon-sm') ?></a>
    </div>
</article>
