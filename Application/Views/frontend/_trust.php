<?php /** Vertrouwensbalk met kerncijfers. */ $items = \App\Services\PageContent::trust((int) ($datacenters ?? 0)); ?>
<div class="trust">
    <?php foreach ($items as [$val, $label, $ic]): ?><div class="trust-item"><span class="trust-i"><?= icon($ic) ?></span><div><strong><?= e($val) ?></strong><small><?= e($label) ?></small></div></div><?php endforeach; ?>
</div>
