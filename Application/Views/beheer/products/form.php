<?php $new = !$product->id(); $extend('beheer', ['title' => $new ? 'Nieuw product' : $product->Naam]); $groups = array_combine(array_keys(\App\Models\Product::GROUPS), array_keys(\App\Models\Product::GROUPS)); ?>
<div class="page-head"><div class="ph-icon"><?= icon('box') ?></div><div class="ph-text"><h1><?= e($new ? 'Nieuw product' : $product->Naam) ?></h1></div><a class="btn btn-ghost" href="<?= e(url('/beheer/producten')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url($new ? '/beheer/producten' : '/beheer/producten/' . $product->id())) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Naam', 'Naam', 'tag', $product->Naam, 'text', [], 'required') ?>
        <?= field('Groep', 'Groep', 'layout', $product->Groep, 'select', $groups) ?>
        <?= field('Slug', 'URL-naam (leeg = automatisch)', 'link', $product->Slug) ?>
        <?= field('Periode', 'Periode', 'refresh', $product->Periode, 'select', \App\Models\Product::PERIODS) ?>
        <?= field('Prijs', 'Prijs excl. btw (€)', 'euro', cents_input((int) $product->Prijs)) ?>
        <?= field('Instelkosten', 'Eenmalige instelkosten (€)', 'euro', cents_input((int) $product->Instelkosten)) ?>
        <?= field('BtwTarief', 'Btw-tarief (%)', 'hash', (string) (float) $product->BtwTarief) ?>
        <?= field('Volgorde', 'Volgorde', 'hash', (string) $product->Volgorde, 'number') ?>
        <?= field('Omschrijving', 'Omschrijving', 'note', $product->Omschrijving, 'textarea') ?>
        <?= field('Kenmerken', 'Kenmerken (één per regel)', 'check', implode("\n", $product->features()), 'textarea', [], 'rows="6"') ?>
        <label class="switch"><input type="checkbox" name="Zichtbaar" value="1" <?= $product->Zichtbaar ? 'checked' : '' ?>> Zichtbaar op de website</label>
        <label class="switch"><input type="checkbox" name="Uitgelicht" value="1" <?= $product->Uitgelicht ? 'checked' : '' ?>> Uitgelicht (populair)</label>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
<?php if (!$new): ?><div style="margin-top:16px"><?= delete_button(url('/beheer/producten/' . $product->id()), 'Product ' . $product->Naam . ' verwijderen? Bestaande diensten blijven bestaan.') ?></div><?php endif; ?>
