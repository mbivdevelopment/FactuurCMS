<?php /** @var list<\App\Models\Product> $products */ ?>
<?php if (count($products) > 3): ?>
<div class="car pk-car" data-carousel>
    <div class="car-track"><?php foreach ($products as $p): ?><div class="car-item"><?= $partial('frontend._plan', ['p' => $p]) ?></div><?php endforeach; ?></div>
    <div class="car-nav"><button type="button" class="car-btn" data-car-prev aria-label="Vorige"><?= icon('chevron-left') ?></button><span class="car-dots"></span><button type="button" class="car-btn" data-car-next aria-label="Volgende"><?= icon('chevron-right') ?></button></div>
</div>
<?php else: ?>
<div class="pk-grid pk-n<?= max(1, count($products)) ?>"><?php foreach ($products as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
<?php endif; ?>
