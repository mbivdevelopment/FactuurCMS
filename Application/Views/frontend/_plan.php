<?php /** @var \App\Models\Product $p */
$periode = (string) $p->Periode;
$per = ['maand' => 'maand', 'kwartaal' => 'kwartaal', 'jaar' => 'jaar'][$periode] ?? null;
[$eur, $cent] = array_pad(explode(',', money((int) $p->Prijs), 2), 2, '00');
$kind = $p->Dienstsoort ? \App\Models\Product::soortLabel($p->Dienstsoort) : (string) $p->Groep;
$yearly = $periode === 'maand' ? (int) $p->Prijs * 12 : ($periode === 'kwartaal' ? (int) $p->Prijs * 4 : 0);
$icons = [
    ['server', '/(opslag|schijfruimte|ssd|nvme)/i'],
    ['layout', '/\bwebsites?\b|\bdomein(en)?\b(?!naam)/i'],
    ['mail', '/e-?mail|mailbox/i'],
    ['trend', '/dataverkeer|bandbreedte|traffic/i'],
    ['cpu', '/\bcpu\b|vcpu|core/i'],
    ['box', '/geheugen|\bram\b/i'],
    ['lock', '/\bssl\b|certificaat/i'],
    ['refresh', '/back-?up/i'],
    ['headset', '/support|ondersteuning/i'],
];
$feats = [];
foreach ($p->featureItems() as $f) {
    $ic = 'check';
    foreach ($icons as [$i, $re]) {
        if (preg_match($re, $f['tekst'])) { $ic = $i; break; }
    }
    $feats[] = ['icoon' => $ic, 'tekst' => $f['tekst']];
} ?>
<article class="pk pk4<?= $p->Uitgelicht ? ' pk-hot' : '' ?>">
    <?php if ($p->Uitgelicht): ?><span class="pk4-badge"><?= icon('star', 'icon icon-sm') ?> Meest gekozen</span><?php endif; ?>
    <div class="pk4-head">
        <span class="pk4-kind"><?= e($kind) ?></span>
        <h3><a href="<?= e(url($p->path())) ?>"><?= e($p->Naam) ?></a></h3>
        <?php if ((string) $p->Omschrijving !== ''): ?><p><?= e($p->Omschrijving) ?></p><?php endif; ?>
    </div>
    <div class="pk4-price">
        <div class="pk4-amount"><span class="pk4-cur">€</span><span class="pk4-eur"><?= e(trim(str_replace('€', '', $eur))) ?></span><span class="pk4-cent">,<?= e($cent) ?></span><?php if ($per !== null): ?><span class="pk4-per">/ <?= e($per) ?></span><?php endif; ?></div>
        <div class="pk4-sub"><?= $per === null ? 'Eenmalig' : 'Excl. btw' ?><?= $yearly > 0 ? ' · ' . e(money($yearly)) . ' per jaar' : '' ?><?= (int) $p->Instelkosten > 0 ? ' · ' . e(money((int) $p->Instelkosten)) . ' instelkosten' : '' ?></div>
    </div>
    <a class="btn btn-lg pk-btn" href="<?= e(url($p->path() . '/bestellen')) ?>">Bestellen</a>
    <?php if ($feats !== []): ?>
    <ul class="pk4-feats">
        <?php foreach ($feats as $f): ?><li><span class="pk4-fi"><?= icon($f['icoon'], 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <a class="pk4-more" href="<?= e(url($p->path())) ?>">Alle details <?= icon('arrow-right', 'icon icon-sm') ?></a>
</article>
