<form class="dsearch<?= !empty($compact) ? ' dsearch-sm' : '' ?>" action="<?= e(url('/domeinen')) ?>" method="get">
    <span class="ds-i"><?= icon('globe') ?></span>
    <input name="domein" value="<?= e($value ?? '') ?>" placeholder="<?= e($placeholder ?? 'Typ je domeinnaam, bijv. jouwbedrijf') ?>" aria-label="Domeinnaam" required>
    <button class="btn btn-lg" type="submit"><?= icon('search', 'icon icon-sm') ?> Controleren</button>
    <?php if (!empty($transfer)): ?><button class="btn btn-lg btn-ghost ds-transfer" type="submit" name="actie" value="verhuizen" title="Verhuis je bestaande domeinnaam naar ons"><?= icon('refresh', 'icon icon-sm') ?> Verhuizen</button><?php endif; ?>
</form>
