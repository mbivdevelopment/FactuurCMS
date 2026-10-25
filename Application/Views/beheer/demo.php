<?php $extend('beheer', ['title' => 'Demo-data opruimen']);
$customers = $demo['customers'];
$empty = $customers === [] && $demo['suppliers'] === [] && $demo['leads'] === [];
$person = static fn (array $r): string => trim((string) ($r['Bedrijfsnaam'] ?: trim($r['Voornaam'] . ' ' . $r['Achternaam']))); ?>
<div class="page-head"><div class="ph-icon"><?= icon('trash') ?></div><div class="ph-text"><h1>Demo-data opruimen</h1><div class="sub">Verwijdert de voorbeeldgegevens van de installatie; je echte klanten en facturen blijven staan</div></div></div>
<div class="grid grid-side">
    <div class="stack">
        <?php if ($empty): ?>
        <div class="card"><div class="card-body"><div class="alert alert-info">Er staat geen demo-data meer in het systeem. De volgende nieuwe klant krijgt nummer <strong class="mono"><?= e($next) ?></strong>.</div></div></div>
        <?php else: ?>
        <div class="card"><div class="card-head"><h2><?= icon('users') ?> Wordt verwijderd</h2></div>
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Wat</th><th>Gegevens</th></tr></thead>
                <tbody>
                <?php foreach ($customers as $c): ?><tr><td>Demoklant <span class="mono"><?= e($c['Nummer']) ?></span></td><td><strong><?= e($person($c)) ?></strong> · <?= e($c['Email']) ?></td></tr><?php endforeach; ?>
                <?php foreach ($demo['counts'] as $label => $n): ?><tr><td><?= e($label) ?></td><td><?= (int) $n ?> van de demoklanten</td></tr><?php endforeach; ?>
                <?php foreach ($demo['suppliers'] as $s): ?><tr><td>Crediteur</td><td><?= e($s['Bedrijfsnaam']) ?> · <?= e($s['Email']) ?></td></tr><?php endforeach; ?>
                <?php foreach ($demo['leads'] as $l): ?><tr><td>Lead</td><td><?= e($l['Naam']) ?><?= $l['Bedrijf'] ? ' · ' . e($l['Bedrijf']) : '' ?></td></tr><?php endforeach; ?>
                </tbody>
            </table></div>
            <form class="card-foot" method="post" action="<?= e(url('/beheer/demo-data')) ?>" data-confirm="Alle demo-data definitief verwijderen? Maak bij voorkeur eerst een back-up."><?= csrf_field() ?>
                <button class="btn btn-danger" type="submit"><?= icon('trash', 'icon icon-sm') ?> Demo-data verwijderen</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <div class="stack">
        <div class="card"><div class="card-head"><h2><?= icon('check') ?> Blijft staan</h2></div>
            <div class="card-body">
                <?php if ($others): ?><dl class="details"><?php foreach ($others as $o): ?><dt class="mono"><?= e($o['Nummer']) ?></dt><dd><?= e($person($o)) ?></dd><?php endforeach; ?></dl>
                <?php else: ?><p class="muted">Nog geen andere klanten.</p><?php endif; ?>
                <p class="muted" style="margin-top:12px">Ook medewerkers, producten, website-content, instellingen en de facturen van deze klanten blijven staan. Nieuwe klanten krijgen het laagste vrije klantnummer.</p>
            </div>
        </div>
    </div>
</div>
