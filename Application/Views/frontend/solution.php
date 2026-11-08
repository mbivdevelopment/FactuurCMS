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
            <div class="solution-photo-card">
                <img src="<?= e(asset('img/qevanto-datacenter.jpg')) ?>" alt="Datacenter met Qevanto hostinginfrastructuur">
                <span class="solution-photo-caption"><?= icon('server', 'icon icon-sm') ?> Qevanto-infrastructuur in Nederland</span>
            </div>
            <div class="hh-badge hh-b1"><?= icon('zap') ?><div><strong>Direct actief</strong><small>na betaling</small></div></div>
            <div class="hh-badge hh-b2"><?= icon('shield') ?><div><strong>Veilig geregeld</strong><small>via Qevanto Hostingpaneel</small></div></div>
        </div>
    </div>
</section>

<section class="sec"><div class="container intro-split dn-split">
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
</div></section>

<?php if ($isWebhosting): ?>
<section class="sec sec-grey solution-copy-section"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('server', 'icon icon-sm') ?> Webhosting bij Qevanto</span><h2>Alles voor een snelle en betrouwbare website</h2><p>Geen ingewikkelde techniek, wel een solide basis voor je website, webshop of online project.</p></div></div>
    <div class="solution-info-grid">
        <article class="solution-info-card"><span class="solution-info-icon"><?= icon('server') ?></span><h3>Webhosting bij Qevanto</h3><p>Je website draait op snelle NVMe-opslag en moderne webservers in Nederlandse datacenters. Wij regelen de infrastructuur, monitoring en beveiliging; jij houdt tijd over voor je bedrijf.</p><p>Met het eigen Qevanto Hostingpaneel beheer je websites, databases, domeinen, SSL en e-mail vanuit één omgeving.</p></article>
        <article class="solution-info-card"><span class="solution-info-icon"><?= icon('globe') ?></span><h3>Wat is webhosting?</h3><p>Webhosting is de plek waar de bestanden, afbeeldingen en databases van je website worden opgeslagen. Wanneer iemand je domeinnaam bezoekt, levert onze server deze bestanden veilig aan.</p><p>Een goed hostingpakket zorgt dat je website bereikbaar, snel en beschermd blijft — ook wanneer het drukker wordt.</p></article>
        <article class="solution-info-card"><span class="solution-info-icon"><?= icon('zap') ?></span><h3>Voor wie is dit?</h3><p>Van een eenvoudige bedrijfswebsite en blog tot een WordPress-site of webshop: er is een pakket voor iedere fase.</p><p>Start klein en schaal later eenvoudig op. Je hoeft niet opnieuw te verhuizen en je instellingen blijven behouden.</p></article>
    </div>
</div></section>
<?php endif; ?>

<?php if ($products !== []): ?><section class="sec sec-grey" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies het pakket dat bij je past</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section><?php endif; ?>

<section class="sec"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom <?= e(mb_strtolower((string) $page['Titel'])) ?> bij <?= e(company_name()) ?></h2></div></div><?= $partial('frontend._benefits', ['items' => $benefits]) ?></div></section>
<section class="sec sec-dark" id="werking"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.</p></div></div><?= $partial('frontend._steps', ['items' => $steps]) ?></div></section>
<section class="sec"><div class="container faq-split"><div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Vragen over <?= e(mb_strtolower((string) $page['Titel'])) ?>?</h2><p class="muted">Staat je vraag er niet bij? Onze specialisten helpen je graag persoonlijk verder.</p><a class="btn btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Stel je vraag</a></div><?= $partial('frontend._faq', ['items' => $faq]) ?></div></section>
<?= $partial('frontend._cta', ['title' => 'Klaar om te starten?', 'text' => 'Kies je pakket en regel het direct online. Onze specialisten helpen je graag bij de overstap.']) ?>
