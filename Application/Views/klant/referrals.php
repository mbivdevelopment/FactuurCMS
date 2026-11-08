<?php $extend('klant', ['title' => 'Referrals']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('users') ?></div><div class="ph-text"><h1>Referrals</h1><p class="muted">Nodig leden uit en spaar automatisch tegoed.</p></div></div>

<section class="ref-hero">
    <div class="ref-hero-copy">
        <span class="eyebrow"><?= icon('handshake') ?> Samen voordeel behalen</span>
        <h2>Verdien tegoed met jouw netwerk</h2>
        <p>Deel jouw link. Zodra een nieuw lid €500 heeft besteed, ontvang je 10% tegoed op de volgende betalingen.</p>
    </div>
    <div class="ref-hero-stats">
        <div><strong><?= (int) $summary['count'] ?></strong><small>Aangebracht</small></div>
        <div><strong><?= (int) $summary['qualified'] ?></strong><small>Gekwalificeerd</small></div>
        <div><strong><?= e(money((int) $summary['rewards'])) ?></strong><small>Verdiend</small></div>
    </div>
</section>

<div class="ref-grid">
    <div class="card ref-invite-card">
        <div class="card-head"><h2><?= icon('link') ?> Jouw uitnodigingslink</h2><span class="ref-status">Actief</span></div>
        <div class="card-body stack">
            <div class="ref-link-box">
                <input id="referral-link" readonly value="<?= e($link) ?>" aria-label="Jouw uitnodigingslink">
                <button type="button" class="btn btn-primary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value).then(() => { this.dataset.copied = 'true'; this.innerHTML = 'Gekopieerd'; setTimeout(() => { this.dataset.copied = 'false'; this.innerHTML = 'Kopiëren'; }, 1800); })"><?= icon('copy') ?> Kopiëren</button>
            </div>
            <div class="ref-code-row"><span class="muted">Jouw code</span><strong><?= e($code) ?></strong></div>
        </div>
    </div>
    <div class="card ref-wallet-card">
        <div class="card-head"><h2><?= icon('wallet') ?> Referraltegoed</h2></div>
        <div class="card-body">
            <div class="ref-wallet-value"><?= e(money((int) $summary['rewards'])) ?></div>
            <p class="muted">Dit tegoed kun je direct gebruiken voor diensten en facturen.</p>
            <a class="btn btn-ghost btn-sm" href="<?= e(url('/klant/tegoed')) ?>">Bekijk tegoed <?= icon('arrow-right') ?></a>
        </div>
    </div>
</div>

<div class="card ref-members-card"><div class="card-head"><h2><?= icon('users') ?> Aangebrachte leden</h2><span class="muted"><?= (int) $summary['count'] ?> totaal</span></div><div class="card-body">
<?php if ($rows === []): ?>
    <div class="ref-empty"><span class="ref-empty-icon"><?= icon('users') ?></span><div><strong>Nog geen leden aangebracht</strong><p class="muted">Deel je uitnodigingslink om jouw eerste referral aan te brengen.</p></div><button type="button" class="btn btn-primary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">Link kopiëren</button></div>
<?php endif; ?>
<?php foreach ($rows as $row): ?><div class="dline ref-member-row"><span class="pk-i"><?= icon('user') ?></span><div class="dline-t"><strong><?= e($row['Naam']) ?></strong><small><?= $row['GekwalificeerdOp'] ? 'Gekwalificeerd' : 'Nog ' . e(money(max(0, (int) $row['Drempel'] - (int) $row['Besteed']))) . ' tot de grens' ?></small></div><span class="ref-member-state <?= $row['GekwalificeerdOp'] ? 'is-qualified' : '' ?>"><?= $row['GekwalificeerdOp'] ? 'Gekwalificeerd' : 'In behandeling' ?></span><strong class="dline-p"><?= e(money((int) $row['Beloond'])) ?></strong></div><?php endforeach; ?>
</div></div>
