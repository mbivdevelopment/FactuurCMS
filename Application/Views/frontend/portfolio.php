<?php $extend('frontend', ['title' => 'Portfolio']); ?>
<?= $partial('frontend._hero', ['title' => 'Portfolio', 'intro' => 'Websites, webshops, hosting en ICT-projecten die we met trots hebben gerealiseerd.', 'icon' => 'image', 'crumbs' => [[null, 'Portfolio']]]) ?>
<section class="sec"><div class="container side-split">
    <aside class="side-card">
        <h3>Categorieën</h3>
        <a class="side-link<?= $category === '' ? ' active' : '' ?>" href="<?= e(url('/portfolio')) ?>"><strong>Alle projecten</strong></a>
        <?php foreach ($categories as $c): ?><a class="side-link<?= $category === $c['Categorie'] ? ' active' : '' ?>" href="<?= e(url('/portfolio?categorie=' . urlencode($c['Categorie']))) ?>"><strong><?= e($c['Categorie']) ?></strong><small><?= (int) $c['Aantal'] ?> project(en)</small></a><?php endforeach; ?>
        <div class="side-help"><?= icon('chat') ?><div><strong>Ook zo'n project?</strong><small>Vraag vrijblijvend een offerte.</small></div></div>
        <a class="btn" href="<?= e(url('/contact')) ?>">Neem contact op</a>
    </aside>
    <div>
        <?php if ($projects === []): ?><div class="empty">Nog geen projecten<?= $category !== '' ? ' in deze categorie' : '' ?>.</div><?php endif; ?>
        <div class="grid grid-3"><?php foreach ($projects as $p): ?><?= $partial('frontend._post-card', ['p' => $p, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project']) ?><?php endforeach; ?></div>
        <?php if ($pages > 1): ?><nav class="pagination site-pag" aria-label="Paginering">
            <?php for ($i = 1; $i <= $pages; $i++): $qs = 'pagina=' . $i . ($category !== '' ? '&categorie=' . urlencode($category) : ''); ?><a href="<?= e(url('/portfolio?' . $qs)) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?>
        </nav><?php endif; ?>
    </div>
</div></section>
<section class="sec"><div class="container">
    <div class="pf-start">
        <div class="pf-start-img"><?php $ctaImg = ''; foreach ($projects as $p) { if (!empty($p['Afbeelding'])) { $ctaImg = media_url($p['Afbeelding']); break; } } ?><?php if ($ctaImg !== ''): ?><img src="<?= e($ctaImg) ?>" alt=""><?php else: ?><?= icon('image') ?><?php endif; ?></div>
        <div class="pf-start-txt"><h2>Start ook een eigen project</h2><p>Van website of webshop tot complete ICT-oplossing: vertel ons je idee en we denken mee. Je krijgt altijd eerst een vrijblijvende offerte.</p><div class="row"><a class="btn" href="<?= e(url('/contact')) ?>">Start je project <?= icon('arrow-right', 'icon icon-sm') ?></a><a class="btn btn-ghost" href="<?= e(url('/diensten')) ?>">Bekijk diensten</a></div></div>
    </div>
    <div class="tiles" style="margin-top:24px">
        <a class="mega-tile tile-card" href="<?= e(url('/oplossingen/websites')) ?>"><span class="mt-i"><?= icon('layout') ?></span><span><strong>Website laten maken</strong><small>Een website of webshop op maat, zoals deze projecten.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/diensten')) ?>"><span class="mt-i"><?= icon('server') ?></span><span><strong>Hosting erbij</strong><small>Snelle hosting, domein en e-mail voor je nieuwe project.</small></span></a>
        <a class="mega-tile tile-card" href="<?= e(url('/kennisbank')) ?>"><span class="mt-i"><?= icon('book') ?></span><span><strong>Kennisbank</strong><small>Handleidingen en antwoorden op veelgestelde vragen.</small></span></a>
    </div>
</div></section>
