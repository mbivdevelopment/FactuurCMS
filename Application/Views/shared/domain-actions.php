<?php
// Domeinbeheer-actieknoppen voor de titelkaart. Verwacht: $service, $base.
$opgezegd = (string) $service->Status === 'opgezegd';
$act = static fn (string $a): string => e(url($base . '/diensten/' . $service->id() . '/domein/' . $a));
?>
<button type="button" class="btn btn-sm btn-ghost" data-open="dom-ns-<?= (int) $service->id() ?>"><?= icon('server', 'icon icon-sm') ?> Nameservers</button>
<button type="button" class="btn btn-sm btn-ghost" data-open="dom-epp-<?= (int) $service->id() ?>"><?= icon('lock', 'icon icon-sm') ?> EPP-code</button>
<button type="button" class="btn btn-sm btn-ghost" data-open="dom-handover-<?= (int) $service->id() ?>"><?= icon('handshake', 'icon icon-sm') ?> Handover</button>
<?php if (!$opgezegd): ?>
    <form method="post" action="<?= $act('opzeggen') ?>" style="display:inline" data-confirm="Domein opzeggen? Het domein loopt af op de verloopdatum."><?= csrf_field() ?><button class="btn btn-sm btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Opzeggen</button></form>
<?php else: ?>
    <form method="post" action="<?= $act('hervatten') ?>" style="display:inline" data-confirm="Opzegging terugdraaien en automatisch verlengen weer aanzetten?"><?= csrf_field() ?><button class="btn btn-sm btn-ghost"><?= icon('check', 'icon icon-sm') ?> Opzegging terugdraaien</button></form>
<?php endif; ?>
