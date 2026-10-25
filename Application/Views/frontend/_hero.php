<?php $search = $search ?? true; ?>
<section class="phero<?= $search ? ' has-search' : '' ?>">
    <div class="container phero-grid">
        <div class="phero-l">
            <div class="phero-row">
                <?php if (!empty($icon)): ?><span class="phero-i"><?= icon($icon) ?></span><?php endif; ?>
                <div><h1><?= e($title) ?></h1><?php if (!empty($intro)): ?><p><?= e($intro) ?></p><?php endif; ?></div>
            </div>
        </div>
        <?php if ($search): ?><div class="phero-r"><?= $partial('frontend._domain-search', ['value' => $searchValue ?? '', 'compact' => true, 'placeholder' => 'Is jouw domeinnaam nog vrij?']) ?></div><?php endif; ?>
    </div>
</section>
