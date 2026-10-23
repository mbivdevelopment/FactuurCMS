<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

final class Guest extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user !== null) {
            return Response::redirect(Auth::homeFor($user));
        }

        return $next($request);
    }
}
