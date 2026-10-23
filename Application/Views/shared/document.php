<?php /** Documentweergave (beheer + klant). @var \App\Models\Document $doc */ $c = $doc->customer(); ?>
<div class="card">
    <div class="card-body">
        <div class="row between" style="align-items:flex-start">
            <div><?php $logo = branding_url('logo'); ?><?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="" style="max-height:50px"><?php else: ?><h2 style="color:var(--primary)"><?= e(company_name()) ?></h2><?php endif; ?></div>
            <div class="right small muted"><?php foreach (array_filter([company_name(), setting('bedrijf.adres'), trim(setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', '')), setting('bedrijf.email'), setting('bedrijf.kvk') ? 'KvK ' . setting('bedrijf.kvk') : '', setting('bedrijf.btw') ? 'Btw ' . setting('bedrijf.btw') : '']) as $l): ?><div><?= e($l) ?></div><?php endforeach; ?></div>
        </div>
        <div class="grid grid-2" style="margin:24px 0">
            <div><strong><?= e($c?->display()) ?></strong><?php foreach (array_filter([$c?->Bedrijfsnaam ? $c->name() : '', $c?->Adres, trim(($c?->Postcode ?? '') . ' ' . ($c?->Plaats ?? ''))]) as $l): ?><div><?= e($l) ?></div><?php endforeach; ?></div>
            <dl class="details"><dt><?= e($doc->label()) ?>nummer</dt><dd><strong><?= e($doc->Nummer) ?></strong></dd><dt>Datum</dt><dd><?= e(nl_date((string) $doc->Datum)) ?></dd><dt><?= $doc->isQuote() ? 'Geldig tot' : 'Vervaldatum' ?></dt><dd><?= e(nl_date((string) $doc->VervalDatum)) ?></dd><dt>Status</dt><dd><?= badge($doc->status(), $doc->statusLabel()) ?></dd></dl>
        </div>
        <?php if ($doc->Onderwerp): ?><p><strong>Betreft:</strong> <?= e($doc->Onderwerp) ?></p><?php endif; ?>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Omschrijving</th><th class="right">Aantal</th><th class="right">Prijs</th><th class="right">Btw</th><th class="right">Totaal</th></tr></thead>
            <tbody><?php foreach ($doc->lines() as $l): ?><tr><td><?= e($l['Omschrijving']) ?></td><td class="right"><?= e(rtrim(rtrim(number_format((float) $l['Aantal'], 2, ',', ''), '0'), ',')) ?></td><td class="right nowrap"><?= e(money((int) $l['Prijs'])) ?></td><td class="right"><?= e((float) $l['BtwTarief']) ?>%</td><td class="right nowrap"><?= e(money((int) round((float) $l['Aantal'] * (int) $l['Prijs']))) ?></td></tr><?php endforeach; ?></tbody>
        </table></div>
        <div class="totals" style="margin-top:12px">
            <div><span>Subtotaal</span><span><?= e(money((int) $doc->Subtotaal)) ?></span></div>
            <div><span>Btw</span><span><?= e(money((int) $doc->Btw)) ?></span></div>
            <div class="grand"><span>Totaal</span><span><?= e(money((int) $doc->Totaal)) ?></span></div>
            <?php if (!$doc->isQuote() && (int) $doc->Betaald > 0): ?><div><span>Betaald</span><span><?= e(money((int) $doc->Betaald)) ?></span></div><div><strong>Openstaand</strong><strong><?= e(money($doc->outstanding())) ?></strong></div><?php endif; ?>
        </div>
        <?php if ($doc->Opmerking): ?><p class="muted" style="white-space:pre-wrap"><?= e($doc->Opmerking) ?></p><?php endif; ?>
        <?php if (!$doc->isQuote() && $doc->outstanding() > 0 && (int) $doc->Totaal > 0): ?><div class="alert alert-info" style="margin:16px 0 0"><?= icon('wallet') ?> Graag <?= e(money($doc->outstanding())) ?> overmaken vóór <?= e(nl_date((string) $doc->VervalDatum)) ?> op <?= e(setting('bedrijf.iban', '-')) ?> t.n.v. <?= e(company_name()) ?> o.v.v. <?= e($doc->Nummer) ?>.</div><?php endif; ?>
    </div>
</div>
