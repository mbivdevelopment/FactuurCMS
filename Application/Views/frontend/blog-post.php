<?php $extend('frontend', ['title' => $post['Titel']]); $img = media_url((string) $post['Afbeelding']); ?>
<?= $partial('frontend._hero', ['title' => $post['Titel'], 'intro' => $post['Samenvatting'], 'crumbs' => [['/blog', 'Blog'], [null, $post['Categorie']]]]) ?>
<section class="sec"><div class="container read-solo">
    <article class="read read-card">
        <div class="read-meta"><span class="badge"><?= e($post['Categorie']) ?></span><span><?= icon('calendar', 'icon icon-sm') ?> <?= e(nl_date((string) $post['PublicatieDatum'])) ?></span><?php if ($post['Auteur']): ?><span><?= icon('user', 'icon icon-sm') ?> <?= e($post['Auteur']) ?></span><?php endif; ?></div>
        <?php if ($img !== ''): ?><img class="read-img" src="<?= e($img) ?>" alt=""><?php endif; ?>
        <div class="prose"><?= rich_text((string) $post['Inhoud']) ?></div>
        <a class="btn btn-ghost" href="<?= e(url('/blog')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug naar blog</a>
    </article>
</div></section>
