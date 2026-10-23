<div class="thread">
<?php foreach ($messages as $m): ?>
    <?php $staff = in_array($m['Rol'], ['beheerder', 'medewerker'], true); ?>
    <div class="msg<?= $m['Intern'] ? ' is-internal' : ($staff ? ' is-staff' : '') ?>">
        <div class="msg-head"><strong><?= e(trim($m['Voornaam'] . ' ' . $m['Achternaam'])) ?><?= $staff ? ' · ' . e(company_name()) : '' ?><?= $m['Intern'] ? ' · interne notitie' : '' ?></strong><span><?= e(nl_date($m['AangemaaktOp'], true)) ?></span></div>
        <div class="msg-body"><?= e($m['Bericht']) ?></div>
    </div>
<?php endforeach; ?>
</div>
