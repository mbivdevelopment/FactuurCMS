<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use Closure;

final class VerifyCsrf extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $token = $request->string('_token') ?: ($request->header('x-csrf-token') ?? '');
        if ($token === '' || !hash_equals(Session::token(), $token)) {
            throw new HttpException(419);
        }

        return $next($request);
    }
}
