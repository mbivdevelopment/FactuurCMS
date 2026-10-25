<?php /** @var \App\Models\Product $product */
$extend('frontend', ['title' => $product->Naam . ' bestellen']);
$vat = (float) $product->BtwTarief;
$instel = (int) $product->Instelkosten;
$per = period_label((string) $product->Periode);
$kind = $product->Dienstsoort ? \App\Models\Product::soortLabel($product->Dienstsoort) : (string) $product->Groep;
$hosting = $product->Periode !== 'eenmalig' && $product->Groep !== 'Domeinen' && $product->Dienstsoort !== 'Domeinnamen';
$recurring = $product->Periode !== 'eenmalig';
$termijnen = [];
if ($recurring) {
    foreach (\App\Services\Billing::TERMIJNEN as $tk => [$m, $d]) {
        $termijnen[$tk] = ['label' => ['maand' => 'Per maand', 'kwartaal' => 'Per kwartaal', 'jaar' => 'Per jaar', '2jaar' => 'Per 2 jaar', '4jaar' => 'Per 4 jaar'][$tk], 'prijs' => \App\Services\Billing::termijnPrijs($product, $tk), 'korting' => $d, 'per' => $m];
    }
}
$user = auth_user(); ?>
<section class="ck-band"><div class="container">
    <div class="ck-band-in">
        <div class="ck-title"><span class="pk-i"><?= icon(\App\Models\Product::soortIcon($product->Dienstsoort, $product->Groep), 'icon icon-sm') ?></span><div><span class="ck-kind"><?= e($kind) ?></span><h1><?= e($product->Naam) ?></h1></div></div>
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
            <?php if ($hosting): ?>
            <div class="card"><div class="card-head"><h2><?= icon('globe') ?> Domeinnaam</h2></div><div class="card-body stack">
                <?= field('Domeinnaam', 'Op welke domeinnaam koppelen we het pakket?', 'globe', (string) ($_GET['domein'] ?? ''), 'text', [], 'placeholder="bijv. mijnbedrijf.nl" required data-domein') ?>
                <div class="dopts">
                    <label class="dopt"><input type="radio" name="DomeinActie" value="hebik" checked data-dactie><span class="dopt-b"><strong>Ik heb deze domeinnaam al</strong><small>We koppelen het pakket aan je bestaande domein.</small></span></label>
                    <label class="dopt"><input type="radio" name="DomeinActie" value="registreren" data-dactie><span class="dopt-b"><strong>Registreer bij <?= e(company_name()) ?></strong><small data-dprijs>Wij registreren de domeinnaam voor je; de prijs komt erbij.</small></span></label>
                    <label class="dopt"><input type="radio" name="DomeinActie" value="verhuizen" data-dactie><span class="dopt-b"><strong>Verhuis mijn domein</strong><small>We verhuizen je domeinnaam naar ons; de verhuisprijs komt erbij.</small></span></label>
                </div>
                <div data-epp hidden><?= field('EppCode', 'Verhuiscode (EPP / authcode)', 'lock', '', 'text', [], 'placeholder="bijv. Xk9f-2LpQ-8mZn"') ?></div>
            </div></div>
            <?php endif; ?>
            <?php if ($termijnen !== []): ?>
            <div class="card"><div class="card-head"><h2><?= icon('calendar') ?> Looptijd</h2></div><div class="card-body">
                <div class="dopts">
                    <?php $first = true; foreach ($termijnen as $tk => $t): ?><label class="dopt"><input type="radio" name="Termijn" value="<?= e($tk) ?>"<?= $tk === (string) $product->Periode || ($first && !isset($termijnen[(string) $product->Periode])) ? ' checked' : '' ?> data-termprijs="<?= (int) $t['prijs'] ?>"><span class="dopt-b"><?php if ($t['korting'] > 0): ?><em><?= (int) $t['korting'] ?>% korting</em><?php endif; ?><strong><?= e($t['label']) ?></strong><small><?= e(money((int) round($t['prijs'] / $t['per']))) ?> / maand</small><b><?= e(money((int) $t['prijs'])) ?></b></span></label><?php $first = false; endforeach; ?>
                </div>
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
                <div class="dline"><span class="pk-i"><?= icon(\App\Models\Product::soortIcon($product->Dienstsoort, $product->Groep)) ?></span><div class="dline-t"><strong><?= e($product->Naam) ?></strong><small><?= e($kind) ?><?= $hosting ? '' : ' · ' . e($per) ?></small></div><strong class="dline-p" data-line-price><?= e(money((int) $product->Prijs)) ?></strong></div>
                <div class="totals" style="width:100%">
                    <div><span data-line-naam><?= e($product->Naam) ?></span><span data-line-bedrag><?= e(money((int) $product->Prijs)) ?></span></div>
                    <div data-dom-line hidden><span data-dom-naam>Domeinnaam</span><span data-dom-bedrag></span></div>
                    <?php if ($instel > 0): ?><div><span>Instelkosten</span><span><?= e(money($instel)) ?></span></div><?php endif; ?>
                    <div><span>Btw</span><span data-vat></span></div>
                    <div class="grand"><span>Totaal vandaag</span><span data-total></span></div>
                </div>
                <?php if (!$hosting): ?><?= field('Notitie', 'Domeinnaam / opmerking', 'globe', (string) ($_GET['domein'] ?? ''), 'text', [], 'placeholder="bijv. mijnbedrijf.nl"') ?><?php endif; ?>
                <label class="switch"><input type="checkbox" name="Akkoord" value="1" required> Ik ga akkoord met de algemene voorwaarden</label>
                <button class="btn btn-lg" type="submit" style="justify-content:center;width:100%"><?= icon('check') ?> Bestellen en betalen</button>
                <p class="small muted" style="margin:0"><?= icon('lock', 'icon icon-sm') ?> De dienst wordt na betaling direct geactiveerd.</p>
            </div></div>
        </aside>
    </form>
