<?php $extend('frontend', ['title' => $item['Titel']]); $img = media_url((string) $item['Afbeelding']); ?>
<?= $partial('frontend._hero', ['title' => $item['Titel'], 'intro' => (string) $item['Samenvatting'] !== '' ? $item['Samenvatting'] : mb_strimwidth(trim((string) strtok(ltrim((string) $item['Inhoud']), "\n")), 0, 180, '…'), 'crumbs' => [['/portfolio', 'Portfolio'], [null, $item['Categorie']]]]) ?>
<section class="sec"><div class="container read-split">
    <article class="read">
        <?php if ($img !== ''): ?><img class="read-img" src="<?= e($img) ?>" alt=""><?php endif; ?>
        <div class="prose"><?= rich_text((string) $item['Inhoud']) ?></div>
    </article>
    <aside class="side-card">
        <h3>Over dit project</h3>
        <dl class="details"><?php if ($item['Klant']): ?><dt>Klant</dt><dd><?= e($item['Klant']) ?></dd><?php endif; ?><dt>Categorie</dt><dd><?= e($item['Categorie']) ?></dd><?php if (!empty($item['Budget'])): ?><dt>Budget</dt><dd><?= e($item['Budget']) ?></dd><?php endif; ?></dl>
        <?php if ($item['Url']): ?><a class="btn" style="margin-top:14px" href="<?= e($item['Url']) ?>" target="_blank" rel="noopener"><?= icon('link', 'icon icon-sm') ?> Bekijk live</a><?php endif; ?>
        <a class="btn btn-ghost" style="margin-top:14px" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Zoiets ook voor jou?</a>
    </aside>
</div></section>
<?php if ($more !== []): ?><section class="sec sec-alt"><div class="container"><div class="sec-head"><div><h2>Meer projecten</h2></div></div><div class="grid grid-3"><?php foreach ($more as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project']) ?><?php endforeach; ?></div></div></section><?php endif; ?>
