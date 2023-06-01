<?php
namespace App\Helpers;

use App\Models\Backend\CMS\Language;
use App\Models\Backend\CMS\Translation;
use Cache;
use Exception;

class HCache {
    public static $KEY = [
        'languages' => 'cache_languages',
        'languages_frontend' => 'cache_languages_frontend',
        'languages_backend' => 'cache_languages_backend',
        'translations' => 'cache_translations',

        'menu_backend' => 'cache_menu_backend',

        'file' => 'cache_file',
        'configurations' => 'cache_configurations',
        'keycloak_admin_token' => 'cache_keycloak_admin_token',
    ];

    public static $TIMEOUT = 60*60*24*7; // 1 week
    public static $TIMEOUT_KEYCLOAK_TOKEN = 60; // 1 minute

    /**
     * Cache flush
     *
     * @param  string $code Code to be used to gather the CACHE KEY prefix
     * @param  string $key The remaining CACHE KEY
     * @return void
     */
    public static function flush(string $code, ?string $key = null): void {
        $name = getField(self::$KEY, $code);

        try {
            if(empty($name)) throw new Exception("Invalid cache code: ".$code);

            if(!empty($key)) $name .= "_".$key;

            Cache::forget($name);
        } catch(Exception $e) {
            logError("Error clearing languages cache '".HCache::$LANGUAGES."': ".$e->getMessage());
            dd($e->getMessage());
        }

    }

    /**
     * Cache remember
     *
     * @param  string $code Cache key prefix
     * @param  string $key Cache key
     * @param  callable $callback Callback function
     * @param  int $timeout Timeout if different from default
     * @return void
     */
    public static function remember(string $code, ?string $key, callable $callback, int $timeout = null): mixed {
        $name = getField(self::$KEY, $code);


        try {
            if(empty($name)) throw new Exception("Invalid cache code: ".$code);

            if(!empty($key)) $name .= "_".$key;

            // TODO: Store key in REDIS array

            return Cache::remember($name, $timeout ?? self::$TIMEOUT, $callback);
        } catch(Exception $e) {
            logError("Error creating cache '".$name."': ".$e->getMessage());
        }

        return null;
    }

    public static function flushLanguages(): void {
        self::flush('languages');
        self::flush('languages_frontend');
        self::flush('languages_backend');
    }

    public static function flushTranslationId($id) {
        try {
            $t = Translation::whereId($id)->withTrashed()->first();
            self::flush('translations', $t->locale."_".$t->namespace.".".$t->group.".".$t->item);
        } catch(Exception $e) {
            logError("Error clearing translation cache '".getField(self::$KEY, 'translations')."_".$t->locale."_".$t->namespace.":".$t->group.".".$t->item."': ".$e->getMessage());
        }
    }

    public static function flushTranslations() {
        try {
            $translations = Translation::all();

            foreach ($translations ?? [] as $t) {
                self::flush('translations', $t->locale . "_" . $t->namespace . "." . $t->group . "." . $t->item);
            }
        } catch(Exception $e) {
            logError("Error clearing translation cache '".getField(self::$KEY, 'translations')."_".$t->locale."_".$t->namespace.":".$t->group.".".$t->item."': ".$e->getMessage());
        }
    }

    public static function flushMenus(string $type = null) {
        try {
            if(empty($type)){
                self::flush('menu_backend');
                //TODO: Flush menu FE

            } elseif ($type == 'private'){
                self::flush('menu_backend');

            } else {
                //TODO: Flush menu FE
            }

        } catch(Exception $e) {
            logError("Error clearing menus cache: ".$e->getMessage());
        }
    }
}
