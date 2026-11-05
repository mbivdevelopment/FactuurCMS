<?php $extend('beheer', ['title' => 'Mailer: ' . $tpl['naam']]); ?>
<div class="page-head"><div class="ph-icon"><?= icon('mail') ?></div><div class="ph-text"><h1><?= e($tpl['naam']) ?></h1><div class="sub">Mailtemplate bewerken · variabelen met {accolades}</div></div>
    <a class="btn btn-ghost" href="<?= e(url('/beheer/instellingen#mailtemplates')) ?>"><?= icon('arrow-right', 'icon icon-sm') ?> Terug naar mailers</a></div>
<form method="post" action="<?= e(url('/beheer/instellingen/mailtemplates/' . $key)) ?>"><?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <p class="muted" style="margin:0 0 16px">Beschikbare variabelen: <?php foreach ($tpl['vars'] as $var): ?><code>{<?= e($var) ?>}</code> <?php endforeach; ?><br>Een leeg veld valt terug op de standaardtekst.</p>
            <div class="form-grid">
                <?= field('onderwerp', 'Onderwerp', 'mail', setting('mailtpl.' . $key . '.onderwerp', $tpl['onderwerp'])) ?>
                <?= field('tekst', 'Berichttekst', 'note', setting('mailtpl.' . $key . '.tekst', $tpl['tekst']), 'textarea', [], 'rows="10"') ?>
            </div>
            <details class="muted" style="margin-top:14px"><summary>Standaardtekst</summary>
                <p><strong><?= e($tpl['onderwerp']) ?></strong></p>
                <pre class="small" style="white-space:pre-wrap"><?= e($tpl['tekst']) ?></pre></details>
        </div>
        <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> Opslaan</button></div>
    </div>
</form>
