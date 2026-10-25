<?php $extend('frontend', ['title' => $name !== '' ? 'Domeinnaam ' . $name : 'Domeinnaam zoeken']);
$inCart = array_column($cart, 'actie', 'domein'); $total = array_sum(array_map(static fn (array $i): int => (int) $i['product']->Prijs, $cart));
$vat = array_sum(array_map(static fn (array $i): int => (int) round((int) $i['product']->Prijs * (float) $i['product']->BtwTarief / 100), $cart)); $shown = 12; $n = 0; ?>
<?= $partial('frontend._hero', ['title' => $name !== '' ? 'Domeinnaam "' . $name . '"' : 'Registreren of verhuizen', 'intro' => 'Typ je domeinnaam; wij laten per extensie zien of je hem kunt registreren of verhuizen.', 'icon' => 'globe', 'crumbs' => [['/oplossingen/domeinnaam-registreren', 'Domeinen'], [null, 'Bestellen']]]) ?>
<div class="container phero-search"><?= $partial('frontend._domain-search', ['value' => $name]) ?></div>
<section class="sec"><div class="container">
<?php if ($name === ''): ?>
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Kies je domeinnaam</h2><p>Typ alleen de naam, de extensies controleren wij. Prijzen per jaar, exclusief btw.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => array_values($products)]) ?>
<?php else: ?>
    <?php if ($typedExt !== null && !isset($products[$typedExt])): ?><div class="alert alert-bad" style="margin-bottom:16px">.<?= e($typedExt) ?> kunnen we (nog) niet leveren. Hieronder zie je de extensies die wel kunnen.</div><?php endif; ?>
    <div class="dorder">
        <div class="dres" data-domain-results>
            <?php foreach ($products as $ext => $p): $domain = $name . '.' . $ext; $hidden = $n++ >= $shown; ?>
            <div class="dres-row is-laden<?= $hidden ? ' is-hidden' : '' ?>" data-domain="<?= e($domain) ?>"<?= $hidden ? ' hidden' : '' ?>>
                <span class="dres-i"><span class="dres-spin"></span><?= icon('check') ?><?= icon('refresh') ?><?= icon('globe') ?></span>
                <div class="dres-name"><strong><?= e($name) ?><b>.<?= e($ext) ?></b></strong><small data-msg>Controleren…</small></div>
                <div class="dres-price"><?= e(money((int) $p->Prijs)) ?><small> /jaar</small></div>
                <div class="dres-act">
                    <?php if (isset($inCart[$domain])): ?><span class="dres-added"><?= icon('check', 'icon icon-sm') ?> In winkelwagen</span>
                    <?php else: ?>
                    <form method="post" action="<?= e(url('/domeinen/mand')) ?>" class="act-reg"><?= csrf_field() ?><input type="hidden" name="domein" value="<?= e($domain) ?>"><input type="hidden" name="actie" value="registreren"><button class="btn"><?= icon('cart', 'icon icon-sm') ?> Registreren</button></form>
                    <form method="post" action="<?= e(url('/domeinen/mand')) ?>" class="act-trans"><?= csrf_field() ?><input type="hidden" name="domein" value="<?= e($domain) ?>"><input type="hidden" name="actie" value="verhuizen"><input class="dres-code" name="Autorisatiecode" placeholder="Verhuiscode" aria-label="Verhuiscode (authcode)"><button class="btn btn-ghost"><?= icon('refresh', 'icon icon-sm') ?> Verhuizen</button></form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if ($n > $shown): ?><button type="button" class="btn btn-ghost dres-more" data-domain-more><?= icon('plus', 'icon icon-sm') ?> Nog <?= $n - $shown ?> extensies tonen</button><?php endif; ?>
        </div>
        <aside class="card dcart" id="mand">
            <div class="card-head"><h2><?= icon('cart') ?> Winkelwagen</h2></div>
            <div class="card-body stack">
                <?php if ($cart === []): ?><p class="muted" style="margin:0">Nog geen domeinnamen gekozen. Klik bij een vrije naam op Registreren, of vul bij je eigen domein de verhuiscode in.</p>
                <?php else: ?>
                    <div class="dcart-items"><?php foreach ($cart as $item): ?>
                        <div class="dcart-item"><span><strong><?= e($item['domein']) ?></strong><small><?= $item['actie'] === 'verhuizen' ? 'Verhuizen' . ($item['authcode'] === '' ? ' · code volgt' : '') : 'Registreren' ?> · 1 jaar</small></span><span><?= e(money((int) $item['product']->Prijs)) ?></span>
                            <form method="post" action="<?= e(url('/domeinen/mand/verwijderen')) ?>"><?= csrf_field() ?><input type="hidden" name="domein" value="<?= e($item['domein']) ?>"><button class="btn btn-ghost btn-icon btn-sm" title="Verwijderen"><?= icon('trash', 'icon icon-sm') ?></button></form></div>
                    <?php endforeach; ?></div>
                    <div class="totals" style="width:100%"><div><span>Subtotaal</span><span><?= e(money($total)) ?></span></div><div><span>Btw</span><span><?= e(money($vat)) ?></span></div><div class="grand"><span>Totaal</span><span><?= e(money($total + $vat)) ?></span></div></div>
                    <form method="post" action="<?= e(url('/domeinen/bestellen')) ?>" class="stack"><?= csrf_field() ?>
                        <label class="switch"><input type="checkbox" name="Akkoord" value="1" required> Ik ga akkoord met de algemene voorwaarden</label>
                        <button class="btn btn-lg" style="justify-content:center"><?= icon('check') ?> <?= auth_user() ? 'Bestellen' : 'Verder naar account' ?></button>
                    </form>
                    <p class="small muted" style="margin:0">Je ontvangt direct een factuur. Na betaling leggen we je domeinnamen meteen vast.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
<?php endif; ?>
</div></section>
<?php if ($name !== ''): ?>
<script>
(function () {
    var msgs = { vrij: 'Is nog vrij!', bezet: 'Al geregistreerd. Van jou? Verhuis hem hierheen.', onbekend: 'Kon niet worden gecontroleerd' };
    function check(row) {
        if (row.dataset.checked) { return; }
        row.dataset.checked = '1';
        fetch(<?= json_encode(url('/domeinen/check')) ?> + '?domein=' + encodeURIComponent(row.dataset.domain), { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); }).catch(function () { return { status: 'onbekend' }; })
            .then(function (d) {
                var s = msgs[d.status] ? d.status : 'onbekend';
                row.classList.remove('is-laden'); row.classList.add('is-' + s);
                row.querySelector('[data-msg]').textContent = msgs[s] + (d.premium ? ' (premium)' : '');
            });
    }
    var rows = document.querySelectorAll('[data-domain]');
    rows.forEach(function (r) { if (!r.hidden) { check(r); } });
    var more = document.querySelector('[data-domain-more]');
    if (more) { more.addEventListener('click', function () { rows.forEach(function (r) { if (r.hidden) { r.hidden = false; check(r); } }); more.remove(); }); }
})();
</script>
<?php endif; ?>
