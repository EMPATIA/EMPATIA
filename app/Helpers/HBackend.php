<?php
namespace App\Helpers;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use App\Http\Controllers\Backend\CMS\MenusController;
use App\Models\Backend\CMS\Language;
use App\Models\Backend\CMS\Translation;
use App\Models\Backend\Configuration;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Exception;
use Request;
use Redirect;
use Session;
use Cache;
use Arr;

class HBackend {
    public static $SESSION_LANGUAGE = 'language';

    public static $TRANSLATIONS_FIX = [
        // rappasoft/laravel-livewire-tables
        'All'                                       => 'backend.datatable.filters.all',
        'Filters'                                   => 'backend.datatable.filters.title',
        'Applied Filters'                           => 'backend.datatable.filters.applied-filters',
        'Remove filter option'                      => 'backend.datatable.filters.remove-filter',
        'Clear'                                     => 'backend.datatable.filters.clear',
        'Search'                                    => 'backend.datatable.search.title',
        'Showing'                                   => 'backend.datatable.showing.title',
        'results'                                   => 'backend.datatable.results.title',
        'You are not connected to the internet.'    => 'backend.datatable.results.no-connection',
        'No records'                                => 'backend.datatable.results.no-records',
        'No results'                                => 'backend.datatable.results.no-results',
        'to'                                        => 'backend.datatable.to',
        'of'                                        => 'backend.datatable.of',
        'Go to page :page'                          => 'backend.datatable.go-to-page',

        // pagination
        'pagination.previous'   => 'backend.datatable.pagination.previous',
        'pagination.next'       => 'backend.datatable.pagination.next',

        // laravel
        'Unauthorized'                  => 'generic.laravel-authorization.unauthorized',
        'Forbidden'                     => 'generic.laravel-authorization.forbidden',
        'Server Error'                  => 'generic.laravel.server-error',
        'This action is unauthorized.'  => 'generic.laravel-authorization.policies.unauthorized',
        
        // laravel-form-component
        'validation.attributes'                     => 'generic.form.validation-atributes',
        '(and :count more errors)'                  => 'generic.form.and-count-more-errors',
        '(and :count more error)'                  => 'generic.form.and-count-more-error',

        // laravel framework
        'Not Found'             => 'frontend.laravel.not-found',

        // laravel
        'Unauthorized'          => 'generic.laravel-authorization.unauthorized',
        'Forbidden'             => 'generic.laravel-authorization.forbidden',
    ];
    
    // ##### LANGUAGE HELPERS #####
    
    /**
     * Get enabled languages
     *
     * @return object
     */
    public static function getLanguages(): array {
        return HCache::remember('languages', null, function () {
            return Language::orderBy('default', 'desc')->orderBy('name')->get()->toArray();
        });
    }

    /**
     * Get frontend enabled languages
     *
     * @return object
     */
    public static function getLanguagesFrontend(): array {
        return HCache::remember('languages_frontend', null, function () {
            return Language::whereFrontend(1)->orderBy('default', 'desc')->orderBy('name')->get()->toArray();
        });
    }

    /**
     * Get backend enabled languages
     *
     * @return object
     */
    public static function getLanguagesBackend(): array {
        return HCache::remember('languages_backend', null, function () {
            return Language::whereBackend(1)->orderBy('default', 'desc')->orderBy('name')->get()->toArray();
        });
    }

    /**
     * Get enabled languages for a specific environment (backend, frontend)
     *
     * @param string|null $environment
     * @return array
     */
    public static function getLanguagesEnvironment(string $environment = null) : array {
        $environment = $environment ?: self::getEnvironment();

        if( $environment == 'backend' ){
            return self::getLanguagesBackend();
        } else if( $environment == 'frontend' ){
            return self::getLanguagesFrontend();
        }

        return [];
    }

    /**
     * Get selected language
     *
     * @return array
     */
    public static function getLang(): string {
        $session = Session::pull(HBackend::$SESSION_LANGUAGE);

        if(isset($session)) {
            return $session;
        }

        [$type, $lang, $url] = HBackend::languages_processURL();
        return $lang;
    }

    public static function getBackendMenuLanguages(): array{
        $languages = self::getLanguagesBackend();
        $menuLanguages = [];
        $submenu = [];

        if($languages <= 1) {
            return $menuLanguages;
        }

        foreach($languages ?? [] as $lang) {
            if(getField($lang, "locale") == getLang()) {
                $menuLanguages = [
                    'currentLang' => getField($lang, "name"),
                ];
            } else {
                $url = Request::segments();
                $url[0] = getField($lang, "locale");

                $submenu = array_merge($submenu, [
                    [
                        'name' => getField($lang, "name"),
                        'locale' => getField($lang, "locale"),
                        'url' => '/' . Arr::join($url, '/'),
                        'active' => [ 'false' ],
                    ],
                ]);
            }
        }
        $menuLanguages["submenu"] = $submenu;

        return $menuLanguages;
    }
    
