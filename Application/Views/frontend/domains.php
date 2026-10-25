<?php $extend('frontend', ['title' => 'Domeinnaam zoeken']); ?>
<?= $partial('frontend._hero', ['title' => $name !== '' ? 'Resultaten voor "' . $name . '"' : 'Zoek je domeinnaam', 'intro' => 'Controleer direct of jouw domeinnaam nog vrij is.', 'icon' => 'globe', 'crumbs' => [['/oplossingen/domeinnaam-registreren', 'Domeinen'], [null, 'Zoeken']]]) ?>
<div class="container phero-search"><?= $partial('frontend._domain-search', ['extensions' => $extensions, 'value' => $name]) ?></div>
<section class="sec"><div class="container">
    <?php if ($results === []): ?><div class="empty">Vul hierboven een domeinnaam in.</div><?php else: ?>
    <div class="dres">
        <?php foreach ($results as $r): $p = $r['product']; ?>
            <div class="dres-row is-<?= e($r['status']) ?>">
                <span class="dres-i"><?= icon($r['status'] === 'vrij' ? 'check' : ($r['status'] === 'bezet' ? 'x' : 'globe')) ?></span>
                <div class="dres-name"><strong><?= e($r['domain']) ?></strong><small><?= $r['status'] === 'vrij' ? 'Is nog vrij!' : ($r['status'] === 'bezet' ? 'Is al geregistreerd' : 'Kon niet worden gecontroleerd') ?></small></div>
                <div class="dres-price"><?= $p ? e(money((int) $p['Prijs'])) . '<small> /jaar</small>' : '' ?></div>
                <div class="dres-act">
                    <?php if ($r['status'] === 'vrij' && $p): ?><a class="btn" href="<?= e(url(\App\Models\Product::pathFor((string) $p['Slug'], $p['Dienstsoort'] ?? null, $p['Groep'] ?? 'Domeinen') . '?domein=' . urlencode($r['domain']))) ?>"><?= icon('cart', 'icon icon-sm') ?> Bestellen</a>
                    <?php elseif ($r['status'] === 'bezet'): ?><a class="btn btn-ghost" href="<?= e(url('/oplossingen/domein-verhuizen')) ?>"><?= icon('refresh', 'icon icon-sm') ?> Verhuizen?</a>
                    <?php else: ?><a class="btn btn-ghost" href="<?= e(url('/contact')) ?>">Vraag het ons</a><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div><?php endif; ?>
</div></section>
