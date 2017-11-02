<?php

namespace App\Http\Middleware;

use App\Jobs\SendLogsSQL;
use Closure;
use Session;
use DB;
use Request;

class LoggerSQL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if(env('LOGS_SQL', 'false') == 'true') {
            DB::enableQueryLog();
        }
        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response)
    {
        if(env('LOGS_SQL', 'false') == 'true') {
            $time= microtime(true);

            dispatch(new SendLogsSQL(DB::getQueryLog(), Request::fullUrl(), Request::getClientIp(), $time));

        }

    }

}
