<?php $extend('beheer', ['title' => 'Updates']); ?>
<div class="page-head"><div class="ph-icon"><?= icon('refresh') ?></div><div class="ph-text"><h1>Updates</h1><div class="sub">Versie <?= e(\App\Services\Updater::localVersion()) ?><?php if ($c = \App\Services\Updater::installedCommit()): ?> · commit <span class="mono"><?= e(substr($c, 0, 7)) ?></span> · <?= e(nl_date((string) setting('updater.bijgewerkt'), true)) ?><?php endif; ?></div></div>
    <a class="btn" href="<?= e(url('/beheer/updates?controleren=1')) ?>"><?= icon('search', 'icon icon-sm') ?> Controleren op updates</a></div>
<div class="grid grid-side">
    <div class="stack">
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($latest): $current = $latest['sha'] === \App\Services\Updater::installedCommit(); ?>
        <div class="card"><div class="card-head"><h2><?= icon('github') ?> Laatste versie op GitHub</h2><?= badge($current ? 'actief' : 'open', $current ? 'Up-to-date' : 'Update beschikbaar') ?></div>
            <div class="card-body"><dl class="details"><dt>Versie</dt><dd><?= e($latest['version'] ?: '-') ?></dd><dt>Commit</dt><dd class="mono"><?= e(substr($latest['sha'], 0, 7)) ?></dd><dt>Bericht</dt><dd><?= e($latest['message']) ?></dd><dt>Datum</dt><dd><?= e(nl_date($latest['date'] ? date('Y-m-d H:i:s', strtotime($latest['date'])) : null, true)) ?></dd></dl></div>
            <form class="card-foot" method="post" action="<?= e(url('/beheer/updates/installeren')) ?>" data-confirm="Update installeren? Maak bij voorkeur eerst een back-up."><?= csrf_field() ?><button class="btn"><?= icon('download', 'icon icon-sm') ?> <?= $current ? 'Opnieuw installeren' : 'Update installeren' ?></button></form></div>
        <?php endif; ?>
        <div class="card"><div class="card-head"><h2><?= icon('shield') ?> Wat wordt nooit overschreven</h2></div><div class="card-body small">
            <ul style="margin:0;padding-left:18px"><li><span class="mono">Application/Config/database.php</span> (databasegegevens)</li><li><span class="mono">Application/Config/app.php</span></li><li><span class="mono">.htaccess</span></li><li><span class="mono">storage/</span> (logs, cache, sessies)</li><li>Geüploade branding (logo/favicon)</li></ul>
            <p class="muted">Na het bijwerken worden <span class="mono">schema.sql</span> en nieuwe bestanden uit <span class="mono">Application/Database/Migrations</span> automatisch uitgevoerd. Instellingen (SMTP, bedrijfsgegevens) staan in de database en blijven behouden.</p></div>
            <form class="card-foot" method="post" action="<?= e(url('/beheer/updates/migraties')) ?>"><?= csrf_field() ?><button class="btn btn-ghost btn-sm"><?= icon('database', 'icon icon-sm') ?> Alleen migraties uitvoeren</button></form></div>
    </div>
    <form class="card" method="post" action="<?= e(url('/beheer/updates')) ?>"><?= csrf_field() ?>
        <div class="card-head"><h2><?= icon('github') ?> GitHub</h2></div>
        <div class="card-body stack">
            <?= field('repo', 'Repository (eigenaar/naam)', 'github', \App\Services\Updater::repo()) ?>
            <?= field('branch', 'Branch', 'link', \App\Services\Updater::branch()) ?>
            <?= field('token', 'Token' . ($hasToken ? ' (ingesteld, leeg = ongewijzigd)' : ' (nodig voor private repo)'), 'lock', '', 'password', [], 'autocomplete="new-password" placeholder="github_pat_…"') ?>
            <?php if ($hasToken): ?><label class="switch"><input type="checkbox" name="token_wissen" value="1"> Token verwijderen</label><?php endif; ?>
        </div>
        <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
    </form>
</div>
