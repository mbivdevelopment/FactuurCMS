<?php $new = !$product->id(); $extend('beheer', ['title' => $new ? 'Nieuw product' : $product->Naam]); $groups = array_combine(array_keys(\App\Models\Product::GROUPS), array_keys(\App\Models\Product::GROUPS)); ?>
<div class="page-head"><div class="ph-icon"><?= icon('box') ?></div><div class="ph-text"><h1><?= e($new ? 'Nieuw product' : $product->Naam) ?></h1></div><a class="btn btn-ghost" href="<?= e(url('/beheer/producten')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url($new ? '/beheer/producten' : '/beheer/producten/' . $product->id())) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Naam', 'Naam', 'tag', $product->Naam, 'text', [], 'required') ?>
        <?= field('Groep', 'Groep', 'layout', $product->Groep, 'select', $groups) ?>
        <?php $soorten = ['' => 'Geen dienstsoort']; foreach (\App\Models\Product::SOORTEN as $kol => $items) { foreach ($items as $naam => $ic) { $soorten[$naam] = $kol . ' › ' . \App\Models\Product::soortLabel($naam); } } ?>
        <?= field('Dienstsoort', 'Dienstsoort', 'tag', (string) $product->Dienstsoort, 'select', $soorten) ?>
        <?= field('Slug', 'URL-naam (leeg = automatisch)', 'link', $product->Slug) ?>
        <?= field('Periode', 'Periode', 'refresh', $product->Periode, 'select', \App\Models\Product::PERIODS) ?>
        <?= field('Prijs', 'Prijs excl. btw (€)', 'euro', cents_input((int) $product->Prijs)) ?>
        <?= field('Instelkosten', 'Eenmalige instelkosten (€)', 'euro', cents_input((int) $product->Instelkosten)) ?>
        <?= field('BtwTarief', 'Btw-tarief (%)', 'hash', (string) (float) $product->BtwTarief) ?>
        <?= field('Volgorde', 'Volgorde', 'hash', (string) $product->Volgorde, 'number') ?>
        <?= field('Bron', 'Aanmaken via', 'link', (string) $product->Bron, 'select', ['' => 'Handmatig', 'qevanto' => 'Qevanto paneel', 'realtimeregister' => 'Realtime Register', 'interserver' => 'InterServer (CloudVPS)']) ?>
        <?= field('Omschrijving', 'Omschrijving', 'note', $product->Omschrijving, 'textarea') ?>
        <?php $iconSelect = static function (string $name, string $current): string {
            $html = '<select class="feature-icon" name="' . e($name) . '" aria-label="Icoon">';
            foreach (\App\Models\Product::FEATURE_ICONS as $key => $label) {
                $html .= '<option value="' . e($key) . '"' . ($key === $current ? ' selected' : '') . '>' . e($label) . '</option>';
            }

            return $html . '</select>';
        }; $row = static fn (string $i, string $icon, string $text): string => '<div class="feature-row" draggable="true"><span class="feature-grip" title="Verslepen">⋮⋮</span><span class="feature-preview">' . icon($icon, 'icon icon-sm') . '</span>' . $iconSelect('Kenmerk[' . $i . '][icoon]', $icon) . '<input class="feature-text" name="Kenmerk[' . e($i) . '][tekst]" value="' . e($text) . '" placeholder="Bijvoorbeeld 25 GB opslag"><button type="button" class="btn btn-ghost btn-sm" data-feature-remove title="Verwijderen">' . icon('trash', 'icon icon-sm') . '</button></div>'; ?>
        <div class="field field-full"><label>Kenmerken</label>
            <div class="feature-list" data-features><?php foreach ($product->featureItems() as $n => $f): ?><?= $row((string) $n, $f['icoon'], $f['tekst']) ?><?php endforeach; ?></div>
            <template data-feature-template><?= $row('__i__', 'check', '') ?></template>
            <template data-feature-icons><?php foreach (array_keys(\App\Models\Product::FEATURE_ICONS) as $key): ?><span data-icon="<?= e($key) ?>"><?= icon($key, 'icon icon-sm') ?></span><?php endforeach; ?></template>
            <button type="button" class="btn btn-ghost btn-sm" data-feature-add style="margin-top:8px"><?= icon('plus', 'icon icon-sm') ?> Kenmerk toevoegen</button>
        </div>
        <label class="switch"><input type="checkbox" name="Zichtbaar" value="1" <?= $product->Zichtbaar ? 'checked' : '' ?>> Zichtbaar op de website</label>
        <label class="switch"><input type="checkbox" name="Uitgelicht" value="1" <?= $product->Uitgelicht ? 'checked' : '' ?>> Uitgelicht (populair)</label>
    </div>
    <div class="card-foot"><?php if (!$new): ?><?= delete_submit() ?><span style="margin-left:auto"></span><?php endif; ?><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
<?php if (!$new): ?><?= delete_form(url('/beheer/producten/' . $product->id()), 'Product ' . $product->Naam . ' verwijderen? Bestaande diensten blijven bestaan.') ?><?php endif; ?>
