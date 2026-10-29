<?php
// Webhosting-actieknoppen voor de titelkaart. Verwacht: $service, $base.
$opgezegd = (string) $service->Status === 'opgezegd';
$hact = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/hosting/' . $a));
?>
<?php if ((string) $service->Bron === 'qevanto'): ?>
    <form method="post" action="<?= e(url($base . '/diensten/' . $service->id() . '/paneel')) ?>" style="display:inline"><?= csrf_field() ?><button class="btn btn-sm"><?= icon('login', 'icon icon-sm') ?> Beheer in paneel</button></form>
<?php endif; ?>
<button type="button" class="btn btn-sm btn-ghost" data-open="host-handover-<?= (int) $service->id() ?>"><?= icon('handshake', 'icon icon-sm') ?> Handover</button>
<?php if (!$opgezegd): ?>
    <form method="post" action="<?= $hact('opzeggen') ?>" style="display:inline" data-confirm="Dienst opzeggen? Hij loopt af op de volgende factuurdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
<?php else: ?>
    <form method="post" action="<?= $hact('hervatten') ?>" style="display:inline" data-confirm="Opzegging terugdraaien en de dienst weer activeren?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
<?php endif; ?>
