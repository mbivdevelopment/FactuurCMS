<?php /** Popup met alle extensies en prijzen. @var list<\App\Models\Product> $domains */ ?>
<dialog id="<?= e($dialog_id ?? 'ext-dlg') ?>" class="pk-dlg ext-dlg">
    <div class="modal-h"><div><strong>Alle extensies</strong><span class="muted"><?= count($domains) ?> extensies · prijzen per jaar, excl. btw</span></div><button type="button" class="pk-dlg-x" data-close aria-label="Sluiten"><?= icon('x', 'icon icon-sm') ?></button></div>
    <label class="exd-search"><?= icon('search', 'icon icon-sm') ?><input type="search" placeholder="Zoek een extensie, bijv. .shop" aria-label="Zoek extensie" data-ext-filter></label>
    <div class="exd-list">
        <?php foreach ($domains as $d): ?>
            <div class="exd-row" data-ext="<?= e(strtolower($d->extensionLabel())) ?>"><strong><?= e($d->extensionLabel()) ?></strong><?php if ($d->Omschrijving !== null && $d->Omschrijving !== ''): ?><span class="exd-desc"><?= e($d->Omschrijving) ?></span><?php endif; ?><b><?= e(money((int) $d->Prijs)) ?></b></div>
        <?php endforeach; ?>
        <p class="exd-empty muted" hidden>Geen extensie gevonden. Probeer een andere zoekterm.</p>
    </div>
</dialog>
