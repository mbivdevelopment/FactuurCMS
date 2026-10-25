<?php /** @var list<\App\Models\Product> $domains */ $max = $max ?? 18; ?>
<div class="ext-grid"><?php foreach (array_slice($domains, 0, $max) as $d): ?>
    <a class="ext" href="<?= e(url($d->path())) ?>"><strong><?= e($d->extensionLabel()) ?></strong><span><b><?= e(money((int) $d->Prijs)) ?></b> / jaar</span><small>Registreren <?= icon('arrow-right', 'icon icon-sm') ?></small></a>
<?php endforeach; ?></div>
<?php if (count($domains) > $max): ?><p class="ext-more"><a class="btn btn-ghost" href="<?= e(url('/diensten#domeinextensies')) ?>">Alle <?= count($domains) ?> extensies bekijken <?= icon('arrow-right', 'icon icon-sm') ?></a></p><?php endif; ?>
