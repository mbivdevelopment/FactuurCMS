<?php $extend('frontend', ['title' => (string) setting('website.titel', 'Hosting')]); ?>
<section class="hero">
    <div class="container">
        <h1><?= e((string) setting('website.titel', 'Snelle en betrouwbare hosting')) ?></h1>
        <p><?= e((string) setting('website.intro', '')) ?></p>
        <div class="row">
            <a class="btn btn-lg" href="<?= e(url('/diensten')) ?>"><?= icon('server') ?> Bekijk onze diensten</a>
            <a class="btn btn-lg btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('chat') ?> Neem contact op</a>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="grid grid-4">
            <?php foreach ([['zap', 'Razendsnel', 'NVMe-opslag en moderne servers in Nederland.'], ['shield', 'Veilig', 'Gratis SSL, firewall en dagelijkse back-ups.'], ['headset', 'Persoonlijke support', 'Direct contact met een specialist via tickets.'], ['euro', 'Heldere prijzen', 'Geen verrassingen, maandelijks opzegbaar.']] as [$i, $t, $d]): ?>
                <div class="card usp"><div class="stat-icon"><?= icon($i) ?></div><h3><?= e($t) ?></h3><p class="muted" style="margin:0"><?= e($d) ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section alt">
    <div class="container">
        <div class="section-title"><h2>Populaire pakketten</h2><p>Kies het pakket dat bij je past; upgraden kan altijd.</p></div>
        <div class="grid grid-3"><?php foreach ($featured as $p): ?><?= $partial('frontend._plan', ['p' => $p]) ?><?php endforeach; ?></div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="section-title"><h2>Al onze diensten</h2></div>
        <div class="grid grid-4">
            <?php foreach ($groups as $g): ?>
                <a class="card stat" href="<?= e(url('/diensten?groep=' . urlencode($g['Groep']))) ?>" style="color:inherit;text-decoration:none">
                    <div class="stat-icon"><?= icon(\App\Models\Product::GROUPS[$g['Groep']] ?? 'box') ?></div>
                    <div><div class="stat-value" style="font-size:16px"><?= e($g['Groep']) ?></div><div class="stat-label"><?= (int) $g['Aantal'] ?> pakket(ten) · vanaf <?= e(money((int) $g['Vanaf'])) ?></div></div>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="cta" style="margin-top:48px">
            <div><h2 style="color:#fff;margin:0">Hulp nodig bij je keuze?</h2><p style="margin:4px 0 0;opacity:.85">We denken graag mee over de beste oplossing.</p></div>
            <a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Contact opnemen <?= icon('arrow-right', 'icon icon-sm') ?></a>
        </div>
    </div>
</section>
