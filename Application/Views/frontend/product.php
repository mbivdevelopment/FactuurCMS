<?php $extend('frontend', ['title' => $product->Naam]); $vat = (float) $product->BtwTarief; $first = (int) $product->Prijs + (int) $product->Instelkosten;
$kind = $product->Dienstsoort ? \App\Models\Product::soortLabel($product->Dienstsoort) : (string) $product->Groep;
$crumbs = [['/diensten', 'Diensten']];
if ($soortPage !== null) { $crumbs[] = ['/oplossingen/' . $soortPage, $kind]; } elseif ($kind !== '') { $crumbs[] = ['/diensten#' . slugify($product->isExtension() ? 'domeinextensies' : $kind), $kind]; }
$crumbs[] = [null, $product->Naam];
$per = ['maand' => '/ maand', 'kwartaal' => '/ kwartaal', 'jaar' => '/ jaar'][(string) $product->Periode] ?? 'eenmalig'; ?>
<?= $partial('frontend._hero', ['title' => $product->Naam, 'intro' => (string) $product->Omschrijving, 'icon' => \App\Models\Product::soortIcon($product->Dienstsoort, $product->Groep), 'crumbs' => $crumbs]) ?>
<section class="sec">
    <div class="container">
        <div class="grid grid-side prod-split">
            <div class="prod-main">
                <div class="prod-card">
                    <span class="pk-kind"><?= e($kind) ?></span>
                    <h2><?= e($product->Naam) ?></h2>
                    <?php if ((string) $product->Omschrijving !== ''): ?><p class="muted"><?= nl2br(e($product->Omschrijving)) ?></p><?php endif; ?>
                    <div class="pk-price"><span class="pk-eur"><?= preg_replace('/,(\d\d)$/', '<sup>,$1</sup>', e(money((int) $product->Prijs))) ?></span><span class="pk-per"><?= e($per) ?></span><span class="pk-vat" style="margin:0 0 0 6px">excl. btw</span></div>
                    <?php if ($product->featureItems() !== []): ?>
                        <h3 class="prod-h">Wat zit erin</h3>
                        <ul class="feat-grid"><?php foreach ($product->featureItems() as $f): ?><li><span class="pk-fi"><?= icon($f['icoon'], 'icon icon-sm') ?></span><span><?= e($f['tekst']) ?></span></li><?php endforeach; ?></ul>
                    <?php endif; ?>
                </div>
                <?= $partial('frontend._usps') ?>
            </div>
            <div class="card order-card">
                <div class="card-head"><h2><?= icon('cart') ?> Bestellen</h2></div>
                <div class="card-body stack">
                    <div class="totals" style="width:100%">
                        <div><span><?= e($product->Naam) ?> (<?= e(period_label((string) $product->Periode)) ?>)</span><span><?= e(money((int) $product->Prijs)) ?></span></div>
                        <?php if ((int) $product->Instelkosten > 0): ?><div><span>Instelkosten</span><span><?= e(money((int) $product->Instelkosten)) ?></span></div><?php endif; ?>
                        <div><span>Btw <?= e(rtrim(rtrim((string) $vat, '0'), '.')) ?>%</span><span><?= e(money((int) round($first * $vat / 100))) ?></span></div>
                        <div class="grand"><span>Eerste factuur</span><span><?= e(money($first + (int) round($first * $vat / 100))) ?></span></div>
                    </div>
                    <a class="btn btn-lg" href="<?= e(url($product->path() . '/bestellen')) ?>" style="justify-content:center"><?= icon('cart') ?> Bestellen</a>
                    <p class="small muted" style="margin:0"><?= icon('lock', 'icon icon-sm') ?> Veilig bestellen en direct betalen.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if ($siblings !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('box', 'icon icon-sm') ?> <?= e($kind) ?></span><h2>Vergelijk de pakketten</h2><p>Upgraden kan altijd.</p></div></div>
    <?= $partial('frontend._plans', ['products' => $siblings]) ?>
</div></section>
<?php endif; ?>
