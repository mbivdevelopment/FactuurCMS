<?php

declare(strict_types=1);

/** Dagelijkse cron: php /pad/naar/cron.php (maakt herhaalfacturen aan en verstuurt ze). */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Alleen via de commandline.');
}

require __DIR__ . '/Application/bootstrap.php';

new \App\Core\App(__DIR__);

$count = \App\Services\Billing::runRecurring();
echo date('Y-m-d H:i') . " - $count herhaalfactuur/-facturen aangemaakt\n";
