<?php $extend('beheer', ['title' => 'Producten']);
$link = static fn (array $over = []): string => '/beheer/producten?' . http_build_query(array_filter(array_merge(['cat' => $cat, 'soort' => $kind, 'q' => $q], $over), static fn ($v) => $v !== '' && $v !== null));
$actief = ['cat' => $cat, 'soort' => $kind]; ?>
<div class="page-head"><div class="ph-icon"><?= icon('box') ?></div><div class="ph-text"><h1><?= e($catDef[0] ?? 'Producten & pakketten') ?></h1><div class="sub">Wat je verkoopt: domeinextensies, pakketten en andere producten<?= $kind !== '' ? ' · ' . e(\App\Models\Product::soortLabel($kind)) : '' ?><?php if ($kind !== ''): ?> · <a href="<?= e(url($link(['soort' => '']))) ?>">alle types</a><?php endif; ?></div></div>
    <div class="row"><form class="search" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Zoeken…"><?php foreach (['cat' => $cat, 'soort' => $kind] as $hn => $hv): ?><?php if ($hv !== '' && $hv !== null): ?><input type="hidden" name="<?= e($hn) ?>" value="<?= e($hv) ?>"><?php endif; ?><?php endforeach; ?></form>
    <a class="btn" href="<?= e(url('/beheer/producten/nieuw' . ($kind !== '' ? '?soort=' . rawurlencode($kind) : ''))) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuw product</a></div></div>
<div class="svc-split">
    <aside class="stack">
        <div class="card"><div class="card-body">
            <?= $partial('beheer.partials.catnav', ['link' => static fn (string $k): string => $link(['cat' => $k]), 'cat' => $cat, 'catCounts' => $catCounts]) ?>
        </div></div>
        <div class="card"><div class="card-body stack">
            <form method="get" class="stack">
                <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= e($cat) ?>"><?php endif; ?>
                <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
                <div class="field"><label for="f-soort">Type dienst</label><div class="field-control"><?= icon('tag') ?><select id="f-soort" name="soort" onchange="this.form.submit()"><option value="">Alle types</option><?php foreach ($soorten as $soort => $n): ?><option value="<?= e($soort) ?>" <?= $kind === $soort ? 'selected' : '' ?>><?= e($soort) ?> (<?= (int) $n ?>)</option><?php endforeach; ?></select></div></div>
            </form>
        </div></div>
    </aside>
    <div class="card"><?php if ($products === []): ?><div class="empty">Geen producten gevonden.</div><?php else: ?>
    <div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Product</th><th>Type</th><th>Prijs</th><th>Instelkosten</th><th>Uitgelicht</th><th>Zichtbaar</th></tr></thead><tbody>
    <?php foreach ($products as $p): ?><tr><td class="mono"><?= e($p->Nummer) ?></td><td><a class="cell-product" href="<?= e(url('/beheer/producten/' . $p->id())) ?>"><span class="cell-tile"><?= icon(\App\Models\Product::GROUPS[$p->Groep] ?? 'box', 'icon icon-sm') ?></span><span><strong><?= e($p->Naam) ?></strong><span class="small muted"><?= e($p->path()) ?></span></span></a></td>
    <td><?= ($t = \App\Models\Product::soortLabel($p->Dienstsoort)) !== '' ? '<span class="small muted">' . e($t) . '</span>' : '<span class="muted">—</span>' ?></td>
    <td class="nowrap"><?= e(money((int) $p->Prijs)) ?> <span class="small muted"><?= e(period_label((string) $p->Periode)) ?></span></td><td><?= (int) $p->Instelkosten ? e(money((int) $p->Instelkosten)) : '-' ?></td>
    <td><?= check_icon((bool) $p->Uitgelicht) ?></td><td><?= check_icon((bool) $p->Zichtbaar) ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?></div>
</div>
