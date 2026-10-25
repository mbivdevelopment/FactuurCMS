<?php /** @var list<\App\Models\Product> $products */ ?>
<div class="pk-grid pk-n<?= min(count($products), 4) ?>"><?php foreach ($products as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
