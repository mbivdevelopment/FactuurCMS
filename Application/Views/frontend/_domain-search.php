<form class="dsearch" action="<?= e(url('/domeinen')) ?>" method="get">
    <span class="ds-i"><?= icon('globe') ?></span>
    <input name="domein" value="<?= e($value ?? '') ?>" placeholder="Zoek jouw domeinnaam, bijv. jouwbedrijf" aria-label="Domeinnaam" required>
    <select name="ext" aria-label="Extensie"><?php foreach ($extensions as $x): ?><option value="<?= e($x) ?>">.<?= e($x) ?></option><?php endforeach; ?></select>
    <button class="btn btn-lg" type="submit"><?= icon('search', 'icon icon-sm') ?> Zoeken</button>
</form>
