<?php $extend('beheer', ['title' => $catDef !== null ? $catDef[0] : 'Diensten']);
$base = ['groep' => $group, 'soort' => $kind, 'status' => $status, 'q' => $q, 'verloopt' => $due > 0 ? $due : ''];
$link = static function (array $over) use ($base): string {
    return '/beheer/diensten?' . http_build_query(array_filter(array_merge($base, $over), static fn ($v) => $v !== '' && $v !== null && $v !== 0));
};
$actief = ['cat' => $cat, 'soort' => $kind, 'status' => $status, 'due' => $due]; ?>
<div class="page-head"><div class="ph-icon"><?= icon('server') ?></div><div class="ph-text"><h1><?= $catDef !== null ? e($catDef[0]) : 'Diensten' ?></h1><div class="sub"><?= (int) $page['total'] ?> diensten<?= $group !== '' ? ' · ' . e($group) : '' ?><?= $kind !== '' ? ' · ' . e($kind) : '' ?><?= $due > 0 ? ' · factuur binnen ' . (int) $due . ' dagen' : '' ?><?php if ($cat !== '' || $group !== '' || $kind !== '' || $due > 0): ?> · <a href="<?= e(url('/beheer/diensten')) ?>">alle diensten</a><?php endif; ?></div></div>
    <div class="row"><form class="search" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Zoeken…"><?php foreach (['cat' => $cat, 'groep' => $group, 'soort' => $kind, 'status' => $status, 'verloopt' => $due > 0 ? $due : ''] as $hn => $hv): ?><?php if ($hv !== '' && $hv !== null): ?><input type="hidden" name="<?= e($hn) ?>" value="<?= e($hv) ?>"><?php endif; ?><?php endforeach; ?></form>
    <a class="btn" href="<?= e(url('/beheer/diensten/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe dienst</a></div></div>
<div class="svc-split">
    <aside class="stack">
        <div class="card"><div class="card-body">
            <?= $partial('beheer.partials.catnav', ['link' => static fn (string $k): string => $link(['cat' => $k, 'page' => '']), 'cat' => $cat, 'catCounts' => $catCounts, 'totalDiensten' => $totalDiensten]) ?>
        </div></div>
        <div class="card"><div class="card-body stack">
            <form method="get" class="stack">
                <?php if ($cat !== ''): ?><input type="hidden" name="cat" value="<?= e($cat) ?>"><?php endif; ?>
                <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
                <div class="field"><label for="f-soort">Type dienst</label><div class="field-control"><?= icon('tag') ?><select id="f-soort" name="soort" onchange="this.form.submit()"><option value="">Alle types</option><?php foreach ($soorten as $soort => $n): ?><option value="<?= e($soort) ?>" <?= $kind === $soort ? 'selected' : '' ?>><?= e($soort) ?> (<?= (int) $n ?>)</option><?php endforeach; ?></select></div></div>
                <div class="field"><label for="f-status">Status</label><div class="field-control"><?= icon('check') ?><select id="f-status" name="status" onchange="this.form.submit()"><option value="">Alle statussen</option><?php foreach (\App\Models\Service::STATUSES as $k => $v): ?><option value="<?= e($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></div></div>
                <div class="field"><label for="f-due">Verloopt binnen</label><div class="field-control"><?= icon('calendar') ?><select id="f-due" name="verloopt" onchange="this.form.submit()"><option value="">Geen filter</option><?php foreach ([30 => '30 dagen', 60 => '60 dagen', 90 => '90 dagen'] as $d => $lbl): ?><option value="<?= (int) $d ?>" <?= $due === $d ? 'selected' : '' ?>><?= e($lbl) ?></option><?php endforeach; ?></select></div></div>
            </form>
        </div></div>
    </aside>
    <div class="card"><?php if ($page['rows'] === []): ?><div class="empty">Geen diensten gevonden.</div><?php else: ?>
    <div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Dienst</th><th>Type</th><th>Klant</th><th>Prijs</th><th>Volgende factuur</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($page['rows'] as $s): ?><tr><td class="mono"><?= e($s['Nummer']) ?></td><td><a href="<?= e(url('/beheer/diensten/' . $s['Id'])) ?>"><strong><?= dienst_naam($s) !== (string) $s['Naam'] ? icon('globe', 'icon icon-sm') . ' ' : '' ?><?= e(dienst_naam($s)) ?></strong></a><?php if (dienst_naam($s) === (string) $s['Naam'] && $s['Omschrijving'] !== '' && $s['Omschrijving'] !== null): ?><div class="small muted"><?= e($s['Omschrijving']) ?></div><?php endif; ?></td>
    <td><?= dienst_type($s) !== '' ? '<span class="small muted">' . e(dienst_type($s)) . '</span>' : '<span class="muted">—</span>' ?></td>
    <td><a href="<?= e(url('/beheer/klanten/' . $s['KlantId'])) ?>"><?= e($s['Bedrijfsnaam'] ?: trim($s['Voornaam'] . ' ' . $s['Achternaam'])) ?></a></td>
    <td class="nowrap"><?= e(money((int) $s['Prijs'])) ?> <span class="small muted"><?= e(period_label($s['Periode'])) ?></span></td><td><?= e(nl_date($s['VolgendeFactuur'])) ?></td>
    <td><?= badge($s['Status'], \App\Models\Service::STATUSES[$s['Status']] ?? '') ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?= pagination($page) ?><?php endif; ?></div>
</div>
