<?php $extend('auth', ['title' => 'Fout ' . $status]); ?>
<h1><?= e((string) $status) ?></h1>
<p class="muted" style="white-space:pre-wrap"><?= e($message !== '' ? $message : ($status === 404 ? 'Deze pagina bestaat niet.' : 'Er ging iets mis.')) ?></p>
<a class="btn" href="<?= e(url('/')) ?>"><?= icon('home', 'icon icon-sm') ?> Naar de homepage</a>
