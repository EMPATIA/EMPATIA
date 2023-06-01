<?php

namespace App\Http\Middleware\Empatia;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vizir\KeycloakWebGuard\Middleware\KeycloakAuthenticated;


class LoginLevelsMiddleware extends KeycloakAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$loginLevel)
    {
        $user = User::find(Auth::user()?->id);

        $loginLevel = $loginLevel[0] ?? '';

        $loginLevels = is_array($loginLevel)
            ? $loginLevel
            : explode('|', $loginLevel);

        if ($user->hasAnyLL($loginLevels)) {
            return $next($request);
        }

        return redirect()->route('profile.show');
    }
}
