<?php /** @var \App\Models\Product $product */
$extend('frontend', ['title' => $product->Naam . ' bestellen']);
$vat = (float) $product->BtwTarief;
$first = (int) $product->Prijs + (int) $product->Instelkosten;
$per = period_label((string) $product->Periode);
$kind = $product->Dienstsoort ? \App\Models\Product::soortLabel($product->Dienstsoort) : (string) $product->Groep;
$user = auth_user(); ?>
<section class="ck-band"><div class="container">
    <div class="ck-band-in">
        <div><h1><?= e($product->Naam) ?></h1></div>
        <span class="ck-secure"><?= icon('lock', 'icon icon-sm') ?> Veilig bestellen</span>
    </div>
</div></section>
<section class="ck-sec"><div class="container">
    <form class="dcheck" method="post" action="<?= e(url($product->path() . '/bestellen')) ?>"><?= csrf_field() ?>
        <div class="dcheck-main stack">
            <?php if ($user === null): ?>
            <div class="card"><div class="card-head"><h2><?= icon('user') ?> Je gegevens</h2><a class="btn btn-ghost btn-sm" href="<?= e(url('/login')) ?>">Al klant? Inloggen</a></div><div class="card-body form-grid">
                <?= field('Voornaam', 'Voornaam', 'user') ?>
                <?= field('Achternaam', 'Achternaam', 'user') ?>
                <?= field('Email', 'E-mailadres', 'mail', '', 'email') ?>
                <?= field('Wachtwoord', 'Wachtwoord', 'lock', '', 'password', [], 'autocomplete="new-password"') ?>
                <?= field('Wachtwoord_confirmation', 'Wachtwoord herhalen', 'lock', '', 'password', [], 'autocomplete="new-password"') ?>
                <?= field('Bedrijfsnaam', 'Bedrijfsnaam (optioneel)', 'building') ?>
            </div></div>
            <?php else: ?>
            <div class="card"><div class="card-head"><h2><?= icon('user') ?> Je gegevens</h2></div><div class="card-body">
                <div class="dline"><span class="pk-i"><?= icon('check') ?></span><div class="dline-t"><strong><?= e($user->display()) ?></strong><small><?= e($user->Email) ?> · klantnummer <?= e($user->Nummer) ?></small></div></div>
            </div></div>
            <?php endif; ?>
            <div class="card"><div class="card-head"><h2><?= icon('wallet') ?> Betaalmethode</h2></div><div class="card-body">
                <div class="dopts">
                    <?php $firstMethod = true; foreach ($methods as $key => $label): ?><label class="dopt"><input type="radio" name="Betaalmethode" value="<?= e($key) ?>"<?= $firstMethod ? ' checked' : '' ?>><span class="dopt-b"><strong><?= e($label) ?></strong><small><?= $key === 'mollie' ? 'Direct betalen, direct actief.' : 'Bitcoin, USDT en meer.' ?></small></span></label><?php $firstMethod = false; endforeach; ?>
                    <label class="dopt"><input type="radio" name="Betaalmethode" value="factuur"<?= $methods === [] ? ' checked' : '' ?>><span class="dopt-b"><strong>Factuur</strong><small>Je betaalt later via de factuur; activering na betaling.</small></span></label>
                </div>
            </div></div>
        </div>
        <aside class="dcheck-side">
            <div class="card"><div class="card-head"><h2><?= icon('cart') ?> Overzicht</h2></div><div class="card-body stack">
                <div class="dline"><span class="pk-i"><?= icon(\App\Models\Product::soortIcon($product->Dienstsoort, $product->Groep)) ?></span><div class="dline-t"><strong><?= e($product->Naam) ?></strong><small><?= e($kind) ?> · <?= e($per) ?></small></div><strong class="dline-p"><?= e(money((int) $product->Prijs)) ?></strong></div>
                <div class="totals" style="width:100%">
                    <div><span><?= e($product->Naam) ?></span><span><?= e(money((int) $product->Prijs)) ?></span></div>
                    <?php if ((int) $product->Instelkosten > 0): ?><div><span>Instelkosten</span><span><?= e(money((int) $product->Instelkosten)) ?></span></div><?php endif; ?>
                    <div><span>Btw <?= e(rtrim(rtrim((string) $vat, '0'), '.')) ?>%</span><span><?= e(money((int) round($first * $vat / 100))) ?></span></div>
                    <div class="grand"><span>Totaal vandaag</span><span><?= e(money($first + (int) round($first * $vat / 100))) ?></span></div>
                </div>
                <?= field('Notitie', $product->Groep === 'Domeinen' ? 'Gewenste domeinnaam' : 'Domeinnaam / opmerking', 'globe', (string) ($_GET['domein'] ?? ''), 'text', [], 'placeholder="bijv. mijnbedrijf.nl"') ?>
                <label class="switch"><input type="checkbox" name="Akkoord" value="1" required> Ik ga akkoord met de algemene voorwaarden</label>
                <button class="btn btn-lg" type="submit" style="justify-content:center;width:100%"><?= icon('check') ?> Bestellen en betalen</button>
                <p class="small muted" style="margin:0"><?= icon('lock', 'icon icon-sm') ?> De dienst wordt na betaling direct geactiveerd.</p>
            </div></div>
        </aside>
    </form>
</div></section>
