<?php $extend('frontend', ['title' => 'Contact']); ?>
<?= $partial('frontend._hero', ['title' => 'Contact', 'intro' => 'Vragen of advies nodig? We helpen je graag verder.', 'icon' => 'chat', 'crumbs' => [[null, 'Contact']]]) ?>
<section class="sec">
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
            <?php $adres = trim(setting('bedrijf.adres', '') . ', ' . setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', ''), ', '); $tel = (string) setting('bedrijf.telefoon', ''); $mail = (string) setting('bedrijf.email', ''); ?>
            <?php if ($tel !== ''): ?><div class="cc"><span class="cc-i"><?= icon('phone') ?></span><div><strong>Bel ons</strong><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $tel)) ?>"><?= e($tel) ?></a><br><span>Werkdagen 09:00 – 17:30</span></div></div><?php endif; ?>
            <?php if ($mail !== ''): ?><div class="cc"><span class="cc-i"><?= icon('mail') ?></span><div><strong>Mail ons</strong><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><br><span>Reactie binnen één werkdag</span></div></div><?php endif; ?>
            <?php if ($adres !== ''): ?><div class="cc"><span class="cc-i"><?= icon('pin') ?></span><div><strong>Bezoekadres</strong><span><?= e($adres) ?></span></div></div><?php endif; ?>
            <div class="cc"><span class="cc-i"><?= icon('headset') ?></span><div><strong>Al klant?</strong><span>Log in op het klantportaal en open een ticket; storingen pakken we 24/7 op.</span><br><a href="<?= e(url('/klant')) ?>">Naar het klantportaal →</a></div></div>
            <?php if (setting('bedrijf.kvk') || setting('bedrijf.btw')): ?><div class="cc"><span class="cc-i"><?= icon('building') ?></span><div><strong><?= e(company_name()) ?></strong><span><?php if (setting('bedrijf.kvk')): ?>KvK <?= e(setting('bedrijf.kvk')) ?><?php endif; ?><?php if (setting('bedrijf.btw')): ?> · Btw <?= e(setting('bedrijf.btw')) ?><?php endif; ?></span></div></div><?php endif; ?>
        </div>
    </div>
</section>
<section class="sec sec-alt"><div class="container"><?= $partial('frontend._trust') ?></div></section>
<section class="sec"><div class="container faq-split">
    <div class="faq-side"><span class="eyebrow"><?= icon('chat', 'icon icon-sm') ?> Veelgestelde vragen</span><h2 class="h-xl">Snel antwoord</h2><p class="muted">Misschien staat je antwoord hier al.</p></div>
    <?= $partial('frontend._faq', ['items' => \App\Services\PageContent::faq(null, 'onze diensten', true)]) ?>
</div></section>
