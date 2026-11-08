<?php
$extend('frontend', ['title' => $page['Titel']]);
$copy = [
    'Webhosting' => ['icon' => 'server', 'headline' => 'Webhosting voor websites die vooruit willen.', 'sub' => 'Een snelle, veilige en betrouwbare basis voor je website, webshop of online project — beheerd vanuit het eigen Qevanto Hostingpaneel.', 'visual' => 'Qevanto Hostingpaneel · Webhosting · Overzicht'],
    'Reseller Hosting' => ['icon' => 'users', 'headline' => 'Jouw hostingbedrijf, onze infrastructuur.', 'sub' => 'Bied klanten professionele hosting onder je eigen merk.', 'visual' => 'Resellerportaal · Klanten · Accounts'],
    'E-mail Hosting' => ['icon' => 'mail', 'headline' => 'Zakelijke e-mail zonder zorgen.', 'sub' => 'Betrouwbare mailboxen op je eigen domeinnaam, overal beschikbaar.', 'visual' => 'Webmail · Inbox · Beveiligde verbinding'],
    'Informatieve Websites' => ['icon' => 'layout', 'headline' => 'Een website die jouw bedrijf laat groeien.', 'sub' => 'Een heldere, snelle en professionele website in jouw huisstijl.', 'visual' => 'Qevanto Webstudio · Website · Overzicht'],
    'Webshops' => ['icon' => 'cart', 'headline' => 'Een webshop die voor je verkoopt.', 'sub' => 'Een gebruiksvriendelijke webshop met betalingen, voorraad en koppelingen.', 'visual' => 'Qevanto Webshop · Bestellingen · Overzicht'],
    'API Koppelingen' => ['icon' => 'code', 'headline' => 'Laat je systemen samenwerken.', 'sub' => 'Betrouwbare koppelingen die gegevens automatisch tussen systemen uitwisselen.', 'visual' => 'Qevanto Integraties · API · Monitoring'],
    'Cloud VPS' => ['icon' => 'cloud', 'headline' => 'Meer vrijheid voor je online omgeving.', 'sub' => 'Schaalbare Cloud VPS met vaste resources, root-toegang en controle vanuit één omgeving.', 'visual' => 'Qevanto Cloud VPS · Resources · Overzicht'],
    'Dedicated Server' => ['icon' => 'server', 'headline' => 'Alle serverkracht volledig voor jou.', 'sub' => 'Een dedicated server met exclusieve hardware voor maximale controle, prestaties en continuïteit.', 'visual' => 'Qevanto Dedicated Server · Hardware · Overzicht'],
    'Big Storage' => ['icon' => 'box', 'headline' => 'Veel opslag voor data die telt.', 'sub' => 'Big Storage voor back-ups, archieven, media en grote bestanden met ruimte om door te groeien.', 'visual' => 'Qevanto Big Storage · Capaciteit · Overzicht'],
    'SSL Certificaten' => ['icon' => 'shield', 'headline' => 'Laat bezoekers veilig vertrouwen op je website.', 'sub' => 'SSL-certificaten versleutelen verkeer, tonen het slotje en geven klanten zekerheid bij ieder bezoek.', 'visual' => 'Qevanto SSL · Certificaat · Beveiligde verbinding'],
    'VPN' => ['icon' => 'lock', 'headline' => 'Veilig verbonden, waar je ook werkt.', 'sub' => 'Een beheerde VPN voor medewerkers, locaties en apparaten met veilige toegang tot jouw bedrijfsnetwerk.', 'visual' => 'Qevanto VPN · Verbindingen · Beveiligd'],
    'Werkplek Beheer' => ['icon' => 'headset', 'headline' => 'Werkplekken die veilig en soepel blijven werken.', 'sub' => 'Wij beheren laptops, accounts, updates en beveiliging zodat je medewerkers zonder technische zorgen doorwerken.', 'visual' => 'Qevanto Werkplekbeheer · Apparaten · Overzicht'],
    'VoIP' => ['icon' => 'phone', 'headline' => 'Zakelijk bereikbaar op ieder apparaat.', 'sub' => 'Professionele VoIP-telefonie met vaste nummers, keuzemenu’s, wachtrijen en flexibel beheer.', 'visual' => 'Qevanto VoIP · Telefonie · Bereikbaarheid'],
    'Licenties' => ['icon' => 'file', 'headline' => 'De juiste softwarelicenties, goed geregeld.', 'sub' => 'Wij beheren Microsoft 365 en andere softwarelicenties, zodat iedereen toegang heeft tot wat nodig is.', 'visual' => 'Qevanto Licenties · Gebruikers · Overzicht'],
    'Marketing SEO' => ['icon' => 'trend', 'headline' => 'Word beter gevonden door de juiste klanten.', 'sub' => 'Technische SEO, content en strategie voor duurzame zichtbaarheid en meer relevante bezoekers.', 'visual' => 'Qevanto SEO · Groei · Rapportage'],
    'Social Media Onderhoud' => ['icon' => 'chat', 'headline' => 'Blijf zichtbaar met social media die doorloopt.', 'sub' => 'Wij plannen, maken en beheren content in jouw stijl, zodat je kanalen actief en professioneel blijven.', 'visual' => 'Qevanto Social Media · Content · Planning'],
    'Linkbuilding' => ['icon' => 'link', 'headline' => 'Bouw online autoriteit met sterke vermeldingen.', 'sub' => 'Relevante backlinks en inhoudelijke PR die je vindbaarheid duurzaam versterken.', 'visual' => 'Qevanto Linkbuilding · Autoriteit · Groei'],
];
$c = $copy[(string) $soort] ?? ['icon' => $page['Icoon'], 'headline' => $page['Titel'], 'sub' => $page['Samenvatting'], 'visual' => $page['Titel']];
$isWebhosting = $soort === 'Webhosting';
$isEmailHosting = $soort === 'E-mail Hosting';
$isReseller = $soort === 'Reseller Hosting';
$isApi = $soort === 'API Koppelingen';
$isInformativeWebsite = $soort === 'Informatieve Websites';
$isWebshop = $soort === 'Webshops';
$isCloudVps = $soort === 'Cloud VPS';
$isDedicated = $soort === 'Dedicated Server';
$isBigStorage = $soort === 'Big Storage';
$isServerService = $isCloudVps || $isDedicated || $isBigStorage;
$isSsl = $soort === 'SSL Certificaten';
$isVpn = $soort === 'VPN';
$isWorkspace = $soort === 'Werkplek Beheer';
$isVoip = $soort === 'VoIP';
$isLicenses = $soort === 'Licenties';
$isMarketing = $soort === 'Marketing SEO';
$isSocial = $soort === 'Social Media Onderhoud';
$isLinkbuilding = $soort === 'Linkbuilding';
$isEnhanced = $isWebhosting || $isEmailHosting || $isReseller || $isApi || $isInformativeWebsite || $isWebshop || $isServerService || $isSsl || $isVpn || $isWorkspace || $isVoip || $isLicenses || $isMarketing || $isSocial || $isLinkbuilding;
$blogLabel = $isReseller ? 'Reseller Hosting' : ($isEmailHosting ? 'E-mail Hosting' : ($isInformativeWebsite ? 'Informatieve Websites' : ($isWebshop ? 'Webshops' : ($isApi ? 'API Koppelingen' : 'Webhosting'))));
$introHeading = match ((string) $soort) {
    'E-mail Hosting' => 'Zakelijke e-mail die overal betrouwbaar werkt',
    'Cloud VPS' => 'Een eigen serveromgeving die met je meegroeit',
    'Dedicated Server' => 'Volledige serverkracht zonder gedeelde hardware',
    'Big Storage' => 'Ruimte voor al je belangrijke data',
    'SSL Certificaten' => 'Beveiliging die bezoekers direct herkennen',
    'VPN' => 'Veilig werken vanaf iedere locatie',
    'Werkplek Beheer' => 'Een moderne werkplek zonder technische zorgen',
    'VoIP' => 'Zakelijk bellen met ruimte om te groeien',
    'Licenties' => 'Softwarebeheer dat rust en overzicht geeft',
    'Marketing SEO' => 'Structureel groeien in organische vindbaarheid',
    'Social Media Onderhoud' => 'Een actieve aanwezigheid op de kanalen die tellen',
    'Linkbuilding' => 'Online autoriteit opbouwen met relevante links',
    default => 'Een betrouwbare plek voor je website',
};
?>
<section class="hhero hero2">
    <div class="hh-float" aria-hidden="true"><?php foreach ([$c['icon'], 'shield', 'zap', 'server', 'lock', 'cloud', 'users', 'refresh'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-text">
            <span class="hh-label"><?= icon($c['icon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?></span>
            <h1><?= e($c['headline']) ?></h1>
            <p><?= e($c['sub']) ?></p>
            <div class="hh-btns"><a class="btn btn-lg" href="<?= $products !== [] ? '#pakketten' : '/contact' ?>"><?= icon($products !== [] ? 'cart' : 'headset', 'icon icon-sm') ?> <?= $products !== [] ? 'Bekijk pakketten' : 'Bespreek je situatie' ?></a><a class="btn btn-lg btn-ghost" href="#werking">Hoe het werkt <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div class="hh-visual solution-photo-wrap">
            <?php if ($isWebhosting || $isEmailHosting || $isReseller || $isApi || $isServerService || $isSsl || $isVpn || $isWorkspace || $isVoip || $isLicenses || $isMarketing || $isSocial || $isLinkbuilding): ?>
            <div class="hh-rack solution-rack<?= $isEmailHosting ? ' mail-rack' : ($isReseller ? ' reseller-rack' : ($isApi ? ' api-rack' : ($isCloudVps ? ' vps-rack' : ($isDedicated ? ' dedicated-rack' : ($isBigStorage ? ' storage-rack' : ''))))) ?>" aria-label="<?= e($page['Titel']) ?> met actieve statuslampjes">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="hh-unit"><span class="hh-bays"><?= icon($c['icon'], 'icon icon-sm') ?><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div>
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

<section class="sec"><div class="container intro-split dn-split<?= $isEnhanced ? ' solution-intro-webhosting' : '' ?>">
    <?php if ($isEnhanced): ?>
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
        <img src="<?= e(asset($isWebhosting ? 'img/qevanto-webhosting.png' : ($isCloudVps ? 'img/qevanto-cloudvps.png' : ($isVpn ? 'img/qevanto-vpn.png' : 'img/qevanto-datacenter.jpg')))) ?>" alt="<?= e($isWebhosting ? 'Webhostingservers van Qevanto' : ($isCloudVps ? 'Cloud VPS-infrastructuur van Qevanto' : ($isVpn ? 'VPN-infrastructuur van Qevanto' : 'Servers in een datacenter van Qevanto'))) ?>">
        <span class="solution-photo-caption"><?= icon($isCloudVps ? 'cloud' : ($isVpn ? 'lock' : 'server'), 'icon icon-sm') ?> <?= $isCloudVps ? 'Cloud VPS bij Qevanto' : ($isVpn ? 'VPN bij Qevanto' : 'Hostinginfrastructuur van Qevanto') ?></span>
    </div>
    <?php endif; ?>
    <div>
        <span class="eyebrow"><?= icon($c['icon'], 'icon icon-sm') ?> <?= e($page['Titel']) ?> bij Qevanto</span>
        <h2 class="h-xl"><?= e($introHeading) ?></h2>
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
            <?php elseif ($isCloudVps): ?>
            <p>Een Cloud VPS geeft je een eigen, schaalbare serveromgeving voor websites, applicaties, databases en andere online diensten. Je beschikt over gereserveerde resources en root-toegang, zonder dat je zelf fysieke hardware hoeft aan te schaffen of te onderhouden.</p>
            <p>Je kiest zelf het besturingssysteem en de configuratie die bij je project past. Vanuit het Qevanto Hostingpaneel beheer je snapshots, herstarts, upgrades en de belangrijkste instellingen vanuit één duidelijke omgeving.</p>
            <p>Wanneer je organisatie groeit, schaal je CPU, geheugen en opslag eenvoudig mee. Zo blijft je omgeving stabiel tijdens pieken en houd je de vrijheid om software en serverinstellingen zelf te beheren.</p>
            <?php elseif ($isDedicated): ?>
            <p>Met een Dedicated Server krijg je een volledige fysieke server exclusief voor jouw organisatie. Alle rekenkracht, geheugen, opslag en netwerkcapaciteit zijn beschikbaar voor jouw toepassingen, zonder invloed van andere gebruikers op dezelfde hardware.</p>
            <p>Deze oplossing past bij bedrijfskritische applicaties, grote databases, drukbezochte websites en omgevingen waarbij voorspelbare prestaties belangrijk zijn. Je kiest de hardware die aansluit op je workload en houdt volledige controle over de inrichting.</p>
            <p>Qevanto verzorgt de colocatie, netwerkverbinding en basisinfrastructuur, terwijl jij bepaalt hoe de server wordt ingezet. Met remote beheer en ondersteuning kun je snel ingrijpen wanneer dat nodig is.</p>
            <?php elseif ($isBigStorage): ?>
            <p>Big Storage biedt ruime en betrouwbare opslag voor back-ups, archieven, mediabestanden en andere data die je veilig wilt bewaren. Je krijgt veel capaciteit tegen een helder tarief, zonder te investeren in een eigen opslagomgeving.</p>
            <p>De opslag is geschikt als centrale datalaag naast je Cloud VPS of als zelfstandige omgeving voor bestanden en back-ups. Door de ruime capaciteit blijft er voldoende ruimte over wanneer je organisatie en datavolume groeien.</p>
            <p>Via het Qevanto Hostingpaneel houd je overzicht over je opslag en beheer je de toegang tot je omgeving. Zo combineer je schaalbaarheid met een praktische oplossing voor dagelijkse opslag en langdurige bewaring.</p>
            <?php elseif ($isSsl): ?>
            <p>Een SSL-certificaat versleutelt de verbinding tussen je website en bezoekers. Daardoor blijven gegevens zoals formulieren, inloggegevens en betaalinformatie beschermd terwijl ze tussen browser en server worden uitgewisseld.</p>
            <p>Bezoekers herkennen een beveiligde website aan het slotje en de HTTPS-verbinding. Voor webshops, klantportalen en zakelijke websites is dat niet alleen technisch belangrijk, maar ook een zichtbaar onderdeel van vertrouwen en professionaliteit.</p>
            <p>Qevanto helpt met de juiste certificaatkeuze, installatie en verlenging. Van een automatisch certificaat voor een gewone website tot wildcard- en organisatievalidatie voor uitgebreidere omgevingen: alles blijft overzichtelijk geregeld.</p>
            <?php elseif ($isVpn): ?>
            <p>Een zakelijke VPN verbindt medewerkers, vestigingen en apparaten veilig met de systemen die zij nodig hebben. De verbinding is versleuteld, zodat medewerkers ook buiten kantoor verantwoord kunnen werken.</p>
            <p>Qevanto richt de VPN in op de manier waarop jouw organisatie werkt. Gebruikers, locaties en toegangsrechten worden overzichtelijk beheerd, terwijl logging en sterke authenticatie helpen om controle te houden.</p>
            <p>Of medewerkers nu thuiswerken, onderweg zijn of op verschillende locaties werken, zij krijgen een betrouwbare toegang tot het bedrijfsnetwerk. Wij helpen met installatie, beheer en uitbreiding wanneer je organisatie groeit.</p>
            <?php elseif ($isWorkspace): ?>
            <p>Werkplekbeheer zorgt ervoor dat laptops, computers, accounts en mobiele apparaten veilig en bruikbaar blijven. Medewerkers kunnen daardoor werken met een omgeving die goed is ingericht en automatisch wordt bijgewerkt.</p>
            <p>Qevanto beheert updates, beveiliging, accounts en de belangrijkste instellingen op afstand. Nieuwe medewerkers kunnen sneller starten en bij een vertrek trek je toegang gecontroleerd in zonder losse stappen te vergeten.</p>
            <p>Je hebt één aanspreekpunt voor technische vragen en dagelijkse ondersteuning. Door monitoring en proactief onderhoud signaleren we problemen voordat ze de productiviteit van je team raken.</p>
            <?php elseif ($isVoip): ?>
            <p>Met VoIP bel je zakelijk via internet met je bestaande nummer of een nieuwe nummerreeks. Medewerkers zijn bereikbaar op hun bureautoestel, laptop en smartphone, zonder dat telefonie vastzit aan één werkplek.</p>
            <p>Een professionele centrale kan worden ingericht met keuzemenu’s, wachtrijen, voicemail naar e-mail en doorschakelingen. Zo krijgt iedere beller snel de juiste medewerker en blijft de telefonie overzichtelijk.</p>
            <p>Qevanto helpt met nummerbehoud, inrichting en uitbreiding. Nieuwe gebruikers en toestellen voeg je eenvoudig toe wanneer je team groeit, terwijl je inzicht houdt in bereikbaarheid en gebruik.</p>
            <?php elseif ($isLicenses): ?>
            <p>De juiste softwarelicenties zorgen ervoor dat medewerkers toegang hebben tot de programma’s die zij nodig hebben. Qevanto helpt met Microsoft 365 en andere zakelijke software, zodat licenties aansluiten op je organisatie.</p>
            <p>Wij houden gebruikers, abonnementsvormen en wijzigingen overzichtelijk bij. Wanneer iemand start, vertrekt of van rol verandert, pas je de licenties aan zonder onnodig te blijven betalen.</p>
            <p>Ook beveiligingsfuncties zoals MFA, Defender en back-upsoftware kunnen onderdeel zijn van de inrichting. Zo combineer je legaal gebruik met een werkbare en beter beveiligde digitale omgeving.</p>
            <?php elseif ($isMarketing): ?>
            <p>Marketing SEO maakt je website beter vindbaar voor mensen die actief zoeken naar jouw producten of diensten. We combineren technische optimalisatie met inhoud die antwoord geeft op echte vragen van je doelgroep.</p>
            <p>We starten met onderzoek naar zoekwoorden, concurrenten en de structuur van je website. Daarna verbeteren we pagina’s, content en technische onderdelen stap voor stap, zodat iedere inspanning bijdraagt aan een duidelijk doel.</p>
            <p>Met maandelijkse rapportage zie je welke posities, bezoekers en conversies zich ontwikkelen. Zo ontstaat een duurzame aanpak die verder gaat dan losse trucs en aansluit op de groei van je organisatie.</p>
            <?php elseif ($isSocial): ?>
            <p>Social media werkt beter wanneer er regelmatig iets relevants verschijnt en de stijl herkenbaar blijft. Qevanto helpt je kanalen actief te houden met een duidelijke planning, passende beelden en teksten die bij je organisatie passen.</p>
            <p>Vooraf spreken we de onderwerpen, tone of voice en goedkeuringsroute af. Daarna maken en plannen we berichten voor de kanalen die voor jouw doelgroep belangrijk zijn, terwijl reacties en vragen netjes worden opgevolgd.</p>
            <p>Je houdt overzicht met een contentkalender en periodieke rapportage. Zo weet je wat er wordt gepubliceerd, welke onderwerpen werken en waar nieuwe kansen liggen voor bereik en interactie.</p>
            <?php elseif ($isLinkbuilding): ?>
            <p>Linkbuilding versterkt de online autoriteit van je website door relevante vermeldingen op betrouwbare platforms op te bouwen. De kwaliteit en context van een link zijn daarbij belangrijker dan zoveel mogelijk links verzamelen.</p>
            <p>We onderzoeken welke websites, onderwerpen en ankerteksten passen bij jouw branche. Met gastartikelen, PR en inhoudelijke plaatsingen ontstaat een natuurlijk profiel dat aansluit op je merk en zoekstrategie.</p>
            <p>Je ontvangt duidelijke rapportage over de geplaatste links en de ontwikkeling van autoriteit en posities. Zo blijft zichtbaar welke werkzaamheden bijdragen aan duurzame vindbaarheid.</p>
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
        <?php foreach (['exact' => 'Exact', 'moneybird' => 'Moneybird', 'mollie' => 'Mollie', 'stripe' => 'Stripe', 'odoo' => 'Odoo', 'informer' => 'Informer', 'yuki' => 'Yuki', 'snelstart' => 'SnelStart', 'onlinefact' => 'OnlineFact', 'gangbang-sheet' => 'GangBang Sheet', 'postnl' => 'PostNL', 'dhl' => 'DHL'] as $slug => $name): ?>
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
