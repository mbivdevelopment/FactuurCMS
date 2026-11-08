<?php $extend('frontend', ['title' => 'Domeinnaam registreren']); ?>
<section class="hhero hero2">
    <div class="hh-float" aria-hidden="true"><?php foreach (['globe', 'shield', 'server', 'cloud', 'lock', 'zap', 'link', 'mail'] as $i => $ic): ?><span class="hh-f hh-f<?= $i ?>"><?= icon($ic) ?></span><?php endforeach; ?></div>
    <div class="container hh-in">
        <div class="hh-text">
            <span class="hh-label"><?= icon('globe', 'icon icon-sm') ?> Domeinnamen &amp; DNS</span>
            <h1>Jouw <span>domeinnaam</span>, direct geregeld.</h1>
            <p>Zoek, registreer of verhuis je domeinnaam in een paar minuten. Met gratis DNS-beheer, automatische verlenging en eerlijke prijzen — zonder kleine lettertjes.</p>
            <div class="hh-btns"><a class="btn btn-lg" href="#zoeken"><?= icon('search', 'icon icon-sm') ?> Zoek je domein</a><a class="btn btn-lg btn-ghost" href="#" data-open="ext-dlg">Alle extensies <?= icon('arrow-right', 'icon icon-sm') ?></a></div>
        </div>
        <div class="hh-visual">
            <div class="hh-rack" aria-hidden="true">
                <?php for ($i = 0; $i < 5; $i++): ?><div class="hh-unit"><span class="hh-bays"><i></i><i></i><i></i><i></i><i></i><i></i></span><span class="hh-leds"><b></b><b></b><b></b></span></div><?php endfor; ?>
                <div class="hh-base"></div>
            </div>
        </div>
    </div>
</section>
<section class="dfind" id="zoeken">
    <div class="container">
        <div class="dfind-card">
            <div class="dfind-head"><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinnaam</span><h2>Begin met jouw domeinnaam</h2><p>Typ je naam en zie direct welke extensies vrij zijn — inclusief gratis DNS-beheer.</p></div>
            <?= $partial('frontend._domain-search', ['placeholder' => 'jouwdomein.nl', 'transfer' => true]) ?>
            <?php if ($extProducts !== []): ?>
            <div class="dfind-exts">
                <?php foreach (array_slice($extProducts, 0, 11) as $p): ?><span class="dfind-ext"><strong><?= e($p->extensionLabel()) ?></strong><span><?= e(money((int) $p->Prijs)) ?></span><small>per jaar</small></span><?php endforeach; ?>
                <a class="dfind-all" href="#" data-open="ext-dlg">Alle extensies <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="sec sec-grey"><div class="container intro-split dn-split">
    <div class="dn-visual" aria-hidden="true">
        <div class="dn-browser">
            <div class="dn-bar"><i></i><i></i><i></i><span class="dn-url"><?= icon('lock', 'icon icon-sm') ?> <?= e(company_name()) ?>.nl/domeinen</span></div>
            <div class="dn-body">
                <div class="dn-search"><?= icon('search', 'icon icon-sm') ?> jouwdomein<span class="dn-caret">|</span></div>
                <?php foreach (array_slice($extProducts, 0, 4) as $p): ?><div class="dn-row"><span class="dn-dot dn-ok"></span><strong>jouwdomein<?= e($p->extensionLabel()) ?></strong><span class="dn-price"><?= e(money((int) $p->Prijs)) ?></span><em>beschikbaar</em></div><?php endforeach; ?>
                <div class="dn-row dn-faded"><span class="dn-dot dn-warn"></span><strong>jouwdomein<?= e(isset($extProducts[4]) ? $extProducts[4]->extensionLabel() : '.shop') ?></strong><span class="dn-price">—</span><em>bezet · verhuis</em></div>
            </div>
        </div>
        <div class="dn-chip dn-c1"><?= icon('globe') ?> 700+ extensies</div>
        <div class="dn-chip dn-c2"><?= icon('zap') ?> Direct actief</div>
    </div>
    <div>
        <span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinnaam registreren</span>
        <h2 class="h-xl">Jouw adres op internet</h2>
        <div class="prose">
            <p>Een domeinnaam is het adres waarop klanten jou vinden. Bij <?= e(company_name()) ?> registreer je hem in een paar minuten: zoek, bestel en je domein staat direct in je klantportaal — met volledig DNS-beheer in het hostingpaneel.</p>
            <p>Je betaalt per jaar, zonder verrassingen. Alle standaardfuncties zitten erbij: gratis doorsturen naar een bestaande website, e-mail doorsturen en bescherming van je gegevens.</p>
            <p>Meerdere extensies vastleggen? Voeg ze in één bestelling toe. En heb je al een domein ergens anders, dan verhuizen wij hem gratis naar ons toe — zonder downtime en zonder dat je verkeer kwijtraakt.</p>
        </div>
        <div class="info-box"><?= icon('globe') ?><div><strong>Goed om te weten</strong><p>Een domeinnaam is direct actief na betaling en inclusief gratis DNS-beheer en automatische verlenging. Verhuizen kan ook — wij regelen de overstap.</p></div></div>
    </div>
