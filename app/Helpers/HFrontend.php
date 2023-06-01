<?php

namespace App\Helpers;

use App;
use App\Models\Backend\Configuration;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use Route;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Frontend\CMS\FrontendController;
use App\Http\Controllers\Backend\FilesController;
use Carbon\Carbon;

// use Modules\Terms\Entities\Term;
use App\Models\Backend\CMS\Content;
use App\Helpers\HContent;
use App\Helpers\HCache;


class HFrontend
{

    public static function getProjectPath(bool $dot = false)
    {
        return project_path($dot);
    }

    /**
     * Get image URL from specific page and section with codes.
     *
     * @param string $codePage
     * @param string $codeSection
     * @param [type] $codeImage
     * @param string|null $default
     * @param integer|null $w
     * @param integer|null $h
     * @param [type] $format
     * @param [type] $quality
     * @return string
     */
    public static function getCMSImage(string $codePage, string $codeSection, string $codeImage, string $default = null, int $w = null, int $h = null, $format = null, $quality = null): ?string
    {
        try {
            HCache::flush("fe_cms_item", $codePage . "_" . $codeSection . "_" . $codeImage);
            $item = HCache::remember("fe_cms_item", $codePage . "_" . $codeSection . "_" . $codeImage, function () use ($codePage, $codeSection, $codeImage) {
                $section = self::getSection($codePage, $codeSection);
                $item = self::getSectionItemWithCode($section, $codeImage);

                return $item;
            });

            if (empty($item)) return $default;

            return FilesController::getImageUrlById(getField($item, "id"), $w, $h, $format, $quality) ?? $default;
        } catch (QueryException|Exception|\Throwable $e) {
            logError("Error getting image: " . $e->getMessage());
            abort(404);
        }

        return $default;
    }

    public static function getCMSFile(string $codePage, string $codeSection, string $codeFile, string $default = null): ?string
    {
        try {
            $item = HCache::remember("fe_cms_item", $codePage . "_" . $codeSection . "_" . $codeFile, function () use ($codePage, $codeSection, $codeFile) {
                $section = self::getSection($codePage, $codeSection);
                $item = self::getSectionItemWithCode($section, $codeFile);

                return $item;
            });

            if (empty($item)) return $default;

            return FilesController::getFileUrlById(getField($item, "id")) ?? $default;
        } catch (QueryException|Exception|\Throwable $e) {
            logError("Error getting file: " . $e->getMessage());
            abort(404);
        }

        return $default;
    }

    public static function getSection(string $codePage, string $codeSection): ?object
    {
        // TODO: add to cache
        $content = self::getContentByCode($codePage);
        return self::getSectionWithCode($content, $codeSection);
    }

    public static function getSectionWithCode(object $content, string $code): ?object
    {
        foreach (getField($content, "sections", []) as $section) {
            if (getField($section, "code") == $code) return $section;
        }

        return null;
    }

    public static function getSectionItemWithCode(object $section, string $code): ?object
    {
        foreach (getField($section, "value", []) as $item) {
            if (getField($item, "code") == $code) return $item;
        }

        return null;
    }


    public static function getConfirmationLink($code)
    {
        return action([FrontendController::class, 'confirmEmail'], ['code' => $code]);
    }

    /**
     * Get content by code.
     *
     * @param string $code
     * @return object
     */
    public static function getContentByCode($code)
    {
        // Add cache
        return Content::whereCode($code)->first();
    }

    /**
     * Get sections by code.
     *
     * @param object $sections
     * @param string $code
     * @return object
     */
    public static function getObjectsByCode($objects, $codes = null)
    {
        if ($codes != null && !is_array($codes))
            return null;

        $codes = $codes ? array_values($codes) : $codes;
        $objectsByCode = [];

        foreach ($objects ?? [] as $object)
            if (!empty($object) && !empty($object->code) && ($codes == null || in_array($object->code, $codes)))
                $objectsByCode[$object->code] = $object;

        return !empty($objectsByCode) ? (object)$objectsByCode : null;
    }

