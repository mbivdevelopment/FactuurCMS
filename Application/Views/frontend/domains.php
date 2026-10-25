<?php $extend('frontend', ['title' => $name !== '' ? 'Domeinnaam ' . $name : 'Domeinnaam zoeken']);
$inCart = array_column($cart, 'actie', 'domein'); $total = array_sum(array_map(static fn (array $i): int => (int) $i['product']->Prijs, $cart));
?>
<?= $partial('frontend._hero', ['title' => $name !== '' ? 'Domeinnaam "' . $name . '"' : 'Registreren of verhuizen', 'intro' => 'Typ je domeinnaam; wij laten per extensie zien of je hem kunt registreren of verhuizen.', 'icon' => 'globe', 'crumbs' => [['/oplossingen/domeinnaam-registreren', 'Domeinen'], [null, 'Zoeken']]]) ?>
<div class="container phero-search"><?= $partial('frontend._domain-search', ['value' => $name]) ?></div>
<section class="sec"><div class="container">
<?php if ($name === '' || $main === null): ?>
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('globe', 'icon icon-sm') ?> Domeinextensies</span><h2>Kies je domeinnaam</h2><p>Typ alleen de naam, de extensies controleren wij. Prijzen per jaar, exclusief btw.</p></div></div>
    <?= $partial('frontend._extensions', ['domains' => array_values($products)]) ?>
<?php else: $mainDomain = $name . '.' . $main; ?>
    <?php if ($typedExt !== null && !isset($products[$typedExt])): ?><div class="alert alert-bad">.<?= e($typedExt) ?> kunnen we (nog) niet leveren. Hieronder zie je de extensies die wel kunnen.</div><?php endif; ?>
    <div class="dorder" data-domains>
        <div class="dorder-main">
            <div class="dmain is-laden<?= isset($inCart[$mainDomain]) ? ' in-cart' : '' ?>" data-row data-domain="<?= e($mainDomain) ?>">
                <div class="dmain-l">
                    <span class="dres-i"><span class="dres-spin"></span><?= icon('check') ?><?= icon('refresh') ?><?= icon('globe') ?></span>
                    <div><small class="dmain-msg" data-msg>Controleren…</small><h2><?= e($name) ?><b>.<?= e($main) ?></b></h2></div>
                </div>
                <div class="dmain-r">
                    <div class="dmain-price"><?= e(money((int) $products[$main]->Prijs)) ?><small> / jaar</small></div>
                    <div class="dres-act"><?= $partial('frontend._domain-actions', ['domain' => $mainDomain, 'big' => true]) ?></div>
                </div>
            </div>
            <div class="dres">
                <?php foreach ($popular as $ext): $p = $products[$ext]; $domain = $name . '.' . $ext; ?>
                <div class="dres-row is-laden<?= isset($inCart[$domain]) ? ' in-cart' : '' ?>" data-row data-domain="<?= e($domain) ?>">
                    <span class="dres-i"><span class="dres-spin"></span><?= icon('check') ?><?= icon('refresh') ?><?= icon('globe') ?></span>
                    <div class="dres-name"><strong><?= e($name) ?><b>.<?= e($ext) ?></b></strong><small data-msg>Controleren…</small></div>
                    <div class="dres-price"><?= e(money((int) $p->Prijs)) ?><small> /jaar</small></div>
                    <div class="dres-act"><?= $partial('frontend._domain-actions', ['domain' => $domain]) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($products) > count($popular) + 1): ?>
            <form class="dother" method="get" action="<?= e(url('/domeinen')) ?>">
                <span><?= icon('search', 'icon icon-sm') ?> Andere extensie?</span>
                <input type="hidden" name="domein" value="<?= e($name) ?>">
                <div class="dother-in"><b><?= e($name) ?></b><input name="ext" list="dext" placeholder=".shop" aria-label="Extensie" required autocomplete="off"></div>
                <datalist id="dext"><?php foreach (array_keys($products) as $x): ?><option value=".<?= e($x) ?>"><?php endforeach; ?></datalist>
                <button class="btn btn-ghost">Controleren</button>
                <small><?= count($products) ?> extensies beschikbaar</small>
            </form>
            <?php endif; ?>
        </div>
        <aside class="card dcart" id="mand" data-cart>
            <div class="card-head"><h2><?= icon('cart') ?> Winkelwagen <span class="dcart-n" data-cart-count><?= count($cart) ?></span></h2></div>
            <div class="card-body stack">
                <p class="muted" style="margin:0" data-cart-empty<?= $cart !== [] ? ' hidden' : '' ?>>Nog niets gekozen. Klik op Registreren bij een vrije naam, of op Verhuizen bij je eigen domein.</p>
                <div class="dcart-items" data-cart-items><?php foreach ($cart as $item): ?><div class="dcart-item"><span><strong><?= e($item['domein']) ?></strong><small><?= $item['actie'] === 'verhuizen' ? 'Verhuizen' : 'Registreren' ?></small></span><span><?= e(money((int) $item['product']->Prijs)) ?></span><button type="button" class="btn btn-ghost btn-icon btn-sm" data-cart-del="<?= e($item['domein']) ?>" title="Verwijderen"><?= icon('trash', 'icon icon-sm') ?></button></div><?php endforeach; ?></div>
                <div class="dcart-total" data-cart-total-wrap<?= $cart === [] ? ' hidden' : '' ?>><span>Totaal per jaar <small>excl. btw</small></span><strong data-cart-total><?= e(money($total)) ?></strong></div>
                <a class="btn btn-lg dcart-go" href="<?= e(url('/domeinen/winkelwagen')) ?>" data-cart-go<?= $cart === [] ? ' hidden' : '' ?>>Bestellen <?= icon('arrow-right', 'icon icon-sm') ?></a>
                <ul class="dcart-usps"><li><?= icon('check', 'icon icon-sm') ?> Direct vastgelegd na betaling</li><li><?= icon('check', 'icon icon-sm') ?> Gratis DNS-beheer</li><li><?= icon('check', 'icon icon-sm') ?> Gratis verhuizen, je jaar loopt door</li></ul>
            </div>
        </aside>
    </div>
    <form method="post" action="<?= e(url('/domeinen/mand/verwijderen')) ?>" data-cart-remove hidden><?= csrf_field() ?><input type="hidden" name="domein"><input type="hidden" name="terug" value="<?= e('/domeinen?domein=' . $name) ?>"></form>
    <template data-trash><?= icon('trash', 'icon icon-sm') ?></template>
