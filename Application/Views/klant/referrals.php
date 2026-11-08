<?php $extend('klant', ['title' => 'Referrals']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('users') ?></div><div class="ph-text"><h1>Referrals</h1><p class="muted">Nodig leden uit en spaar tegoed.</p></div></div>
<div class="grid grid-side">
    <div class="card"><div class="card-head"><h2><?= icon('link') ?> Jouw uitnodigingslink</h2></div><div class="card-body stack">
        <p class="muted">Na €500 besteding door een aangebracht lid ontvang je 10% tegoed op volgende betalingen.</p>
        <div class="field-control"><input readonly value="<?= e($link) ?>" onclick="this.select()"></div>
        <div class="row"><strong><?= e($code) ?></strong><span class="muted"><?= (int) $summary['count'] ?> aangebracht</span></div>
    </div></div>
    <div class="card"><div class="card-head"><h2><?= icon('wallet') ?> Referraltegoed</h2></div><div class="card-body"><span class="wallet-balance"><?= e(money((int) $summary['rewards'])) ?></span><p class="small muted">Dit tegoed wordt automatisch gebruikt voor je diensten en facturen.</p></div></div>
</div>
<div class="card"><div class="card-head"><h2><?= icon('users') ?> Aangebrachte leden</h2></div><div class="card-body">
<?php if ($rows === []): ?><p class="muted">Nog niemand aangebracht.</p><?php endif; ?>
<?php foreach ($rows as $row): ?><div class="dline"><span class="pk-i"><?= icon('user') ?></span><div class="dline-t"><strong><?= e($row['Naam']) ?></strong><small><?= $row['GekwalificeerdOp'] ? 'Gekwalificeerd' : 'Nog ' . e(money(max(0, (int) $row['Drempel'] - (int) $row['Besteed']))) . ' tot de grens' ?></small></div><strong class="dline-p"><?= e(money((int) $row['Beloond'])) ?></strong></div><?php endforeach; ?>
</div></div>
