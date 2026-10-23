<?php $fav = branding_url('favicon'); ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(\App\Core\Session::token()) ?>">
<title><?= e(isset($title) ? $title . ' · ' . company_name() : company_name()) ?></title>
<?php if ($fav !== ''): ?><link rel="icon" href="<?= e($fav) ?>"><?php endif; ?>
<?php $kleur = preg_match('/^#[0-9a-f]{6}$/i', (string) setting('branding.kleur', '')) ? (string) setting('branding.kleur') : '#ff5a1f'; ?>
<link rel="stylesheet" href="<?= e(asset('css/' . (($sheet ?? 'app') === 'beheer' ? 'beheer' : 'app') . '.css')) ?>">
<style>:root { --primary: <?= e($kleur) ?>; --accent: <?= e($kleur) ?>; }</style>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
