<?php
/**
 * Created by PhpStorm.
 * User: Vitor Fonseca
 * Date: 18/04/2017
 * Time: 14:12
 */

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ONE;

class SendLogsSQL implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $parameters;
    protected $url;
    protected $ip;
    protected $time;

    public function __construct($parameters, $url, $ip, $time)
    {
        $this->parameters = $parameters;
        $this->url = $url;
        $this->ip = $ip;
        $this->time = $time;
    }

    public function handle()
    {
        $identifier = str_random(32);

        foreach ($this->parameters as $query) {
            foreach ($query["bindings"] as $replace) {
                $pos = strpos($query["query"], "?");
                if ($pos !== false) {
                    if (is_string($replace))
                        $replace = '"' . $replace . '"';

                    $query["query"] = substr_replace($query["query"], $replace, $pos, strlen("?"));
                }
            }

            ONE::post([
                'component' => 'logs',
                'api' => 'TrackingController',
                'method' => 'saveTrackingDataToDB',
                'params' => ["is_logged" => 0,
                    "auth_token" => null,
                    "user_key" => null,
                    "ip" => $this->ip,
                    "url" => $this->url,
                    "site_key" => '',
                    "method" => 'SQL',
                    "session_id" => $identifier,
                    "table_key" => '',
                    "time_start" => $this->time,
                    "message" => $query["query"],
                    "time_end" => $query["time"]]
            ]);
        }
    }
}
