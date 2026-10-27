<?php /** Categorie-navigatie: verwacht $link(closure key=>url), $cat, $catCounts. */ ?>
<h3 class="side-title">Categorieën</h3>
<nav class="sidecat">
    <?php foreach (\App\Models\Service::CAT_GROUPS as $gk => [$glabel, $gicon, $keys]): $gtotal = array_sum(array_map(static fn ($k) => (int) ($catCounts[$k] ?? 0), $keys)); ?>
        <a href="<?= e(url($link($gk))) ?>"<?= $cat === $gk ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon($gicon, 'icon icon-sm') ?></span><span><?= e($glabel) ?></span><small><?= $gtotal ?></small></a>
    <?php endforeach; ?>
</nav>
