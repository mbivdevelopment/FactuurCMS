<?php if ($toast ?? false): ?>
<?php $ok = flash('success'); $bad = flash('error'); ?>
<?php if ($ok || $bad): ?><div class="toasts" data-toasts><?php if ($ok): ?><div class="toast"><?= icon('check', 'icon icon-sm') ?> <?= e($ok) ?></div><?php endif; ?><?php if ($bad): ?><div class="toast error"><?= icon('x', 'icon icon-sm') ?> <?= e($bad) ?></div><?php endif; ?></div><?php endif; ?>
<?php if (errors() !== []): ?><div class="alert alert-bad"><?= icon('x') ?> Controleer de gemarkeerde velden.</div><?php endif; ?>
<?php else: ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-ok"><?= icon('check') ?> <?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-bad"><?= icon('x') ?> <?= e($msg) ?></div><?php endif; ?>
<?php if (errors() !== [] && !flash('error')): ?><div class="alert alert-bad"><?= icon('x') ?> Controleer de gemarkeerde velden.</div><?php endif; ?>
<?php endif; ?>
