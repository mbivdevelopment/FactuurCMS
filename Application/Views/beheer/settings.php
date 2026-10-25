<?php $extend('beheer', ['title' => 'Instellingen']); $s = static fn (string $k, mixed $d = ''): mixed => setting($k, $d); $f = static fn (string $k): string => str_replace('.', '_', $k); ?>
<div class="page-head"><div class="ph-icon"><?= icon('settings') ?></div><div class="ph-text"><h1>Instellingen</h1></div></div>
<div class="card" data-tabs>
    <nav class="tab-nav">
        <a href="#bedrijf" class="tab-link" data-tab="bedrijf"><?= icon('building', 'icon icon-sm') ?> Bedrijfsgegevens</a>
        <a href="#branding" class="tab-link" data-tab="branding"><?= icon('image', 'icon icon-sm') ?> Branding</a>
        <a href="#facturatie" class="tab-link" data-tab="facturatie"><?= icon('file', 'icon icon-sm') ?> Facturatie</a>
        <a href="#email" class="tab-link" data-tab="email"><?= icon('mail', 'icon icon-sm') ?> E-mail</a>
        <a href="#mailtemplates" class="tab-link" data-tab="mailtemplates"><?= icon('send', 'icon icon-sm') ?> E-mailtemplates</a>
        <a href="#website" class="tab-link" data-tab="website"><?= icon('globe', 'icon icon-sm') ?> Website</a>
    </nav>
    <div class="tab-pane" data-pane="bedrijf"><form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="bedrijf">
        <div class="card-body form-grid">
            <?php foreach (['bedrijf.naam' => ['Bedrijfsnaam', 'building'], 'bedrijf.email' => ['E-mailadres', 'mail'], 'bedrijf.adres' => ['Adres', 'map'], 'bedrijf.telefoon' => ['Telefoon', 'phone'], 'bedrijf.postcode' => ['Postcode', 'map'], 'bedrijf.plaats' => ['Plaats', 'map'], 'bedrijf.land' => ['Land', 'globe'], 'bedrijf.website' => ['Website', 'link'], 'bedrijf.kvk' => ['KvK-nummer', 'hash'], 'bedrijf.btw' => ['Btw-nummer', 'hash'], 'bedrijf.iban' => ['IBAN', 'wallet'], 'bedrijf.bic' => ['BIC', 'wallet']] as $k => [$l, $i]): ?>
                <?= field($f($k), $l, $i, $s($k, $k === 'bedrijf.land' ? 'Nederland' : '')) ?>
            <?php endforeach; ?>
        </div><div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div></form></div>
    <div class="tab-pane" data-pane="branding">
        <div class="card-body grid grid-3">
            <?php foreach ($brandingKinds as $kind => $label): $u = branding_url($kind); ?>
            <div class="card"><div class="card-body stack">
                <strong><?= e($label) ?></strong>
                <div class="brand-preview<?= $kind === 'logo_licht' ? ' dark' : '' ?>"><?php if ($u !== ''): ?><img src="<?= e($u) ?>" alt="" data-preview="<?= e($kind) ?>"><?php else: ?><span class="muted small" data-preview="<?= e($kind) ?>">Nog niet ingesteld</span><?php endif; ?></div>
                <form method="post" enctype="multipart/form-data" action="<?= e(url('/beheer/instellingen/branding')) ?>" class="stack"><?= csrf_field() ?><input type="hidden" name="soort" value="<?= e($kind) ?>">
                    <input type="file" name="bestand" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" data-preview-input="<?= e($kind) ?>" required>
                    <div class="row"><button class="btn btn-sm"><?= icon('upload', 'icon icon-sm') ?> Uploaden</button></div></form>
                <?php if ($u !== ''): ?><form method="post" action="<?= e(url('/beheer/instellingen/branding/verwijderen')) ?>"><?= csrf_field() ?><input type="hidden" name="soort" value="<?= e($kind) ?>"><button class="btn btn-ghost btn-sm"><?= icon('trash', 'icon icon-sm') ?> Verwijderen</button></form><?php endif; ?>
            </div></div>
            <?php endforeach; ?>
        </div>
        <form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="branding">
            <div class="card-body form-grid"><div class="field"><label for="branding_kleur">Primaire kleur (beheer, website en PDF)</label><div class="row"><input type="color" id="branding_kleur" name="branding_kleur" value="<?= e($s('branding.kleur', '#ff5a1f')) ?>" style="width:60px;height:38px;padding:2px"><span class="small muted">PNG/JPG/WEBP/SVG/ICO, max 2 MB. Logo verschijnt in beheer, website, e-mails en PDF's.</span></div></div></div>
            <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Kleur opslaan</button></div></form>
    </div>
    <div class="tab-pane" data-pane="facturatie"><form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="facturatie">
        <div class="card-body form-grid">
            <?= field('facturatie_prefix_factuur', 'Prefix factuurnummer', 'hash', $s('facturatie.prefix_factuur', 'F')) ?>
            <?= field('facturatie_prefix_offerte', 'Prefix offertenummer', 'hash', $s('facturatie.prefix_offerte', 'O')) ?>
            <?= field('facturatie_betaaltermijn', 'Betaaltermijn (dagen)', 'calendar', $s('facturatie.betaaltermijn', 14), 'number') ?>
            <?= field('facturatie_offerte_geldig', 'Offerte geldig (dagen)', 'calendar', $s('facturatie.offerte_geldig', 30), 'number') ?>
            <?= field('facturatie_btw', 'Standaard btw-tarief (%)', 'hash', $s('facturatie.btw', 21), 'number') ?>
            <?= field('facturatie_dagen_vooraf', 'Herhaalfacturen X dagen vooraf maken', 'refresh', $s('facturatie.dagen_vooraf', 14), 'number') ?>
            <?= field('facturatie_voettekst', 'Voettekst factuur', 'note', $s('facturatie.voettekst'), 'textarea') ?>
            <?= field('facturatie_voettekst_offerte', 'Voettekst offerte', 'note', $s('facturatie.voettekst_offerte'), 'textarea') ?>
            <?= field('cron_sleutel', 'Cron-sleutel', 'lock', $s('cron.sleutel')) ?>
            <div class="field"><label>Cronjob (1x per dag)</label><code class="small" style="word-break:break-all">curl -s <?= e(absolute_url('/cron/' . $s('cron.sleutel'))) ?></code><span class="small muted">of: php <?= e(\App\Core\App::instance()->path('cron.php')) ?></span></div>
        </div><div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div></form></div>
    <div class="tab-pane" data-pane="email"><form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="email">
        <div class="card-body form-grid">
            <?= field('mail_methode', 'Verzendmethode', 'send', $s('mail.methode', 'log'), 'select', \App\Core\Mailer::METHODS) ?>
            <?= field('mail_afzender', 'Afzender e-mail', 'mail', $s('mail.afzender', $s('bedrijf.email'))) ?>
            <?= field('mail_afzendernaam', 'Afzendernaam', 'user', $s('mail.afzendernaam', company_name())) ?>
            <?= field('mail_host', 'SMTP-server', 'server', $s('mail.host')) ?>
            <?= field('mail_poort', 'Poort', 'hash', $s('mail.poort', 587), 'number') ?>
            <?= field('mail_beveiliging', 'Beveiliging', 'lock', $s('mail.beveiliging', 'tls'), 'select', ['tls' => 'STARTTLS (587)', 'ssl' => 'SSL (465)', '' => 'Geen']) ?>
            <?= field('mail_gebruiker', 'Gebruikersnaam', 'user', $s('mail.gebruiker'), 'text', [], 'autocomplete="off"') ?>
            <?= field('mail_wachtwoord', 'Wachtwoord' . ($hasMailPassword ? ' (ingesteld, leeg = ongewijzigd)' : ''), 'lock', '', 'password', [], 'autocomplete="new-password"') ?>
        </div><div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div></form>
        <form method="post" action="<?= e(url('/beheer/instellingen/testmail')) ?>" class="card-body row" style="border-top:1px solid var(--line)"><?= csrf_field() ?><input name="naar" type="email" placeholder="<?= e(auth_user()?->Email) ?>" style="max-width:300px"><button class="btn btn-ghost"><?= icon('send', 'icon icon-sm') ?> Testmail versturen</button><span class="small muted">In de modus "Alleen loggen" komen mails in storage/logs/mail.log.</span></form></div>
        <div class="tab-pane" data-pane="mailtemplates"><form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="mailtemplates">
        <div class="card-body"><p class="muted" style="margin:0 0 18px">Deze mails gaan automatisch naar klanten. Vul bij onderwerp en tekst de variabelen met {accolades} in; een leeg veld valt terug op de standaard.</p>
        <?php foreach (\App\Core\Mailer::TEMPLATES as $tk => $t): ?>
        <div class="mailtpl">
            <div class="mailtpl-h"><?= icon('mail', 'icon icon-sm') ?> <strong><?= e($t['naam']) ?></strong><span class="mailtpl-vars"><?php foreach ($t['vars'] as $var): ?><code>{<?= e($var) ?>}</code><?php endforeach; ?></span></div>
            <div class="form-grid">
                <?= field('mailtpl_' . $tk . '_onderwerp', 'Onderwerp', 'mail', $s('mailtpl.' . $tk . '.onderwerp', $t['onderwerp'])) ?>
                <?= field('mailtpl_' . $tk . '_tekst', 'Berichttekst', 'note', $s('mailtpl.' . $tk . '.tekst', $t['tekst']), 'textarea', [], 'rows="7"') ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div><div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div></form></div>
