<?php $search = $search ?? true; ?>
<section class="phero<?= $search ? ' has-search' : '' ?>">
    <div class="container phero-grid">
        <div class="phero-l">
            <nav class="crumbs"><a href="<?= e(url('/')) ?>">Home</a><?php foreach ($crumbs ?? [] as [$href, $text]): ?> <?= icon('chevron-right', 'icon icon-sm') ?> <?php if ($href): ?><a href="<?= e(url($href)) ?>"><?= e($text) ?></a><?php else: ?><span><?= e($text) ?></span><?php endif; ?><?php endforeach; ?></nav>
            <div class="phero-row">
                <?php if (!empty($icon)): ?><span class="phero-i"><?= icon($icon) ?></span><?php endif; ?>
                <div><h1><?= e($title) ?></h1><?php if (!empty($intro)): ?><p><?= e($intro) ?></p><?php endif; ?></div>
            </div>
        </div>
        <?php if ($search): ?><div class="phero-r"><span class="phero-sl"><?= icon('globe', 'icon icon-sm') ?> Is jouw domeinnaam nog vrij?</span><?= $partial('frontend._domain-search', ['value' => $searchValue ?? '', 'compact' => true]) ?></div><?php endif; ?>
    </div>
</section>