    /**
     * Get section code.
     *
     * @param object $section
     * @return string
     */
    public static function getSectionCode(object $section): string
    {
        $value = "";

        try {
            $value = $section->code ?? '';
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    public static function getSectionValueWithCode(?object $content, string $code, string $lang = null): string
    {
        $section = self::getSectionWithCode($content, $code);
        return self::getSectionValue($section, $lang);
    }

    /**
     * Get translated value from section.
     *
     * @param object $section
     * @param string|null $lang
     * @return string
     */
    public static function getSectionValue(?object $section, string $lang = null): string
    {
        $value = "";

        try {
            $value = getFieldLang($section, "value", "", $lang);
        } catch (QueryException|Exception|\Throwable $e) {
            logError("Error getting section value: " . $e->getMessage());
        }

        return $value;
    }

    /**
     * Get section class. If empty returns the default value.
     *
     * @param object $section
     * @param string $default
     * @return string
     */
    public static function isSectionIgnore(object $section): bool
    {
        try {
            $classes = self::getSectionClass($section);
            return in_array("ignore", explode(" ", $classes));
        } catch (QueryException|Exception|\Throwable $e) {
            logError("Error checking if section is to ignore: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Get section class. If empty returns the default value.
     *
     * @param object $section
     * @param string $default
     * @return string
     */
    public static function getSectionClass(object $section, string $default = ''): string
    {
        $value = "";

        try {
            $value = $section->class ?? $default;
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    /**
     * Get section options. $field can be a nested parameter in the "->" format.
     * Does not consider language.
     *
     * @param object $section
     * @param string|null $field
     * @return string
     */
    public static function getSectionOptions(object $section, string $field = null): string
    {
        $value = "";

        try {
            if (empty($field)) {
                if (!isset($section->options)) {
                    return $value;
                }
                if (is_string($section->options)) return $section->options;
                return json_encode($section->options);
            }

            $options = json_decode(getField($section, "options", ""));
            $value = getField($options, $field);

            if (is_string($value)) return $value;
            return !empty($value) ? json_encode($value) : '';
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }


    /**
     * Get section heading. Default 'h1'
     *
     * @param object $section
     * @return string
     */
    public static function getSectionHeading(object $section): string
    {
        $value = "h1";
        $validValues = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        try {
            $value = !empty($section->value->heading) && in_array($section->value->heading, $validValues) ? $section->value->heading : $value;
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    /**
     * Get section button fields for specific language.
     *
     * @param object $section
     * @param string $field
     * @param string|null $lang
     * @return string
     */
    public static function getSectionButtonValue(object $section, string $field, string $lang = null): string
    {
        $value = "";

        try {
            if ($section->type != 'button') return '';

            if (empty($lang)) $lang = getLang();

            $value = $section->value->$lang->$field ?? '';
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    /**
     * Get section enabled items.
     *
     * @param object $section
     * @param string|null $lang
     * @return string
     */
    public static function getSectionEnabledItems(object $section, string $lang = null)
    {
        $values = [];

        try {
            if (empty($lang)) $lang = getLang();

            $values = $section->value;

            foreach ($values as $key => $value) {
                if (!($value->$lang->enabled ?? false)) {
                    // Item not enabled for current language
                    unset($values->$key);
                }
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $values;
    }

    /**
     * Get item list specific fields for the specific language
     *
     * @param object $item
     * @param string $field
     * @param string|null $lang
     * @return string
     */
    public static function getSectionItemField(object $item, string $field, string $lang = null): string
    {
        $value = "";

        try {
            if (empty($lang)) $lang = getLang();

            $value = $item->$lang->$field ?? '';
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    /**
     * Get image URL from item. Optional default image. Optional width, height, image format and quality.
     *
     * @param object $item
     * @param string|null $default
     * @param int|null $w
     * @param int|null $h
     * @param null $format
     * @param null $quality
     * @return string
     */
    public static function getSectionItemImage(object $item, string $default = null, int $w = null, int $h = null, $format = null, $quality = null): string
    {
        $value = $default;
        try {
            if (!empty(getField($item, "id"))) {
                $img = FilesController::getImageUrlByName($item->id, $w, $h, $format, $quality);
                return $img ?? $value;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError("Error: " . $e->getMessage());
        }

        return $value;
    }

    /**
     * @param object $item
     * @return string
     */
    public static function getSectionItemDownload(object $item): string
    {
        $value = '';

        try {
            if (!empty($item->id)) {
                $img = FilesController::getFileUrlById($item->id);
                return $img ?? $value;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        return $value;
    }

    /**
     * @param $menus
     * @param $menuArray
     * @return array|null
     */
    public static function findActiveMenu($menus, $menuArray)
    {
        if (!empty($menuArray)) return $menuArray;

        foreach ($menus ?? [] as $menu) {
            if ($menu['active']) {
                $menuArray[] = $menu;
                return $menuArray;
            }

            foreach ($menu['children'] ?? [] as $child) {
                if ($child['active']) {
                    $menuArray[] = $menu;
                    return $menuArray;
                }
            }
        }
        return null;
    }

    /**
     * @param $menus
     * @param $breadcrumbs
     * @return mixed
     */
    public static function buildBreadcrumbs($menus, $breadcrumbs)
    {
        foreach ($menus as $menu) {
            if ($menu['active']) {
                array_push($breadcrumbs, $menu);
            } else {
                if (!empty($menu['children'])) {
                    array_push($breadcrumbs, $menu);
                }
                $breadcrumbs = self::buildBreadcrumbs($menu['children'], $breadcrumbs);
            }
        }
        return $breadcrumbs;
    }

    /**
     * Display a listing of the topics by slug.
     * @param string
     * @return array
     */

    public static function getPrivacyPolicyAndTermsUse($options)
    {
        $terms = Term::where('code', $options)->get();
        return $terms;
    }

    public static function checkUserTerms($get = false)
    {
        $user = Auth::user();
        if (!isset($user))
            return true;

        $terms = Term::all()->toArray();
        $user_terms = \DB::table('user_terms')->where('user_id', $user->id)->whereNull('deleted_at')->get();

        $termsAux = collect($terms)->pluck('version', 'id');
        $notAccepted = [];

        foreach ($termsAux as $key => $term) {
            $exists = collect($user_terms)->where('term_id', '=', $key)->where('version', '=', $term)->first();

            if (!isset($exists))
                $notAccepted [] = $key;
        }

        if (!empty($notAccepted)) {
            $termsToReturn = Term::whereIn('id', $notAccepted)->get();
            //meter na session a variável json_encode($termsToReturn)

            if ($get) {
                return $termsToReturn;
            } else {
                return false;
            }
        }

        return true;
    }

    public static function getConfigurationByCode($code)
    {
        return (array)Configuration::whereCode($code)->first()->configurations;
    }

    public static function getContentConfigurations($contentType): object|null
    {
        return HContent::getContentConfigurations($contentType);
    }

    public static function getSeoDefault($code, $field, $content, $seo, $locale = null): string
    {
        return HContent::getSeoDefault($code, $field, $content, $seo, $locale);
    }


    /**
     * Get array formatted for select options from a Collection
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $collection The collection
     * @param string|null $labelField The field to get the option label from
     * @param bool $hasLang Indicate if field has language
     * @param array $validFields
     * @return array
     */
    public static function optionsFromCollection(\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $collection, string $labelField = null, bool $hasLang = true, array $validFields = []): array
    {
        try {
            $validFields = !empty($validFields) ? $validFields : ['name', 'title'];
            $array = [];

            if (!empty($labelField)) {
                array_unshift($validFields, $labelField);
            }

            foreach ($collection as $item) {
                foreach ($validFields as $validField) {
                    if (getField($item, $validField) != null) {
                        $array[getField($item, "id", 0)] = getField($item, "$validField" . ($hasLang ? '.' . getLang() : ''));
                        break;
                    }
                }
            }

            return $array;

        } catch (\Exception $e) {
            logError($e->getMessage());
        }

        return [];
    }

    /**
     * Get array formatted for select options from a Parameter
     *
     * @param object $parameter The parameter
     * @return array
     */
    public static function optionsFromParameter(object $parameter): array
    {
        try {
            if (getField($parameter, 'target') == 'model') {
                if (class_exists($model = getField($parameter, 'model'))) {
                    return self::optionsFromCollection($model::select('id', 'name', 'slug')->get());
                }
            }

            return array_combine(
                array_map(function ($element) {
                    return getField($element, 'code' ?? 'value');
                }, $parameter->options ?? []),
                array_map(function ($element) {
                    return getField($element, 'label.' . getLang()) ?? first_element(getField($element, 'label'));
                }, getField($parameter, 'options') ?? [])
            );

        } catch (\Exception $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
        }

        return [];
    }

    /**
     * Retrieve current action from route
     * @return string|null
     */
    public static function action(): ?string
    {
        return \Route::current()->getActionMethod() ?? null;
    }


    /**
     * Get tabs allowed to show
     * @return array
     */
    public static function getTabsToShow()
    {
        try {
            $profileConfigs = HFrontend::getConfigurationByCode('user_settings');
            return getField($profileConfigs, 'profile.tabs') ?? [];
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }

    /**
     * Get user submitted proposals
     * @return array
     */
    public static function getUserProposals()
    {
        try {
            $userProposals = [];
            $proposals = Topic::all();


            if (!empty($proposals)) {
                foreach ($proposals as $proposal) {
                    $proponents = getField($proposal, 'proponents');
                    if (!empty($proponents)) {
                        foreach ($proponents as $user) {
                            if (getField($user, 'user_id') == 2) {
                                $cb = CB::whereId(getField($proposal, 'cb_id'))->first();
                                $userProposals[getField($proposal, 'id')]['title'] = getField($proposal, 'title.' . getLang());
                                $userProposals[getField($proposal, 'id')]['category'] = self::getProposalCategory($cb, getField($proposal, 'parameters.category'));
                                $userProposals[getField($proposal, 'id')]['created_at'] = Carbon::parse(getField($proposal, 'created_at'))->format('d-m-Y');
                            }
                        }
                    }
                }
            }

            return $userProposals ?? [];
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }

    /**
     * Get user details according to profile tab
     * @param $localUser
     * @param string $tab
     * @return array
     */
    public static function getUserDetails($localUser, string $tab)
    {
        try {
            $keycloakUsers = HKeycloak::getUsers();
            foreach ($keycloakUsers ?? [] as $kUser) {
                if (getField($kUser, 'id') == getField($localUser, 'uuid')) {
                    $keycloakUser = $kUser;
                    break;
                }
            }

            if ($tab === 'generic') {
                $user['firstName'] = getField($keycloakUser, 'firstName') ?? '-';
                $user['lastName'] = getField($keycloakUser, 'lastName') ?? '-';
                $user['email'] = getField($keycloakUser, 'email');
            }
            if ($tab === 'generic' || $tab === 'details') {
                $user['parameters'] = json_decode(getField($localUser, 'parameters')) ?? '-';
            }

            $user['id'] = getField($localUser, 'id');
            $user['name'] = getField($localUser, 'name') ?? '-';
            $user['emailVerified'] = Carbon::parse(getField($localUser, 'email_verified_at'))->format('d-m-Y H:i') ?? '-';

            return $user;

        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }


    public static function getProposalCategory($cb, $category)
    {
        try {
            $categoryLabel = [];
            foreach ($cb->parameters ?? [] as $parameter) {
                if ($parameter->code == 'category') {
                    foreach ($parameter->options as $categories) {
                        foreach ($category as $c) {
                            if (getField($categories, 'code') == $c) {
                                $categoryLabel[] = getField($categories, 'label.' . getLang());
                            }
                        }

                    }
                }
            }
            return $categoryLabel;
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }

    /**
     * Whether a request was made from the front end or not
     * @return bool
     */
    public static function isPublicRequest(): bool
    {
        return (request()->site ?? null) instanceof Site;
    }
}
