<?php

declare(strict_types=1);

// Autoloading: Composer indien aanwezig, anders de eigen PSR-4-loader (er zijn geen runtime-dependencies)
$root = dirname(__DIR__);
if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        if (str_starts_with($class, 'App\\')) {
            $file = $root . '/Application/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
    require $root . '/Application/Core/helpers.php';
}
unset($root);
