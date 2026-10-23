<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use Closure;

/** Alleen gebruikers met rol beheerder (instellingen, medewerkers, updates). */
final class AdminOnly extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!(Auth::user()?->isAdmin() ?? false)) {
            throw new HttpException(403, 'Alleen een beheerder heeft toegang tot dit onderdeel.');
        }

        return $next($request);
    }
}
