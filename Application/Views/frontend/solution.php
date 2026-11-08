<?php
$extend('frontend', ['title' => $page['Titel']]);
$copy = [
    'Webhosting' => ['icon' => 'server', 'headline' => 'Webhosting voor websites die vooruit willen.', 'sub' => 'Een snelle, veilige en betrouwbare basis voor je website, webshop of online project — beheerd vanuit het eigen Qevanto Hostingpaneel.', 'visual' => 'Qevanto Hostingpaneel · Webhosting · Overzicht'],
    'Reseller Hosting' => ['icon' => 'users', 'headline' => 'Jouw hostingbedrijf, onze infrastructuur.', 'sub' => 'Bied klanten professionele hosting onder je eigen merk.', 'visual' => 'Resellerportaal · Klanten · Accounts'],
    'E-mail Hosting' => ['icon' => 'mail', 'headline' => 'Zakelijke e-mail zonder zorgen.', 'sub' => 'Betrouwbare mailboxen op je eigen domeinnaam, overal beschikbaar.', 'visual' => 'Webmail · Inbox · Beveiligde verbinding'],
    'Informatieve Websites' => ['icon' => 'layout', 'headline' => 'Een website die jouw bedrijf laat groeien.', 'sub' => 'Een heldere, snelle en professionele website in jouw huisstijl.', 'visual' => 'Qevanto Webstudio · Website · Overzicht'],
    'Webshops' => ['icon' => 'cart', 'headline' => 'Een webshop die voor je verkoopt.', 'sub' => 'Een gebruiksvriendelijke webshop met betalingen, voorraad en koppelingen.', 'visual' => 'Qevanto Webshop · Bestellingen · Overzicht'],
    'API Koppelingen' => ['icon' => 'code', 'headline' => 'Laat je systemen samenwerken.', 'sub' => 'Betrouwbare koppelingen die gegevens automatisch tussen systemen uitwisselen.', 'visual' => 'Qevanto Integraties · API · Monitoring'],
];
$c = $copy[(string) $soort] ?? ['icon' => $page['Icoon'], 'headline' => $page['Titel'], 'sub' => $page['Samenvatting'], 'visual' => $page['Titel']];
$isWebhosting = $soort === 'Webhosting';
$isEmailHosting = $soort === 'E-mail Hosting';
$isReseller = $soort === 'Reseller Hosting';
$isApi = $soort === 'API Koppelingen';
$isInformativeWebsite = $soort === 'Informatieve Websites';
$isWebshop = $soort === 'Webshops';
$blogLabel = $isReseller ? 'Reseller Hosting' : ($isEmailHosting ? 'E-mail Hosting' : ($isInformativeWebsite ? 'Informatieve Websites' : ($isWebshop ? 'Webshops' : ($isApi ? 'API Koppelingen' : 'Webhosting'))));
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
            <?php if ($isWebhosting || $isEmailHosting || $isReseller || $isApi): ?>
            <div class="hh-rack solution-rack<?= $isEmailHosting ? ' mail-rack' : ($isReseller ? ' reseller-rack' : ($isApi ? ' api-rack' : '')) ?>" aria-label="<?= $isEmailHosting ? 'Mailserver met actieve statuslampjes' : ($isReseller ? 'Reseller-server met actieve statuslampjes' : ($isApi ? 'API-server met actieve statuslampjes' : 'Rack-server met actieve statuslampjes')) ?>">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="hh-unit"><span class="hh-bays"><?= $isEmailHosting ? icon('mail', 'icon icon-sm') : ($isReseller ? icon('users', 'icon icon-sm') : ($isApi ? icon('code', 'icon icon-sm') : '')) ?><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div>
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

