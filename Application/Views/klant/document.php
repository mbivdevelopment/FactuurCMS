<?php $extend('klant', ['title' => $doc->label() . ' ' . $doc->Nummer]); ?>
<div class="page-head"><div class="ph-icon"><?= icon($doc->isQuote() ? 'quote' : 'file') ?></div><div class="ph-text"><h1><?= e($doc->label() . ' ' . $doc->Nummer) ?></h1></div>
    <div class="row">
        <?php if ($doc->isQuote() && $doc->status() === 'verzonden'): ?>
            <form method="post" action="<?= e(url('/klant/offertes/' . $doc->id() . '/afwijzen')) ?>" data-confirm="Weet je zeker dat je deze offerte wilt afwijzen?"><?= csrf_field() ?><button class="btn btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Afwijzen</button></form>
            <form method="post" action="<?= e(url('/klant/offertes/' . $doc->id() . '/akkoord')) ?>" data-confirm="Offerte accepteren?"><?= csrf_field() ?><button class="btn"><?= icon('check', 'icon icon-sm') ?> Akkoord</button></form>
        <?php endif; ?>
        <?php foreach ($methods ?? [] as $method => $text): ?>
            <form method="post" action="<?= e(url('/klant/documenten/' . $doc->id() . '/betalen')) ?>"><?= csrf_field() ?><input type="hidden" name="methode" value="<?= e($method) ?>"><button class="btn"><?= icon($method === 'mollie' ? 'wallet' : 'zap', 'icon icon-sm') ?> <?= e($text) ?></button></form>
        <?php endforeach; ?>
        <a class="btn btn-ghost" href="<?= e(url('/klant/documenten/' . $doc->id() . '/pdf')) ?>" target="_blank"><?= icon('download', 'icon icon-sm') ?> Download PDF</a>
    </div></div>
<?= $partial('shared.document', ['doc' => $doc]) ?>
