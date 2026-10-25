<?php $extend('frontend', ['title' => $post['Titel']]); $img = media_url((string) $post['Afbeelding']); ?>
<?= $partial('frontend._hero', ['title' => $post['Titel'], 'intro' => $post['Samenvatting'], 'crumbs' => [['/blog', 'Blog'], [null, $post['Categorie']]]]) ?>
<section class="sec"><div class="container read-split">
    <article class="read">
        <div class="read-meta"><span class="badge"><?= e($post['Categorie']) ?></span><span><?= icon('calendar', 'icon icon-sm') ?> <?= e(nl_date((string) $post['PublicatieDatum'])) ?></span><?php if ($post['Auteur']): ?><span><?= icon('user', 'icon icon-sm') ?> <?= e($post['Auteur']) ?></span><?php endif; ?></div>
        <?php if ($img !== ''): ?><img class="read-img" src="<?= e($img) ?>" alt=""><?php endif; ?>
        <div class="prose"><?= rich_text((string) $post['Inhoud']) ?></div>
        <a class="btn btn-ghost" href="<?= e(url('/blog')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug naar blog</a>
    </article>
    <aside class="side-card"><h3>Recente berichten</h3><?php foreach ($recent as $r): ?><a class="side-link" href="<?= e(url('/blog/' . $r['Slug'])) ?>"><strong><?= e($r['Titel']) ?></strong><small><?= e(nl_date((string) $r['PublicatieDatum'])) ?></small></a><?php endforeach; ?></aside>
</div></section>