</div></section>
<script>
(function () {
    var exts = <?= json_encode($extPrijzen) ?>;
    var instel = <?= $instel ?>, vatRate = <?= json_encode($vat) ?>;
    var money = function (c) { return '€ ' + (c / 100).toFixed(2).replace('.', ','); };
    function compute() {
        var term = document.querySelector('input[name="Termijn"]:checked');
        var base = term ? parseInt(term.dataset.termprijs, 10) : <?= (int) $product->Prijs ?>;
        var dom = 0, domVat = 0, domName = '';
        var actie = document.querySelector('input[name="DomeinActie"]:checked');
        var epp = document.querySelector('[data-epp]');
        if (epp) epp.hidden = !(actie && actie.value === 'verhuizen');
        var inp = document.querySelector('[data-domein]');
        if (actie && (actie.value === 'registreren' || actie.value === 'verhuizen') && inp && inp.value) {
            var parts = inp.value.trim().toLowerCase().split('.');
            var ext = parts.length > 1 ? parts[parts.length - 1] : '';
            if (exts[ext]) { dom = exts[ext].prijs; domVat = exts[ext].btw; domName = (actie.value === 'verhuizen' ? 'Verhuizing ' : 'Registratie ') + inp.value.trim(); }
        }
        document.querySelectorAll('[data-dom-line]').forEach(function (el) {
            el.hidden = !domName;
            var n = el.querySelector('[data-dom-naam]'), b = el.querySelector('[data-dom-bedrag]');
            if (n) n.textContent = domName || 'Domeinnaam';
            if (b) b.textContent = dom ? money(dom) : '';
        });
        var prijs = document.querySelector('[data-line-price]'), bedrag = document.querySelector('[data-line-bedrag]');
        if (prijs) prijs.textContent = money(base);
        if (bedrag) bedrag.textContent = money(base);
        var vat = Math.round(base * vatRate / 100) + Math.round(dom * domVat / 100);
        var total = base + instel + dom + vat;
        var v = document.querySelector('[data-vat]'); if (v) v.textContent = money(vat);
        var t = document.querySelector('[data-total]'); if (t) t.textContent = money(total);
    }
    document.addEventListener('change', compute);
    document.addEventListener('input', compute);
    compute();
})();
</script>
