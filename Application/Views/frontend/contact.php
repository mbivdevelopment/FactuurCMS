<?php $extend('frontend', ['title' => 'Klantenservice']); ?>
<?php $adres = trim(setting('bedrijf.adres', '') . ', ' . setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', ''), ', '); $tel = (string) setting('bedrijf.telefoon', ''); $mail = (string) setting('bedrijf.email', ''); ?>
<section class="cs-hero">
    <div class="container">
        <span class="eyebrow eyebrow-light"><?= icon('headset', 'icon icon-sm') ?> Klantenservice</span>
        <h1>Hulp nodig? We zijn er voor je.</h1>
        <p>Vind het antwoord zelf of neem direct contact op — via telefoon, e-mail, ticket of live chat.</p>
        <form class="cs-search" method="get" action="<?= e(url('/contact')) ?>#faq">
            <?= icon('search') ?>
            <input type="search" name="q" value="<?= e((string) ($_GET['q'] ?? '')) ?>" placeholder="Waarmee kunnen we je helpen? Bijv. verhuizen, factuur, SSL…">
            <button class="btn" type="submit">Zoeken</button>
        </form>
    </div>
</section>
<section class="sec">
    <div class="container">
        <div class="cs-topics">
            <?php foreach ([
                ['book', 'Kennisbank', 'Handleidingen en veelgestelde vragen.', '/kennisbank'],
                ['globe', 'Domeinnamen', 'Registreren, verhuizen en DNS instellen.', '/domeinen'],
                ['server', 'Webhosting & e-mail', 'Accounts, FTP, SSL en mailboxen.', '/oplossingen/webhosting'],
                ['send', 'SMTP Relay', 'Verzendgegevens en e-mailbezorging.', '/oplossingen/smtp-relay'],
                ['doc', 'Facturen & betalingen', 'Kosten, betalingsachterstanden en contracten.', '/klant'],
                ['lock', 'Klantportaal', 'Inloggen, gegevens wijzigen en diensten.', '/klant'],
                ['ticket', 'Tickets & storingen', 'Open een ticket of meld een storing.', '/klant/tickets/nieuw'],
            ] as [$ic, $t, $txt, $href]): ?>
                <a class="cs-topic" href="<?= e(url($href)) ?>"><span class="cs-topic-i"><?= icon($ic) ?></span><strong><?= e($t) ?></strong><span class="muted"><?= e($txt) ?></span></a>
            <?php endforeach; ?>
        </div>
        <div class="cs-channels">
            <?php if ($tel !== ''): ?>
            <div class="cs-ch"><span class="cs-ch-i"><?= icon('phone') ?></span><div><h3>Bel ons</h3><a class="cs-ch-l" href="tel:<?= e(preg_replace('/[^\d+]/', '', $tel)) ?>"><?= e($tel) ?></a><p class="muted">Werkdagen 09:00 – 17:30<br>Storingen pakken we 24/7 op.</p></div></div>
            <?php endif; ?>
            <?php if ($mail !== ''): ?>
            <div class="cs-ch"><span class="cs-ch-i"><?= icon('mail') ?></span><div><h3>E-mail</h3><a class="cs-ch-l" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><p class="muted">Reactie binnen één werkdag<br>op werkdagen.</p></div></div>
            <?php endif; ?>
            <div class="cs-ch"><span class="cs-ch-i"><?= icon('ticket') ?></span><div><h3>Ticket</h3><a class="cs-ch-l" href="<?= e(url('/klant/tickets/nieuw')) ?>">Open een ticket</a><p class="muted">Voor klanten met een actief account.<br>Altijd en overal bereikbaar.</p></div></div>
            <div class="cs-ch"><span class="cs-ch-i"><?= icon('chat') ?></span><div><h3>Live chat</h3><a class="cs-ch-l" href="#bericht" data-scroll>Start een chat</a><p class="muted">Werkdagen 09:00 – 17:30<br>Buiten die tijden: laat een bericht achter.</p></div></div>
        </div>
    </div>
</section>
<section class="sec sec-alt" id="faq"><div class="container faq-split">
    <div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Snel antwoord</h2><p class="muted">Misschien staat je antwoord hier al. Zoek boven op een onderwerp.</p></div>
    <?= $partial('frontend._faq', ['items' => \App\Services\PageContent::faq(null, 'onze diensten', true)]) ?>
</div></section>
<section class="sec" id="bericht">
    <div class="container contact-grid">
        <form class="card" method="post" action="<?= e(url('/contact')) ?>">
            <?= csrf_field() ?>
            <div class="card-head"><h2><?= icon('chat') ?> Stuur ons een bericht</h2></div>
            <div class="card-body form-grid">
                <?= field('Naam', 'Naam', 'user', '', 'text', [], 'required') ?>
                <?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required') ?>
                <?= field('Bericht', 'Bericht', 'note', '', 'textarea', [], 'required') ?>
                <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">
            </div>
            <div class="card-foot"><button class="btn" type="submit"><?= icon('send', 'icon icon-sm') ?> Versturen</button><span class="small muted" style="margin-left:14px"><?= icon('clock', 'icon icon-sm') ?> Reactie binnen één werkdag</span></div>
        </form>
        <div class="contact-cards">
            <?php if ($adres !== ''): ?><div class="cc"><span class="cc-i"><?= icon('pin') ?></span><div><strong>Bezoekadres</strong><span><?= e($adres) ?></span></div></div><?php endif; ?>
            <div class="cc"><span class="cc-i"><?= icon('headset') ?></span><div><strong>Al klant?</strong><span>Log in op het klantportaal voor je diensten, facturen en tickets; storingen pakken we 24/7 op.</span><br><a href="<?= e(url('/klant')) ?>">Naar het klantportaal →</a></div></div>
            <?php if (setting('bedrijf.kvk') || setting('bedrijf.btw')): ?><div class="cc"><span class="cc-i"><?= icon('building') ?></span><div><strong><?= e(company_name()) ?></strong><span><?php if (setting('bedrijf.kvk')): ?>KvK <?= e(setting('bedrijf.kvk')) ?><?php endif; ?><?php if (setting('bedrijf.btw')): ?> · Btw <?= e(setting('bedrijf.btw')) ?><?php endif; ?></span></div></div><?php endif; ?>
        </div>
    </div>
</section>
<section class="sec sec-tight"><div class="container"><?= $partial('frontend._trust') ?></div></section>
