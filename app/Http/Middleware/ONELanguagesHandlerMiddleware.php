<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\HBackend;
use Redirect;
use Str;

class ONELanguagesHandlerMiddleware {
    public static $LANG_EXCEPTIONS = [
        'login',
        'logout',
        'register',
        'callback',
        'download',
    ];

    public function handle(Request $request, Closure $next) {
        // \Log::debug("[LanguagesHandlerMiddleware] init: ".$request->url());

        // Ignores all non GET requests:
        if ($request->method() !== 'GET') {
            // \Log::debug("[LanguagesHandlerMiddleware] Ignoring: not GET");
            return $next($request);
        }

        // Handle exceptions
        if(Str::startsWith($request->path(), self::$LANG_EXCEPTIONS)) return $next($request);

        [$type, $lang, $path] = HBackend::languages_processURL();

        // URL contains valid LANG
        if($type == "URL") {
            HBackend::languages_registerLang($lang);
            // \Log::debug("[LanguagesHandlerMiddleware] LANG OK: ".$lang." -- ".getLang());
            return $next($request);
        }

        // URL does not contain valid LAND
        // \Log::debug("[LanguagesHandlerMiddleware] REDIRECT: ".$path);
        return Redirect::to($path);
    }
}
