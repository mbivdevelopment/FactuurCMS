<?php $extend('frontend', ['title' => (string) setting('website.titel', 'Hosting')]); $overImg = branding_url('over'); $heroImg = branding_url('hero');
$homeTitle = preg_replace('/\{([^}]+)\}/', '<span>$1</span>', e((string) (setting('home.titel', '') ?: 'Het startpunt van jouw {online succes}.')));
$homeText = (string) (setting('home.tekst', '') ?: 'Domeinnaam, hosting, e-mail en servers op snelle NVMe-infrastructuur in Nederlandse datacenters. Wij regelen de techniek, de beveiliging en de back-ups — jij onderneemt.');
$per = static fn (string $p): string => ['maand' => 'p/m', 'kwartaal' => 'p/kw', 'jaar' => 'p/j'][$p] ?? 'eenmalig';
$kindOrder = ['Webhosting', 'Domeinnamen', 'Cloud VPS', 'E-mail Hosting', 'Informatieve Websites', 'Reseller Hosting', 'Webshops', 'Marketing SEO'];
$kindTag = ['Webhosting' => 'Klaar om mee te groeien', 'Domeinnamen' => 'Jouw naam, direct geregeld', 'Cloud VPS' => 'Flexibel, simpel, schaalbaar', 'E-mail Hosting' => 'Professionele e-mail op je domein', 'Informatieve Websites' => 'Een website die werkt', 'Reseller Hosting' => 'Hosting onder jouw merk', 'Webshops' => 'Verkoop zonder grenzen', 'Marketing SEO' => 'Gevonden worden in Google'];
$cards = [];
foreach ($kindOrder as $k) { if (isset($kindPrices[$k]) && count($cards) < 4) { $cards[$k] = $kindPrices[$k]; } }
foreach ($kindPrices as $k => $v) { if (count($cards) < 4 && !isset($cards[$k])) { $cards[$k] = $v; } }
$kindUrl = static fn (string $k): string => isset($soortPages[$k]) ? page_path(['Slug' => $soortPages[$k]]) : ($k === 'Domeinnamen' ? '/domeinen' : '/contact');
$tabs = [];
foreach (['Domeinnamen', 'Webhosting', 'E-mail Hosting', 'Cloud VPS', 'Informatieve Websites', 'Webshops', 'VPN', 'Werkplek Beheer'] as $k) { if (isset($soortPages[$k]) || isset($kindPrices[$k])) { $tabs[] = $k; } }
$score = (string) setting('reviews.score', ''); $faq = \App\Services\PageContent::faq(null, 'onze diensten', true); ?>
<section class="hhero hero2">
    <div class="hh-float" aria-hidden="true"><?php foreach (['cloud', 'shield', 'server', 'globe', 'lock', 'zap', 'cpu', 'mail'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-text">
            <span class="hh-label"><?= icon('server', 'icon icon-sm') ?> <?= e((string) (setting('home.label', '') ?: 'Hosting & cloud uit Nederland')) ?></span>
            <h1><?= $homeTitle ?></h1>
            <p><?= e($homeText) ?></p>
            <div class="hh-btns"><a class="btn btn-lg" href="<?= e(url('/oplossingen/webhosting')) ?>"><?= icon('box', 'icon icon-sm') ?> Bekijk hosting</a><a class="btn btn-lg btn-ghost" href="<?= e(url('/domeinen')) ?>">Domeinnaam zoeken <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div class="hh-visual">
            <?php if ($heroImg !== ''): ?><img class="hh-img" src="<?= e($heroImg) ?>" alt="<?= e(company_name()) ?>"><?php else: ?>
            <div class="hh-rack" aria-hidden="true">
                <?php for ($i = 0; $i < 5; $i++): ?><div class="hh-unit"><span class="hh-bays"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div><?php endfor; ?>
                <div class="hh-base"></div>
            </div>
            <?php endif; ?>
            <div class="hh-badge hh-b1"><?= icon('zap') ?><div><strong>99,9% uptime</strong><small>gegarandeerd</small></div></div>
            <div class="hh-badge hh-b2"><?= icon('headset') ?><div><strong>24/7 support</strong><small>echte specialisten</small></div></div>
        </div>
    </div>
</section>
<section class="dfind">
    <div class="container">
        <div class="dfind-card">
            <div class="dfind-head"><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinnaam</span><h2><?= e((string) (setting('hero.titel', '') ?: 'Begin met jouw domeinnaam')) ?></h2><p><?= e((string) (setting('hero.tekst', '') ?: 'Typ je naam en zie direct welke extensies vrij zijn — inclusief gratis DNS-beheer.')) ?></p></div>
            <?= $partial('frontend._domain-search', ['placeholder' => 'jouwdomein.nl', 'transfer' => true]) ?>
            <?php if ($extProducts !== []): ?>
            <div class="dfind-exts">
                <?php foreach (array_slice($extProducts, 0, 11) as $p): ?><span class="dfind-ext"><strong><?= e($p->extensionLabel()) ?></strong><span><?= e(money((int) $p->Prijs)) ?></span><small>per jaar</small></span><?php endforeach; ?>
                <a class="dfind-all" href="<?= e(url('/domeinen')) ?>">Alle extensies <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="sec sec-about">
    <div class="container about-split">
        <div class="about-text">
            <span class="eyebrow"><?= icon('building', 'icon icon-sm') ?> Over <?= e(company_name()) ?></span>
            <h2 class="h-xl"><?= e((string) setting('over.titel', 'Over ons')) ?></h2>
            <div class="prose"><?= rich_text((string) setting('over.tekst', '') ?: (string) setting('website.intro', '')) ?></div>
            <div class="about-btns"><a class="btn btn-lg" href="<?= e(url('/over-ons')) ?>">Meer over ons <?= icon('arrow-right', 'icon icon-sm') ?></a><a class="btn btn-lg btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Kennismaken</a></div>
        </div>
        <div class="about-visual has-img about-illu<?= $overImg !== '' ? ' is-photo' : '' ?>">
            <img src="<?= e($overImg !== '' ? $overImg : asset('img/over-team.webp')) ?>" alt="<?= e(company_name()) ?>" loading="lazy">
            <div class="av-badge"><?= icon('shield') ?><div><strong>Ijzersterk verbonden</strong><small>Eigen netwerk en datacenters</small></div></div>
        </div>
    </div>
</section>

<?php if ($cards !== []): ?>
<section class="sec kc-sec">
    <div class="container"><div class="kc-wrap">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Onze diensten</span><h2>Internet voor elk idee</h2><p>Van eerste website tot eigen server: alles wat je online nodig hebt, bij één betrouwbare partner.</p></div></div>
        <div class="kc-grid kc-n<?= count($cards) ?>">
            <?php foreach ($cards as $k => $kp): $b = array_slice(\App\Services\PageContent::benefits($k), 0, 3); ?>
            <a class="kc" href="<?= e(url($kindUrl($k))) ?>">
                <div class="kc-visual"><span class="kc-i"><?= icon(\App\Models\Product::soortIcon($k)) ?></span><span class="kc-ring"></span></div>
                <div class="kc-body">
                    <span class="pk-kind"><?= icon(\App\Models\Product::soortIcon($k), 'icon icon-sm') ?><?= e(\App\Models\Product::soortLabel($k)) ?></span>
                    <h3><?= e($kindTag[$k] ?? \App\Models\Product::soortText($k) ?: $k) ?></h3>
                    <ul><?php foreach ($b as [$ic, $t]): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
                    <div class="kc-foot"><span class="kc-from"><small>Vanaf</small><strong><?= e(money($kp['min'])) ?> <em><?= e($per($kp['periode'])) ?></em></strong></span><span class="btn btn-sm">Bekijk pakketten</span></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div></div>
</section>
<?php endif; ?>

<section class="sec sec-succ">
    <div class="container">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('heart', 'icon icon-sm') ?> Waarom <?= e(company_name()) ?></span><h2>Vind jouw online succes</h2><p>Geen gedoe, geen verrassingen: wij regelen de techniek zodat jij kunt ondernemen.</p></div></div>
        <div class="rsn-grid">
            <?php foreach ([
                ['headset', 'Persoonlijke support', 'Een specialist die je kent en meedenkt.'],
                ['zap', 'Razendsnelle hosting', 'NVMe en LiteSpeed: merkbaar sneller.'],
                ['tag', 'Duidelijke prijzen', 'Vooraf duidelijk, zonder verborgen kosten.'],
                ['shield', 'Veilig in Nederland', 'ISO 27001-datacenters in de EU.'],
                ['refresh', 'Dagelijkse back-ups', 'Elke nacht een back-up, zelf terug te zetten.'],
                ['box', 'Alles onder één dak', 'Hosting, servers en ICT bij één partner.'],
            ] as [$ic, $t, $txt]): ?>
            <div class="rsn"><span class="rsn-i"><?= icon($ic) ?></span><div><h3><?= e($t) ?></h3><p><?= e($txt) ?></p></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($packages !== []): ?>
<section class="sec sec-plans">
    <div class="container">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('server', 'icon icon-sm') ?> Webhosting</span><h2>Hostingpakketten voor elke website</h2><p>Snelle NVMe-servers met LiteSpeed in Nederlandse datacenters. Gratis SSL, dagelijkse back-ups en gratis verhuizing — upgraden kan altijd.</p></div></div>
        <?= $partial('frontend._plans', ['products' => $packages]) ?>
    </div>
</section>
<?php endif; ?>

<?php if ($score !== ''): $num = (float) str_replace(',', '.', $score); $stars = (int) round($num > 5 ? $num / 2 : $num); ?>
<section class="revbar"><div class="container"><a class="revbar-in" href="<?= e((string) (setting('reviews.url', '') ?: url('/over-ons'))) ?>"<?= setting('reviews.url') ? ' target="_blank" rel="noopener"' : '' ?>>
    <span class="revbar-stars"><?php for ($i = 0; $i < 5; $i++): ?><?= icon('star', 'icon icon-sm' . ($i < $stars ? ' on' : '')) ?><?php endfor; ?></span>
    <strong><?= e(company_name()) ?>: beoordeeld met <?= e($score) ?><?= $num > 5 ? '' : ' van 5' ?></strong>
    <span><?php if (setting('reviews.aantal')): ?>op basis van <?= e((string) setting('reviews.aantal')) ?> beoordelingen<?php endif; ?><?php if (setting('reviews.bron')): ?> via <?= e((string) setting('reviews.bron')) ?><?php endif; ?></span>
    <?= icon('arrow-right', 'icon icon-sm') ?>
</a></div></section>
<?php endif; ?>

<?php if ($solutionPages !== []): ?>
<?php $smtp = \App\Models\Product::firstWhere(['Dienstsoort' => 'SMTP Relay', 'Zichtbaar' => 1]); ?>
<section class="sec sec-cat">
    <div class="container">
        <div class="cta-grid">
            <div class="cta-l">
                <span class="eyebrow eyebrow-light"><?= icon('send', 'icon icon-sm') ?> Nieuw · SMTP Relay</span>
                <h2>Verstuur e-mail die aankomt via onze SMTP Relay.</h2>
                <p>Orderbevestigingen, wachtwoordresets en nieuwsbrieven vanuit je website of app verzonden via onze betrouwbare mailinfrastructuur — met SPF, DKIM en DMARC goed geregeld.</p>
                <div class="cta-btns"><a class="btn" href="<?= e(url($smtp !== null ? '/oplossingen/smtp-relay' : '/contact')) ?>"><?= icon('send', 'icon icon-sm') ?> Start met SMTP Relay</a><a class="btn btn-ghost-light" href="<?= e(url('/contact')) ?>"><?= icon('headset', 'icon icon-sm') ?> Vraag advies</a></div>
            </div>
            <div class="cta-stats">
                <div class="cta-stat"><b>Inbox</b><span>Hoge inbox-komst met correcte SPF, DKIM en DMARC</span></div>
                <div class="cta-stat"><b>SMTP &amp; API</b><span>Verzenden via standaard SMTP of de e-mail-API</span></div>
                <div class="cta-stat"><b>Realtime</b><span>Inzage in verzonden, bezorgde en gebouncte e-mail</span></div>
                <div class="cta-stat"><b>Schaalbaar</b><span>Van honderd tot miljoenen e-mails per maand</span></div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($projects !== []): ?>
<section class="sec sec-proj">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('image', 'icon icon-sm') ?> Projecten</span><h2>Onze projecten</h2><p>Een greep uit de projecten die we voor ondernemers realiseerden.</p></div></div>
        <?= $partial('frontend._carousel', ['items' => $projects, 'base' => '/portfolio/', 'icon' => 'image', 'more' => '', 'autoplay' => true, 'nav_top' => true]) ?>
    </div>
</section>
<?php endif; ?>

<section class="sec sec-dark"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen online</h2><p>Van bestelling tot actieve dienst: snel, duidelijk en zonder gedoe.</p></div></div>
    <?= $partial('frontend._steps', ['items' => \App\Services\PageContent::steps(null, true)]) ?>
</div></section>

<section class="sec">
    <div class="container about-split">
        <div class="dc-side">
            <?php if ($datacenters !== []): ?><?= $partial('frontend._map', ['datacenters' => $datacenters]) ?><?php endif; ?>
        </div>
        <div>
            <span class="eyebrow"><?= icon('pin', 'icon icon-sm') ?> Netwerk</span>
            <h2 class="h-xl">Onze datacenters</h2>
            <div class="prose">
                <p>Jouw diensten draaien in moderne datacenters met redundante stroom en verbindingen, dichtbij in Nederland en met partners wereldwijd. Valt er iets uit, dan neemt een onderdeel het automatisch over. Daardoor blijft je website, webshop of applicatie gewoon bereikbaar.</p>
                <p>We kiezen bewust voor Tier III-faciliteiten: dubbele stroomtoevoer, meerdere netwerkcarriers en strenge beveiliging met toegangscontrole en camerabewaking. Jouw data blijft binnen Europa, volledig conform de AVG. En omdat onze eigen engineers dag en nacht meekijken, merken we problemen vaak op voordat jij ze ervaart.</p>
                <p>Nieuw platform of juist extra schaal nodig? We schalen mee: van een enkele website tot complete VPS-clusters met eigen netwerk. Je hoeft niet te verhuizen naar een andere partij als je groeit — onze datacenters groeien met je mee.</p>
                <ul class="dc-perks">
                    <li>Redundante stroom, koeling en verbindingen (N+1)</li>
                    <li>Dichtbij: Nederlandse datacenters en lage ping</li>
                    <li>24/7-monitoring en storingbestrijding door eigen engineers</li>
                    <li>Dagelijkse back-ups op een aparte locatie</li>
                    <li>Data in Europa, volledig AVG-proof</li>
                    <li>Tier III-faciliteiten met dubbele stroomtoevoer</li>
                </ul>
            </div>
            <div class="row" style="margin-top:22px"><a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Vraag advies <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
    </div>
</section>



<?php if ($articles !== []): ?>
<section class="sec sec-kb">
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

<?php $logos = ['almalinux' => 'AlmaLinux', 'apache' => 'Apache', 'cloudflare' => 'Cloudflare', 'debian' => 'Debian', 'docker' => 'Docker', 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'laravel' => 'Laravel', 'letsencrypt' => 'Let\'s Encrypt', 'magento' => 'Magento', 'mariadb' => 'MariaDB', 'mysql' => 'MySQL', 'nginx' => 'nginx', 'nodedotjs' => 'Node.js', 'openlitespeed' => 'OpenLiteSpeed', 'php' => 'PHP', 'prestashop' => 'PrestaShop', 'python' => 'Python', 'redis' => 'Redis', 'ubuntu' => 'Ubuntu', 'woocommerce' => 'WooCommerce', 'wordpress' => 'WordPress']; ?>
<section class="sec">
    <div class="container">
        <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Onze stack</span><h2>Bewezen software waarop we draaien</h2><p>Van besturingssysteem en webserver tot framework en CMS. Alles wat we inzetten is bewezen, snel en veilig — software waar je op kunt bouwen.</p></div></div>
        <div class="logo-grid">
            <?php foreach ($logos as $slug => $name): ?><span class="logo-item"><img src="<?= e(asset('img/logos/' . $slug . '.svg')) ?>" alt="" loading="lazy"><?= e($name) ?></span><?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec sec-status">
    <div class="container">
        <div class="up-wrap">
            <div class="up-head">
                <div><span class="eyebrow"><?= icon('zap', 'icon icon-sm') ?> Netwerkstatus</span><h2><?= $uptime['actief'] === [] ? 'Alle systemen operationeel' : 'Er loopt een incident' ?></h2><p><?= $uptime['pct'] ?>% uptime in de laatste 90 dagen — per dienst hieronder.</p></div>
                <a class="btn btn-ghost" href="<?= e(url('/storingen')) ?>">Bekijk alle storingen <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <div class="up-grid">
                <?php foreach ($uptime['services'] as $svc): ?>
                <a class="up-svc" href="<?= e(url('/storingen')) ?>">
                    <div class="up-svc-top"><strong><?= e($svc['naam']) ?></strong><span class="up-state <?= $svc['st'] === 'ok' ? 'is-ok' : ($svc['st'] === 'down' ? 'is-down' : 'is-warn') ?>"><?= $svc['st'] === 'ok' ? 'Operationeel' : ($svc['st'] === 'down' ? 'Storing' : ($svc['st'] === 'warn' ? 'Beperkt' : 'Onderhoud')) ?></span></div>
                    <div class="up-bar up-bar-sm"><?php foreach ($svc['dagen'] as [$dag, $st]): ?><span class="up-d up-<?= $st ?>" title="<?= e(date('d-m-Y', strtotime($dag))) ?>"></span><?php endforeach; ?></div>
                    <div class="up-svc-foot"><span><?= e($svc['pct']) ?>% uptime</span><span class="muted">90 dagen</span></div>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="up-card up-leg"><span class="up-leg"><span><i class="up-d up-ok"></i> Operationeel</span><span><i class="up-d up-warn"></i> Beperkt</span><span><i class="up-d up-down"></i> Storing</span><span><i class="up-d up-plan"></i> Onderhoud</span></span></div>
        </div>
    </div>
</section>
<?php if (($posts ?? []) !== []): ?>
<section class="sec">
    <div class="container">
        <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Blog</span><h2>Laatste berichten</h2></div></div>
        <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note', 'more' => 'Lees verder', 'autoplay' => true, 'nav_top' => true]) ?>
    </div>
</section>
<?php endif; ?>
