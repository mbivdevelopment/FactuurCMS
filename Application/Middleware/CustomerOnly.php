<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

final class CustomerOnly extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/login');
        }
        if ($user->isStaff() && Auth::impersonator() === null) {
            return Response::redirect('/beheer');
        }

        return $next($request);
    }
}
