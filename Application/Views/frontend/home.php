<?php $extend('frontend', ['title' => (string) setting('website.titel', 'Hosting')]); $overImg = branding_url('over'); ?>
<section class="dhero">
    <div class="container dhero-in">
        <span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinregistratie</span>
        <h1><?= e((string) setting('hero.titel', 'Jouw domeinnaam, vlot geregeld')) ?></h1>
        <p><?= e((string) setting('hero.tekst', '')) ?></p>
        <?= $partial('frontend._domain-search', ['extensions' => $extensions]) ?>
        <div class="dhero-exts">
            <?php foreach (\App\Models\Product::query("SELECT * FROM Producten WHERE Groep = 'Domeinen' AND Zichtbaar = 1 ORDER BY Volgorde LIMIT 5") as $d): ?>
                <a href="<?= e(url('/diensten/' . $d->Slug)) ?>"><strong><?= e(strtok((string) $d->Naam, '-')) ?></strong> <?= e(money((int) $d->Prijs)) ?><small>/jaar</small></a>
            <?php endforeach; ?>
        </div>
        <div class="dhero-usps"><span><?= icon('check', 'icon icon-sm') ?> Direct actief</span><span><?= icon('check', 'icon icon-sm') ?> Gratis DNS-beheer</span><span><?= icon('check', 'icon icon-sm') ?> Gratis SSL</span><span><?= icon('check', 'icon icon-sm') ?> Persoonlijke support</span></div>
    </div>
</section>

<section class="sec">
    <div class="container about-split">
        <div>
            <span class="eyebrow"><?= icon('building', 'icon icon-sm') ?> Over <?= e(company_name()) ?></span>
            <h2 class="h-xl"><?= e((string) setting('over.titel', 'Over ons')) ?></h2>
            <div class="prose"><?= rich_text((string) setting('over.tekst', '')) ?></div>
            <div class="row" style="margin-top:22px"><a class="btn btn-lg" href="<?= e(url('/over-ons')) ?>">Meer over ons <?= icon('arrow-right', 'icon icon-sm') ?></a><a class="btn btn-lg btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Kennismaken</a></div>
        </div>
        <div class="about-visual<?= $overImg !== '' ? ' has-img' : '' ?>">
            <?php if ($overImg !== ''): ?><img src="<?= e($overImg) ?>" alt="<?= e(company_name()) ?>"><?php else: ?>
                <div class="av-card">
                    <div class="av-rack"><?php for ($i = 0; $i < 4; $i++): ?><span><i></i><i></i><i></i><b></b></span><?php endfor; ?></div>
                    <div class="av-stats"><div><strong>99,9%</strong><small>uptime</small></div><div><strong>24/7</strong><small>monitoring</small></div><div><strong><?= count($datacenters) ?></strong><small>datacenters</small></div></div>
                </div>
            <?php endif; ?>
            <div class="av-badge"><?= icon('shield') ?><div><strong>Ijzersterk verbonden</strong><small>Eigen netwerk en datacenters</small></div></div>
        </div>
    </div>
</section>

