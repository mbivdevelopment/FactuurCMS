<?php $extend('klant', ['title' => 'Tegoed']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('wallet') ?></div><div class="ph-text"><h1>Je tegoed</h1><p class="muted">Waardeer je saldo op en betaal facturen direct vanuit je tegoed.</p></div>
    <div class="row"><span class="wallet-balance"><?= e(money($saldo)) ?></span></div></div>
<div class="grid grid-side">
    <div class="card"><div class="card-head"><h2><?= icon('list') ?> Mutaties</h2></div><div class="card-body">
        <?php if ($mutaties === []): ?><p class="muted">Nog geen mutaties.</p><?php endif; ?>
        <?php foreach ($mutaties as $m): ?>
            <div class="dline">
                <span class="pk-i"><?= icon((int) $m['Bedrag'] >= 0 ? 'plus' : 'euro') ?></span>
                <div class="dline-t"><strong><?= e($m['Reden']) ?></strong><small><?= e(date('d-m-Y H:i', strtotime((string) $m['AangemaaktOp']))) ?></small></div>
                <strong class="dline-p" style="color:<?= (int) $m['Bedrag'] >= 0 ? 'var(--green,#16a34a)' : 'inherit' ?>"><?= (int) $m['Bedrag'] >= 0 ? '+' : '' ?><?= e(money((int) $m['Bedrag'])) ?></strong>
            </div>
        <?php endforeach; ?>
    </div></div>
    <div class="card"><div class="card-head"><h2><?= icon('plus') ?> Opwaarderen</h2></div><div class="card-body">
        <form method="post" action="<?= e(url('/klant/tegoed')) ?>" class="stack"><?= csrf_field() ?>
            <?= field('Bedrag', 'Bedrag (min. € 5)', 'euro', '25', 'text', [], 'inputmode="decimal" placeholder="bijv. 25,00"') ?>
            <button class="btn" type="submit"><?= icon('wallet', 'icon icon-sm') ?> Opwaarderen</button>
            <p class="small muted" style="margin:0">Er wordt een factuur aangemaakt; na betaling staat het bedrag direct op je tegoed.</p>
        </form>
    </div></div>
</div>
