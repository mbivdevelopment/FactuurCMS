<?php $extend('frontend', ['title' => $product->Naam]); $vat = (float) $product->BtwTarief; $first = (int) $product->Prijs + (int) $product->Instelkosten; ?>
<?= $partial('frontend._hero', ['title' => $product->Naam, 'intro' => (string) $product->Omschrijving, 'icon' => \App\Models\Product::GROUPS[$product->Groep] ?? 'box', 'crumbs' => [['/diensten?groep=' . urlencode((string) $product->Groep), (string) $product->Groep], [null, $product->Naam]]]) ?>
<section class="sec">
    <div class="container">
        <div class="grid grid-side">
            <div class="card"><div class="card-body">
                <div class="small" style="color:var(--primary);font-weight:600"><?= e($product->Groep) ?></div>
                <h1><?= e($product->Naam) ?></h1>
                <p class="muted"><?= nl2br(e($product->Omschrijving)) ?></p>
                <ul class="plan" style="padding:0;list-style:none"><?php foreach ($product->featureItems() as $f): ?><li style="display:flex;gap:8px;padding:5px 0"><span style="color:var(--ok)"><?= icon($f['icoon'], 'icon icon-sm') ?></span> <?= e($f['tekst']) ?></li><?php endforeach; ?></ul>
            </div></div>
            <form class="card" method="post" action="<?= e(url($product->path() . '/bestellen')) ?>">
                <?= csrf_field() ?>
                <div class="card-head"><h2><?= icon('cart') ?> Bestellen</h2></div>
                <div class="card-body stack">
                    <div class="totals" style="width:100%">
                        <div><span><?= e($product->Naam) ?> (<?= e(period_label((string) $product->Periode)) ?>)</span><span><?= e(money((int) $product->Prijs)) ?></span></div>
                        <?php if ((int) $product->Instelkosten > 0): ?><div><span>Instelkosten</span><span><?= e(money((int) $product->Instelkosten)) ?></span></div><?php endif; ?>
                        <div><span>Btw <?= e(rtrim(rtrim((string) $vat, '0'), '.')) ?>%</span><span><?= e(money((int) round($first * $vat / 100))) ?></span></div>
                        <div class="grand"><span>Eerste factuur</span><span><?= e(money($first + (int) round($first * $vat / 100))) ?></span></div>
                    </div>
                    <?= field('Notitie', $product->Groep === 'Domeinen' ? 'Gewenste domeinnaam' : 'Domeinnaam / opmerking', 'globe', (string) ($_GET['domein'] ?? ''), 'text', [], 'placeholder="bijv. mijnbedrijf.nl"') ?>
                    <label class="switch"><input type="checkbox" name="Akkoord" value="1" required> Ik ga akkoord met de algemene voorwaarden</label>
                    <button class="btn btn-lg" type="submit" style="justify-content:center"><?= icon('check') ?> <?= auth_user() ? 'Bestelling plaatsen' : 'Verder naar account' ?></button>
                    <p class="small muted" style="margin:0">Je ontvangt direct een factuur; de dienst wordt na betaling geactiveerd.</p>
                </div>
            </form>
        </div>
    </div>
</section>