    public static function languages_routeLang() {
        // logDebug("Lang: ".HBackend::getLang());
        
        $languages = self::getLanguagesEnvironment();
        foreach($languages as $l) {
            $lang = getField($l, "locale");
            if($lang == Request::segment(1)) {
                return getLang();
            }
        }
        
        return '';
    }
    
    public static function languages_registerLang($lang) {
        App::setLocale($lang);
        
        // Sent locale in session or cookie?
        Session::put(HBackend::$SESSION_LANGUAGE, $lang);
        
        // logDebug("Lang: ".getLang()." | should be ".$lang);
    }
    
    public static function languages_processURL() {
        $browser = null;
        $default = null;
        
        // TODO:
        //   refactor this function;
        //   for each lang source, it should be checked against the database langs;
        //   if no DB langs, fallback to default
        
        // Find selected language in database
        $languages = self::getLanguages();
        foreach($languages as $l) {
            // get locale from route
            $lang = getField($l, "locale");
            // if route lang is valid, return immediatly
            if($lang == Request::segment(1)) {
                return ["URL", $lang, null];
            }
            
            // get locale from browser
            if((explode('_', Request::getPreferredLanguage())[0] ?? null) == $lang) {
                $browser = $lang;
            }
            
            // get locale from db default
            if(getField($l, "default")) {
                $default = $lang;
            }
        }
        
        $url = Request::path();
        // TODO: check if first segment is a valid locale (check locale standard)
        $urlSegments = explode('/',$url);
        if(strlen($urlSegments[0] ?? '') == 2) array_shift($urlSegments);
        $url = implode('/', $urlSegments);
        
        // If lang in session use it as default
        $session = Session::pull(HBackend::$SESSION_LANGUAGE);
        if(isset($session)) {
            return ["REDIRECT", $session, "/".$session."/".$url];
        }
        
        // Redirect to path in the browser default language
        if(!empty($browser)) {
            return ["REDIRECT", $browser, "/".$browser."/".$url];
        }
        
        // Redirect to path in the database default language
        if(!empty($default)) {
            return ["REDIRECT", $default, "/".$default."/".$url];
        }
        
        // Redirect to path in the app default language
        $fallback = config('app.fallback_locale');
        if(!empty($fallback)) {
            return ["REDIRECT", $fallback, "/".$fallback."/".$url];
        }
        
        return ["REDIRECT", 'en', '/en'];
    }
    
    // ##### ENVIRONMENT HELPERS #####

    /**
     * Get request environment (backend, frontend)
     *
     * @param \Illuminate\Http\Request|null $request
     * @return object
     */
    public static function getEnvironment(Request $request = null) : string {
        $request = $request ?? request();

        $environment = 'frontend';

        if( $request->segment(0) == 'private' ||
            ($request->segment(0) == self::getLang() && $request->segment(1) == 'private' )
        ){
            $environment = 'backend';
        }

        return $environment;
    }

    /**
     * Check if the current request environment is backend
     *
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    public static function isBackend(Request $request = null) : bool {
        return self::getEnvironment($request) == 'backend';
    }

    /**
     * Check if the current request environment is frontend
     *
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    public static function isFrontend(Request $request = null) : bool {
        return self::getEnvironment($request) == 'frontend';
    }
    
    // ##### CONFIGURATIONS HELPERS #####
    
    /**
     * Get configurations from a specific type
     *
     * @param  string $configurationType
     * @return object
     */
    public static function getConfigurations(string $configurationType): object {
        return HCache::remember('configurations', $configurationType, function () use($configurationType) {
            return Configuration::whereCode($configurationType)->firstOrFail();
        });
    }
    
