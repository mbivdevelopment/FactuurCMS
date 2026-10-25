<section class="phero">
    <div class="container">
        <nav class="crumbs"><a href="<?= e(url('/')) ?>">Home</a><?php foreach ($crumbs ?? [] as [$href, $text]): ?> <?= icon('chevron-right', 'icon icon-sm') ?> <?php if ($href): ?><a href="<?= e(url($href)) ?>"><?= e($text) ?></a><?php else: ?><span><?= e($text) ?></span><?php endif; ?><?php endforeach; ?></nav>
        <div class="phero-row">
            <?php if (!empty($icon)): ?><span class="phero-i"><?= icon($icon) ?></span><?php endif; ?>
            <div><h1><?= e($title) ?></h1><?php if (!empty($intro)): ?><p><?= e($intro) ?></p><?php endif; ?></div>
        </div>
    </div>
</section>
