<?php $isQ = $kind === 'offerte'; $t = $isQ ? 'Offertes' : 'Facturen'; $base = $isQ ? '/beheer/offertes' : '/beheer/facturen'; $extend('beheer', ['title' => $t]);
$statuses = $isQ ? \App\Models\Document::QUOTE_STATUSES : \App\Models\Document::INVOICE_STATUSES; ?>
<div class="page-head"><div><h1><?= icon($isQ ? 'quote' : 'file') ?> <?= e($t) ?></h1><div class="sub">Totaal <?= e(money((int) $totals['Totaal'])) ?> · <?= $isQ ? 'in afwachting' : 'openstaand' ?> <?= e(money((int) $totals['Open'])) ?></div></div>
    <div class="row"><form class="search" method="get"><?= icon('search') ?><input name="q" value="<?= e($q) ?>" placeholder="Zoeken…"><select name="status" onchange="this.form.submit()"><option value="">Alle statussen</option><?php foreach ($statuses as $k => $v): ?><option value="<?= e($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></form>
    <a class="btn" href="<?= e(url($base . '/nieuw')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nieuwe <?= $isQ ? 'offerte' : 'factuur' ?></a></div></div>
<div class="card"><?php if ($page['rows'] === []): ?><div class="empty">Geen <?= strtolower($t) ?> gevonden.</div><?php else: ?>
<div class="table-wrap"><table class="table"><thead><tr><th>Nummer</th><th>Klant</th><th>Onderwerp</th><th>Datum</th><th><?= $isQ ? 'Geldig tot' : 'Vervalt' ?></th><th class="right">Totaal</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($page['rows'] as $d): ?><tr><td><a href="<?= e(url('/beheer/documenten/' . $d->id())) ?>"><strong><?= e($d->Nummer) ?></strong></a></td>
<td><a href="<?= e(url('/beheer/klanten/' . $d->KlantId)) ?>"><?= e($d->Bedrijfsnaam ?: trim($d->Voornaam . ' ' . $d->Achternaam)) ?></a></td><td><?= e($d->Onderwerp) ?></td>
<td><?= e(nl_date((string) $d->Datum)) ?></td><td><?= e(nl_date($d->VervalDatum)) ?></td><td class="right nowrap"><?= e(money((int) $d->Totaal)) ?></td><td><?= badge($d->status(), $d->statusLabel()) ?></td>
<td class="actions"><a class="btn btn-ghost btn-sm" target="_blank" href="<?= e(url('/beheer/documenten/' . $d->id() . '/pdf')) ?>" data-tip="PDF"><?= icon('download', 'icon icon-sm') ?></a> <a class="btn btn-ghost btn-sm" href="<?= e(url('/beheer/documenten/' . $d->id())) ?>"><?= icon('eye', 'icon icon-sm') ?> Bekijken</a></td></tr><?php endforeach; ?>
</tbody></table></div><?= pagination($page) ?><?php endif; ?></div>
