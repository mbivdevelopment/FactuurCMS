<?php /** Voordelen-grid: 6 kaarten per dienstsoort. @var list<array{0:string,1:string,2:string}> $items */ ?>
<div class="bene-grid">
    <?php foreach ($items as $i => [$ic, $titel, $tekst]): ?>
    <div class="bene"><span class="bene-i"><?= icon($ic) ?></span><div><strong><?= e($titel) ?></strong><p><?= e($tekst) ?></p></div></div>
    <?php endforeach; ?>
</div>
