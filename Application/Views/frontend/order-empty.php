<?php $extend('frontend', ['title' => 'Bestellen']); ?>
<section class="ck-band"><div class="container"><div class="ck-band-in">
    <div class="ck-title"><span class="pk-i"><?= icon('cart', 'icon icon-sm') ?></span><div><span class="ck-kind">Bestellen</span><h1>Je bestelling</h1></div></div>
    <span class="ck-secure"><?= icon('lock', 'icon icon-sm') ?> Veilig bestellen</span>
</div></div></section>
<section class="sec"><div class="container"><div class="card" style="max-width:680px;margin:0 auto">
    <div class="card-body stack" style="text-align:center;padding:48px 28px">
        <span class="pk-i" style="margin:0 auto"><?= icon('cart') ?></span>
        <h2 style="margin:0">Je winkelwagen is leeg</h2>
        <p class="muted" style="margin:0">Kies een pakket op een productpagina. Daarna onthouden we je keuze en vul je alles verder in op deze bestelpagina.</p>
        <a class="btn btn-lg" href="<?= e(url('/oplossingen/webhosting')) ?>" style="align-self:center"><?= icon('box') ?> Bekijk pakketten</a>
    </div>
</div></div></section>
