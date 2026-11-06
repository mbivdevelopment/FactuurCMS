<?php /** Hoe het werkt: genummerde stappen. @var list<array{0:string,1:string}> $items */ ?>
<ol class="steps">
    <?php foreach ($items as $n => [$titel, $tekst]): ?>
    <li class="step"><span class="step-n"><?= $n + 1 ?></span><strong><?= e($titel) ?></strong><p><?= e($tekst) ?></p></li>
    <?php endforeach; ?>
</ol>
