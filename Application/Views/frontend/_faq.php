<?php /** Veelgestelde vragen als uitklapbare lijst. @var list<array{0:string,1:string}> $items */ ?>
<div class="faq">
    <?php foreach ($items as $i => [$q, $a]): ?>
    <details class="faq-item"<?= $i === 0 ? ' open' : '' ?>><summary><span><?= e($q) ?></span><?= icon('chevron-down', 'icon icon-sm') ?></summary><div class="faq-a"><p><?= e($a) ?></p></div></details>
    <?php endforeach; ?>
</div>
