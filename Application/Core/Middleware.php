<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

abstract class Middleware
{
    /** @var array<string, string> */
    private static array $aliases = [
        'csrf' => \App\Middleware\VerifyCsrf::class,
        'guest' => \App\Middleware\Guest::class,
        'auth' => \App\Middleware\Authenticate::class,
        'klant' => \App\Middleware\CustomerOnly::class,
        'staff' => \App\Middleware\StaffOnly::class,
        'admin' => \App\Middleware\AdminOnly::class,
        'throttle' => \App\Middleware\Throttle::class,
    ];

    /** @var string[] */
    protected array $args = [];

    abstract public function handle(Request $request, Closure $next): Response;

    public static function resolve(string $definition): self
    {
        [$alias, $args] = array_pad(explode(':', $definition, 2), 2, '');
        $class = self::$aliases[$alias] ?? $alias;
        if (!is_subclass_of($class, self::class)) {
            throw new \RuntimeException("Onbekende middleware '$definition'.");
        }
        $instance = new $class();
        $instance->args = $args === '' ? [] : explode(',', $args);

        return $instance;
    }
}
