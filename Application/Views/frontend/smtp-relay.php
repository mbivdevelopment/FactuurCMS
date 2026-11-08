<?php $extend('frontend', ['title' => $page['Titel']]); $host = setting('smtp.relay_host', 'smtp.' . strtolower(company_name()) . '.nl'); ?>
<section class="hhero hero2">
    <div class="hh-float" aria-hidden="true"><?php foreach (['send', 'mail', 'shield', 'server', 'lock', 'zap', 'link', 'cloud'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-text">
            <span class="hh-label"><?= icon('send', 'icon icon-sm') ?> SMTP Relay</span>
            <h1>E-mail die <span>aankomt</span>, elke keer.</h1>
            <p><?= e((string) $page['Samenvatting']) ?> Verbind je website of applicatie via standaard SMTP en verstuur via onze Nederlandse mailinfrastructuur.</p>
            <div class="hh-btns"><a class="btn btn-lg" href="#pakketten"><?= icon('cart', 'icon icon-sm') ?> Bekijk pakketten</a><a class="btn btn-lg btn-ghost" href="#werking">Hoe het werkt <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div class="hh-visual">
            <div class="hh-rack" aria-hidden="true">
                <?php for ($i = 0; $i < 5; $i++): ?><div class="hh-unit"><span class="hh-bays"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div><?php endfor; ?>
                <div class="hh-base"></div>
            </div>
            <div class="hh-badge hh-b1"><?= icon('zap') ?><div><strong>Direct actief</strong><small>na betaling</small></div></div>
            <div class="hh-badge hh-b2"><?= icon('shield') ?><div><strong>SPF · DKIM · DMARC</strong><small>goed geregeld</small></div></div>
        </div>
    </div>
</section>

<section class="sec"><div class="container intro-split dn-split">
    <div class="solution-intro-photo">
        <img src="<?= e(asset('img/qevanto-smtprelay.png')) ?>" alt="SMTP Relay-infrastructuur van Qevanto">
        <span class="solution-photo-caption"><?= icon('send', 'icon icon-sm') ?> SMTP Relay bij Qevanto</span>
    </div>
    <div>
        <span class="eyebrow"><?= icon('send', 'icon icon-sm') ?> SMTP Relay</span>
        <h2 class="h-xl">Betrouwbaar verzenden vanuit elke applicatie</h2>
        <div class="prose"><?= rich_text((string) $page['Inhoud']) ?></div>
        <div class="info-box"><?= icon('mail') ?><div><strong>Goed om te weten</strong><p>SMTP Relay werkt met elke website, webshop of applicatie die e-mail via SMTP kan versturen: WordPress, Magento, Laravel, Microsoft 365-apparaten, printers en scanners. Je eigen domeinnaam blijft de afzender.</p></div></div>
    </div>
</div></section>

<?php if ($products !== []): ?>
<section class="sec sec-grey" id="pakketten"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Kies je verzendvolume</h2><p>Alle prijzen zijn exclusief btw. Bestel direct online; na betaling staan je gegevens in het klantportaal. Upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $products]) ?>
</div></section>
<?php endif; ?>

<section class="sec"><div class="container intro-split dn-split dn-split-rev">
    <div>
        <span class="eyebrow"><?= icon('shield', 'icon icon-sm') ?> Authenticatie</span>
        <h2 class="h-xl">SPF, DKIM en DMARC zonder gedoe</h2>
        <div class="prose">
            <p>Of je e-mail in de inbox of in de spammap landt, hangt af van authenticatie. Met SPF geef je aan dat onze servers namens jouw domein mogen versturen, DKIM ondertekent elk bericht digitaal en DMARC vertelt ontvangers wat ze met afwijkende mail moeten doen.</p>
            <p>Bij SMTP Relay zetten wij deze records voor je klaar in het klantportaal. Heb je je domein bij ons, dan voegen we ze met één klik toe aan je DNS. Staat je domein elders, dan kopieer je ze in een minuut over.</p>
            <ul>
                <li>SPF-include en DKIM-sleutel per domein, automatisch gegenereerd</li>
                <li>DMARC-rapportages inzichtelijk in je portaal</li>
                <li>Eigen afzenderdomein; geen "via" of "namens" in de inbox</li>
            </ul>
        </div>
    </div>
    <div class="solution-intro-photo">
        <img src="<?= e(asset('img/qevanto-spf-dkim.png')) ?>" alt="SPF, DKIM en DMARC voor SMTP Relay">
        <span class="solution-photo-caption"><?= icon('shield', 'icon icon-sm') ?> SPF, DKIM en DMARC</span>
    </div>
</div></section>

<section class="sec sec-grey"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom SMTP Relay bij <?= e(company_name()) ?></h2><p>Alles wat je nodig hebt om e-mail betrouwbaar te versturen, zit erin.</p></div></div>
    <?= $partial('frontend._benefits', ['items' => $benefits]) ?>
</div></section>

<section class="sec"><div class="container intro-split dn-split">
    <div class="solution-intro-photo">
        <img src="<?= e(asset('img/qevanto-smtp-koppelen.png')) ?>" alt="SMTP Relay koppelen aan websites, shops en applicaties">
        <span class="solution-photo-caption"><?= icon('link', 'icon icon-sm') ?> Koppelen met SMTP Relay</span>
    </div>
    <div>
        <span class="eyebrow"><?= icon('link', 'icon icon-sm') ?> Koppelen</span>
        <h2 class="h-xl">Aansluiten op elke website, shop of applicatie</h2>
        <div class="prose">
            <p>SMTP Relay gebruikt het standaard SMTP-protocol, dus je hebt geen plug-ins of maatwerk nodig. Vul host, poort en je inloggegevens in bij je website, webshop of applicatie en alle e-mail loopt via onze servers. In WordPress volstaat een gratis SMTP-plug-in; Laravel, Magento, Shopware en Microsoft 365 ondersteunen het standaard.</p>
            <p>Ook apparaten die mail versturen — zoals multifunctionals, scanners, NAS-systemen en alarmcentrales — kunnen rechtstreeks via SMTP Relay versturen. Zo verlaten alle berichten je organisatie met dezelfde afzender en authenticatie.</p>
            <ul>
                <li>Standaard SMTP met STARTTLS (587) of SSL (465), geen vendor lock-in</li>
                <li>Meerdere websites of apps op één account; per afzender inzicht in het logboek</li>
                <li>Hulp nodig? Onze support koppelt het samen met je in</li>
            </ul>
        </div>
    </div>
</div></section>

<section class="sec sec-dark" id="werking"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen verzenden</h2><p>Van bestelling tot eerste bezorgde e-mail: snel, duidelijk en zonder gedoe.</p></div></div>
    <?= $partial('frontend._steps', ['items' => $steps]) ?>
</div></section>

<?php if (($posts ?? []) !== []): ?>
<section class="sec"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Blog</span><h2>Lezen over e-mail en bezorging</h2></div></div>
    <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note', 'more' => 'Lees verder', 'autoplay' => true, 'nav_top' => true]) ?>
</div></section>
<?php endif; ?>
