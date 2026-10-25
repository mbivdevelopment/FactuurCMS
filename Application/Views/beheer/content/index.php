<?php $extend('beheer', ['title' => $type['label']]); $base = \App\Services\Content::base($type); $customers = in_array('customer', $type['columns'], true) ? \App\Services\Content::customers() : []; ?>
<div class="page-head"><div class="ph-icon"><?= icon($type['icon']) ?></div><div class="ph-text"><h1><?= e($type['label']) ?></h1><div class="sub"><?= e($type['desc']) ?></div></div><?php foreach ($type['links'] ?? [] as [$lh, $li, $lt]): ?><a class="btn btn-ghost" href="<?= e(url($lh)) ?>"><?= icon($li, 'icon icon-sm') ?> <?= e($lt) ?></a> <?php endforeach; ?><a class="btn" href="<?= e(url($base . '/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuw <?= e($type['single']) ?></a></div>
<div class="card"><?php if ($rows === []): ?><div class="empty">Nog niets toegevoegd.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><?php foreach ($type['columns'] as $col => $kind): ?><th><?= e($kind === 'image' ? '' : ($type['fields'][$col][0] ?? $col)) ?></th><?php endforeach; ?><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): $edit = url($base . '/' . $r['Id']); ?><tr>
    <?php foreach ($type['columns'] as $col => $kind): $v = $r[$col] ?? ''; ?>
        <?php if ($kind === 'image'): ?><td style="width:72px"><?php $src = media_url((string) $v); ?><?php if ($src !== ''): ?><img class="thumb" src="<?= e($src) ?>" alt=""><?php else: ?><span class="thumb thumb-empty"><?= icon($type['icon'], 'icon icon-sm') ?></span><?php endif; ?></td>
        <?php elseif ($kind === 'title'): ?><td><a href="<?= e($edit) ?>"><strong><?= e($v) ?></strong></a><?php if (!empty($r['Slug']) && $type['public']): ?><div class="small muted"><?= e($type['public'] . $r['Slug']) ?></div><?php elseif (!empty($r['Samenvatting'])): ?><div class="small muted"><?= e(mb_strimwidth((string) $r['Samenvatting'], 0, 80, '…')) ?></div><?php endif; ?></td>
        <?php elseif ($kind === 'badge'): ?><td><?php $label = $type['fields'][$col][4][$v] ?? $v; ?><span class="badge"><?= e($label) ?></span></td>
        <?php elseif ($kind === 'customer'): ?><td><?php if ($v): ?><a href="<?= e(url('/beheer/klanten/' . (int) $v)) ?>"><?= e($customers[(int) $v] ?? '#' . $v) ?></a><?php else: ?>-<?php endif; ?></td>
        <?php elseif ($kind === 'money'): ?><td class="nowrap"><?= (int) $v ? e(money((int) $v)) : '-' ?></td>
        <?php elseif ($kind === 'bool'): ?><td><?= check_icon((bool) $v) ?></td>
        <?php elseif ($kind === 'date'): ?><td class="nowrap"><?= e(nl_date((string) $v)) ?></td>
        <?php else: ?><td><?= e($v === '' || $v === null ? '-' : $v) ?></td><?php endif; ?>
    <?php endforeach; ?>
    <td class="actions"><?php if ($type['public'] && !empty($r['Slug'])): ?><a class="btn btn-ghost btn-sm" target="_blank" href="<?= e(url($type['public'] . $r['Slug'])) ?>"><?= icon('eye', 'icon icon-sm') ?></a> <?php endif; ?><a class="btn btn-ghost btn-sm" href="<?= e($edit) ?>"><?= icon('edit', 'icon icon-sm') ?> Bewerken</a></td>
</tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></div>
