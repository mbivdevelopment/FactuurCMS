<?php /** Categorie-navigatie: verwacht $link(closure key=>url), $cat, $catCounts, $totalDiensten. */ ?>
<h3 class="side-title">Categorieën</h3>
<nav class="sidecat">
    <a href="<?= e(url($link(''))) ?>"<?= $cat === '' ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon('layout', 'icon icon-sm') ?></span><span>Alle diensten</span><small><?= (int) $totalDiensten ?></small></a>
    <?php foreach (\App\Models\Service::CAT_GROUPS as $gk => [$glabel, $gicon, $keys]): $gtotal = array_sum(array_map(static fn ($k) => (int) ($catCounts[$k] ?? 0), $keys)); ?>
        <a href="<?= e(url($link($gk))) ?>"<?= $cat === $gk ? ' class="active"' : '' ?>><span class="sc-ic"><?= icon($gicon, 'icon icon-sm') ?></span><span><?= e($glabel) ?></span><small><?= $gtotal ?></small></a>
        <?php if (count($keys) > 1): foreach ($keys as $k): $def = \App\Models\Service::CATEGORIES[$k]; ?>
            <a class="sub<?= $cat === $k ? ' active' : '' ?>" href="<?= e(url($link($k))) ?>"><span><?= e($def[0]) ?></span><small><?= (int) ($catCounts[$k] ?? 0) ?></small></a>
        <?php endforeach; endif; ?>
    <?php endforeach; ?>
</nav>
