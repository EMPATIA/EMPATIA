<?php

namespace App\Helpers\Empatia\Cbs;

use App;
use App\Helpers\HBackend;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Vote;
use Illuminate\Support\Str;
use Modules\Users\Entities\Role;

class HCb
{
    /**
     * Get Cb view
     * @param string $view The view to search for
     * @param object|string $cb The Cb
     * @return string $viewPath
     */
    public static function getView($view, $type = 'default')
    {
        try {
            if ($type instanceof Cb) {
                if ($type->type == 'city-poll') {
                    $type = 'default';
                } else {
                    $type = $type->type ?? 'default';
                }
            }

            if ($type != 'default') {
                $types = HCb::getCbTypes();
                if (!getField($types, $type)) {
                    abort(404);
                }
            }
            $viewPath = "empatia::cbs.cbs.$type.$view";
            if (!view()->exists($viewPath)) {
                $viewPath = "empatia::cbs.cbs.default.$view";
            }
            return $viewPath;
        } catch (Exception $e) {
            logError('error getting view; ' . $e->getMessage());
            dd($e);
        }
    }

    public static function getParametersRules($parameters)
    {
        if (!is_array($parameters) || is_object($parameters))
            return null;

        $rules = [];

        foreach ($parameters ?? [] as $parameter) {
            $rules[$parameter->code] = explode('|', $parameter->rules);

            // if mandatory parameter, make sure it has 'required' rule
            if (($parameter->mandatory ?? false) && (!partial_in_array('required', $rules[$parameter->code]) && !partial_in_array('required_if', $rules[$parameter->code]))) {
                $rules[$parameter->code][] = 'required';
            }
        }

        return $rules;
    }

