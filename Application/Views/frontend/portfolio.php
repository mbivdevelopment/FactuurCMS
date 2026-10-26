<?php $extend('frontend', ['title' => 'Portfolio']); ?>
<?= $partial('frontend._hero', ['title' => 'Portfolio', 'intro' => 'Websites, webshops, hosting en ICT-projecten die we met trots hebben gerealiseerd.', 'icon' => 'image', 'crumbs' => [[null, 'Portfolio']]]) ?>
<section class="sec"><div class="container side-split">
    <aside class="side-card">
        <h3>Categorieën</h3>
        <a class="side-link<?= $category === '' ? ' active' : '' ?>" href="<?= e(url('/portfolio')) ?>"><strong>Alle projecten</strong></a>
        <?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/portfolio?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> project(en)</small></a><?php endforeach; ?>
        <div class="side-start"><?php $ctaImg = ''; foreach ($projects as $p) { if (!empty($p['Afbeelding'])) { $ctaImg = media_url($p['Afbeelding']); break; } } ?><?php if ($ctaImg !== ''): ?><img class="side-start-img" src="<?= e($ctaImg) ?>" alt=""><?php endif; ?>
            <strong>Start ook een eigen project</strong><p>Vertel ons je idee; we denken mee en maken een vrijblijvende offerte.</p>
            <a class="btn" href="<?= e(url('/contact')) ?>">Start je project <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>
    </aside>
    <div>
        <?php if ($projects === []): ?><div class="empty">Nog geen projecten<?= $category !== '' ? ' in deze categorie' : '' ?>.</div><?php endif; ?>
        <div class="grid grid-3"><?php foreach ($projects as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project', 'btn' => true]) ?><?php endforeach; ?></div>
        <?php if ($pages > 1): ?><nav class="pagination site-pag" aria-label="Paginering">
            <?php for ($i = 1; $i <= $pages; $i++): $qs = 'pagina=' . $i . ($category !== '' ? '&categorie=' . urlencode($category) : ''); ?><a href="<?= e(url('/portfolio?' . $qs)) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?>
        </nav><?php endif; ?>
    </div>
</div></section>
<section class="sec"><div class="container">
    <div class="tiles">
        <a class="mega-tile tile-card" href="<?= e(url('/oplossingen/websites')) ?>"><span class="mt-i"><?= icon('layout') ?></span><span><strong>Website laten maken</strong><small>Een website of webshop op maat, zoals deze projecten.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/diensten')) ?>"><span class="mt-i"><?= icon('server') ?></span><span><strong>Hosting erbij</strong><small>Snelle hosting, domein en e-mail voor je nieuwe project.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/kennisbank')) ?>"><span class="mt-i"><?= icon('book') ?></span><span><strong>Kennisbank</strong><small>Handleidingen en antwoorden op veelgestelde vragen.</small></span></a>
    </div>
</div></section>
