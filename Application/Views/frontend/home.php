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
