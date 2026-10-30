<?php
// VPS-actieknoppen voor de titelkaart. Verwacht: $service, $base.
$opgezegd = (string) $service->Status === 'opgezegd';
$vact = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/vps/' . $a));
?>
<button type="button" class="btn btn-sm btn-ghost" data-open="vps-handover-<?= (int) $service->id() ?>"><?= icon('handshake', 'icon icon-sm') ?> Handover</button>
<?php if (!$opgezegd): ?>
    <form method="post" action="<?= $vact('opzeggen') ?>" style="display:inline" data-confirm="Dienst opzeggen? Hij loopt af op de volgende factuurdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
<?php else: ?>
    <form method="post" action="<?= $vact('hervatten') ?>" style="display:inline" data-confirm="Opzegging terugdraaien en de dienst weer activeren?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
<?php endif; ?>
