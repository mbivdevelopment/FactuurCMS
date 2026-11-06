<?php /** @var \App\Models\Product $p */
$periode = (string) $p->Periode;
$per = ['maand' => 'per maand', 'kwartaal' => 'per kwartaal', 'jaar' => 'per jaar'][$periode] ?? 'eenmalig';
$price = (string) preg_replace('/,(\d\d)$/', '<sup>,$1</sup>', e(money((int) $p->Prijs)));
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$yearly = $periode === 'maand' ? (int) $p->Prijs * 12 : ($periode === 'kwartaal' ? (int) $p->Prijs * 4 : 0);
$feats = $p->featureItems();
$specs = [];
$rest = [];
$defs = [
    ['server', 'Opslag', '/(opslag|schijfruimte|ssd|nvme)/i'],
    ['layout', 'Websites', '/\bwebsites?\b|\bdomein(en)?\b(?!naam)/i'],
    ['mail', 'E-mail', '/e-?mail|mailbox/i'],
    ['trend', 'Dataverkeer', '/dataverkeer|bandbreedte|traffic/i'],
    ['cpu', 'CPU', '/\bcpu\b|vcpu|core/i'],
    ['box', 'Geheugen', '/geheugen|\bram\b/i'],
];
foreach ($feats as $f) {
    $hit = false;
    foreach ($defs as $i => [$ic, $label, $re]) {
        if (!isset($specs[$i]) && count($specs) < 3 && preg_match($re, $f['tekst'])) {
            $specs[$i] = ['icoon' => $ic, 'label' => $label, 'tekst' => $f['tekst']];
            $hit = true;
            break;
        }
    }
    if (!$hit) {
        $rest[] = $f;
    }
}
ksort($specs); ?>
<article class="pk pk3<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <div class="pk3-top">
        <div class="pk3-kind"><?= icon(\App\Models\Product::soortIcon($p->Dienstsoort, $p->Groep), 'icon icon-sm') ?> <?= e($kind) ?><?php if ($p->Uitgelicht): ?><span class="pk3-hot"><?= icon('star', 'icon icon-sm') ?> Meest gekozen</span><?php endif; ?></div>
        <h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a></h3>
        <?php if ((string) $p->Omschrijving !== ''): ?><p><?= e($p->Omschrijving) ?></p><?php endif; ?>
    </div>
    <div class="pk3-body">
        <div class="pk3-price">
            <div class="pk3-amount"><span class="pk-eur"><?= $price ?></span><span class="pk3-per"><?= e($per) ?><br><small>excl. btw</small></span></div>
            <?php if ($yearly > 0): ?><div class="pk3-year"><?= e(money($yearly)) ?> per jaar<?= (int) $p->Instelkosten > 0 ? ' · ' . e(money((int) $p->Instelkosten)) . ' instelkosten' : '' ?></div><?php elseif ((int) $p->Instelkosten > 0): ?><div class="pk3-year"><?= e(money((int) $p->Instelkosten)) ?> instelkosten</div><?php endif; ?>
        </div>
        <?php if ($specs !== []): ?>
        <div class="pk3-specs pk3-n<?= count($specs) ?>">
            <?php foreach ($specs as $sp): ?><div><span class="pk3-si"><?= icon($sp['icoon'], 'icon icon-sm') ?></span><strong><?= e($sp['tekst']) ?></strong></div><?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ($rest !== []): ?>
        <ul class="pk3-feats">
            <?php foreach ($rest as $f): ?><li><?= icon('check', 'icon icon-sm') ?><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="pk3-actions">
            <a class="btn btn-lg pk-btn" href="<?= e(url($p->path() . '/bestellen')) ?>">Bestellen <?= icon('arrow-right', 'icon icon-sm') ?></a>
            <a class="pk3-more" href="<?= e(url($p->path())) ?>">Alle details</a>
        </div>
    </div>
</article>
