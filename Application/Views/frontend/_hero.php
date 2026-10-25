<?php $search = $search ?? true; ?>
<section class="phero<?= $search ? ' has-search' : '' ?>">
    <div class="container phero-grid">
        <div class="phero-l">
            <div class="phero-row">
                <?php if (!empty($icon)): ?><span class="phero-i"><?= icon($icon) ?></span><?php endif; ?>
                <div><h1><?= e($title) ?></h1><?php if (!empty($intro)): ?><p><?= e($intro) ?></p><?php endif; ?></div>
            </div>
        </div>
        <?php if ($search): ?><div class="phero-r"><?php if (!empty($searchAction)): ?><form class="dsearch dsearch-sm" action="<?= e(url($searchAction)) ?>" method="get">
                <span class="ds-i"><?= icon('search') ?></span>
                <input name="<?= e($searchName ?? 'q') ?>" value="<?= e($searchValue ?? '') ?>" placeholder="<?= e($searchPlaceholder ?? 'Zoeken') ?>" aria-label="Zoeken" required>
                <button class="btn btn-lg" type="submit"><?= icon('search', 'icon icon-sm') ?> Zoeken</button>
            </form><?php else: ?><?= $partial('frontend._domain-search', ['value' => $searchValue ?? '', 'compact' => true, 'placeholder' => 'Is jouw domeinnaam nog vrij?']) ?><?php endif; ?></div><?php endif; ?>
    </div>
</section>
