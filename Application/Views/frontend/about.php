<?php $extend('frontend', ['title' => 'Over ons']); $kind = ['mijlpaal' => ['flag', 'Mijlpaal'], 'lancering' => ['zap', 'Lancering'], 'overname' => ['handshake', 'Overname']]; ?>
<?= $partial('frontend._hero', ['title' => 'Over ' . company_name(), 'intro' => (string) setting('topbar.slogan', ''), 'icon' => 'building', 'crumbs' => [[null, 'Over ons']]]) ?>
<section class="sec"><div class="container about-cols">
    <div class="ab-card"><span class="ab-i"><?= icon('users') ?></span><h2>Wie wij zijn</h2><div class="prose"><?= rich_text((string) setting('over.wie', '')) ?></div></div>
    <div class="ab-card"><span class="ab-i"><?= icon('heart') ?></span><h2>Hoe wij zijn</h2><div class="prose"><?= rich_text((string) setting('over.hoe', '')) ?></div>
        <div class="values"><span><?= icon('check', 'icon icon-sm') ?> Persoonlijk</span><span><?= icon('check', 'icon icon-sm') ?> Eerlijk</span><span><?= icon('check', 'icon icon-sm') ?> Bereikbaar</span><span><?= icon('check', 'icon icon-sm') ?> No-nonsense</span></div></div>
</div></section>
<?php if ((string) setting('over.missie', '') !== ''): ?>
<section class="mission"><div class="container"><span class="eyebrow"><?= icon('target', 'icon icon-sm') ?> Onze missie</span><blockquote><?= e((string) setting('over.missie')) ?></blockquote></div></section>
<?php endif; ?>
<?php if ($team !== []): ?>
<section class="sec"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('users', 'icon icon-sm') ?> Team</span><h2>Maak kennis met ons team</h2><p>De mensen die elke dag voor je klaarstaan.</p></div></div>
    <div class="team">
        <?php foreach ($team as $t): $img = media_url((string) $t['Afbeelding']); $ini = mb_strtoupper(implode('', array_map(static fn ($w) => mb_substr($w, 0, 1), array_slice(preg_split('/\s+/', trim($t['Naam'])) ?: [], 0, 2)))); ?>
            <div class="tm">
                <div class="tm-photo"><?php if ($img !== ''): ?><img src="<?= e($img) ?>" alt="<?= e($t['Naam']) ?>" loading="lazy"><?php else: ?><span><?= e($ini) ?></span><?php endif; ?></div>
                <strong><?= e($t['Naam']) ?></strong><span class="tm-role"><?= e((string) $t['Functie']) ?></span>
                <p><?= e((string) $t['Bio']) ?></p>
                <div class="tm-links"><?php if ($t['Email']): ?><a href="mailto:<?= e($t['Email']) ?>" aria-label="E-mail"><?= icon('mail', 'icon icon-sm') ?></a><?php endif; ?><?php if ($t['LinkedIn']): ?><a href="<?= e($t['LinkedIn']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><?= icon('linkedin', 'icon icon-sm') ?></a><?php endif; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div></section>
<?php endif; ?>
<?php if ($timeline !== []): ?>
<section class="sec sec-alt"><div class="container">
    <div class="sec-head center"><div><span class="eyebrow"><?= icon('clock', 'icon icon-sm') ?> Tijdlijn</span><h2>Ons verhaal</h2><p>Van zolderkamer tot ICT-partner: de belangrijkste momenten.</p></div></div>
    <div class="tl">
        <?php foreach ($timeline as $m): [$ki, $kl] = $kind[$m['Soort']] ?? $kind['mijlpaal']; $img = media_url((string) $m['Afbeelding']); ?>
            <div class="tl-item is-<?= e($m['Soort']) ?>">
                <span class="tl-dot"><?= icon($ki, 'icon icon-sm') ?></span>
                <div class="tl-card"><div class="tl-head"><span class="tl-year"><?= e($m['Jaar']) ?></span><span class="tl-kind"><?= e($kl) ?></span></div><h3><?= e($m['Titel']) ?></h3><?php if ($img !== ''): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php endif; ?><p><?= e((string) $m['Verhaal']) ?></p></div>
            </div>
        <?php endforeach; ?>
    </div>
</div></section>
<?php endif; ?>
<?php if ($datacenters !== []): ?><section class="sec"><div class="container"><div class="sec-head"><div><span class="eyebrow"><?= icon('pin', 'icon icon-sm') ?> Netwerk</span><h2>Onze datacenters</h2></div></div><?= $partial('frontend._map', ['datacenters' => $datacenters]) ?></div></section><?php endif; ?>
<section class="sec sec-cta"><div class="container"><div class="cta"><div><h2>Zullen we kennismaken?</h2><p>Bel, mail of kom langs; we maken graag tijd voor je.</p></div><a class="btn btn-lg" href="<?= e(url('/contact')) ?>">Contact opnemen <?= icon('arrow-right', 'icon icon-sm') ?></a></div></div></section>
