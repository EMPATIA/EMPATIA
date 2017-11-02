<?php

namespace App\Http\Controllers;

use App\HomePageConfiguration;
use App\HomePageType;
use App\One\One;
use App\Site;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class HomePageConfigurationsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Home Page Configuration",
 *   description="Everything about Home Page Configurations",
 * )
 *
 *  @SWG\Definition(
 *      definition="homePageConfigurationErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="translations",
 *   type="object",
 *   allOf={
 *     @SWG\Schema(
 *           @SWG\Property(property="language_code", format="string", type="string"),
 *           @SWG\Property(property="value", format="string", type="string")
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="homePageConfigurationUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"home_page_type_key", "value", "translations"},
 *           @SWG\Property(property="value", format="string", type="string"),
 *           @SWG\Property(property="home_page_type_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="translations",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/translations")
 *           )
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="homePageConfigurationReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="home_page_configuration_key", format="string", type="string"),
 *           @SWG\Property(property="home_page_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="site_id", format="integer", type="integer"),
 *           @SWG\Property(property="group_name", format="string", type="string"),
 *           @SWG\Property(property="group_key", format="string", type="string"),
 *           @SWG\Property(property="value", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="homePageConfigurationDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class HomePageConfigurationsController extends Controller
{
    protected $required = [
        'store' => [
            'site_key',
            'home_page_type_key',
            'translations'
        ],
        'update' => [
            'home_page_type_key',
            'translations'
        ]
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $homePageConfigurations = HomePageConfiguration::all();
            foreach ($homePageConfigurations as $homePageConfiguration) {
                if($homePageConfiguration->value == null) {
                    if (!($homePageConfiguration->translation($request->header('LANG-CODE')))) {
                        if (!$homePageConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }

            return response()->json(['data' => $homePageConfigurations], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configurations'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/homePageConfiguration/{home_page_configuration_key}",
     *  summary="Show a Home Page Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Configuration"},
     *
     *  @SWG\Parameter(
     *      name="home_page_configuration_key",
     *      in="path",
     *      description="Home Page Configuration Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Home Page Configuration data",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Home Page Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Home Page Configuration",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $homePageConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $homePageConfigurationKey)
    {
        try {
            $homePageConfiguration = HomePageConfiguration::whereHomePageConfigurationKey($homePageConfigurationKey)->with('homePageType', 'site')->firstOrFail();

            if (!($homePageConfiguration->translation($request->header('LANG-CODE')))) {
                if (!$homePageConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($homePageConfiguration, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $homePageConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $homePageConfigurationKey)
    {
        try {
            $homePageConfiguration = HomePageConfiguration::whereHomePageConfigurationKey($homePageConfigurationKey)->with('homePageType', 'site')->firstOrFail();

            $homePageConfiguration->translations();

            return response()->json($homePageConfiguration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
/*    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $site = Site::where('key',$request->json('site_key'))->firstOrFail();
            $homePageType = HomePageType::whereHomePageTypeKey($request->json('home_page_type_key'))->firstOrFail();

            do {
                $rand = str_random(32);
                $key = "";

                if (!($exists = HomePageConfiguration::whereHomePageConfigurationKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $homePageConfiguration = HomePageConfiguration::create(
                [
                    'home_page_configuration_key' => $key,
                    'home_page_type_id' => $homePageType->id,
                    'site_id' => $site->id,
                    'value' => is_null($request->json('value')) ? null : $request->json('value')
                ]
            );

            if(!is_null($request->json('translations'))){
                foreach ($request->json('translations') as $translation) {
                    if (isset($translation['language_code']) && (isset($translation['value']) || !empty($homePageConfiguration->value))) {
                        $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'value' => empty($homePageConfiguration->value) ? $translation['value'] : ""
                            ]
                        );
                    }
                }
            }
            return response()->json($homePageConfiguration, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }*/

    /**
     * @SWG\Put(
     *  path="/homePageConfiguration/{home_page_configuration_key}",
     *  summary="Update a Home Page Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Configuration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Home Page Configuration Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="home_page_configuration_key",
     *      in="path",
     *      description="Home Page Configuration Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Home Page Configuration",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Home Page Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Home Page Configuration",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $homePageConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $homePageConfigurationKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $translationsOld = [];
            $translationsNew = [];

            $homePageConfiguration = HomePageConfiguration::whereHomePageConfigurationKey($homePageConfigurationKey)->firstOrFail();
            $homePageType = HomePageType::whereHomePageTypeKey($request->json('home_page_type_key'))->firstOrFail();

            $homePageConfiguration->home_page_type_id = $homePageType->id;
            $homePageConfiguration->value = $request->json('value');
            $homePageConfiguration->save();

            $translationsId = $homePageConfiguration->homePageConfigurationTranslation()->get();
            foreach ($translationsId as $translationId) {
                $translationsOld[] = $translationId->id;
            }

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && (isset($translation['value']) || !empty($homePageConfiguration->value))) {
                    $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($homePageConfigurationTranslation)) {
                        $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'value' => empty($homePageConfiguration->value) ? $translation['value'] : ""
                            ]
                        );
                    } else {
                        $homePageConfigurationTranslation->value = empty($homePageConfiguration->value) ? $translation['value'] : "";
                        $homePageConfigurationTranslation->save();
                    }
                }
                $translationsNew[] = $homePageConfigurationTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $homePageConfiguration->homePageConfigurationTranslation()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($homePageConfiguration, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update Home Page Configuration'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/homePageConfiguration/{home_page_configuration_key}",
     *  summary="Delete a Home Page Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Configuration"},
     *
     * @SWG\Parameter(
     *      name="home_page_configuration_key",
     *      in="path",
     *      description="Home Page Configuration Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Home Page Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Home Page Configuration",
     *      @SWG\Schema(ref="#/definitions/homePageConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $homePageConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $homePageConfigurationKey)
    {
        ONE::verifyToken($request);

        try {
            $homePageConfiguration = HomePageConfiguration::whereHomePageConfigurationKey($homePageConfigurationKey)->firstOrFail();

            $homePageConfiguration->homePageConfigurationTranslation()->delete();
            $homePageConfiguration->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Home Page Configuration'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function sitePages(Request $request, $siteKey)
    {
        ONE::verifyToken($request);

        try {
            $site = Site::where('key',$siteKey)->firstOrFail();
            $homePageConfigurations = $site->homePageConfigurations()->get();

            foreach ($homePageConfigurations as $homePageConfiguration) {
                if (!($homePageConfiguration->translation($request->header('LANG-CODE')))) {
                    if (!$homePageConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(['data' => $homePageConfigurations], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site Home Page Configurations'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeGroup(Request $request)
    {
        ONE::verifyToken($request);

        try {
            $homePageConfigs = $request->json('home_page_configurations');
            $groupName = $request->json('group_name');

            do {
                $rand = str_random(32);
                $groupKey = "";

                if (!($exists = HomePageConfiguration::whereGroupKey($rand)->exists())) {
                    $groupKey = $rand;
                }
            } while ($exists);

            foreach ($homePageConfigs as $homePageConfig) {
                if (isset($homePageConfig['site_key']) && isset($homePageConfig['home_page_type_key']) && (isset($homePageConfig['value']) || isset($homePageConfig['translations']))) {

                    $site = Site::where('key',$homePageConfig['site_key'])->firstOrFail();
                    $homePageType = HomePageType::whereHomePageTypeKey($homePageConfig['home_page_type_key'])->firstOrFail();

                    do {
                        $rand = str_random(32);
                        $key = "";

                        if (!($exists = HomePageConfiguration::whereHomePageConfigurationKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $homePageConfiguration = HomePageConfiguration::create(
                        [
                            'home_page_configuration_key' => $key,
                            'home_page_type_id' => $homePageType->id,
                            'site_id' => $site->id,
                            'value' => isset($homePageConfig['value']) ? $homePageConfig['value'] : null,
                            'group_name' => $groupName,
                            'group_key' => $groupKey
                        ]
                    );

                    if(isset($homePageConfig['translations'])) {
                        foreach ($homePageConfig['translations'] as $translation) {
                            if (isset($translation['language_code']) && (isset($translation['value']) || !empty($homePageConfiguration->value))) {
                                $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->create(
                                    [
                                        'language_code' => $translation['language_code'],
                                        'value' => empty($homePageConfiguration->value) ? $translation['value'] : ""
                                    ]
                                );
                            }
                        }
                    }
                }
            }

            return response()->json(['group_name' => $groupName,'group_key' => $groupKey], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listGroups(Request $request, $siteKey)
    {
        try {
            $site = Site::where('key',$siteKey)->firstOrFail();
            $response = [];

            $groups = HomePageConfiguration::whereSiteId($site->id)->pluck('group_name', 'group_key')->each(function ($item, $key) use(&$response) {
                $response[] = array('group_name' => $item, 'group_key' => $key);
            });

            return response()->json(['data' => $response], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configurations'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $groupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function showGroup(Request $request, $groupKey)
    {
        try {
            $response = [];
            $homePageConfigurations = HomePageConfiguration::whereGroupKey($groupKey)->get();
            $groupName = $homePageConfigurations[0]->group_name;

            foreach ($homePageConfigurations as $homePageConfiguration) {
                if(is_null($homePageConfiguration->value)) {
                    if (!($homePageConfiguration->translation($request->header('LANG-CODE')))) {
                        if (!$homePageConfiguration->translation($request->header('LANG-CODE-DEFAULT'))){
                            if (!$homePageConfiguration->translation('en'))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }
                $homePageConfiguration['home_page_type'] = HomePageType::findOrFail($homePageConfiguration->home_page_type_id);
            }

            $homePageTypeParent = HomePageType::find($homePageConfigurations[0]->home_page_type->parent_id);

            $response['group_name'] = $groupName;
            $response['group_key'] = $groupKey;
            $response['home_page_configurations'] = $homePageConfigurations;
            $response['home_page_type_parent'] = $homePageTypeParent;
            $response['site'] = $homePageConfigurations[0]->site()->firstOrFail();

            return response()->json($response, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $groupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function editGroup(Request $request, $groupKey)
    {
        try {
            $response = [];

            $homePageConfigurations = HomePageConfiguration::with('homePageType')->whereGroupKey($groupKey)->get()->keyBy('homePageType.home_page_type_key');

            $groupName = $homePageConfigurations->first()->group_name;

            $homePageTypeParent = HomePageType::whereId($homePageConfigurations->first()->homePageType->parent_id)->first();

            foreach ($homePageConfigurations as $homePageConfiguration) {
                $homePageConfiguration->translations();
            }

            $response['group_name'] = $groupName;
            $response['group_key'] = $groupKey;
            $response['data'] = $homePageConfigurations;
            $response['home_page_type_parent_key'] = is_null($homePageTypeParent) ? null : $homePageTypeParent->home_page_type_key;

            return response()->json($response, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $groupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGroup(Request $request, $groupKey)
    {
        ONE::verifyToken($request);

        try {
            $homePageConfigs = $request->json('home_page_configurations');
            $groupName = HomePageConfiguration::whereGroupKey($groupKey)->first()->group_name;

            foreach ($homePageConfigs as $homePageConfig) {

                if (isset($homePageConfig['home_page_type_key']) && (isset($homePageConfig['value']) || isset($homePageConfig['translations']))) {

                    $homePageType = HomePageType::whereHomePageTypeKey($homePageConfig['home_page_type_key'])->firstOrFail();

                    try {
                        $homePageConfiguration = HomePageConfiguration::whereGroupKey($groupKey)->whereHomePageTypeId($homePageType->id)->firstOrFail();

                        if (!empty($homePageConfig['value'])) {
                            $homePageConfiguration->value = $homePageConfig['value'];
                            $homePageConfiguration->save();
                        } elseif (!empty($homePageConfig['translations'])) {
                            $translationsOld = [];
                            $translationsNew = [];

                            $translationsId = $homePageConfiguration->homePageConfigurationTranslation()->get();
                            foreach ($translationsId as $translationId) {
                                $translationsOld[] = $translationId->id;
                            }

                            foreach ($homePageConfig['translations'] as $translation) {
                                if (isset($translation['language_code']) && (isset($translation['value']) || !empty($homePageConfiguration->value))) {
                                    $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->whereLanguageCode($translation['language_code'])->first();
                                    if (empty($homePageConfigurationTranslation)) {
                                        $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->create(
                                            [
                                                'language_code' => $translation['language_code'],
                                                'value' => empty($homePageConfiguration->value) ? $translation['value'] : ""
                                            ]
                                        );
                                    } else {
                                        $homePageConfigurationTranslation->value = empty($homePageConfiguration->value) ? $translation['value'] : "";
                                        $homePageConfigurationTranslation->save();
                                    }
                                }
                                $translationsNew[] = $homePageConfigurationTranslation->id;
                            }

                            $deleteTranslations = array_diff($translationsOld, $translationsNew);
                            foreach ($deleteTranslations as $deleteTranslation) {
                                $deleteId = $homePageConfiguration->homePageConfigurationTranslation()->whereId($deleteTranslation)->first();
                                $deleteId->delete();
                            }
                        }
                    } catch (Exception $e){
                        do {
                            $rand = str_random(32);
                            $key = "";

                            if (!($exists = HomePageConfiguration::whereHomePageConfigurationKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $homePageConfiguration = HomePageConfiguration::create(
                            [
                                'home_page_configuration_key' => $key,
                                'home_page_type_id' => $homePageType->id,
                                'site_id' => HomePageConfiguration::whereGroupKey($groupKey)->first()->site_id,
                                'value' => isset($homePageConfig['value']) ? $homePageConfig['value'] : null,
                                'group_name' => $groupName,
                                'group_key' => $groupKey
                            ]
                        );

                        if(isset($homePageConfig['translations'])) {
                            foreach ($homePageConfig['translations'] as $translation) {
                                if (isset($translation['language_code']) && (isset($translation['value']) || !empty($homePageConfiguration->value))) {
                                    $homePageConfigurationTranslation = $homePageConfiguration->homePageConfigurationTranslation()->create(
                                        [
                                            'language_code' => $translation['language_code'],
                                            'value' => empty($homePageConfiguration->value) ? $translation['value'] : ""
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }
            }

            return response()->json(['group_name' => $groupName,'group_key' => $groupKey], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $groupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyGroup(Request $request, $groupKey)
    {
        ONE::verifyToken($request);

        try {
            $configurationsGroup = HomePageConfiguration::whereGroupKey($groupKey)->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete Home Page Configurations'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configurations not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteConfigurations(Request $request)
    {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->first();

            $homePageConfigurations = $site->homePageConfigurations()->pluck('home_page_type_id')->toArray();
            $types = HomePageType::with('homePageTypeSons')->whereParentId(null)->get();

            $typesSite = [];
            foreach ($types as $type){

                if(sizeof($type->homePageTypeSons) == 0){
                    if(in_array( $type->id, $homePageConfigurations)){
                        $typesSite[] = $type;
                    }
                }else{
                    if(in_array($type->homePageTypeSons->first()->id, $homePageConfigurations)){
                        $typesSite[] = $type;
                    }
                }
            }

            $groups = [];
            foreach ($typesSite as $type){
                $group = null;
                if(sizeof($type->homePageTypeSons) == 0){
                    $configs = $site->homePageConfigurations()->whereHomePageTypeId($type->id)->get()->keyBy('group_key');
                    foreach ($configs as $key => $config){
                        if(!$config->translation($request->header('LANG-CODE'))){
                            $config->translation($request->header('LANG-CODE-DEFAULT'));
                        }
                        $group[$key][$type->code] = $config->value;
                    }
                }else{
                    foreach ($type->homePageTypeSons as $son) {
                        $configs = $site->homePageConfigurations()->whereHomePageTypeId($son->id)->get()->keyBy('group_key');
                        foreach ($configs as $key => $config){
                            if(!$config->translation($request->header('LANG-CODE'))){
                                $config->translation($request->header('LANG-CODE-DEFAULT'));
                            }
                            $group[$key][$son->code] = $config->value;
                            $group[$key]['group_name'] = $config->group_name;
                        }
                    }
                }
                $groups[$type->code]= $group;
            }
            return response()->json(['data' => $groups]);

        } catch (Exception $e) {
            return response()->json(['error' => 'failed to retrieve Site Configurations'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
