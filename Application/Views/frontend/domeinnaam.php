<?php $extend('frontend', ['title' => 'Domeinnaam registreren']); ?>
<section class="hero2 sec-dark"><div class="container">
    <div class="dfind">
        <div class="dfind-card">
            <div class="dfind-head"><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinnaam</span><h1>Domeinnaam registreren</h1><p>Vind direct of jouw naam vrij is. Registreren in een paar minuten, inclusief gratis DNS-beheer en automatische verlenging.</p></div>
            <?= $partial('frontend._domain-search', ['placeholder' => 'jouwdomein.nl', 'transfer' => true]) ?>
            <?php if ($extProducts !== []): ?>
            <div class="dfind-exts">
                <?php foreach (array_slice($extProducts, 0, 11) as $p): ?><span class="dfind-ext"><strong><?= e($p->extensionLabel()) ?></strong><span><?= e(money((int) $p->Prijs)) ?></span><small>per jaar</small></span><?php endforeach; ?>
                <a class="dfind-all" href="<?= e(url('/domeinen')) ?>">Alle extensies <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div></section>

<section class="sec"><div class="container intro-split">
    <div>
        <span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinnaam registreren</span>
        <h2 class="h-xl">Jouw adres op internet</h2>
        <div class="prose">
            <p>Een domeinnaam is het adres waarop klanten jou vinden. Bij <?= e(company_name()) ?> registreer je hem in een paar minuten: zoek, bestel en je domein staat direct in je klantportaal — met volledig DNS-beheer in het hostingpaneel.</p>
            <p>Je betaalt per jaar, zonder verrassingen. Alle standaardfuncties zitten erbij: gratis doorsturen naar een bestaande website, e-mail doorsturen en bescherming van je gegevens.</p>
        </div>
        <div class="info-box"><?= icon('globe') ?><div><strong>Goed om te weten</strong><p>Een domeinnaam is direct actief na betaling en inclusief gratis DNS-beheer en automatische verlenging. Verhuizen kan ook — wij regelen de overstap.</p></div></div>
    </div>
    <aside class="intro-aside">
        <div class="ia-card dark"><span class="ia-i"><?= icon('cart') ?></span><div><strong>Direct regelen</strong><small>Zoek je domein hierboven of bekijk alle extensies met prijzen. Na betaling is registratie automatisch geregeld.</small></div><a class="btn" href="<?= e(url('/domeinen')) ?>" style="margin-top:12px"><?= icon('search', 'icon icon-sm') ?> Zoek je domein</a></div>
        <div class="ia-card"><span class="ia-i"><?= icon('refresh') ?></span><div><strong>Verhuizen kan gewoon</strong><small>Al een domein ergens anders? Wij regelen de verhuizing gratis, zonder downtime.</small></div></div>
        <div class="ia-card"><span class="ia-i"><?= icon('shield') ?></span><div><strong>Veilig en beschermd</strong><small>Gegevensbescherming, tweestapsverificatie en sloten op je domein tegen ongewenste overdracht.</small></div></div>
        <div class="ia-card"><span class="ia-i"><?= icon('euro') ?></span><div><strong>Heldere prijzen</strong><small>Prijzen per jaar, exclusief btw — geen verborgen kosten bij verlenging.</small></div></div>
    </aside>
</div></section>

<?php if ($extProducts !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Populaire extensies en prijzen</h2><p>Prijzen per jaar, exclusief btw. Inclusief gratis DNS-beheer en automatische verlenging.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => $extProducts, 'max' => 12]) ?>
</div></section>
<?php endif; ?>

<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('star', 'icon icon-sm') ?> Voordelen</span><h2>Waarom je domein bij <?= e(company_name()) ?></h2><p>Alles wat je bij een domeinnaam nodig hebt, zit er gewoon bij.</p></div></div>
    <?= $partial('frontend._benefits', ['items' => $benefits]) ?>
</div></section>

<section class="sec sec-dark"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('refresh', 'icon icon-sm') ?> Hoe het werkt</span><h2>In vier stappen geregeld</h2><p>Van zoeken tot actief: snel, duidelijk en zonder gedoe.</p></div></div>
    <?= $partial('frontend._steps', ['items' => $steps]) ?>
</div></section>

<section class="sec"><div class="container faq-split">
    <div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Vragen over domeinnamen?</h2><p class="muted">Staat je vraag er niet bij? Onze specialisten helpen je graag persoonlijk verder.</p><a class="btn btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat', 'icon icon-sm') ?> Stel je vraag</a></div>
    <?= $partial('frontend._faq', ['items' => $faq]) ?>
</div></section>

<?= $partial('frontend._cta', ['title' => 'Twijfel je over een extensie?', 'text' => 'Onze specialisten adviseren je vrijblijvend over de beste keuze voor jouw naam of merk.']) ?>