    /******************************
     * Configurations
     * @param string $code
     * @return array|null
     */
    public static function getConfigurationByCode(string $code): ?array {
        try {
            return (array)Configuration::whereCode($code)->first()->configurations;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()) . ' at line ' . $e->getLine());
        }
        return null;
    }

    /**
     * Convert a validate array into validateRules and validateMessages arrays to use in Components validation
     *
     * @param array $validateArray
     * @param string $prefix Message prefix which can include the translation namespace and group
     * @return array[] Rules and messages validation arrays
     */
    public static function createControllerValidate(array $validateArray, string $prefix) : array {
        $validateRules = [];
        $validateMessages = [];

        try {
            foreach ($validateArray ?? [] as $field => $v) {
                if ($v["locale"] ?? false) {
                    foreach(self::getLanguagesFrontend() as $language) {
                        $locale = $language["locale"];

                        $validateRules[$field."->".$locale] = $v["rules"] ?? [];

                        foreach ($v["rules"] ?? [] as $rule) {
                            $rule = Str::of($rule)->explode(":")->first();
                            $validateMessages[$field."->".$locale.".".$rule] = __($prefix . "." . $field . "."."$rule" . "." . $locale);
                        }
                    }
                } else {
                    $validateRules[$field] = $v["rules"] ?? [];

                    foreach ($v["rules"] ?? [] as $rule) {
                        $rule = Str::of($rule)->explode(":")->first();
                        $validateMessages[$field.".".$rule] = __($prefix . "." . $field . "."."$rule");
                    }
                }
            }
        } catch(Exception | \Throwable $e) {
            logError("Error processing controller validation array: ".$e->getMessage());
        }

        return [$validateRules, $validateMessages];
    }

    /**
     * Get formatted string with parent (level) class name and function name
     *
     * @param string|null $class Custom class name
     * @param string|null $function Custom function name
     * @param int $level Default level 3 (parent hierarchy)
     * @return string
     */
    public static function getCurrentMethod(string $class = null, string $function = null, int $level = 3) : string {
        if(empty($class) && empty($function)) {
            try {
                $previous = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $level));
                $class = Str::of($previous["class"] ?? '')->explode('\\')->last();
                $function = $previous["function"];
            } catch (Exception | Throwable $e) {
            }
        }

        if(!empty($class) && !empty($function) ) {
            return "[".$class."@".$function."]";
        } else if(!empty($class)) {
            return "[".$class."]";
        } else if(!empty($function)) {
            return "[".$function."]";
        }
        return "";
    }
    
    /**
     * Create request input with languages -> "pt":"Input", "en":"Input"
     * 
     * @param  $request
     * @param string $input
     * @param bool $decode
     * @return object
     */
    public static function setInput($request, string $input, bool $decode = false) {
        $data = [];
        try {
            foreach (self::getLanguages() as $key => $lang) {
                $value = data_get($request, $input . '->' . $lang['locale']);
                $data[$lang['locale']] = $decode ? json_decode($value) : $value;
            }
        } catch (Exception $e) {
            logError( $e->getMessage() );
        }
        return (object) $data;
    }
    
    
    /**
     * Get translation from database (If not in cache) or create it
     *
     * @param string $locale
     * @param string $key
     * @return string
     */
    public static function getTranslation(string $locale, string $key): string
    {
        try {
            // TODO: Try another method to fix translations generated from packages in wrong format
            if (array_key_exists($key, HBackend::$TRANSLATIONS_FIX)) {
                $key = HBackend::$TRANSLATIONS_FIX[$key];
            }
            
            $key = str_replace("::", ".", $key);
            $key = str_replace(":", ".", $key);
            
//             logDebug("Checking cache for translation with [LOCALE]: " . $locale . " and [KEY]: " . $key);

            // Get translation from DB (or CACHE)
            $text = HCache::remember('translations', $locale . "_" . $key, function () use ($locale, $key) {
                logDebug("Translation not in CACHE with [LOCALE]: " . $locale . " and [KEY]: " . $key);
                
                [$namespace, $group, $item] = explode(".", $key, 3);
                
                // withTrashed because if translation is soft deleted, it will try to create an equal translation, which will lead to an error
                $translation = Translation::where("locale", $locale)->where("namespace", $namespace)->where('group', $group)->where("item", $item)->withTrashed()->first();
                
                if ($translation == null) {                                      // If translation doesn't exist
                    foreach (self::getLanguages() ?? [] as $language) {
                        $langLocale = getField($language, "locale");
                        $text = $langLocale . "::" . $namespace . "." . $group . "." . $item;       // Translation default text
                        
                        // If translation doesn't exist for one active language, create it in DB
                        if (empty(Translation::where("locale", $langLocale)->where("namespace", $namespace)->where('group', $group)->where("item", $item)->withTrashed()->first())) {
                            logDebug("Creating translation with [LOCALE]: " . $langLocale . " and [KEY]: " . $key);
                            DB::beginTransaction();
                            Translation::create(['locale' => $langLocale, 'namespace' => $namespace, 'group' => $group, 'item' => $item, 'text' => $text]);
                            DB::commit();
                        }
                    }
                    
                } elseif (!empty($translation->deleted_at)) {    // Translation exists but is soft deleted
                    DB::beginTransaction();
                    if ($translation->restore()) {              // Restore translation
                        DB::commit();
                        HCache::flushTranslationId($translation->id);      // Flush translation from cache
                    }
                    $text = getField($translation, "text");         // Get translation text
                    
                } else {                                          // Translation exists and is 
                    $text = getField($translation, "text");
                }
                
                return $text ?? "$locale::$key";
            });
        } catch (Exception $e) {
            DB::rollback();
            logError("Error creating translation with [LOCALE]: $locale and [KEY]: $key ");
            return "TRANSLATION ERROR: " . $locale . " - " . $key;
        }
        
        return $text ?? "$locale::$key";
    }

    public static function createMenuOption($menu, $level = 0): array {
        $l = 2*$level;

        if(!empty(getField($menu, 'children'))) {
            // dd(getField($menu, 'children'));

            $m = [];
            foreach(getField($menu, 'children') as $menuChild) {
                $m = array_merge($m, self::createMenuOption($menuChild, $level+1)) ;
            }

            return [
                [
                    'text'    => getFieldLang($menu, "title"),
                    'icon'    => getField($menu, "options.".getLang().".icon"),
                    'classes' => 'pl-'.$l,
                    'submenu' => $m,
                ]
            ];
        } else if(empty(getFieldLang($menu, "link"))) {
            return [
                [
                    'text'    => getFieldLang($menu, "title"),
                    'header' => getFieldLang($menu, "title"),
                    'icon'    => getField($menu, "options.".getLang().".icon"),
                    'classes' => 'pl-'.$l,
                ],
            ];
        } else {
            return [
                [
                    'text' => getFieldLang($menu, "title"),
                    'url' => getFieldLang($menu, "link"),
                    'icon'    => getField($menu, "options.".getLang().".icon"),
                    'classes' => 'pl-'.$l,
                    'active' => ['*/'.getFieldLang($menu, "link").'*']
                ],
            ];
        }
    }
    
    
    
    
    
    // ##### UNUSED HELPERS #####
    
    /**
     * Get Google Recaptcha Key
     *
     * @return string
     */
    public static function googleRecaptchaKey(): string {
        return env("GOOGLE_RECAPTCHA_KEY");
    }
    
    /**
     * Verify if GoogleRecaptcha is enabled
     *
     * @return bool
     */
    public static function googleRecaptchaEnabled(): bool {
        return (!empty(env("GOOGLE_RECAPTCHA_KEY")) && !empty(env("GOOGLE_RECAPTCHA_SECRET")));
    }
    
    /**
     * Validate Google Recaptcha
     *
     * @param string $value Google Recaptcha value
     *
     * @return bool
     */
    public static function validateGoogleRecaptcha(string $value): bool {
        try {
            $recaptcha = new \ReCaptcha\ReCaptcha(env('GOOGLE_RECAPTCHA_SECRET'));
            $resp = $recaptcha->verify($value, $_SERVER['REMOTE_ADDR']);
            
            if ($resp->isSuccess()) {
                logDebug("Google Recaptcha successful");
                return true;
            } else {
                $errors = $resp->getErrorCodes();
                
                logInfo("Google Recaptcha FAILED: ".json_encode($errors));
                
                return false;
            }
        } catch (Exception $e) {
            logError("Error processing Google Recaptcha: ".$e->getMessage());
        }
        

        return false;
    }

    public static function getScaleOptions(string $scaleId, string $text, array $extendOptions): array
    {
        if( !in_array($scaleId, ['x', 'y']) ){
            return [];
        }

        return [
                'display' => true,
                'title' => [
                    'display' => true,
                    'centerPointLabels' => true,
                    'align' => "center",
                    'text' => $text,
                    'color' => "#2c6899",
                    'font' => [
                        'family' => '"Roboto", sans-serif',
                        'size' => '18',
                        'lineHeight' => 1.2,
                    ],
                ],
            ] + $extendOptions;
    }

    public static function getRandomHsl($opacity = 1)
    {
        $hue = 208;
        return "hsl($hue, ". rand(40, 90) ."%, ". rand(40, 90) ."%, $opacity)";
    }

    // Get menu delete options available for filter
    public static function getMenuTypeOptionsDelete(){
        return array(
            '0' => __('backend::datatable.filter.deleted-false'),
            '1' => __('backend::datatable.filter.deleted-true'),
        );
    }
}
