<?php $extend('frontend', ['title' => 'Bestellen']); ?>
<section class="ck-band"><div class="container"><div class="ck-band-in">
    <div class="ck-title"><span class="pk-i"><?= icon('cart', 'icon icon-sm') ?></span><div><span class="ck-kind">Bestellen</span><h1>Kies je pakket</h1></div></div>
    <span class="ck-secure"><?= icon('lock', 'icon icon-sm') ?> Veilig bestellen</span>
</div></div></section>
<section class="sec"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> Pakketten</span><h2>Waar wil je mee starten?</h2><p>Kies een pakket. Daarna vul je alle gegevens in op deze bestelpagina.</p></div></div>
    <div class="grid grid-3">
        <?php foreach ($products as $product): ?>
        <form class="card" method="post" action="<?= e(url('/bestellen')) ?>" style="display:flex;flex-direction:column">
            <?= csrf_field() ?><input type="hidden" name="KiesProduct" value="1"><input type="hidden" name="product" value="<?= e((string) $product->Slug) ?>">
            <div class="card-body stack" style="flex:1">
                <span class="pk-kind"><?= e($product->Dienstsoort ? \App\Models\Product::soortLabel($product->Dienstsoort) : (string) $product->Groep) ?></span>
                <h2 style="margin:0"><?= e($product->Naam) ?></h2>
                <?php if ((string) $product->Omschrijving !== ''): ?><p class="muted" style="margin:0"><?= e((string) $product->Omschrijving) ?></p><?php endif; ?>
                <div class="pk-price"><span class="pk-eur"><?= e(money((int) $product->Prijs)) ?></span><span class="pk-per"><?= e(period_label((string) $product->Periode)) ?></span></div>
                <button class="btn btn-lg" type="submit" style="margin-top:auto;justify-content:center"><?= icon('cart') ?> Dit pakket kiezen</button>
            </div>
        </form>
        <?php endforeach; ?>
    </div>
</div></section>
