<?php

declare(strict_types=1);

use App\Core\App;

// Statische assets/uploads: ingebouwde PHP-server, of fallback wanneer de webserver de rewrite niet doet (subdirectory, geen AllowOverride)
$script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$base = str_ends_with($script, '/index.php') ? rtrim(dirname($script), '/') : '';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($base !== '' && str_starts_with($path, $base . '/')) {
    $path = substr($path, strlen($base));
}
foreach (['/assets/' => '/Application/Theme/assets/', '/uploads/' => '/Application/Theme/assets/branding/'] as $prefix => $dir) {
    if (str_starts_with($path, $prefix) && !str_contains($path, '..')) {
        $file = __DIR__ . $dir . substr($path, strlen($prefix));
        if (is_file($file)) {
            $types = ['css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'gif' => 'image/gif', 'ico' => 'image/x-icon', 'woff2' => 'font/woff2', 'woff' => 'font/woff'];
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            header('Content-Type: ' . ($types[$ext] ?? (mime_content_type($file) ?: 'application/octet-stream')));
            header('Cache-Control: public, max-age=604800');
            header('X-Content-Type-Options: nosniff');
            readfile($file);
            return PHP_SAPI === 'cli-server' ? true : null;
        }
    }
}
unset($script, $base, $path, $prefix, $dir);

require __DIR__ . '/Application/bootstrap.php';

$app = new App(__DIR__);
$app->run();
