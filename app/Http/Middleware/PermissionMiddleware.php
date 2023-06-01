<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Vizir\KeycloakWebGuard\Middleware\KeycloakAuthenticated;

class PermissionMiddleware extends KeycloakAuthenticated
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (empty($guards) && Auth::check()) {
            return $next($request);
        }

        $guards = explode('|', ($guards[0] ?? ''));
        if (Auth::user()->can($guards)) {
            return $next($request);
        }

        throw new AuthorizationException('Forbidden', 403);
    }
}
