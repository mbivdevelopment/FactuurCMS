<?php $extend('frontend', ['title' => 'Contact']); ?>
<section class="section">
    <div class="container">
        <div class="grid grid-side">
            <form class="card" method="post" action="<?= e(url('/contact')) ?>">
                <?= csrf_field() ?>
                <div class="card-head"><h2><?= icon('chat') ?> Stuur ons een bericht</h2></div>
                <div class="card-body form-grid">
                    <?= field('Naam', 'Naam', 'user', '', 'text', [], 'required') ?>
                    <?= field('Email', 'E-mailadres', 'mail', '', 'email', [], 'required') ?>
                    <?= field('Bericht', 'Bericht', 'note', '', 'textarea', [], 'required') ?>
                    <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">
                </div>
                <div class="card-foot"><button class="btn" type="submit"><?= icon('send', 'icon icon-sm') ?> Versturen</button></div>
            </form>
            <div class="card"><div class="card-head"><h2><?= icon('building') ?> <?= e(company_name()) ?></h2></div><div class="card-body">
                <dl class="details">
                    <?php foreach (['Adres' => trim(setting('bedrijf.adres', '') . ', ' . setting('bedrijf.postcode', '') . ' ' . setting('bedrijf.plaats', ''), ', '), 'E-mail' => setting('bedrijf.email'), 'Telefoon' => setting('bedrijf.telefoon'), 'KvK' => setting('bedrijf.kvk'), 'Btw' => setting('bedrijf.btw')] as $k => $v): ?>
                        <?php if ($v): ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endif; ?>
                    <?php endforeach; ?>
                </dl>
            </div></div>
        </div>
    </div>
</section>
