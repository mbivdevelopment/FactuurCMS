<?php /** @var list<\App\Models\Product> $products */ ?>
<div class="pk-grid pk-n<?= min(4, max(1, count($products))) ?>"><?php foreach ($products as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
