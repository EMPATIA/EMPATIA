<?php

namespace App\Helpers;

use App\Jobs\LogsProcess;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\LogOne;
use Request;
use Session;
use App;

class LogsOne {

    const LOG_DEBUG = 'debug';
    const LOG_ERROR = 'error';
    const LOG_INFO = 'info';
    const LOG_ACCESS = 'access';
    const LOG_PERFORMANCE = 'performance';
    const LOG_AUTH = 'auth';

    private const RELATIONAL_COLS = [];

    /**
     * Create an ERROR log message.
     *
     * @param $action Action code
     * @param $context String or array with context information
     * @param $result Optional | bool action result, if applicable. Default is true.
     */
    public static function error($action, $context, $result = false, $facility = null, $details = null, $user_id = null) {
        self::log(self::LOG_ERROR, $action, $context, $result, $facility, $details, $user_id);
    }

    /**
     * Create an INFO log message.
     *
     * @param $action Action code
     * @param $context String or array with context information
     * @param $result Optional | bool action result, if applicable. Default is true.
     */
    public static function info($action, $context, $result = true, $facility = null, $details = null, $user_id = null) {
        self::log(self::LOG_INFO, $action, $context, $result, $facility, $details, $user_id);
    }

    /**
     * Create a DEBUG log message.
     *
     * @param $action Action code
     * @param $context String or array with context information
     * @param $result Optional | bool action result, if applicable. Default is true.
     */
    public static function debug($action, $context, $result = true, $facility = null, $user_id = null) {
        self::log(self::LOG_DEBUG, $action, $context, $result, $facility, $user_id);
    }

    /**
     * Create an ACCESS log message (to be used in middleware).
     */
    public static function access() {
        self::log(self::LOG_ACCESS, self::LOG_ACCESS, null, true);
    }

    /**
     * Create an PERFORMANCE log message (to be used in middleware).
     */
    public static function performance($start, $finish) {
        $context = array(
            'start' => $start,
            'finish' => $finish,
            'time' => ($finish-$start)
        );
        self::log(self::LOG_PERFORMANCE, self::LOG_PERFORMANCE, $context, true);
    }

    /**
     * Create an AUTH log message.
     *
     * @param $action Action code
     * @param $context String or array with context information
     * @param $result Optional | bool action result, if applicable. Default is true.
     */
    public static function auth($action, $context, $result = true) {
        self::log(self::LOG_AUTH, $action, $context, $result);
    }

    /**
     * Private function to print and store log messages.
     *
     * @param $severity Log | string level.
     * @param $action Action | string code
     * @param $context String | array with context information
     * @param $result Optional | bool action result, if applicable. Default is true.
     * @return bool|void
     */
    private static function log($severity, $action, $context, $result, $facility = null, $details = null, $user_id = null) {

        try {
            // Verify if logs are enabled in database
            if (!config('logging.env_logs_one_enabled')) return;
            if ($severity == self::LOG_ACCESS && !config('logging.env_logs_one_access')) return;
            if ($severity == self::LOG_PERFORMANCE && !config('logging.env_logs_one_performance')) return;

            try {
                // TODO: seems to enter some kind of loop here; doesn't output logs; server throws a 500 error code
                $user_id = $user_id ?? Auth::id();
            } catch(\Exception $e) {
            }

            $ip = Request()->ip();

            // Prepare structure to save on db
            $arr = array(
                'date'       => Carbon::now(),
                'severity'   => $severity,
                'ip'         => $ip,
                'url'        => substr(url()->full(), 0, 250),
                'method'     => Request()->method(),
                'session_id' => \Session::getId(),
                'user_agent' => Request()->header('User-Agent'),
                'user_id'    => $user_id,
                'facility'   => $facility,
                'action'     => $action,
                'result'     => $result,
                'context'    => json_encode($context),
                'details'    => json_encode($details)
            );


            // Set relational columns value if available in $context
            foreach (self::RELATIONAL_COLS as $column) {
                $arr[$column] = !empty($context[$column]) && is_numeric($context[$column]) ? $context[$column] : null;
            }

            // Get env configurations
            $log_file = config('logging.env_logs_one_file_level');
            $log_db = config('logging.env_logs_one_db_level');


            // Save log in log file if enabled in configurations
            if (empty($log_file) || strpos($log_file, $severity) !== false) {

                $str = "";
                if(! App::environment('local')) {
                    $str .= "[IP:".$ip."]";
                    $str .= "[S:".Session::getId()."]";
                    $str .= "[U:".$user_id."]";
                }

                if(is_string($context) || is_int($context))
                    $msg = $action . " " . $context;
                else
                    $msg = $action . " " . json_encode($context);

                switch ($severity){
                    case self::LOG_ACCESS:
                        \Log::info("[LOG: " . $severity . "]". $str . " " . Request::method() . " => " . Request::url());
                        break;
                    case self::LOG_ERROR:
                        \Log::error($str . $msg);
                        break;
                    case self::LOG_DEBUG:
                        \Log::debug($str . $msg);
                        break;
                    default:
                        \Log::info($str . $msg);
                        break;
                }
            }

            // Save log in database if enabled in configurations
            if (!empty($log_db) && strpos($log_db, $severity) !== false) {
                if (config('logging.env_logs_one_jobs')) {
                    // Create JOB to write log to database
                    LogsProcess::dispatch($arr);
                } else {
                    // Process log immediately
                    self::processLog($arr);
                }
            }
            return true;
        } catch (\Exception | \Throwable $e) {
		    \Log::error("Log error: ".json_encode($e));
	        dd("Log error: ",$e);
            return false;
        }
    }

    /**
     * Process log to insert in database.
     *
     * @param $log Array with log to store in database
     */
    public static function processLog($log) {
        try {
            LogOne::insert($log);
        } catch (\Exception $e) {
		    \Log::error("ERROR PROCESSLOG: ".json_encode($e));
            dd("ProcessLog", $e, $log);
        }
    }
}
