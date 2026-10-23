<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use Closure;

final class Authenticate extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                throw new HttpException(401);
            }
            Session::set('intended', $request->path);

            return Response::redirect('/login')->with('warning', 'Log eerst in om verder te gaan.');
        }

        return $next($request);
    }
}
