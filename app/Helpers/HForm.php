<?php

namespace App\Helpers;

use App;
use Route;
use Illuminate\Support\Str;

class HForm {
    public static $INDEX = 'index';
    public static $SHOW = 'show';
    public static $CREATE = 'create';
    public static $EDIT = 'edit';
    public static $DELETE = 'delete';
    

    public static function getInputClass(string $action = null): string {
        return self::isShow($action) ? "form-control-plaintext pt-0 ": "form-control mb-2";
    }

    public static function getInputPlaceholder(string $placeholder, string $placeholderShow = "-", string $action = null): string {
        return self::isShow($action) ? $placeholderShow : $placeholder;
    }

    public static function getInputReadonly(string $action = null): bool {
        return self::isShow($action);
    }

    /**
     * Get form action based on action parameter and route details
     *
     * @param null $action
     * @return string
     */
    public static function getAction(string $action = null): string {
        $route = Str::of(Route::currentRouteAction());
        $a = Str::of($action);

        if($a->is([self::$INDEX, self::$CREATE, self::$EDIT, self::$SHOW])) {
            return $action;
        }

        if($route->endsWith('create')) {
            return self::$CREATE;
        } elseif($route->endsWith('edit')) {
            return self::$EDIT;
        } elseif($route->endsWith('index')) {
            return self::$INDEX;
        } else {
            return self::$SHOW;
        }
    }

    public static function isShow($action = null): bool {
        return self::getAction($action) == self::$SHOW;
    }

    public static function isEdit($action = null): bool {
        return self::getAction($action) == self::$EDIT;
    }

    public static function isCreate($action = null): bool {
        return self::getAction($action) == self::$CREATE;
    }

    public static function getFormAction($routeCreate = '', $routeEdit = '', $action = null): string {
        if(self::isCreate($action)) {
            return $routeCreate;
        } elseif(self::isEdit($action)) {
            return $routeEdit;
        }

        return "";
    }

    public static function getFormMethod($action = null): string {
        if(self::isEdit($action)) {
            return "PUT";
        }

        return "POST";
    }


/********************************************************** */

    /**
     * Get content translation if JSON received includes translation record, otherwise returns empty string
     *
     * @param $json
     * @param null $lang
     * @return string
     */
    public static function getTrans($json, $lang = null) {
        if(empty($lang)) $lang = App::getLocale();

        if(is_string($json)) {
            $json = json_decode($json);
        }

        if(is_array($json)) {
            $json = (object) $json;
        }

        if(!is_object($json)) {
            return '';
        }

        return $json->$lang ?? '';
    }

    public function getFormActionAndMethod($id = 0, $action = null, $controller = null, $version = 0): array {
        if($this->isCreate($action)) {
            return [
                'action' => action([$controller ?? get_class(Route::current()->controller), 'store']),
                'method' => 'POST'
            ];
        } else if($this->isEdit($action)) {
            return [
                'action' => action([$controller ?? get_class(Route::current()->controller), 'update'], ['id' => $id]),
                'method' => 'PUT'
            ];
        } else {
            return [
                'action' => '',
                'method' => ''
            ];
        }
    }

    /**
     * Get text to present in input or textfield
     *
     * @param $value
     * @param bool $isInput
     * @param null $lang
     * @param null $default
     * @return string
     */
    public static function getFromInputValue($value, $isInput = true, $lang = null, $default = null): string {
        if(!empty($lang)) {
            $value = HForm::getTrans($value, $lang);
        }

        if(is_object($value) || is_array($value)) {
            $value = json_encode($value);
        }

//        logDebug("VALUE: ".$value." || ".is_numeric($value));

        if(is_numeric($value) || !(is_null($value) || strlen($value) <= 0)) {
//            logDebug("===> Return value");
            return $value;
        }

        if(!(is_null($default) || strlen($default) <= 0)) {
            return $default;
        }

        if($isInput)
            return '';
        else
            return '<span class="form-input-no-text">'.__('content::form.input.no-text').'</span>';
    }

    public function getActionShow($id = 0, $controller = null, $version = 0): string {
        try {
            return action([$controller ?? get_class(Route::current()->controller), 'show'], ['id' => $id]);
        } catch (\Exception $e) {
            return action([$controller ?? get_class(Route::current()->controller), 'show'], ['slug' => Cb::findOrFail($id)->slug->{\UriLocalizer::localeFromRequest()}]);
        }
    }

    public function getActionCreate($controller = null): string {
        return action([$controller ?? get_class(Route::current()->controller), 'create']);
    }

    public function getActionEdit($id = 0, $controller = null, $version = 0): string {
        try {
            return action([$controller ?? get_class(Route::current()->controller), 'edit'], ['id' => $id]);
        } catch (\Exception $e) {
            return action([$controller ?? get_class(Route::current()->controller), 'edit'], ['id' => Cb::findOrFail($id)->id]);
        }
    }

    public function getActionToggleVersion($id = 0, $version = 0, $controller = null): string {
        return action([$controller ?? get_class(Route::current()->controller), 'toggle_version'], ['id' => $id, 'version' => $version]);
    }

    public function getActionDelete($id = 0, $controller = null): string {
        try {
            return action([$controller ?? get_class(Route::current()->controller), 'destroy'], ['id' => $id]);
        } catch (\Exception $e) {
            return action([$controller ?? get_class(Route::current()->controller), 'destroy'], ['slug' => Cb::findOrFail($id)->slug->{\UriLocalizer::localeFromRequest()}]);
        }
    }

    public function getActionRestore($id = 0, $controller = null): string {
        return action([$controller ?? get_class(Route::current()->controller), 'restore'], ['id' => $id]);
    }

    public static  function getActionIndex($controller = null): string {
        return action([$controller ?? get_class(Route::current()->controller), 'index']);
    }

    public function getActionCancel($id = 0, $action = null, $controller = null, $version = 0): string {
        if($this->isEdit($action)) {
            try {
                return action([$controller ?? get_class(Route::current()->controller), 'show'], ['id' => $id]);
            } catch (\Exception $e) {
                return action([$controller ?? get_class(Route::current()->controller), 'show'], ['slug' => Cb::findOrFail($id)->slug->{\UriLocalizer::localeFromRequest()}]);
            }
        } else {
            return $this->getActionIndex($controller);
        }
    }
}
