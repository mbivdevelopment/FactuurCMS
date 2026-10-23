<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

final class StaffOnly extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user === null || !$user->isStaff()) {
            throw new HttpException(403);
        }

        return $next($request);
    }
}
