<?php /** Vergelijkingstabel van pakketten op kenmerken. @var list<\App\Models\Product> $products */
$cols = array_values($products);
if (count($cols) < 2) { return; }
$rows = [];
foreach ($cols as $ci => $p) {
    foreach ($p->featureItems() as $f) {
        $key = mb_strtolower(preg_replace('/^[\d.,]+\s*(gb|mb|tb|%|x)?\s*/iu', '', $f['tekst']) ?: $f['tekst']);
        $rows[$key]['label'] ??= ucfirst(trim((string) preg_replace('/^[\d.,]+\s*(gb|mb|tb|%|x)?\s*/iu', '', $f['tekst'])));
        $rows[$key]['icon'] ??= $f['icoon'];
        $rows[$key]['vals'][$ci] = preg_match('/^([\d.,]+\s*(gb|mb|tb|%|x)?)/iu', $f['tekst'], $m) ? trim($m[1]) : true;
    }
}
if ($rows === []) { return; } ?>
<div class="cmp-wrap"><table class="cmp">
    <thead><tr><th></th><?php foreach ($cols as $p): ?><th<?= $p->Uitgelicht ? ' class="hot"' : '' ?>><?php if ($p->Uitgelicht): ?><span class="cmp-hot"><?= icon('star', 'icon icon-sm') ?> Populair</span><?php endif; ?><strong><?= e($p->Naam) ?></strong><span class="cmp-price"><?= e(money((int) $p->Prijs)) ?> <small>/ <?= e(['maand' => 'maand', 'kwartaal' => 'kwartaal', 'jaar' => 'jaar'][(string) $p->Periode] ?? 'eenmalig') ?></small></span></th><?php endforeach; ?></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?><tr><th><span class="pk-fi"><?= icon($r['icon'], 'icon icon-sm') ?></span><?= e($r['label']) ?></th><?php foreach ($cols as $ci => $p): $v = $r['vals'][$ci] ?? null; ?><td<?= $p->Uitgelicht ? ' class="hot"' : '' ?>><?php if ($v === true): ?><span class="cmp-yes"><?= icon('check', 'icon icon-sm') ?></span><?php elseif ($v === null): ?><span class="cmp-no">—</span><?php else: ?><b><?= e($v) ?></b><?php endif; ?></td><?php endforeach; ?></tr><?php endforeach; ?>
    </tbody>
    <tfoot><tr><th></th><?php foreach ($cols as $p): ?><td<?= $p->Uitgelicht ? ' class="hot"' : '' ?>><a class="btn btn-sm<?= $p->Uitgelicht ? '' : ' btn-ghost' ?>" href="<?= e(url($p->path() . '/bestellen')) ?>">Bestellen</a></td><?php endforeach; ?></tr></tfoot>
</table></div>
