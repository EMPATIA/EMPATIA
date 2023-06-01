<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\LogsOne;

class ONELogsHandlerMiddleware {
    public function handle(Request $request, Closure $next)
    {
        // Log all requests
        auditAccess();
        return $next($request);
    }

    public function terminate($request, $response)
    {
        auditPerformance(LARAVEL_START, microtime(true));
    }
}