</div></section>

<?php if ($extProducts !== []): ?>
<section class="sec"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Populaire extensies en prijzen</h2><p>Prijzen per jaar, exclusief btw. Inclusief gratis DNS-beheer en automatische verlenging.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => $extProducts, 'max' => 12, 'dialog' => true]) ?>
</div></section>
<?php endif; ?>

<section class="sec sec-grey"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom je domein bij <?= e(company_name()) ?></h2><p>Alles wat je bij een domeinnaam nodig hebt, zit er gewoon bij.</p></div></div>
    <?= $partial('frontend._benefits', ['items' => $benefits]) ?>
</div></section>

<section class="sec"><div class="container intro-split dn-split dn-split-rev">
    <div>
        <span class="eyebrow"><?= icon('settings', 'icon icon-sm') ?> DNS-beheer &amp; verhuizen</span>
        <h2 class="h-xl">Volledige controle over je domein</h2>
        <div class="prose">
            <p>Elk domein komt met gratis DNS-beheer in je klantportaal. Je past A-, CNAME-, MX- en TXT-records zelf aan — wijzigingen zijn binnen minuten actief. Koppel je domein aan onze hosting met één klik, of wijs hem naar een externe server of e-maildienst.</p>
            <p>Verhuis je een domein naar ons? Voer de verhuiscode (EPP/auth-code) in en wij regelen de rest. Je website en e-mail blijven gewoon bereikbaar; de nameservers nemen we desgewenst één-op-één over.</p>
            <p>Je houdt zelf controle over alle belangrijke instellingen, inclusief SPF, DKIM en DMARC voor veilige e-mail. Met transferlock, privacybescherming en automatische verlenging blijft je domein goed beschermd en hoef je niet bang te zijn dat het onverwacht verloopt.</p>
        </div>
    </div>
    <div class="dn-visual" aria-hidden="true">
        <div class="dn-browser">
            <div class="dn-bar"><i></i><i></i><i></i><span class="dn-url"><?= icon('lock', 'icon icon-sm') ?> klantportaal · DNS-beheer</span></div>
            <div class="dn-body dn-dns">
                <div class="dn-dns-head"><strong>jouwdomein.nl</strong><span class="badge is-ok">Actief</span></div>
                <?php foreach ([['A', '@', '185.12.34.56'], ['A', 'www', '185.12.34.56'], ['MX', '@', 'mail.jouwdomein.nl'], ['TXT', '@', 'v=spf1 include:_spf.' . strtolower(company_name()) . '.nl ~all'], ['CNAME', 'autodiscover', 'mail.jouwdomein.nl']] as [$t, $n, $v]): ?>
                <div class="dn-rec"><span class="dn-type"><?= e($t) ?></span><strong><?= e($n) ?></strong><span class="dn-val"><?= e($v) ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="dn-chip dn-c1"><?= icon('zap') ?> Binnen minuten actief</div>
        <div class="dn-chip dn-c2"><?= icon('shield') ?> Transferlock aan</div>
    </div>
</div></section>

<section class="sec sec-dark"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van zoeken tot actief: snel, duidelijk en zonder gedoe.</p></div></div>
    <?= $partial('frontend._steps', ['items' => $steps]) ?>
</div></section>

<?php if (($posts ?? []) !== []): ?>
<section class="sec"><div class="container">
    <div class="sec-head"><div><span class="eyebrow"><?= icon('note', 'icon icon-sm') ?> Blog</span><h2>Lezen over domeinnamen</h2></div></div>
    <?= $partial('frontend._carousel', ['items' => $posts, 'base' => '/blog/', 'icon' => 'note', 'more' => 'Lees verder', 'autoplay' => true, 'nav_top' => true]) ?>
</div></section>
<?php endif; ?>