<section class="sec"><div class="container intro-split dn-split<?= ($isWebhosting || $isEmailHosting || $isReseller || $isApi || $isInformativeWebsite || $isWebshop) ? ' solution-intro-webhosting' : '' ?>">
    <?php if ($isWebhosting || $isEmailHosting || $isReseller || $isApi || $isInformativeWebsite || $isWebshop): ?>
    <?php if ($isEmailHosting): ?>
    <div class="solution-intro-photo">
        <img src="<?= e(asset('img/qevanto-emailhosting.png')) ?>" alt="E-mailhosting-infrastructuur van Qevanto">
        <span class="solution-photo-caption"><?= icon('mail', 'icon icon-sm') ?> E-mailhosting bij Qevanto</span>
    </div>
    <?php elseif ($isReseller || $isApi || $isInformativeWebsite || $isWebshop): ?>
    <div class="solution-intro-photo">
        <img src="<?= e(asset($isReseller ? 'img/qevanto-resellerhosting.png' : ($isApi ? 'img/qevanto-api-koppelingen.png' : ($isInformativeWebsite ? 'img/qevanto-website.png' : 'img/qevanto-webshop.png')))) ?>" alt="<?= e($isReseller ? 'Resellerhosting-infrastructuur van Qevanto' : ($isApi ? 'API-infrastructuur van Qevanto' : ($isInformativeWebsite ? 'Website-infrastructuur van Qevanto' : 'Webshop-infrastructuur van Qevanto'))) ?>">
        <span class="solution-photo-caption"><?= icon($isReseller ? 'users' : ($isApi ? 'code' : ($isInformativeWebsite ? 'layout' : 'cart')), 'icon icon-sm') ?> <?= $isReseller ? 'Resellerhosting bij Qevanto' : ($isApi ? 'API-infrastructuur bij Qevanto' : ($isInformativeWebsite ? 'Websites bij Qevanto' : 'Webshops bij Qevanto')) ?></span>
    </div>
    <?php else: ?>
    <div class="solution-intro-photo">
        <img src="<?= e(asset($isWebhosting ? 'img/qevanto-webhosting.png' : 'img/qevanto-datacenter.jpg')) ?>" alt="<?= e($isWebhosting ? 'Webhostingservers van Qevanto' : 'Servers in een datacenter van Qevanto') ?>">
        <span class="solution-photo-caption"><?= icon('server', 'icon icon-sm') ?> Hostinginfrastructuur van Qevanto</span>
    </div>
    <?php endif; ?>
    <div>
        <span class="eyebrow"><?= icon($isEmailHosting ? 'mail' : 'server', 'icon icon-sm') ?> <?= $isEmailHosting ? 'E-mail Hosting bij Qevanto' : 'Webhosting bij Qevanto' ?></span>
        <h2 class="h-xl"><?= $isEmailHosting ? 'Zakelijke e-mail die overal betrouwbaar werkt' : 'Een betrouwbare plek voor je website' ?></h2>
        <div class="prose">
            <?php if ($isEmailHosting): ?>
            <p>E-mail is voor iedere organisatie een belangrijk communicatiekanaal. Met Qevanto E-mail Hosting krijg je professionele mailboxen op je eigen domeinnaam, zonder dat je zelf een mailserver hoeft te beheren of je zorgen hoeft te maken over updates, opslag en beveiliging.</p>
            <p>Onze mailinfrastructuur verwerkt je berichten veilig en betrouwbaar. Je gebruikt je mailbox op je computer, telefoon en tablet via webmail en standaardprotocollen, terwijl spam- en virusfilters ongewenste berichten tegenhouden voordat ze je inbox bereiken.</p>
            <p>Via het eigen Qevanto Hostingpaneel beheer je mailboxen, aliassen, wachtwoorden en domeininstellingen vanuit één duidelijke omgeving. TLS, SPF, DKIM en DMARC helpen je e-mail beter te beveiligen en zorgen ervoor dat berichten namens jouw domein herkenbaar en betrouwbaar worden afgeleverd.</p>
            <?php elseif ($isReseller): ?>
            <p>Resellerhosting geeft je de mogelijkheid om zelf hosting aan te bieden aan klanten, terwijl Qevanto de servers, beveiliging en technische infrastructuur beheert. Je bouwt je eigen dienstverlening op zonder zelf een datacenter of serveromgeving te hoeven onderhouden.</p>
            <p>Via het eigen Qevanto Hostingpaneel maak je klantaccounts aan, beheer je pakketten en houd je overzicht over je hostingomgeving. Je kunt onder je eigen merk werken met eigen nameservers en een professionele klantbeleving.</p>
            <p>De infrastructuur is ontworpen om mee te groeien met jouw klantenbestand. Nieuwe accounts voeg je eenvoudig toe, terwijl back-ups, monitoring en de dagelijkse technische werking op de achtergrond geregeld blijven.</p>
            <?php elseif ($isApi): ?>
            <p>API-koppelingen zorgen ervoor dat je systemen automatisch met elkaar samenwerken. In plaats van gegevens telkens handmatig over te typen, wisselen je webshop, boekhouding, CRM, voorraadbeheer en andere applicaties informatie uit wanneer dat nodig is.</p>
            <p>Qevanto ontwikkelt koppelingen die passen bij jouw processen en bestaande software. We brengen eerst in kaart welke gegevens moeten worden uitgewisseld en bouwen daarna een veilige verbinding met duidelijke logging en foutafhandeling.</p>
            <p>Zo blijven gegevens actueel, worden fouten sneller zichtbaar en houd je meer tijd over voor je organisatie. De koppeling kan meegroeien met je volumes en wordt onderhouden wanneer een aangesloten systeem of API verandert.</p>
            <?php elseif ($isInformativeWebsite): ?>
            <p>Een informatieve website is vaak het eerste contactmoment tussen jouw organisatie en een potentiële klant. Daarom moet de website helder uitleggen wat je doet, vertrouwen opbouwen en bezoekers op een natuurlijke manier naar contact of een aanvraag begeleiden.</p>
            <p>Qevanto bouwt websites die passen bij je merk, doelgroep en manier van werken. De inhoud, structuur en vormgeving worden afgestemd op de informatie die bezoekers nodig hebben, met aandacht voor snelheid, toegankelijkheid en een goede weergave op ieder scherm.</p>
            <p>Na oplevering beschik je over een professionele online basis die je zelf kunt beheren en die kan meegroeien met je organisatie. Hosting, SSL, back-ups en technisch onderhoud kunnen vanuit dezelfde Qevanto-omgeving worden geregeld.</p>
            <?php elseif ($isWebshop): ?>
            <p>Een webshop moet meer doen dan producten tonen. Hij moet bezoekers helpen kiezen, vertrouwen geven tijdens het afrekenen en bestellingen overzichtelijk verwerken. Een duidelijke structuur en een soepel bestelproces maken het verschil tussen rondkijken en daadwerkelijk bestellen.</p>
            <p>Qevanto bouwt webshops die aansluiten op je assortiment, betaalmethoden en dagelijkse werkzaamheden. Producten, voorraad, orders en klantgegevens komen samen in een omgeving die prettig werkt voor jou én voor je klanten.</p>
            <p>De webshop kan worden verbonden met betaalproviders, boekhouding, voorraadbeheer en andere systemen. Zo blijft je administratie actueel en houd je tijd over voor verkoop, service en de verdere groei van je online winkel.</p>
            <?php else: ?>
            <p>Webhosting is de serverruimte waar de bestanden, afbeeldingen en databases van je website worden opgeslagen. Zodra iemand je domeinnaam bezoekt, zorgt onze infrastructuur ervoor dat je website snel en veilig wordt geladen.</p>
            <p>Bij Qevanto draait je website op moderne NVMe-servers in Nederlandse datacenters. Je krijgt een stabiele basis voor een bedrijfswebsite, webshop, blog of online platform, zonder dat je zelf servers hoeft te beheren.</p>
            <p>Via het eigen Qevanto Hostingpaneel regel je alles op één plek. Je beheert je website, domeinen, databases, SSL en e-mail vanuit een duidelijke omgeving. Na betaling wordt je hosting automatisch geactiveerd en kun je direct aan de slag.</p>
            <?php endif; ?>
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

