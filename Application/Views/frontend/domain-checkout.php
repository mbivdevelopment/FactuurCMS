<?php $extend('frontend', ['title' => 'Domeinen bestellen']);
$steps = [1 => ['Winkelwagen', '/domeinen/winkelwagen'], 2 => ["Extra's", '/domeinen/extras'], 3 => ['Gegevens', '/domeinen/gegevens'], 4 => ['Overzicht', '/domeinen/overzicht']];
$guest = $user === null; $staff = $user !== null && $user->isStaff(); $u = static fn (string $k): string => $user !== null ? (string) $user->{$k} : ''; ?>
<?= $partial('frontend._hero', ['title' => 'Domeinen bestellen', 'intro' => 'In vier stappen geregeld. Na betaling leggen we je domeinnamen direct vast.', 'icon' => 'cart', 'crumbs' => [['/domeinen', 'Domeinen'], [null, $steps[$step][0]]]]) ?>
<section class="sec dcheck-sec"><div class="container">
    <ol class="dsteps"><?php foreach ($steps as $n => [$label, $href]): ?><li class="<?= $n === $step ? 'active' : ($n < $step ? 'done' : '') ?>"><?php if ($n < $step): ?><a href="<?= e(url($href)) ?>"><?php endif; ?><span class="ds-n"><?= $n < $step ? icon('check', 'icon icon-sm') : $n ?></span><span class="ds-l"><?= e($label) ?></span><?php if ($n < $step): ?></a><?php endif; ?></li><?php endforeach; ?></ol>
    <div class="dcheck">
        <div class="dcheck-main">
        <?php if ($step === 1): ?>
            <form class="card" method="post" action="<?= e(url('/domeinen/winkelwagen')) ?>"><?= csrf_field() ?><input type="hidden" name="terug" value="/domeinen/winkelwagen">
                <div class="card-head"><h2><?= icon('globe') ?> Je domeinnamen</h2><a class="btn btn-ghost btn-sm" href="<?= e(url('/domeinen')) ?>"><?= icon('plus', 'icon icon-sm') ?> Nog een domein</a></div>
                <div class="card-body stack">
                    <?php if ($cart === []): ?><p class="muted">Je winkelwagen is leeg. <a href="<?= e(url('/domeinen')) ?>">Zoek een domeinnaam</a>.</p><?php endif; ?>
                    <?php foreach ($cart as $item): ?>
                    <div class="dline">
                        <span class="pk-i"><?= icon($item['actie'] === 'verhuizen' ? 'refresh' : 'globe') ?></span>
                        <div class="dline-t"><strong><?= e($item['domein']) ?></strong><small><?= $item['actie'] === 'verhuizen' ? 'Verhuizen naar ' . e(company_name()) : 'Nieuwe registratie' ?> · 1 jaar</small>
                            <?php if ($item['actie'] === 'verhuizen'): ?><div class="dline-code"><?= icon('lock', 'icon icon-sm') ?><input name="Autorisatiecode[<?= e($item['domein']) ?>]" value="<?= e($item['authcode']) ?>" placeholder="Verhuiscode (authcode), mag ook later" aria-label="Verhuiscode"></div><?php endif; ?>
                        </div>
                        <strong class="dline-p"><?= e(money((int) $item['product']->Prijs)) ?></strong>
                        <button class="btn btn-ghost btn-icon btn-sm" formaction="<?= e(url('/domeinen/mand/verwijderen')) ?>" name="domein" value="<?= e($item['domein']) ?>" title="Verwijderen" formnovalidate><?= icon('trash', 'icon icon-sm') ?></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($cart !== []): ?><div class="card-foot row dcheck-nav"><a class="btn btn-ghost" href="<?= e(url('/domeinen')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Verder zoeken</a><button class="btn btn-lg">Verder naar extra's <?= icon('arrow-right', 'icon icon-sm') ?></button></div><?php endif; ?>
            </form>
        <?php elseif ($step === 2): ?>
            <form method="post" action="<?= e(url('/domeinen/extras')) ?>" class="stack"><?= csrf_field() ?>
                <div class="card"><div class="card-head"><h2><?= icon('server') ?> Hosting erbij?</h2></div><div class="card-body">
                    <div class="dopts">
                        <label class="dopt"><input type="radio" name="Hosting" value="0"<?= $extra['hosting'] === 0 ? ' checked' : '' ?>><span class="dopt-b"><strong>Alleen de domeinnaam</strong><small>Geen website of e-mail nodig, of je hebt al hosting.</small><b>€ 0</b></span></label>
                        <?php foreach ($hostingProducts as $hp): ?><label class="dopt"><input type="radio" name="Hosting" value="<?= (int) $hp->id() ?>"<?= $extra['hosting'] === $hp->id() ? ' checked' : '' ?>><span class="dopt-b"><?php if ($hp->Uitgelicht): ?><em>Meest gekozen</em><?php endif; ?><strong><?= e($hp->Naam) ?></strong><small><?= e(implode(' · ', array_slice($hp->features(), 0, 3))) ?></small><b><?= e(money((int) $hp->Prijs)) ?> <i><?= e(period_label((string) $hp->Periode)) ?></i></b></span></label><?php endforeach; ?>
                    </div>
                </div></div>
                <div class="card"><div class="card-head"><h2><?= icon('settings') ?> Nameservers</h2></div><div class="card-body stack">
                    <div class="dopts dopts-2">
                        <label class="dopt"><input type="radio" name="Nameservers" value="ons"<?= $extra['ns'] === [] ? ' checked' : '' ?>><span class="dopt-b"><strong>Onze nameservers</strong><small>Aanbevolen. Je regelt DNS zelf in je klantportaal.</small></span></label>
                        <label class="dopt"><input type="radio" name="Nameservers" value="eigen"<?= $extra['ns'] !== [] ? ' checked' : '' ?>><span class="dopt-b"><strong>Eigen nameservers</strong><small>Je DNS staat ergens anders.</small></span></label>
                    </div>
                    <div class="form-grid dns-own"><?php for ($i = 0; $i < 4; $i++): ?><?= field('NS[' . $i . ']', 'Nameserver ' . ($i + 1) . ($i < 2 ? '' : ' (optioneel)'), 'server', $extra['ns'][$i] ?? '', 'text', [], 'placeholder="ns' . ($i + 1) . '.voorbeeld.nl"') ?><?php endfor; ?></div>
                </div></div>
                <div class="row dcheck-nav"><a class="btn btn-ghost" href="<?= e(url('/domeinen/winkelwagen')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug</a><button class="btn btn-lg">Verder naar gegevens <?= icon('arrow-right', 'icon icon-sm') ?></button></div>
            </form>
        <?php elseif ($step === 3): ?>
            <?php if ($staff): ?><div class="alert alert-bad">Je bent ingelogd als medewerker. Log uit en log in als klant om te bestellen.</div><?php else: ?>
            <div class="<?= $guest ? 'dauth' : '' ?>">
                <form class="card" method="post" action="<?= e(url('/domeinen/gegevens')) ?>"><?= csrf_field() ?>
                    <div class="card-head"><h2><?= icon('user') ?> <?= $guest ? 'Nieuwe klant' : 'Domeinhouder' ?></h2></div>
                    <div class="card-body stack">
                        <p class="muted" style="margin:0"><?= $guest ? 'Maak een account aan; deze gegevens worden ook de houder van je domeinnamen.' : 'Deze gegevens worden de houder van je domeinnamen en staan op je factuur.' ?></p>
                        <div class="form-grid">
                            <?php if ($guest): ?><?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required autocomplete="email"') ?><?= field('Telefoon', 'Telefoon', 'phone', '', 'tel', [], 'required') ?><?= field('Wachtwoord', 'Wachtwoord', 'lock', '', 'password', [], 'required minlength="8" autocomplete="new-password"') ?><?= field('Wachtwoord_confirmation', 'Herhaal wachtwoord', 'lock', '', 'password', [], 'required autocomplete="new-password"') ?><?php endif; ?>
                            <?= field('Voornaam', 'Voornaam', 'user', $u('Voornaam'), 'text', [], 'required') ?>
                            <?= field('Achternaam', 'Achternaam', 'user', $u('Achternaam'), 'text', [], 'required') ?>
                            <?= field('Bedrijfsnaam', 'Bedrijfsnaam (optioneel)', 'building', $u('Bedrijfsnaam')) ?>
                            <?php if (!$guest): ?><?= field('Telefoon', 'Telefoon', 'phone', $u('Telefoon'), 'tel', [], 'required') ?><?php endif; ?>
                            <?= field('Adres', 'Straat en huisnummer', 'building', $u('Adres'), 'text', [], 'required') ?>
                            <?= field('Postcode', 'Postcode', 'tag', $u('Postcode'), 'text', [], 'required') ?>
                            <?= field('Plaats', 'Plaats', 'globe', $u('Plaats'), 'text', [], 'required') ?>
                            <?= field('Land', 'Land', 'globe', $u('Land') ?: 'NL', 'select', $countries) ?>
                        </div>
                    </div>
                    <div class="card-foot row dcheck-nav"><a class="btn btn-ghost" href="<?= e(url('/domeinen/extras')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug</a><button class="btn btn-lg"><?= $guest ? 'Account aanmaken en verder' : 'Verder naar overzicht' ?> <?= icon('arrow-right', 'icon icon-sm') ?></button></div>
                </form>
                <?php if ($guest): ?>
                <form class="card dauth-login" method="post" action="<?= e(url('/login')) ?>"><?= csrf_field() ?>
                    <div class="card-head"><h2><?= icon('lock') ?> Al klant?</h2></div>
                    <div class="card-body stack"><p class="muted" style="margin:0">Log in, dan gaan we verder met je bestelling.</p><?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required autocomplete="username"') ?><?= field('Wachtwoord', 'Wachtwoord', 'lock', '', 'password', [], 'required autocomplete="current-password"') ?><button class="btn btn-ghost btn-lg" style="justify-content:center"><?= icon('user', 'icon icon-sm') ?> Inloggen</button><a class="small" href="<?= e(url('/wachtwoord-vergeten')) ?>">Wachtwoord vergeten?</a></div>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <form method="post" action="<?= e(url('/domeinen/bestellen')) ?>" class="stack"><?= csrf_field() ?>
                <div class="card"><div class="card-head"><h2><?= icon('globe') ?> Domeinnamen</h2><a class="btn btn-ghost btn-sm" href="<?= e(url('/domeinen/winkelwagen')) ?>">Wijzigen</a></div><div class="card-body stack">
                    <?php foreach ($cart as $item): ?><div class="dsum-row"><span><strong><?= e($item['domein']) ?></strong> <small class="muted"><?= $item['actie'] === 'verhuizen' ? 'Verhuizen' . ($item['authcode'] === '' ? ' · verhuiscode volgt' : ' · verhuiscode ingevuld') : 'Registreren' ?></small></span><span><?= e(money((int) $item['product']->Prijs)) ?></span></div><?php endforeach; ?>
                </div></div>
                <div class="grid grid-2">
                    <div class="card"><div class="card-head"><h2><?= icon('server') ?> Extra's</h2><a class="btn btn-ghost btn-sm" href="<?= e(url('/domeinen/extras')) ?>">Wijzigen</a></div><div class="card-body stack">
                        <div><strong><?= e($hosting !== null ? $hosting->Naam : 'Geen hosting') ?></strong></div>
                        <div class="muted"><?= $extra['ns'] === [] ? 'Onze nameservers' : 'Eigen nameservers: ' . e(implode(', ', $extra['ns'])) ?></div>
                    </div></div>
                    <div class="card"><div class="card-head"><h2><?= icon('user') ?> Domeinhouder</h2><a class="btn btn-ghost btn-sm" href="<?= e(url('/domeinen/gegevens')) ?>">Wijzigen</a></div><div class="card-body">
                        <strong><?= e(trim($u('Voornaam') . ' ' . $u('Achternaam'))) ?></strong><?php if ($u('Bedrijfsnaam') !== ''): ?><br><?= e($u('Bedrijfsnaam')) ?><?php endif; ?><br><span class="muted"><?= e($u('Adres')) ?>, <?= e($u('Postcode')) ?> <?= e($u('Plaats')) ?><br><?= e($u('Email')) ?> · <?= e($u('Telefoon')) ?></span>
                    </div></div>
                </div>
                <div class="card"><div class="card-head"><h2><?= icon('tag') ?> Betalen</h2></div><div class="card-body stack">
                    <div class="dopts dopts-2">
                        <?php $first = true; foreach ($methods as $key => $label): ?><label class="dopt"><input type="radio" name="Betaalmethode" value="<?= e($key) ?>"<?= $first ? ' checked' : '' ?>><span class="dopt-b"><strong><?= e($label) ?></strong><small><?= $key === 'mollie' ? 'Direct betaald, direct vastgelegd.' : 'Bitcoin, USDT en meer.' ?></small></span></label><?php $first = false; endforeach; ?>
                        <label class="dopt"><input type="radio" name="Betaalmethode" value="factuur"<?= $methods === [] ? ' checked' : '' ?>><span class="dopt-b"><strong>Factuur</strong><small>Je betaalt later via de factuur; we leggen vast na betaling.</small></span></label>
                    </div>
                    <label class="switch"><input type="checkbox" name="Akkoord" value="1" required> Ik ga akkoord met de algemene voorwaarden</label>
                </div></div>
                <div class="row dcheck-nav"><a class="btn btn-ghost" href="<?= e(url('/domeinen/gegevens')) ?>"><?= icon('chevron-left', 'icon icon-sm') ?> Terug</a><button class="btn btn-lg"><?= icon('check', 'icon icon-sm') ?> Bestellen en betalen</button></div>
            </form>
        <?php endif; ?>
        </div>
        <aside class="card dsummary">
            <div class="card-head"><h2><?= icon('cart') ?> Je bestelling</h2></div>
            <div class="card-body stack">
                <?php if ($totals['lines'] === []): ?><p class="muted" style="margin:0">Nog leeg.</p><?php endif; ?>
                <?php foreach ($totals['lines'] as [$label, $sub, $cents]): ?><div class="dsum-row"><span><strong><?= e($label) ?></strong><small><?= e($sub) ?></small></span><span><?= e(money($cents)) ?></span></div><?php endforeach; ?>
                <div class="totals" style="width:100%"><div><span>Subtotaal</span><span><?= e(money($totals['subtotal'])) ?></span></div><div><span>Btw</span><span><?= e(money($totals['vat'])) ?></span></div><div class="grand"><span>Totaal</span><span><?= e(money($totals['total'])) ?></span></div></div>
                <ul class="dcart-usps"><li><?= icon('check', 'icon icon-sm') ?> Direct vastgelegd na betaling</li><li><?= icon('check', 'icon icon-sm') ?> Gratis DNS-beheer</li><li><?= icon('shield', 'icon icon-sm') ?> Veilig betalen</li></ul>
            </div>
        </aside>
    </div>
</div></section>
