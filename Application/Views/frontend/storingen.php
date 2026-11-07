<?php $extend('frontend', ['title' => 'Netwerkstatus']); $sev = ['onderhoud' => 'Onderhoud', 'klein' => 'Kleine verstoring', 'groot' => 'Grote verstoring', 'uitval' => 'Uitval']; $stat = ['onderzocht' => 'Wordt onderzocht', 'bevestigd' => 'Bevestigd', 'monitoring' => 'Wordt gemonitord', 'opgelost' => 'Opgelost', 'gepland' => 'Gepland']; ?>
<?= $partial('frontend._hero', ['title' => 'Netwerkstatus', 'intro' => 'Live status van onze platformen en een overzicht van eerdere storingen.', 'icon' => 'zap', 'crumbs' => [[null, 'Netwerkstatus']]]) ?>
<section class="sec"><div class="container">
    <div class="up-card" style="margin-bottom:26px">
        <div class="up-head"><div><h2 style="margin:0 0 4px"><?= $open === [] ? 'Alle systemen operationeel' : 'Actieve incidenten' ?></h2><p><?= e($uptime['pct']) ?>% uptime in de laatste 90 dagen.</p></div></div>
        <div class="up-bar"><?php foreach ($uptime['dagen'] as [$dag, $st]): ?><span class="up-d up-<?= $st ?>" title="<?= e(date('d-m-Y', strtotime($dag))) ?>"></span><?php endforeach; ?></div>
        <div class="up-foot"><span>90 dagen geleden</span><span class="up-leg"><i class="up-d up-ok"></i> Operationeel <i class="up-d up-warn"></i> Beperkt <i class="up-d up-down"></i> Storing <i class="up-d up-plan"></i> Onderhoud</span><span>Vandaag</span></div>
    </div>
    <?php if ($open === [] && $done === []): ?>
        <div class="card"><div class="card-body" style="text-align:center;padding:50px 20px"><p class="muted">Er zijn geen storingen bekend. Ligt er toch iets? Meld het via de <a href="<?= e(url('/contact')) ?>">klantenservice</a> of open een ticket in het klantportaal.</p></div></div>
    <?php else: ?>
        <div class="st-list">
            <?php foreach (array_merge($open, $done) as $i): ?>
            <div class="st-item">
                <div class="st-side"><span class="st-dot st-<?= $i['Ernst'] ?>"></span></div>
                <div class="st-main">
                    <div class="st-top"><strong><?= e($i['Titel']) ?></strong><span class="st-badges"><span class="st-badge st-b-<?= $i['Ernst'] ?>"><?= e($sev[$i['Ernst']] ?? $i['Ernst']) ?></span><span class="st-badge st-s-<?= $i['Status'] === 'st-s-opgelost' ? 'ok' : 'act' ?>"><?= e($stat[$i['Status']] ?? $i['Status']) ?></span></span></div>
                    <div class="st-meta muted"><?= e(nl_date($i['StartTijd'], true)) ?><?= $i['EindTijd'] ? ' – ' . e(nl_date($i['EindTijd'], true)) : '' ?><?= $i['Dienst'] ? ' · ' . e($i['Dienst']) : '' ?></div>
                    <?php if ($i['Omschrijving']): ?><p class="st-text"><?= e($i['Omschrijving']) ?></p><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p class="muted" style="margin-top:18px">Problemen met je eigen dienst? Open een <a href="<?= e(url('/klant/tickets/nieuw')) ?>">ticket</a> — storingen pakken we 24/7 op.</p>
</div></section>
<section class="sec sec-alt"><div class="container"><?= $partial('frontend._trust') ?></div></section>
