<?php /** Uniforme call-to-action onderaan pagina's. */ $title = $title ?? 'Hulp nodig bij het kiezen?'; $text = $text ?? 'Onze specialisten adviseren je graag, vrijblijvend en in begrijpelijke taal.'; $tel = (string) setting('bedrijf.telefoon', ''); ?>
<section class="sec sec-cta"><div class="container"><div class="cta cta-rich">
    <div class="cta-l"><span class="cta-k"><?= icon('headset', 'icon icon-sm') ?> Persoonlijk advies</span><h2><?= e($title) ?></h2><p><?= e($text) ?></p></div>
    <div class="cta-r"><a class="btn btn-lg" href="<?= e(url($href ?? '/contact')) ?>"><?= e($label ?? 'Neem contact op') ?> <?= icon('arrow-right', 'icon icon-sm') ?></a><?php if ($tel !== ''): ?><a class="btn btn-lg btn-ghost" href="tel:<?= e(preg_replace('/[^\d+]/', '', $tel)) ?>"><?= icon('phone', 'icon icon-sm') ?> <?= e($tel) ?></a><?php endif; ?></div>
</div></div></section>