    public static function getCbByCode($code): ?object
    {
        try {
            if (is_string($code) && $code != "") {
                return Cb::whereCode($code)->first();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('cb not found: ' . json_encode($e->getMessage()));
        }
        return null;
    }

    public static function getCb($cbId): ?object
    {
        try {
            if (empty($cbId) || $cbId == 0) {
                return null;
            }
            return Cb::find($cbId);
        } catch (QueryException|Exception|\Throwable $e) {
            logError('content config: ' . json_encode($e->getMessage()));
        }

        return null;
    }

    public static function getCbsByIds($ids): array
    {
        try {
            $cbs = [];
            if (is_numeric($ids)) {
                $ids = [$ids];
            }

            if (is_array($ids)) {
                return Cb::whereIn('id', $ids)->get();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('cb_year not found: ' . json_encode($e->getMessage()));

        }
        return $cbs;
    }

    public static function getCbParametersByVersion($cb, $version)
    {
        if ($version == $cb->version)
            return $cb->parameters;
        foreach ($cb->versions as $v) {
            if ($v->version == $version)
                return $v->parameters;
        }
    }

    public static function getCbTypes(): array
    {
        $cbTypes = [];
        $types = getField(HBackend::getConfigurationByCode('cb_settings'), 'types');
        foreach ($types ?? [] as $key => $type) {
            $cbTypes[$key] = $type->name->{getLang()};
        }
        return $cbTypes;
    }

    /**
     * Get Cb type slug
     * @param string|null $type The Cb type
     * @param string|null $lang The locale to search for
     * @return  string|null
     */
    public static function getCbTypeSlug(string $type, string $lang = null): ?string
    {
        $slug = null;

        logDebug("Init ('$type')");

        try {
            if (empty($type)) {
                throw new \Exception("Type cannot be empty nor null");
            }

            $cbTypes = getField(HBackend::getConfigurationByCode('cb_settings'), 'types');
            $typeData = getField($cbTypes, $type);

            if (!$typeData) {
                throw new \Exception("Invalid Cb type");
            }

            if ($lang) {
                $slug = getField($typeData, "slug.$lang");

                if ($slug) {
                    return $slug;
                } else {
                    throw new \Exception("Slug not found for the provided locale ('$lang')");
                }
            } else {
                $slug = getField($typeData, 'slug.' . getLang());

                if ($slug) {
                    return $slug;
                } else {
                    logDebug("Slug not found for the current locale (" . getLang() . ")");
                }
            }

            logDebug("Searching all locales");
            foreach (getLanguages() as $language) {
                $slug = getField($typeData, 'slug.' . $language['locale']);

                if ($slug) {
                    return $slug;
                }
            }

            throw new \Exception("Slug not found");

        } catch (\Exception $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
        }

        return $slug;
    }

    /**
     * Get Cb config
     * @param Cb $cb The Cb
     * @param string $code The config code
     * @return  object|null
     */
    public static function getConfig(Cb $cb, string $code): ?object
    {
        $config = null;

        try {
            $configs = getField($cb, 'data.configurations');
            $config = findObjectByProperty('code', $code, $configs) ?: null;

        } catch (\Exception $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
        }

        return $config;
    }

    public static function getMatchingOptionText($cb, $parameterId, $optionId)
    {
        try {
            if (!$cb instanceof Cb)
                return null;
            foreach ($cb->parameters as $parameter) {
                if ($parameter->id == $parameterId) {
                    if (!empty($parameter->options)) {
                        foreach ($parameter->options as $opt) {
                            if ($opt->id == $optionId) {
                                return $opt->value->{getLang()};
                            }
                        }
                    }
                }
            }
            return null;
        } catch (\Exception $e) {
            logError('getting id of option');
            return null;
        }
    }

    public static function getMatchingOptionId($cb, $parameterId, $option)
    {
        try {
            if (!$cb instanceof Cb)
                return null;
            foreach ($cb->parameters as $parameter) {
                if ($parameter->id == $parameterId) {
                    if (!empty($parameter->options)) {
                        foreach ($parameter->options as $opt) {
                            if ($opt->value->{getLang()} == $option) {
                                return $opt->id;
                            }
                        }
                    }
                }
            }
            return null;
        } catch (\Exception $e) {
            logError('getting id of option');
            return null;
        }
    }

    public static function getParameterByCode($parameters, $code)
    {
        foreach ($parameters as $parameter) {
            if ($parameter->code == $code) {
                return $parameter;
            }

        }
    }

    public static function getParameterById($parameters, $id)
    {
        foreach ($parameters as $parameter) {
            if ($parameter->id == $id) {
                return $parameter;
            }

        }
    }

    /**
     * Method to map a parameter option ID to the option value (in the selected language).
     * If the parameter or option are not identified return the received option value.
     *
     * @param object $cb CB to search the parameters configuration
     * @param string $paramCode Parameter code to search for
     * @param $option $option int | string with the option value to match
     *
     * @return string String with the parameter value to display
     */
    public static function mapParameterOptionByCode(object $cb, string $paramCode, $option, $params = null, $defaultValue = ''): string
    {
        try {
            // Check if CB is valid
            if (!$cb instanceof Cb)
                return (string)$option;

            // Find parameter with code
            foreach (getField($cb, "parameters", []) as $param) {
                if (getField($param, "code") == $paramCode) {
                    // Found parameter

                    // Break if no option field
                    if (empty(getField($param, "options", [])))
                        break;

                    // Find option with ID
                    foreach (getField($param, "options", []) as $opt) {
                        if (getField($opt, "id") == $option) {
                            if (isset($opt->input)) {
                                return getFieldLang($params, $opt->input, $defaultValue);
                            }
                            return getFieldLang($opt, "value", $defaultValue);
                        }
                    }

                    // Stop if option not found
                    break;
                }
            }

            // Return option received value
            return (string)$option;
        } catch (Exception $e) {
            logError('Error mapping option to value: ' . $e->getMessage());
        }

        // On failure return empty string
        return $defaultValue;
    }

    /**
     * Get a list of valid vote submitters id's.
     * @param Cb|int $cb
     * @return Collection
     */
    public static function getValidVoteSubmitters(Cb|int $cb): Collection
    {
        if (is_numeric($cb)) {
            $cb = Cb::find($cb);
        }

        if (!($cb instanceof Cb)) {
            return collect();
        }

        // TODO: make this function work with any cb (configurable number of votes, weights, etc)

        return Vote::where('user_id', '!=', null)
            ->whereCbId($cb->id)
            ->where('created_at', '>=', Carbon::parse($cb->start_date))
            ->where('created_at', '<', Carbon::parse($cb->end_date)->addDay())
            ->where('details->submitted', true)
            ->select(\DB::raw('*, count(*) as votes'))
            ->groupBy('user_id')
            ->get()
            ->where('votes', '=', 5)
            ->pluck('user_id');
    }



    /**
     * Get a cb status.
     * @param Cb $cb    The CB template
     * @return array    The status
     */
    public static function getCbStatus(Cb $cb): string {
        $status = 'undefined';

        try {
            $now = Carbon::now();
            $startDate = Carbon::parse($cb->start_date);
            $endDate = Carbon::parse($cb->end_date);

            if( $now < $startDate ){
                $status = 'upcoming';
            } else if( $now > $endDate ){
                $status = 'closed';
            } else {
                $status = 'open';
            }

        } catch (\Exception $e) {
            logError( $e->getMessage() );
        }

        return $status;
    }

    /**
     * Display a listing of the topics by cb.
     * @param string
     * @return array
     */
    public static function getTopics($cb)
    {
        $topics = [];
        $categoryIcons = [
            'transportation'        => '<i class="fa-solid fa-truck-front me-2"></i>',
            'recycling'             => '<i class="fa-solid fa-recycle me-2"></i>',
            'green_spaces'          => '<i class="fa-solid fa-tree me-2"></i>',
            'energetic_transition'  => '<i class="fa-solid fa-bolt me-2"></i>',
        ];

        $topicModalCategoryIcons = [  'transportation' => '<i class="fa-solid fa-truck-front fa-2x me-1 text-primary "></i>',
            'recycling' => '<i class="fa-solid fa-recycle fa-2x me-2 text-primary"></i>',
            'green_spaces' => '<i class="fa-solid fa-tree fa-2x me-2 text-primary"></i>',
            'energetic_transition' => '<i class="fa-solid fa-bolt fa-2x me-2 text-primary"></i>'
        ];
        try {
            $tps = Topic::where('cb_id', $cb->id)->get();

            foreach ($tps as $key => $topic) {
                $coverImg = null;
                $gallery = [];
                $videos = [];
                $files = [];
                $params = [];
                foreach ($cb->parameters ?? [] as $parameter) {
                    if (in_array($parameter->type, ['image', 'images', 'file'])) {
                        $arr = getField($topic->parameters, $parameter->code);
                        if (!empty($arr)) {
                            if ($parameter->type == 'image' && $parameter->code == 'cover_img') {
                                $coverImg = FilesController::getImage(is_string($arr) ? $arr : '', 600);

                            } elseif ($parameter->type == 'images' && $parameter->code == 'gallery') {
                                foreach ($arr as $img) {
                                    $gallery[] = FilesController::getImage($img);
                                }
                            } else {
                                if ($parameter->code == 'video') {
                                    foreach ($arr as $vid) {
                                        $file = \Disk::get($vid);
                                        $videos[] = is_array($file) ? $file : null;
                                    }
                                } else {
                                    foreach ($arr as $f) {
                                        $file = \Disk::get($f->id);
                                        $files[] = is_array($file) ? $file : null;
                                    }
                                }
                            }
                        }

                    } elseif (in_array($parameter->type, ['text', 'textarea', 'date'])) {
                        $params[$parameter->code] = [
                            'label' => getField($parameter, 'title.' . getLang()) ?? __('frontend::voting.topic.parameter.' . $parameter->code),
                            'value' => getFieldLang($topic->parameters, $parameter->code)
                        ];

                    } elseif (in_array($parameter->type, ['select', 'checkbox', 'radiobutton'])) {
                        if (!empty(getField($topic->parameters,$parameter->code))) {
                            foreach ($parameter->options as $option) {
                                if ($parameter->type == 'checkbox') {
                                    foreach (getField($topic->parameters, $parameter->code) ?? [] as $param) {
                                        if ($option->value == $param) {
                                            $params[$parameter->code] = [
                                                'title' => getFieldLang($parameter, 'title'),
                                                'label' => getFieldLang($option, 'label'),
                                                'value' => getField($option, 'value')
                                            ];
                                            if($parameter->code == 'category'){
                                                $icon = getField($categoryIcons, getField($option, 'value'), '');
                                            }
                                        }
                                    }
                                } elseif ($option->value == getField($topic->parameters, $parameter->code)) {
                                    $params[$parameter->code] = [
                                        'title' => getFieldLang($parameter, 'title'),
                                        'label' => getFieldLang($option, 'label'),
                                        'value' => getField($option, 'value')
                                    ];
                                    if($parameter->code == 'category'){
                                        $icon = getField($categoryIcons, getField($option, 'value'), '');
                                        $modalIcon = getField($topicModalCategoryIcons, getField($option, 'value'), '');
                                    }
                                }

                            }
                        }else {
                            $params[$parameter->code] = [
                                'title' => getFieldLang($parameter, 'title'),
                                'label' => null,
                                'value' => null
                            ];

                        }
                    }
                }
                $topics[] = [
                    'id' => $topic->id,
                    'number' => $topic->number,
                    'description' => getFieldLang($topic, 'content'),
                    'title' => getFieldLang($topic, 'title'),
                    'owner' => data_get($topic->owner, 'name', null),
                    'cover_img' => $coverImg,
                    'slug' => data_get($topic, 'slug'),
                    'parameters' => $params,
                    'gallery' => $gallery,
                    'videos' => $videos,
                    'files' => $files,
                    'icon' => $icon ?? null,
                    'modalIcon' => $modalIcon ?? null
                ];
            }
        } catch (\Exception $e) {
            logError($e->getMessage());
        }

        return $topics;
    }

    /**
     * Validate a cb type.
     * 
     */
    public static function validateType($type)
    {
        try {
            $configs = getField(HBackend::getConfigurationByCode('cb_settings'), 'types');
            if (!getField($configs, $type)) {
                throw new \Exception("Invalid CB type: $type");
            }

        } catch (\Exception $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
            abort(404);
        }
        return $type;
    }

    /**
     * Whether a parameter has multiple locales or not
     * @param $parameter
     * @return bool
     */
    public static function isParameterMultilang($parameter) : bool
    {
        return
            isset($parameter->title)
            && (($parameter->multilang ?? false)
                || (
                    empty($parameter->options)
                    && !in_array($parameter->type ?? null, [
                        'image', 'file', 'json', 'label', 'date', 'radiobutton'
                    ])
                    && ($parameter->multilang ?? false)
                )
            );
    }


    /**  PROJECT  **/

    public static function setCbSlug($data): object
    {
        $slugs = [];
        try {
            $cbs = Cb::withTrashed()->get();
            foreach (getLanguages() as $language) {
                $locale = $language['locale'];
                if (empty($data->slug)) {
                    $baseSlug = Str::slug($data->input('title->' . $locale));
                    $slug = $baseSlug;

                    for ($i = 0; $cbs->where("slug.{$locale}", $slug)->first(); $i++)
                        $slug = "{$baseSlug}-" . ($i + 1);

                    $slugs[$language['locale']] = $slug;
                }
            }

        } catch (\Illuminate\Database\QueryException|Exception|\Throwable $e) {
            logError('slug: ' . json_encode($e->getMessage()));
        }
        return (object)$slugs;
    }

    public static function setCbCode($data, $action, $id = null)
    {
        try {
            if($action == 'store'){
               $code = Str::slug($data->input("code"));

                if (Cb::where('code', $code)->count() > 0) {
                    $code = $code . "-" . rand(1111, 9999);
                }

            }else{
                $cb = Cb::findOrFail($id);
                $code = Str::slug($data->input("code"));

                // Check for duplicate names
                if (Cb::where('code', $code)->count() > 0) {
                    $exist_cb= Cb::where('code', $code)->first();
                    if($exist_cb->id != $cb->id){
                        $code = $code . "-" . rand(1111, 9999);
                    }
                }
            }
            return $code;
        } catch (\Illuminate\Database\QueryException|Exception|\Throwable $e) {
            logError('code: ' . json_encode($e->getMessage()));
        }
    }
}