<?php if ($isApi): ?>
<section class="sec sec-grey integration-logo-section"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('link', 'icon icon-sm') ?> Integraties</span><h2>Koppel met de tools die je al gebruikt</h2><p>Van boekhouding en betalingen tot voorraad en bedrijfssoftware: jouw systemen werken samen vanuit één betrouwbare koppeling.</p></div></div>
    <div class="logo-grid">
        <?php foreach (['exact' => 'Exact', 'moneybird' => 'Moneybird', 'mollie' => 'Mollie', 'stripe' => 'Stripe', 'odoo' => 'Odoo', 'informer' => 'Informer', 'yuki' => 'Yuki', 'snelstart' => 'SnelStart'] as $slug => $name): ?>
        <span class="logo-item"><img src="<?= e(asset('img/logos/integrations/' . $slug . '.svg')) ?>" alt="" loading="lazy"><?= e($name) ?></span>
        <?php endforeach; ?>
    </div>
</div></section>
<?php endif; ?>

<?php if ($products !== []): ?><section class="sec sec-grey" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies het pakket dat bij je past</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section><?php endif; ?>

<section class="sec"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom <?= e(mb_strtolower((string) $page['Titel'])) ?> bij <?= e(company_name()) ?></h2></div></div><?= $partial('frontend._benefits', ['items' => $benefits]) ?></div></section>
<section class="sec sec-dark" id="werking"><div class="container"><div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.</p></div></div><?= $partial('frontend._steps', ['items' => $steps]) ?></div></section>
<?php if (($posts ?? []) !== []): ?>
<section class="sec solution-blog-section"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> <?= e($blogLabel) ?> kennisbank</span><h2>Lees meer over <?= e(mb_strtolower($blogLabel)) ?></h2><p>Praktische uitleg, tips en inzichten voor jouw hostingbedrijf.</p></div></div>
    <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note', 'more' => 'Lees artikel', 'autoplay' => true, 'nav_top' => true]) ?>
</div></section>
<?php endif; ?>
