<?php $extend('beheer', ['title' => $i ? 'Storing bewerken' : 'Nieuwe storing']); $sev = \App\Controllers\Beheer\OutageController::SEVERITIES; $stat = \App\Controllers\Beheer\OutageController::STATUSES; $diensten = ['' => 'Alle diensten (algemeen)'] + array_combine(\App\Controllers\Beheer\OutageController::SERVICES, \App\Controllers\Beheer\OutageController::SERVICES);
$start = $i ? date('Y-m-d\TH:i', strtotime((string) $i['StartTijd'])) : date('Y-m-d\TH:i'); $eind = $i && $i['EindTijd'] ? date('Y-m-d\TH:i', strtotime((string) $i['EindTijd'])) : ''; ?>
<div class="page-head"><div class="ph-icon"><?= icon('zap') ?></div><div class="ph-text"><h1><?= $i ? 'Storing bewerken' : 'Nieuwe storing' ?></h1></div><a class="btn btn-ghost" href="<?= e(url('/beheer/storingen')) ?>">Terug</a></div>
<form class="card" method="post" action="<?= e(url($i ? '/beheer/storingen/' . $i['Id'] : '/beheer/storingen')) ?>"><?= csrf_field() ?>
    <div class="card-body form-grid">
        <?= field('Titel', 'Titel', 'zap', $i['Titel'] ?? '', 'text', [], 'required') ?>
        <?= field('Dienst', 'Dienst', 'server', $i['Dienst'] ?? '', 'select', $diensten) ?>
        <?= field('Ernst', 'Ernst', 'flag', $i['Ernst'] ?? 'klein', 'select', $sev) ?>
        <?= field('Status', 'Status', 'check', $i['Status'] ?? 'onderzocht', 'select', $stat) ?>
        <?= field('StartTijd', 'Starttijd', 'clock', $start, 'datetime-local', [], 'required') ?>
        <?= field('EindTijd', 'Eindtijd', 'clock', $eind, 'datetime-local', [], '') ?>
        <?= field('Omschrijving', 'Omschrijving', 'note', $i['Omschrijving'] ?? '', 'textarea', [], 'rows="4"') ?>
    </div>
    <div class="card-foot"><button class="btn"><?= icon('check', 'icon icon-sm') ?> <?= $i ? 'Opslaan' : 'Aanmaken' ?></button></div>
</form>
