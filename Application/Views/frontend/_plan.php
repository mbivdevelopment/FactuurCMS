<?php /** @var \App\Models\Product $p */
$per = ['maand' => '/ maand', 'kwartaal' => '/ kwartaal', 'jaar' => '/ jaar'][(string) $p->Periode] ?? 'eenmalig';
$price = (string) preg_replace('/,(\d\d)$/', '<sup>,$1</sup>', e(money((int) $p->Prijs)));
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$sr = $p->specItems();
$specs = $sr['specs'];
$feats = $sr['rest'];
$badges = [];
$rest = [];
foreach ($feats as $f) {
    $t = mb_strtolower($f['tekst']);
    if (str_contains($t, 'ssl')) {
        $badges[] = ['lock', 'tag-ssl', $f['tekst']];
    } elseif (str_contains($t, 'back-up') || str_contains($t, 'backup')) {
        $badges[] = ['refresh', 'tag-bak', $f['tekst']];
    } else {
        $rest[] = $f;
    }
}
$score = 0.0;
$weights = ['CPU' => .45, 'Geheugen' => .30, 'Netwerk' => .25];
foreach ($specs as $sp) {
    if (isset($weights[$sp['label']])) {
        $score += min(1.0, $sp['num'] / $sp['max']) * $weights[$sp['label']];
    }
}
$segs = (int) round(min(1.0, $score) * 8);
$max = (int) ($max ?? 0);
$extra = 0;
if ($max > 0 && count($rest) > $max) {
    $extra = count($rest) - $max;
    $rest = array_slice($rest, 0, $max);
} ?>
<article class="pk<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="pk-badge"><?= icon('star', 'icon icon-sm') ?> Meest gekozen</span><?php endif; ?>
    <div class="pk-top">
        <span class="pk-i"><?= icon(\App\Models\Product::soortIcon($p->Dienstsoort, $p->Groep)) ?></span>
        <div><span class="pk-kind"><?= e($kind) ?></span><h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a></h3></div>
    </div>
    <div class="pk-price"><span class="pk-eur"><?= $price ?></span><span class="pk-per"><?= e($per) ?></span></div>
    <?php if ((int) $p->Instelkosten > 0): ?><div class="pk-vat">+ <?= e(money((int) $p->Instelkosten)) ?> instelkosten</div><?php endif; ?>
    <a class="btn btn-lg pk-btn" href="<?= e(url($p->path())) ?>"><?= icon('cart', 'icon icon-sm') ?> Bestellen</a>
        <?php if ($specs !== []): ?><?php $specIcons = ['CPU' => 'cpu', 'Geheugen' => 'box', 'Schijfruimte' => 'server', 'Dataverkeer' => 'trend', 'Netwerk' => 'globe']; ?>
    <a class="pk-specs-link" href="#" data-specs-open><?= icon('cpu', 'icon icon-sm') ?> Alle specificaties</a>
    <dialog class="pk-dlg">
        <div class="modal-h"><span><span class="pk-kind"><?= e($kind) ?></span><strong><?= e($p->Naam) ?></strong></span><button type="button" class="pk-dlg-x" data-specs-close aria-label="Sluiten"><?= icon('x') ?></button></div>
        <div class="pk-specs">
            <div class="pk-spec"><span class="pk-si"><?= icon('zap', 'icon icon-sm') ?></span><span class="pk-st"><span class="pk-sl">Snelheid</span><span class="pk-seg"><?php for ($i = 0; $i < 8; $i++): ?><span class="pk-segs<?= $i < $segs ? ' on' : '' ?>"></span><?php endfor; ?></span></span></div>
            <?php foreach ($specs as $sp): ?><div class="pk-spec"><span class="pk-si"><?= icon($specIcons[$sp['label']] ?? 'server', 'icon icon-sm') ?></span><span class="pk-st"><span class="pk-sl"><?= e($sp['label']) ?></span><span class="pk-sv"><?= e($sp['disp']) ?></span></span></div><?php endforeach; ?>
        </div>
    </dialog><?php endif; ?>
    <?php if ($rest !== [] || $extra > 0): ?><ul class="pk-feats"><?php foreach ($rest as $f): ?><li><span class="pk-fi"><?= icon($f['icoon'], 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?><?php if ($extra > 0): ?><li class="pk-plus"><a href="<?= e(url($p->path())) ?>">+ <?= $extra ?> meer <?= icon('arrow-right', 'icon icon-sm') ?></a></li><?php endif; ?></ul><?php endif; ?>
    <?php if ($badges !== []): ?><div class="pk-tags"><?php foreach ($badges as [$bic, $cls, $bt]): ?><span class="pk-tag <?= $cls ?>"><?= icon($bic, 'icon icon-sm') ?> <?= e($bt) ?></span><?php endforeach; ?></div><?php endif; ?>
    <a class="pk-more" href="<?= e(url($p->path())) ?>">Meer informatie <?= icon('arrow-right', 'icon icon-sm') ?></a>
</article>
