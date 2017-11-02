<?php
/**
 * Copyright (C) 2016 OneSource - Consultoria Informatica Lda <geral@onesource.pt>
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses>.
 */

namespace App\One;

use App\Jobs\SendLog;
use Illuminate\Foundation\Bus\DispatchesJobs;


class OneLog {
    use DispatchesJobs;
    
    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $message The log message
     */
    public static function debug($message){
        $job = OneLog::log("info",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
    
    /**
     * Adds a log record at the INFO level.
     *
     * @param string $message The log message
     */
    public static function info($message){
        $job = OneLog::log("info",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
    
    /**
     * Adds a log record at the NOTICE level.
     *
     * @param string $message The log message
     */
    public static function notice($message){
        $job = OneLog::log("notice",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    
        
    /**
     * Adds a log record at the ERROR level.
     *
     * @param string $message The log message
     */
    public static function error($message){
        $job = OneLog::log("error",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param string $message The log message
     */
    public static function critical($message){
        $job = OneLog::log("crtical",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    

    /**
     * Adds a log record at the ALERT level.
     *
     * @param string $message The log message
     */
    public static function alert($message){
        $job = OneLog::log("crtical",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    
    
    /**
      * Adds a log record at the EMERGENCY level.
      *
      * @param string $message The log message
      */
     public static function emergency($message){
        $job = OneLog::log("emergency",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }         
    
    /**
     * Sets object to create the job «SendLog»
     *
     * @param string $type
     * @param string $message
     * @static 
     */
    private static function log($type, $message){
        $job = new SendLog();
        $job->setType($type);
        $job->setMessage($message);
        return $job;
    }    
    

}