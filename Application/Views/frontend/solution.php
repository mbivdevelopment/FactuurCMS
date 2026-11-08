<?php
$extend('frontend', ['title' => $page['Titel']]);
$copy = [
    'Webhosting' => ['icon' => 'server', 'headline' => 'Webhosting voor websites die vooruit willen.', 'sub' => 'Een snelle, veilige en betrouwbare basis voor je website, webshop of online project — beheerd vanuit het eigen Qevanto Hostingpaneel.', 'visual' => 'Qevanto Hostingpaneel · Webhosting · Overzicht'],
    'Reseller Hosting' => ['icon' => 'users', 'headline' => 'Jouw hostingbedrijf, onze infrastructuur.', 'sub' => 'Bied klanten professionele hosting onder je eigen merk.', 'visual' => 'Resellerportaal · Klanten · Accounts'],
    'E-mail Hosting' => ['icon' => 'mail', 'headline' => 'Zakelijke e-mail zonder zorgen.', 'sub' => 'Betrouwbare mailboxen op je eigen domeinnaam, overal beschikbaar.', 'visual' => 'Webmail · Inbox · Beveiligde verbinding'],
];
$c = $copy[(string) $soort] ?? ['icon' => $page['Icoon'], 'headline' => $page['Titel'], 'sub' => $page['Samenvatting'], 'visual' => $page['Titel']];
$isWebhosting = $soort === 'Webhosting';
?>
<section class="hhero hero2">
    <div class="hh-float" aria-hidden="true"><?php foreach ([$c['icon'], 'shield', 'zap', 'server', 'lock', 'cloud', 'users', 'refresh'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-text">
            <span class="hh-label"><?= icon($c['icon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?></span>
            <h1><?= e($c['headline']) ?></h1>
            <p><?= e($c['sub']) ?></p>
            <div class="hh-btns"><a class="btn btn-lg" href="#pakketten"><?= icon('cart', 'icon icon-sm') ?> Bekijk pakketten</a><a class="btn btn-lg btn-ghost" href="#werking">Hoe het werkt <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div class="hh-visual solution-photo-wrap">
            <?php if ($isWebhosting): ?>
            <div class="hh-rack solution-rack" aria-label="Rack-server met actieve statuslampjes">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="hh-unit"><span class="hh-bays"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div>
                <?php endfor; ?>
                <div class="hh-base"></div>
            </div>
            <?php else: ?>
            <div class="solution-photo-card">
                <img src="<?= e(asset('img/qevanto-datacenter.jpg')) ?>" alt="Datacenter met Qevanto hostinginfrastructuur">
                <span class="solution-photo-caption"><?= icon('server', 'icon icon-sm') ?> Qevanto-infrastructuur in Nederland</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="sec"><div class="container intro-split dn-split<?= $isWebhosting ? ' solution-intro-webhosting' : '' ?>">
    <?php if ($isWebhosting): ?>
    <div class="solution-intro-photo">
        <img src="<?= e(asset('img/qevanto-datacenter.jpg')) ?>" alt="Servers in een datacenter van Qevanto">
        <span class="solution-photo-caption"><?= icon('server', 'icon icon-sm') ?> Hostinginfrastructuur van Qevanto</span>
    </div>
    <div>
        <span class="eyebrow"><?= icon('server', 'icon icon-sm') ?> Webhosting bij Qevanto</span>
        <h2 class="h-xl">Een betrouwbare plek voor je website</h2>
        <div class="prose">
            <p>Webhosting is de serverruimte waar de bestanden, afbeeldingen en databases van je website worden opgeslagen. Zodra iemand je domeinnaam bezoekt, zorgt onze infrastructuur ervoor dat je website snel en veilig wordt geladen.</p>
            <p>Bij Qevanto draait je website op moderne NVMe-servers in Nederlandse datacenters. Je krijgt een stabiele basis voor een bedrijfswebsite, webshop, blog of online platform, zonder dat je zelf servers hoeft te beheren.</p>
            <p>Via het eigen Qevanto Hostingpaneel regel je alles op één plek. Je beheert je website, domeinen, databases, SSL en e-mail vanuit een duidelijke omgeving. Na betaling wordt je hosting automatisch geactiveerd en kun je direct aan de slag.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="dn-visual" aria-hidden="true">
        <div class="dn-browser">
            <div class="dn-bar"><i></i><i></i><i></i><span class="dn-url"><?= icon($c['icon'], 'icon icon-sm') ?> <?= e($c['visual']) ?></span></div>
            <div class="dn-body dn-dns">
                <div class="dn-dns-head"><strong><?= e($page['Titel']) ?></strong><span class="badge is-ok">Online</span></div>
                <?php foreach (array_slice($benefits, 0, 4) as [$ic, $title, $text]): ?><div class="dn-rec"><span class="dn-type"><?= icon($ic, 'icon icon-sm') ?></span><strong><?= e($title) ?></strong><span class="dn-val">✓</span></div><?php endforeach; ?>
            </div>
        </div>
        <div class="dn-chip dn-c1"><?= icon('zap') ?> Snel geregeld</div>
        <div class="dn-chip dn-c2"><?= icon('lock') ?> Veilig beheerd</div>
    </div>
    <div>
        <span class="eyebrow"><?= icon($c['icon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?></span>
        <h2 class="h-xl"><?= e($page['Titel']) ?> die gewoon werkt</h2>
        <div class="prose"><?= rich_text((string) $page['Inhoud']) ?></div>
        <div class="info-box"><?= icon('shield') ?><div><strong>Goed om te weten</strong><p>Je beheert alles vanuit één klantportaal. Na betaling wordt je dienst automatisch geactiveerd en staan de gegevens direct klaar.</p></div></div>
    </div>
    <?php endif; ?>
</div></section>

<?php if ($products !== []): ?><section class="sec sec-grey" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies het pakket dat bij je past</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section><?php endif; ?>

<section class="sec"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom <?= e(mb_strtolower((string) $page['Titel'])) ?> bij <?= e(company_name()) ?></h2></div></div><?= $partial('frontend._benefits', ['items' => $benefits]) ?></div></section>
<section class="sec sec-dark" id="werking"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.</p></div></div><?= $partial('frontend._steps', ['items' => $steps]) ?></div></section>
<?php if (($posts ?? []) !== []): ?>
<section class="sec solution-blog-section"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Webhosting kennisbank</span><h2>Lees meer over webhosting</h2><p>Praktische uitleg over snelheid, veiligheid, updates en het beheer van je website.</p></div><a class="btn btn-ghost" href="<?= e(url('/blog?categorie=Webhosting')) ?>">Alle webhostingartikelen <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
    <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note', 'more' => 'Lees artikel', 'autoplay' => true, 'nav_top' => true]) ?>
</div></section>
<?php endif; ?>
