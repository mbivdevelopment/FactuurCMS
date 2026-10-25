<div class="car" data-carousel<?= !empty($autoplay) ? ' data-autoplay="6000"' : '' ?>>
    <div class="car-track">
        <?php foreach ($items as $p): ?><div class="car-item"><?= $partial('frontend._post-card', ['p' => $p, 'base' => $base, 'icon' => $icon, 'more' => $more ?? null]) ?></div><?php endforeach; ?>
    </div>
    <div class="car-nav"><button type="button" class="car-btn" data-car-prev aria-label="Vorige"><?= icon('chevron-left') ?></button><span class="car-dots"></span><button type="button" class="car-btn" data-car-next aria-label="Volgende"><?= icon('chevron-right') ?></button></div>
</div>
