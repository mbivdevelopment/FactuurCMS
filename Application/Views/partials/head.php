<?php $fav = branding_url('favicon'); ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(\App\Core\Session::token()) ?>">
<title><?= e(isset($title) ? $title . ' · ' . company_name() : company_name()) ?></title>
<?php if ($fav !== ''): ?><link rel="icon" href="<?= e($fav) ?>"><?php endif; ?>
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<style>:root { --primary: <?= e(preg_match('/^#[0-9a-f]{6}$/i', (string) setting('branding.kleur', '')) ? setting('branding.kleur') : '#4f46e5') ?>; }</style>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