<?php if ($featured !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('server', 'icon icon-sm') ?> Pakketten</span><h2>Populaire pakketten</h2><p>Kies het pakket dat bij je past; upgraden kan altijd.</p></div><a class="btn btn-ghost" href="<?= e(url('/diensten')) ?>">Alle diensten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <div class="grid grid-3"><?php foreach ($featured as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
    </div>
</section>
<?php endif; ?>

<?php if ($projects !== []): ?>
<section class="sec">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('image', 'icon icon-sm') ?> Portfolio</span><h2>Ons werk</h2><p>Een greep uit de projecten die we voor ondernemers realiseerden.</p></div><a class="btn btn-ghost" href="<?= e(url('/portfolio')) ?>">Hele portfolio <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <?= $partial('frontend._carousel', ['items' => $projects, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project', 'autoplay' => true]) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($logos !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('code', 'icon icon-sm') ?> Technieken</span><h2>Wij werken met de beste software</h2><p>Van webserver tot CMS: wij kennen het, hosten het en ondersteunen het.</p></div></div>
        <div class="logos" data-logo-tabs>
            <div class="logo-tabs"><?php $i = 0; foreach (array_keys($logos) as $group): ?><button type="button" class="<?= $i++ === 0 ? 'active' : '' ?>" data-logo-tab="<?= e(slugify($group)) ?>"><?= e($group) ?></button><?php endforeach; ?></div>
            <?php $i = 0; foreach ($logos as $group => $items): ?>
                <div class="logo-pane<?= $i++ === 0 ? ' active' : '' ?>" data-logo-pane="<?= e(slugify($group)) ?>">
                    <div class="marquee"><div class="marquee-track">
                        <?php for ($copy = 0; $copy < 2; $copy++): foreach ($items as $l): $src = media_url((string) $l['Afbeelding']); $tag = $l['Url'] ? 'a' : 'span'; ?>
                            <<?= $tag ?> class="logo-item"<?= $l['Url'] ? ' href="' . e($l['Url']) . '" target="_blank" rel="noopener"' : '' ?><?= $copy ? ' aria-hidden="true"' : '' ?>><?php if ($src !== ''): ?><img src="<?= e($src) ?>" alt="<?= e($l['Naam']) ?>" loading="lazy"><?php endif; ?><span><?= e($l['Naam']) ?></span></<?= $tag ?>>
                        <?php endforeach; endfor; ?>
                    </div></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($datacenters !== []): ?>
<section class="sec">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('pin', 'icon icon-sm') ?> Netwerk</span><h2>Onze datacenters</h2><p>Jouw diensten draaien in moderne datacenters met redundante stroom en verbindingen.</p></div></div>
        <?= $partial('frontend._map', ['datacenters' => $datacenters]) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($posts !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Blog</span><h2>Laatste nieuws &amp; tips</h2><p>Updates over onze diensten en handige tips voor je online aanwezigheid.</p></div><a class="btn btn-ghost" href="<?= e(url('/blog')) ?>">Alle berichten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note']) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($articles !== []): ?>
<section class="sec">
    <div class="container kb-home">
        <div>
            <span class="eyebrow"><?= icon('book', 'icon icon-sm') ?> Kennisbank</span>
            <h2 class="h-xl">Zelf aan de slag</h2>
            <p class="muted">Handleidingen en antwoorden op veelgestelde vragen, stap voor stap uitgelegd.</p>
            <form class="kb-search" action="<?= e(url('/kennisbank')) ?>" method="get"><?= icon('search') ?><input name="q" placeholder="Waar kunnen we je mee helpen?" aria-label="Zoeken in de kennisbank"></form>
            <a class="btn btn-ghost" href="<?= e(url('/kennisbank')) ?>">Naar de kennisbank <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>
        <div class="kb-list">
            <?php foreach ($articles as $a): ?>
                <a class="kb-item" href="<?= e(url('/kennisbank/' . $a['Slug'])) ?>"><span class="kb-ic"><?= icon('book', 'icon icon-sm') ?></span><span><strong><?= e($a['Titel']) ?></strong><small><?= e($a['Categorie']) ?> · <?= e((string) $a['Samenvatting']) ?></small></span><?= icon('chevron-right', 'icon icon-sm') ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="sec sec-cta">
    <div class="container">
        <div class="cta">
            <div><h2>Hulp nodig bij je keuze?</h2><p>We denken graag mee over de beste oplossing voor jouw bedrijf.</p></div>
            <div class="row"><a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Contact opnemen <?= icon('arrow-right', 'icon icon-sm') ?></a><?php $tel = (string) setting('topbar.telefoon', ''); if ($tel !== ''): ?><a class="btn btn-lg btn-ghost" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $tel)) ?>"><?= icon('phone', 'icon icon-sm') ?> <?= e($tel) ?></a><?php endif; ?></div>
        </div>
    </div>
</section>
