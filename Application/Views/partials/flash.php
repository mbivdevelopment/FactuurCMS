<?php if ($msg = flash('success')): ?><div class="alert alert-ok"><?= icon('check') ?> <?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-bad"><?= icon('x') ?> <?= e($msg) ?></div><?php endif; ?>
<?php if (errors() !== [] && !flash('error')): ?><div class="alert alert-bad"><?= icon('x') ?> Controleer de gemarkeerde velden.</div><?php endif; ?>
