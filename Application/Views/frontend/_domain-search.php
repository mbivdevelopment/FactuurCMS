<form class="dsearch<?= !empty($compact) ? ' dsearch-sm' : '' ?>" action="<?= e(url('/domeinen')) ?>" method="get">
    <span class="ds-i"><?= icon('globe') ?></span>
    <input name="domein" value="<?= e($value ?? '') ?>" placeholder="Typ je domeinnaam, bijv. jouwbedrijf" aria-label="Domeinnaam" required>
    <button class="btn btn-lg" type="submit"><?= icon('search', 'icon icon-sm') ?> Controleren</button>
</form>
