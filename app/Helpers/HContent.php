<?php
namespace App\Helpers;

use App\Models\Backend\Configuration;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Route;
use App\Models\Backend\CMS\Content;
use App\Http\Controllers\Backend\FilesController;
use App\Http\Controllers\Frontend\CMS\FrontendController;

class HContent {

    /**
     * Get content type configurations
     *
     * @param $contentType
     * @return object
     */
    public static function getContentConfigurations($contentType): object|null {
        try {
            $configuration = HBackend::getConfigurations('content_types');

            return getField($configuration, "configurations.".$contentType);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('Error getting content config: '.$e->getMessage());
        }

        return (object)[];
    }

    /**
     * Get content seo tags
     *
     * @return object
     */
    public static function getContentSeo(): object {
        try {
            $seo = Configuration::whereCode('content_seo')->first();
            return $seo->configurations;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('content config: '.json_encode($e->getMessage()));
        }

        return (object)[];
    }

    /**
     * Get default sections
     *
     * @return object
     */
    public static function getContentSections(): object {
        try {
            $sections = Configuration::whereCode('content_sections')->first();
            dd($sections->configurations);
            return $sections->configurations;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('content config: '.json_encode($e->getMessage()));
        }

        return (object)[];
    }



    /**
     * Get CMS create url
     *
     * @param $type
     * @return string
     */
    public function getActionCreate($type = null): string {
        return action([ContentsController::class, 'create'], ['type' => $type]);
    }

//    public function getActionEdit($type = null, $id = 0){
//        return action([get_class(Route::current()->controller), 'edit'], ['id' => $id, 'type' => $type]);
//    }

    public function getActionDelete($type, $id = 0): string {
        return action([ContentsController::class, 'destroy'], ['type' => $type,'id' => $id]);
    }

    public function getActionIndex($type = null): string {
        return action([ContentsController::class, 'index'], ['type' => $type]);
    }

    public function getActionCancel($type = null, $id = 0, $action = null): string {
        return $this->getActionIndex($type);
    }

    /**
     * Get CMS form actions and methods
     *
     * @param $type
     * @param $id
     * @param $action
     * @return array
     */
    public function getFormActionAndMethod($type, $id = 0, $action = null): array {
        if($this->isCreate($action)) {
            return [
                'action' => action([get_class(Route::current()->controller), 'store'], ['type' => $type]),
                'method' => 'POST'
            ];
        } else if($this->isEdit($action)) {
            return [
                'action' => action([get_class(Route::current()->controller), 'update'], ['id' => $id, 'type' => $type]),
                'method' => 'PATCH'
            ];
        } else {
            return [
                'action' => '',
                'method' => ''
            ];
        }
    }

    /**
     * Get form action based on action parameter and route details
     *
     * @param null $action
     * @return string
     */
    public static function getAction($action = null): string {
        $route = Str::of(Route::currentRouteAction());
        $a = Str::of($action);


        if($a->is(['create', 'edit', 'show'])) {
            return $action;
        }

        if($route->endsWith('create')) {
            return 'create';
        } elseif($route->endsWith('edit')) {
            return 'edit';
        } else {
            return 'show';
        }
    }

    public function isShow($action = null): bool {
        return $this->getAction($action) == 'show';
    }

    public function isEdit($action = null): bool {
        return $this->getAction($action) == 'edit';
    }

    public function isCreate($action = null): bool {
        return $this->getAction($action) == 'create';
    }

    /****************************
     * Versions
     */

    public static function getVersionString(Content $content, int $ver = null, $addVersion = true) : string {
        $str = '';

        try {
            if(empty($ver)) $ver = $content->version ?? 0;

            $version = $content->versions->$ver ?? null;

            if(empty($version)) return "v0";

            $userId = $version->user_version;
            $date = Carbon::parse($version->date_version)->format('Y-m-d H:i');

            if($addVersion)
                $str.= "v".$version->version.": ";

            $str.= $date." (".getUserName($userId).")";
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()));
            $str = "-- ERROR --";
        }

        return $str;
    }

    /******************************
     * SEO
     */

    public static function getSeoDefault($code, $field, $content, $seo, $locale = null) : string {
        $value = '';

        try {
        //    logDebug(">>>>>>>>>>>>>>>>>> getSeoDefault: ".$code." | ".json_encode($field));

            $sections = $content->sections ?? [];

            // Set default if available
            if(!empty($field->default)) {
                // logDebug("Default: ".$field->default);
                $value = $field->default;
            }

            // Type: url
            if(!empty($field->type) && $field->type == 'url') {
                return action([FrontendController::class, 'pageBySlug'], ['slug' => $content->slug->$locale ?? '']) ?? $field->default ?? '';
            }

            // Type: locale
            if(!empty($field->type) && $field->type == 'locale') {
                return getLang();
            }

            // Type: locale-list
            if(!empty($field->type) && $field->type == 'locale-list') {
                //TODO: languages list!!!
                return '';
            }

            // Type: site-name
            if(!empty($field->type) && $field->type == 'site-name') {
                //TODO: site name!!!
                return '';
            }

            // Type: date-updated
            if(!empty($field->type) && $field->type == 'date-updated') {
                return $content->updated_at;
            }

            // Type: image & image-alt
            if(!empty($field->type) && ($field->type == 'image' || $field->type == 'image-alt')) {
//                logDebug("Searching for IMAGE code in sections: ".$field->code);

                foreach($sections as $section) {
                    if(($section->code ?? '') == $field->code && ($section->type ?? '') == 'images') {
                        $images = (array)$section->value;

                        if(empty($images[0])) return $value;

                        if($field->type == 'image-alt') {
//                            logDebug("ALT: ".$field->code);

                            if($field->locale ?? false) {
                                return data_get($images[0], $locale . ".alt", '');
                            }

                            return $images[0]->alt ?? $value;
                        }

                        $url = $value;

                        if(!empty($images[0]->id)) {
                            $img = FilesController::getImageUrlByName($images[0]->id);

                            if($img instanceof Exception)
                                $url = $value;
                            else
                                $url = $img["url"] ?? $value;
                        }

//                        logDebug("Found IMAGE section code: ".$section->code." | ".json_encode($images));

                        return $url;
                    }
                }
            }

            // Get value from section
            if(!empty($field->code)) {
//                logDebug("Searching for code in sections: ".$field->code);

                foreach($sections as $section) {
                    if(($section->code ?? '') == $field->code && (($section->type ?? '') == 'text' || ($section->type ?? '') == 'heading')) {
                        if($field->locale ?? false) {
                            $value = empty($section->value->$locale) ? $value : $section->value->$locale;
                        } else {
                            $value = empty($section->value) ? $value : $section->value;
                        }

//                        logDebug("Found section code: ".$section->code." | ".json_encode($value));

                        break;
                    }
                }
            }

            // Get value from section
            if(!empty($field->seo) && !empty($seo)) {
//                logDebug("Searching for SEO type: ".$field->seo);

                foreach($seo as $c => $f) {
//                    logDebug("Searching SEO code: ".$c);

                    if (($c ?? '') == $field->seo) {
                        if($field->locale ?? false) {
                            $value = empty($f->$locale) ? $value : $f->$locale;
                        } else {
                            $value = empty($f) ? $value : $f;
                        }

//                        logDebug("Found SEO type: " . json_encode($value));

                        break;
                    }
                }
            }

            if((is_array($value) || is_object($value)) && $field->locale ?? false) {
                $value = $value->$locale ?? '';
            }

            if(($field->max ?? 0) > 0) {
                $value = substr($value, 0, $field->max);
            }

            return $value ?? '';
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return '';
    }

}