<div class="tab-pane" data-pane="website"><form method="post" action="<?= e(url('/beheer/instellingen')) ?>"><?= csrf_field() ?><input type="hidden" name="tab" value="website">
        <div class="card-body form-grid">
            <?= field('website_titel', 'Titel homepage', 'layout', $s('website.titel')) ?>
            <?= field('app_url', 'Website-URL (voor links in e-mail/PDF)', 'link', $s('app.url'), 'url', [], 'placeholder="https://www.jouwdomein.nl"') ?>
            <?= field('website_intro', 'Korte omschrijving (zoekmachines)', 'note', $s('website.intro'), 'textarea', [], 'rows="2"') ?>
            <div class="field-full set-title"><?= icon('phone', 'icon icon-sm') ?> Topbar</div>
            <?= field('topbar_telefoon', 'Telefoonnummer (links)', 'phone', $s('topbar.telefoon')) ?>
            <?= field('topbar_email', 'E-mailadres (links)', 'mail', $s('topbar.email'), 'email') ?>
            <?= field('topbar_slogan', 'Slogan (midden)', 'tag', $s('topbar.slogan')) ?>
            <?= field('links_webmail', 'Link webmail (rechts)', 'webmail', $s('links.webmail'), 'url') ?>
            <?= field('links_paneel', 'Link hostingpaneel (rechts)', 'server', $s('links.paneel'), 'url') ?>
            <div class="field-full set-title"><?= icon('globe', 'icon icon-sm') ?> Homepage</div>
            <?= field('home_label', 'Hero: label boven de titel', 'tag', $s('home.label', 'Hosting & cloud uit Nederland')) ?>
            <?= field('home_titel', 'Hero: titel (woord tussen {} wordt oranje)', 'server', $s('home.titel', 'Hosting die gewoon {werkt}.')) ?>
            <?= field('home_tekst', 'Hero: tekst (foto via Branding)', 'note', $s('home.tekst'), 'textarea', [], 'rows="3"') ?>
            <?= field('hero_titel', 'Titel domeinbanner', 'globe', $s('hero.titel')) ?>
            <?= field('hero_extensies', 'Extensies in de domeinzoeker (komma-gescheiden)', 'hash', $s('hero.extensies', '.nl,.com,.eu,.be,.net,.org')) ?>
            <?= field('hero_tekst', 'Tekst domeinbanner', 'note', $s('hero.tekst'), 'textarea', [], 'rows="2"') ?>
            <?= field('over_titel', 'Titel blok "Over ons"', 'building', $s('over.titel')) ?>
            <?= field('over_tekst', 'Tekst blok "Over ons" (afbeelding via Branding)', 'note', $s('over.tekst'), 'textarea') ?>
            <div class="field-full set-title"><?= icon('users', 'icon icon-sm') ?> Pagina Over ons</div>
            <?= field('over_wie', 'Wie wij zijn', 'note', $s('over.wie'), 'textarea') ?>
            <?= field('over_hoe', 'Hoe wij zijn', 'note', $s('over.hoe'), 'textarea') ?>
            <?= field('over_missie', 'Onze missie', 'target', $s('over.missie'), 'textarea') ?>
            <div class="field-full set-title"><?= icon('layout', 'icon icon-sm') ?> Footer</div>
            <?= field('footer_tekst', 'Tekst onder het logo', 'note', $s('footer.tekst'), 'textarea', [], 'rows="2"') ?>
            <?= field('social_linkedin', 'LinkedIn', 'linkedin', $s('social.linkedin'), 'url') ?>
            <?= field('social_facebook', 'Facebook', 'facebook', $s('social.facebook'), 'url') ?>
            <?= field('social_instagram', 'Instagram', 'instagram', $s('social.instagram'), 'url') ?>
        </div><div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div></form></div>
</div>
