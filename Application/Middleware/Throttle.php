<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

/**
 * Gebruik: 'throttle:login,5,15' — max 5 pogingen per 15 minuten per IP, geteld via tabel Logboek (Type = throttle:<naam>).
 * Roep Throttle::clear('login') aan na een geslaagde poging.
 */
final class Throttle extends Middleware
{
    public static function clear(string $name, ?string $ip = null): void
    {
        Database::run('DELETE FROM Logboek WHERE Type = ? AND Ip = ?', ['throttle:' . $name, $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '')]);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method, ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $name = $this->args[0] ?? 'default';
        $max = (int) ($this->args[1] ?? 10);
        $minutes = (int) ($this->args[2] ?? 15);
        $type = 'throttle:' . $name;

        $count = (int) Database::scalar(
            'SELECT COUNT(*) FROM Logboek WHERE Type = ? AND Ip = ? AND AangemaaktOp > ?',
            [$type, $request->ip, date('Y-m-d H:i:s', time() - $minutes * 60)],
        );
        if ($count >= $max) {
            throw new HttpException(429, "Te veel pogingen. Probeer het over $minutes minuten opnieuw.");
        }

        Database::insert('Logboek', ['Type' => $type, 'Ip' => $request->ip, 'Bericht' => $request->path, 'AangemaaktOp' => now()]);

        return $next($request);
    }
}
