<?php
$new = empty($row['Id']);
$base = '/beheer/website/' . $type['key'];
$heading = $new ? 'Nieuw ' . $type['single'] : (string) ($row[$type['title']] ?? $type['single']);
$extend('beheer', ['title' => $heading]);
$groups = ['' => '- Geen pakketten -'] + array_combine(array_keys(\App\Models\Product::GROUPS), array_keys(\App\Models\Product::GROUPS));
?>
<div class="page-head"><div class="ph-icon"><?= icon($type['icon']) ?></div><div class="ph-text"><h1><?= e($heading) ?></h1><div class="sub"><?= e($type['label']) ?></div></div><a class="btn btn-ghost" href="<?= e(url($base)) ?>">Terug</a></div>
<form class="card" method="post" enctype="multipart/form-data" action="<?= e(url($new ? $base : $base . '/' . $row['Id'])) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?php foreach ($type['fields'] as $name => $def): [$label, $ico, $kind] = $def; $value = $row[$name] ?? ''; $req = ($def[3] ?? false) ? 'required' : ''; ?>
            <?php if ($kind === 'bool'): ?>
                <label class="switch"><input type="checkbox" name="<?= e($name) ?>" value="1" <?= $value ? 'checked' : '' ?>> <?= e($label) ?></label>
            <?php elseif ($kind === 'image'): $src = media_url((string) $value); ?>
                <div class="field field-full"><label><?= e($label) ?></label>
                    <div class="upload-row">
                        <img id="pv-<?= e($name) ?>" class="upload-preview" src="<?= e($src) ?>" alt=""<?= $src === '' ? ' hidden' : '' ?>>
                        <input type="file" name="<?= e($name) ?>_bestand" accept=".png,.jpg,.jpeg,.webp,.gif,.svg" data-preview="#pv-<?= e($name) ?>">
                        <?php if ($src !== ''): ?><label class="switch"><input type="checkbox" name="<?= e($name) ?>_verwijderen" value="1"> Afbeelding verwijderen</label><?php endif; ?>
                    </div>
                    <?php if (error($name)): ?><small class="field-error"><?= e(error($name)) ?></small><?php endif; ?>
                </div>
            <?php elseif ($kind === 'select'): ?>
                <?= field($name, $label, $ico, (string) $value, 'select', $def[4]) ?>
            <?php elseif ($kind === 'icon'): ?>
                <?= field($name, $label, $ico, (string) $value, 'select', array_combine(\App\Services\Content::ICONS, \App\Services\Content::ICONS)) ?>
            <?php elseif ($kind === 'productgroup'): ?>
                <?= field($name, $label, $ico, (string) $value, 'select', $groups) ?>
            <?php elseif ($kind === 'textarea'): ?>
                <?= field($name, $label, $ico, (string) $value, 'textarea', [], ($name === 'Inhoud' ? 'rows="12"' : 'rows="3"') . ' ' . $req) ?>
            <?php else: ?>
                <?= field($name, $label, $ico, (string) $value, $kind === 'decimal' ? 'text' : $kind, [], $req . ($kind === 'decimal' ? ' inputmode="decimal"' : '')) ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
</form>
<?php if (!$new): ?><div style="margin-top:16px"><?= delete_button(url($base . '/' . $row['Id']), ucfirst($type['single']) . ' "' . ($row[$type['title']] ?? '') . '" verwijderen?') ?></div><?php endif; ?>
