<?php $logo = branding_url(($dark ?? false) && branding_url('logo_licht') !== '' ? 'logo_licht' : 'logo'); ?>
<?php if ($logo !== ''): ?><img src="<?= e($logo) ?>" alt="<?= e(company_name()) ?>"><?php else: ?><span class="brand-mark"><?= icon('server', 'icon icon-sm') ?></span> <span><?= e(company_name()) ?></span><?php endif; ?>
