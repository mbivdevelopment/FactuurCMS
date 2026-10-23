<?php $extend('beheer', ['title' => 'Producten']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('box') ?></div><div class="ph-text"><h1>Producten &amp; pakketten</h1><div class="sub">Wat je aanbiedt op de website en gebruikt in facturen.</div></div><a class="btn" href="<?= e(url('/beheer/producten/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuw product</a></div>
<div class="card"><?php if ($products === []): ?><div class="empty">Nog geen producten.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th>Groep</th><th>Naam</th><th>Prijs</th><th>Instelkosten</th><th>Uitgelicht</th><th>Zichtbaar</th><th></th></tr></thead><tbody>
<?php foreach ($products as $p): ?><tr><td><?= icon(\App\Models\Product::GROUPS[$p->Groep] ?? 'box', 'icon icon-sm') ?> <?= e($p->Groep) ?></td><td><a href="<?= e(url('/beheer/producten/' . $p->id())) ?>"><strong><?= e($p->Naam) ?></strong></a><div class="small muted">/diensten/<?= e($p->Slug) ?></div></td>
<td class="nowrap"><?= e(money((int) $p->Prijs)) ?> <span class="small muted"><?= e(period_label((string) $p->Periode)) ?></span></td><td><?= (int) $p->Instelkosten ? e(money((int) $p->Instelkosten)) : '-' ?></td>
<td><?= check_icon((bool) $p->Uitgelicht) ?></td><td><?= check_icon((bool) $p->Zichtbaar) ?></td>
<td class="actions"><a class="btn btn-ghost btn-sm" target="_blank" href="<?= e(url('/diensten/' . $p->Slug)) ?>"><?= icon('eye', 'icon icon-sm') ?></a> <a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/producten/' . $p->id())) ?>"><?= icon('edit', 'icon icon-sm') ?> Bewerken</a></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></div>
