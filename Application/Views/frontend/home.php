<?php $extend('frontend', ['title' => (string) setting('website.titel', 'Hosting')]); $overImg = branding_url('over'); $heroImg = branding_url('hero');
$homeTitle = preg_replace('/\{([^}]+)\}/', '<span>$1</span>', e((string) (setting('home.titel', '') ?: 'Hosting die gewoon {werkt}.')));
$homeText = (string) (setting('home.tekst', '') ?: 'Bij ' . company_name() . ' draait je website, webshop of applicatie op snelle NVMe-servers in Nederlandse datacenters. Wij regelen de techniek, de beveiliging en de back-ups, zodat jij kunt ondernemen.'); ?>
<section class="hhero">
    <div class="hh-float" aria-hidden="true"><?php foreach (['cloud', 'shield', 'server', 'globe', 'lock', 'zap', 'cpu', 'mail'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-visual">
            <?php if ($heroImg !== ''): ?><img class="hh-img" src="<?= e($heroImg) ?>" alt="<?= e(company_name()) ?>"><?php else: ?>
            <div class="hh-rack" aria-hidden="true">
                <?php for ($i = 0; $i < 5; $i++): ?><div class="hh-unit"><span class="hh-bays"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div><?php endfor; ?>
                <div class="hh-base"></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="hh-text">
            <span class="hh-label"><?= icon('server', 'icon icon-sm') ?> <?= e((string) (setting('home.label', '') ?: 'Hosting & cloud uit Nederland')) ?></span>
            <h1><?= $homeTitle ?></h1>
            <p><?= e($homeText) ?></p>
            <div class="hh-btns"><a class="btn btn-lg" href="<?= e(url('/diensten')) ?>"><?= icon('box', 'icon icon-sm') ?> Bekijk pakketten</a><a class="btn btn-lg btn-ghost" href="<?= e(url('/domeinen')) ?>"><?= icon('globe', 'icon icon-sm') ?> Domein zoeken</a></div>
            <div class="hh-pills"><span><?= icon('zap', 'icon icon-sm') ?> Snelle NVMe-servers</span><span><?= icon('headset', 'icon icon-sm') ?> Persoonlijke support</span><span><?= icon('globe', 'icon icon-sm') ?> Datacenters in Nederland</span></div>
        </div>
    </div>
</section>

<section class="sec">
    <div class="container about-split">
        <div>
            <span class="eyebrow"><?= icon('building', 'icon icon-sm') ?> Over <?= e(company_name()) ?></span>
            <h2 class="h-xl"><?= e((string) setting('over.titel', 'Over ons')) ?></h2>
            <div class="prose"><?= rich_text((string) setting('over.tekst', '') ?: (string) setting('website.intro', '')) ?></div>
            <div class="row" style="margin-top:22px"><a class="btn btn-lg" href="<?= e(url('/over-ons')) ?>">Meer over ons <?= icon('arrow-right', 'icon icon-sm') ?></a><a class="btn btn-lg btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Kennismaken</a></div>
        </div>
        <div class="about-visual<?= $overImg !== '' ? ' has-img' : '' ?>">
            <?php if ($overImg !== ''): ?><img src="<?= e($overImg) ?>" alt="<?= e(company_name()) ?>"><?php else: ?>
                <div class="av-card">
                    <div class="av-rack"><?php for ($i = 0; $i < 4; $i++): ?><span><i></i><i></i><i></i><b></b></span><?php endfor; ?></div>
                    <div class="av-stats"><div><strong>99,9%</strong><small>uptime</small></div><div><strong>24/7</strong><small>monitoring</small></div><?php if ($datacenters !== []): ?><div><strong><?= count($datacenters) ?></strong><small>datacenter<?= count($datacenters) === 1 ? '' : 's' ?></small></div><?php else: ?><div><strong>NVMe</strong><small>opslag</small></div><?php endif; ?></div>
                </div>
            <?php endif; ?>
            <div class="av-badge"><?= icon('shield') ?><div><strong>Ijzersterk verbonden</strong><small>Eigen netwerk en datacenters</small></div></div>
        </div>
    </div>
</section>

<?php if ($packages !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('server', 'icon icon-sm') ?> Shared hosting</span><h2>Onze hostingpakketten</h2><p>Snelle NVMe-servers in Nederlandse datacenters; upgraden kan altijd.</p></div><a class="btn btn-ghost" href="<?= e(url('/oplossingen/webhosting')) ?>">Alle hostingpakketten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <div class="car" data-carousel>
            <div class="car-track">
                <?php foreach ($packages as $p): ?><div class="car-item"><?= $partial('frontend._plan', ['p' => $p]) ?></div><?php endforeach; ?>
            </div>
            <div class="car-nav"><button type="button" class="car-btn" data-car-prev aria-label="Vorige"><?= icon('chevron-left') ?></button><span class="car-dots"></span><button type="button" class="car-btn" data-car-next aria-label="Volgende"><?= icon('chevron-right') ?></button></div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="sec">
    <div class="container">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Waarom <?= e(company_name()) ?></span><h2>Daarom kiezen ondernemers voor ons</h2><p>Geen gedoe, geen verrassingen: wij regelen de techniek zodat jij kunt ondernemen.</p></div></div>
        <?= $partial('frontend._usps') ?>
    </div>
</section>

<?php if ($projects !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('image', 'icon icon-sm') ?> Projecten</span><h2>Onze projecten</h2><p>Een greep uit de projecten die we voor ondernemers realiseerden.</p></div><a class="btn btn-ghost" href="<?= e(url('/portfolio')) ?>">Alle projecten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <?= $partial('frontend._carousel', ['items' => $projects, 'base' => '/portfolio/', 'icon' => 'image', 'more' => 'Bekijk project', 'autoplay' => true]) ?>
    </div>
</section>
<?php endif; ?>

<section class="sec">
    <div class="container about-split">
        <div>
            <span class="eyebrow"><?= icon('pin', 'icon icon-sm') ?> Netwerk</span>
            <h2 class="h-xl">Onze datacenters</h2>
            <div class="prose"><p>Jouw diensten draaien in moderne datacenters met redundante stroom en verbindingen, dichtbij in Nederland en met partners wereldwijd. Dankzij 24/7-monitoring, dagelijkse back-ups en ons eigen netwerk sta jij er nooit alleen voor.</p></div>
            <div class="row" style="margin-top:22px"><a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Vraag advies <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div>
            <?php if ($datacenters !== []): ?><?= $partial('frontend._map', ['datacenters' => $datacenters]) ?><?php endif; ?>
        </div>
    </div>
</section>

<?php if ($articles !== []): ?>
<section class="sec sec-alt">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('book', 'icon icon-sm') ?> Kennisbank</span><h2>Zelf aan de slag</h2><p>Handleidingen en antwoorden op veelgestelde vragen, stap voor stap uitgelegd.</p></div><a class="btn btn-ghost" href="<?= e(url('/kennisbank')) ?>">Naar de kennisbank <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <div class="kb-list kb-2rows">
            <?php foreach ($articles as $a): ?>
                <a class="kb-item" href="<?= e(url('/kennisbank/' . $a['Slug'])) ?>"><span class="kb-ic"><?= icon('book', 'icon icon-sm') ?></span><span><strong><?= e($a['Titel']) ?></strong><small><?= e($a['Categorie']) ?> · <?= e((string) $a['Samenvatting']) ?></small></span><?= icon('chevron-right', 'icon icon-sm') ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($posts !== []): ?>
<section class="sec">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Blog</span><h2>Laatste nieuws &amp; tips</h2><p>Updates over onze diensten en handige tips voor je online aanwezigheid.</p></div><a class="btn btn-ghost" href="<?= e(url('/blog')) ?>">Alle berichten <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note']) ?>
    </div>
</section>
<?php endif; ?>
