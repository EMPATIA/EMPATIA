<?php

use App\Models\User;
use App\Helpers\HBackend;
use App\Helpers\LogsOne;
use App\Models\Permission;


/**
 * Convert bytes to human-readable units
 *
 * @param $bytes
 * @return string
 */
function bytesToHuman($bytes): string {
    $units = ['b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];

    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Get user name (default: logged user)
 *
 * @param null $id
 * @return mixed
 */
function getUserName($id = null) {
    if(empty($id)) return Auth::user()->name;
    return User::findOrFail($id)->name;
}

/**
* Search in array or object for an object having certain property value.
 *
 */
function findObjectByProperty(string $property, $value, $haystack, $returnKey = false){
    if( !is_array($haystack) && !is_object($haystack) )
        return null;

    foreach ( $haystack ?? [] as $key => $object ) {
        $objectValue = data_get($object, $property);

        if ( $value == $objectValue ) {
            return $returnKey ? $key : $object;
        }
    }

    return false;
}

/**
 * Search in array or object for objects having certain property value.
 * Can do partial matching but only when values are strings.
 *
 */
function findObjectsByProperty(string $property, $value, $haystack, bool $partial = false, bool $keyed = false)
{
    if( !is_array($haystack) && !is_object($haystack) )
        return null;

    $objects = [];

    foreach ( $haystack ?? [] as $key => $object ) {
        $objectValue = data_get($object, $property);

        if( $partial && is_string($value) && is_string($objectValue) ){
            $condition = stripos( $objectValue , $value ) !== false;
        } else {
            $condition = $value == $objectValue;
        }

        if ( $condition ) {
            if( $keyed )
                $objects[ $key ] = $object;
            else
                $objects[] = $object;
        }
    }

    return $objects;
}

/**
 * Get array or object first element.
 *
 */
function first_element($object){
    if( !is_array($object) && !is_object($object) )
        return null;

    $element = false;

    foreach ( $object ?? [] as $element )
        return $element;

    return $element;
}

/**
 * Get current language
 *
 * @return string
 */
function getLang(): string {
    return HBackend::getLang();
}

/**
 * Get active languages array
 *
 * @return array
 */
// function getLanguages(): array {
//     return HBackend::getLanguages();
// }

function getLanguagesFrontend(): array {
    return HBackend::getLanguagesFrontend();
}

function getLanguagesBackend(): array {
    return HBackend::getLanguagesBackend();
}

function getLanguagesEnvironment(string $environment = null): array {
    return HBackend::getLanguagesEnvironment($environment);
}

function getField($object, $field, $default = null) {
    try {
        if(is_array($object)) {
            $object = (object)$object;
        }

        if(!is_object($object)) return $default;
        if(!is_string($field)) return $default;

        str_replace('->','.', $field);
        return data_get($object, $field) ?: $default;
    } catch(Exception $e) {
        return $default;
    }
}

function getFieldLang($object, $field, $default = null, $lang = null) {
    if($lang == null) $lang = getLang();

    try {
        if(is_array($object)) {
            $object = (object)$object;
        }

        if(!is_object($object)) return $default;
        if(!is_string($field)) return $default;

        str_replace('->','.', $field);

        # Add language
        $field = $field.".".$lang;

        return data_get($object, $field) ?: $default;
    } catch(Exception $e) {
        return $default;
    }
}

/**
 * HELPER LOGS
 */
function logDebug($message, $function = null, $class = null, $facility = null, $user_id = null) {
    $context = HBackend::getCurrentMethod($class, $function);
    LogsOne::debug($context, $message, true, $facility, $user_id);
}

function logError($message, $result = true, $facility = null, $function = null, $class = null, $details = null, $user_id = null) {
    $context = HBackend::getCurrentMethod($class, $function);
    LogsOne::error($context, $message, $result, $facility, $details, $user_id);
}

function logInfo($message, $function = null, $class = null, $facility = null, $user_id = null) {
    $context = HBackend::getCurrentMethod($class, $function);
    LogsOne::info($context, $message, true, $facility, $user_id);
}

function auditLog($message, $facility = null, $function = null, $class = null, $details = null, $user_id = null) { // $facility -> payment, menu, user, etc (module)
    $context = HBackend::getCurrentMethod($class, $function);
    LogsOne::info($context, trim($message, '\n'), true, $facility, $details, $user_id);
}

function auditAccess() {
    LogsOne::access();
}

function auditPerformance($start,$finish) {
    LogsOne::performance($start,$finish);
}

// custom message for the log (json so it supports translations)
// code in the same context of message
// model that was changed or takes part on action (model_id is self explanatory)
function customLog($message, $context, $code, $model, $model_id, $function = null, $class = null, $user_id = null) {
    $action = HBackend::getCurrentMethod($class, $function);
    LogsOne::customLog($message, $context, $code, $action, $model, $model_id, $user_id);
}


/**
 * Gets the name of the method called in a class.
 *
 * @param   int $offset     The debug trace offset.
 * @return  string|bool     Returns the method name. Returns <b>false</b> if called outside a class.
 */
function get_called_method(int $offset = 0): string|bool {
    try {
        return debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS)[1+$offset]['function'];
    } catch (Exception $e) {
        return false;
    }
}


/**
 * Get carbon from date.
 *
 */
function carbon($date): \Illuminate\Support\Carbon|\Carbon\Carbon|null
{
    if( $date instanceof Carbon\Carbon || $date instanceof Illuminate\Support\Carbon ){
        return $date;
    }

    if( empty($date) || !is_string($date) ){
        return null;
    }

    return Carbon\Carbon::parse($date);
}

/**
 * Search in array but using a partial needle
 *
 */
function partial_in_array( $needle , $haystack ): bool {
    foreach ($haystack as $element){
        if( stripos( strtolower($element) , strtolower($needle) ) !== false ){
            return true;
        }
    }
    return false;
}

/**
 * Get parameter value from a route
 * @param string $name
 * @param Route|null $route
 * @return mixed
 */
function route_parameter_get(string $name, \Route $route = null) : mixed {
    $route = $route ?? \Route::current();

    if( !empty($route) ){
        return $route->parameter($name);
    }

    return null;
}

/**
 * Get parameter value from a route
 * @param mixed $object
 * @param string $key
 * @param mixed|null $default
 * @param bool $defaultToFirst
 * @return mixed
 */
function data_lang_get(mixed $object, string $key, mixed $default = null, bool $defaultToFirst = false) : mixed {
    $data = data_get($object, $key);

    try {
        return data_get($data, getLang(), $defaultToFirst ? first_element($data) : null) ?? $default;
    } catch (Exception $e) {
        logError($e->getMessage());
    }

    return null;
}

/**
 * Unset a variable property given a dot notation key.
 *
 * @param mixed $target
 * @param string $key
 * @return void
 */
function data_unset(mixed &$target, string $key): void
{
    $levels = explode('.', $key);
    $levelsCount = count($levels);
    $levels = array_filter($levels);

    if( empty($key) || empty($levels) || count($levels) != $levelsCount ){
        return;
    }

    $levelsCount = count($levels);
    $levelTargets = [&$target];
    foreach ($levels as $i => $level) {
        if( !is_array($levelTargets[$i]) && !is_object($levelTargets[$i]) ){
            return;
        }

        if( is_array($levelTargets[$i]) && isset($levelTargets[$i][$level]) ){
            $levelTargets[$i+1] = &$levelTargets[$i][$level];
        }
        if( is_object($levelTargets[$i]) && isset($levelTargets[$i]->{$level}) ){
            $levelTargets[$i+1] = &$levelTargets[$i]->{$level};
        }

        if( empty($levelTargets[$i+1]) ){
            return;
        }
    }

    if( is_array($levelTargets[$levelsCount-1]) ){
        unset($levelTargets[$levelsCount-1][$levels[$levelsCount-1]]);
    } else {
        unset($levelTargets[$levelsCount-1]->{$levels[$levelsCount-1]});
    }
}

function project_path(bool $dot = false)
{
    $path = config('one.project_path', '');
    if( $dot ){
        $path = str_replace('/', '.', $path);
    }

    return $path;
}

/**
 * Checks whether the current request is in backend environment
 * @param \Illuminate\Http\Request|null $request
 * @return bool
 */
function isBackend(\Illuminate\Http\Request $request = null) : bool
{
    return HBackend::isBackend($request);
}

/**
 * Checks whether the current request is in frontend environment
 * @param \Illuminate\Http\Request|null $request
 * @return bool
 */
function isFrontend(\Illuminate\Http\Request $request = null) : bool
{
    return HBackend::isFrontend($request);
}

/**
 * Checks whether a given string is the active framework variant
 * @param string $variant
 * @return bool
 */
function isVariant(string $variant) : bool
{
    return config('one.framework_variant') == $variant;
}

/**
 * Gets the http status code from an execption
 * @param Exception|Throwable $e
 * @param int $default
 * @return int
 */
function http_status_code_from_exception(\Exception|\Throwable $exception, int $default = 404) : string
{
    return method_exists($exception, 'getStatusCode') ? (int)$exception->getStatusCode() : $default;
}

/**
 * Retrieves a permission from permissions config.
 *
 * @param string $key
 * @return array|null
 */
function permission(string $key) : ?Permission
{
    return Permission::byKey($key);
}

/**
 * Checks if there is some variable empty
 * @param ...$variables
 * @return bool
 */
function checkEmpty(...$variables) : bool {
    foreach ($variables as $var) {
        if (empty($var)) {
            return true;
        }
    }
    return false;
}

