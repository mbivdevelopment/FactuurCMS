<?php $extend('klant', ['title' => $doc->label() . ' ' . $doc->Nummer]); ?>
<div class="page-head"><h1><?= icon($doc->isQuote() ? 'quote' : 'file') ?> <?= e($doc->label() . ' ' . $doc->Nummer) ?></h1>
    <div class="row">
        <?php if ($doc->isQuote() && $doc->status() === 'verzonden'): ?>
            <form method="post" action="<?= e(url('/klant/offertes/' . $doc->id() . '/afwijzen')) ?>" data-confirm="Weet je zeker dat je deze offerte wilt afwijzen?"><?= csrf_field() ?><button class="btn btn-danger-ghost"><?= icon('x', 'icon icon-sm') ?> Afwijzen</button></form>
            <form method="post" action="<?= e(url('/klant/offertes/' . $doc->id() . '/akkoord')) ?>" data-confirm="Offerte accepteren?"><?= csrf_field() ?><button class="btn"><?= icon('check', 'icon icon-sm') ?> Akkoord</button></form>
        <?php endif; ?>
        <a class="btn btn-ghost" href="<?= e(url('/klant/documenten/' . $doc->id() . '/pdf')) ?>" target="_blank"><?= icon('download', 'icon icon-sm') ?> Download PDF</a>
    </div></div>
<?= $partial('shared.document', ['doc' => $doc]) ?>