<?php endif; ?>
</div></section>
<?php if ($name !== '' && $main !== null): ?>
<script>
(function () {
    var box = document.querySelector('[data-domains]');
    var main = box.querySelector('.dmain');
    var msgs = { vrij: 'Is nog vrij!', bezet: 'Al geregistreerd. Van jou? Verhuis hem hierheen.', onbekend: 'Kon niet worden gecontroleerd' };
    var mainMsgs = { vrij: 'Gefeliciteerd, deze domeinnaam is nog vrij!', bezet: 'Deze domeinnaam is al bezet. Is hij van jou? Verhuis hem dan naar ons.', onbekend: 'We konden deze naam nu niet controleren.' };
    var queue = [], busy = 0;
    function check(row) {
        if (row.dataset.checked) { return; }
        row.dataset.checked = '1'; queue.push(row); next();
    }
    function next() {
        if (busy >= 3 || !queue.length) { return; }
        var row = queue.shift(); busy++;
        fetch(<?= json_encode(url('/domeinen/check')) ?> + '?domein=' + encodeURIComponent(row.dataset.domain), { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); }).catch(function () { return { status: 'onbekend' }; })
            .then(function (d) {
                var s = msgs[d.status] ? d.status : 'onbekend';
                row.classList.remove('is-laden'); row.classList.add('is-' + s);
                row.querySelector('[data-msg]').textContent = (row === main ? mainMsgs[s] : msgs[s]) + (d.premium ? ' (premium)' : '');
            })
            .then(function () { busy--; next(); });
    }
    var rows = box.querySelectorAll('[data-row]');
    rows.forEach(check);
    var trash = document.querySelector('[data-trash]').innerHTML;
    function render(cart) {
        var list = document.querySelector('[data-cart-items]'); list.innerHTML = '';
        var names = {};
        cart.items.forEach(function (i) {
            names[i.domein] = 1;
            var el = document.createElement('div'); el.className = 'dcart-item';
            el.innerHTML = '<span><strong></strong><small></small></span><span></span><button type="button" class="btn btn-ghost btn-icon btn-sm" title="Verwijderen">' + trash + '</button>';
            el.querySelector('strong').textContent = i.domein; el.querySelector('small').textContent = i.actie; el.children[1].textContent = i.prijs;
            el.querySelector('button').dataset.cartDel = i.domein;
            list.appendChild(el);
        });
        document.querySelector('[data-cart-count]').textContent = cart.count;
        document.querySelector('[data-cart-total]').textContent = cart.total;
        document.querySelector('[data-cart-empty]').hidden = cart.count > 0;
        document.querySelector('[data-cart-total-wrap]').hidden = cart.count === 0;
        document.querySelector('[data-cart-go]').hidden = cart.count === 0;
        rows.forEach(function (r) { r.classList.toggle('in-cart', !!names[r.dataset.domain]); });
    }
    function send(form) {
        return fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d.cart) { render(d.cart); } if (!d.ok) { alert(d.message); } })
            .catch(function () { form.submit(); });
    }
    document.addEventListener('submit', function (e) { if (e.target.matches('[data-cart-add]')) { e.preventDefault(); send(e.target); } });
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-cart-del]'); if (!b) { return; }
        var f = document.querySelector('[data-cart-remove]'); f.querySelector('[name=domein]').value = b.dataset.cartDel; send(f);
    });
})();
</script>
<?php endif; ?>
