<?php

namespace App\Http\Controllers;

use App\Site;
use App\SiteConf;
use App\SiteConfGroup;
use App\SiteConfTranslation;
use App\SiteSiteConfs;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

/**
 * Class SiteConfController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Site Configuration",
 *   description="Everything about Site configurations",
 * )
 *
 *  @SWG\Definition(
 *      definition="siteConfigurationErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationTranslations",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "language_code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="language_code", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationRequest",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "group", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="group", format="integer", type="integer"),
 *           @SWG\Property(
 *              property="translations",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/siteConfigurationTranslations")
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"request"},
 *           @SWG\Property(
 *              property="request",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/siteConfigurationRequest")
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="site_conf_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="site_conf_group_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="siteConfigurationDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class SiteConfController extends Controller
{
    protected $required = [
        'store' => [],
        'update' => [],
    ];

    /**
     * Returns the list of Site Confs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $types = SiteConf::all();
            foreach ($types as $type) {
                if (!($type->translation($request->header('LANG-CODE')))) {
                    if (!$type->translation($request->header('LANG-CODE-DEFAULT'))) {
                        $type->name = $type->code;
                        $type->description = "";
                    }
                }
                $type->siteConfValues($type->id);
            }

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Confs'], 500);
        }
    }

    /**
     * Returns the list of Site Confs By Group Key
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByGroupKey(Request $request,$groupKey)
    {
        try {
            $site = Site::where('key',$request->header('X-SITE-KEY'))->first();
            $groupId = SiteConfGroup::siteConfGroupKey($groupKey)->firstOrFail();
            $types = SiteConf::whereSiteConfGroupId($groupId->id)->get();

            foreach ($types as $type) {
                if (!($type->translation($request->header('LANG-CODE')))) {
                    if (!$type->translation($request->header('LANG-CODE-DEFAULT'))) {
                        $type->name = $type->code;
                        $type->description = "";
                    }
                }
                $type->siteConfValues($site->id);
            }

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Confs','e'=>$e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/SiteConf/{site_configuration_key}",
     *  summary="Show a Site Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration"},
     *
     *  @SWG\Parameter(
     *      name="site_configuration_key",
     *      in="path",
     *      description="Site Configuration Key",
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
     *      description="Show the Site Configuration data",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * Returns the details of the specified SiteConf.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $typeId)
    {
        try {
            $siteConf = SiteConf::siteConfKey($typeId)->firstOrFail();
            $siteConf->translations();

            return response()->json($siteConf, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConf not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the SiteConf']);
        }
    }

    /**
     * @SWG\Post(
     *  path="/SiteConf",
     *  summary="Create a Site Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration"},
     *
     *  @SWG\Parameter(
     *      name="Site Configuration",
     *      in="body",
     *      description="Site Configuration Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteConfigurationCreate")
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
     *      response=201,
     *      description="the newly created Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration not found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * Stores a new Parameter Type returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            do {
                $rand = str_random(32);
                $key = "";

                if (!($exists = SiteConf::whereSiteConfKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $requestArray = $request->toArray()["request"];

            $siteConf = SiteConf::create([
                'site_conf_key' => $key,
                'code'  => $requestArray["code"],
                'site_conf_group_id' => $requestArray["group"],
            ]);

            $translationsArray = $request->toArray()["translations"];
            foreach ($translationsArray as $translation) {
                if (isset($translation['language_code']) && (isset($translation['name']) || !empty($siteConf->name)) ) {
                    $siteConfTranslation = SiteConfTranslation::create([
                        'name' => $translation["name"],
                        'description' => !empty($translation["description"]) ? $translation["description"] : "",
                        'lang_code' => $translation["language_code"],
                        'site_conf_id' => $siteConf->id,
                    ]);
                }
            }

            return response()->json($siteConf, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Conf','resp' => $e], 500);
        }
    }

    /**
     * @SWG\Put(
     *  path="/SiteConf/{site_configuration_key}",
     *  summary="Update a Site Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Site Configuration Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteConfigurationCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="site_configuration_key",
     *      in="path",
     *      description="Site Configuration Key",
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
     *      description="The updated Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Site Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * Updates the specified Site Config returning it afterwards.
     *
     * @param Request $request
     * @param $confId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $confId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $translationsOld = [];
            $translationsNew = [];

            $siteConf = SiteConf::siteConfKey($confId)->firstOrFail();

            $requestArray = $request->toArray()["request"];

            $siteConf->code                 = $requestArray["code"];
            $siteConf->site_conf_group_id   = $requestArray["group"];
            $siteConf->save();

            $translationsId = $siteConf->siteConfTranslation()->get();
            foreach ($translationsId as $translationId) {
                $translationsOld[] = $translationId->id;
            }

            $translationsArray = $request->toArray()["translations"];
            foreach ($translationsArray as $translation) {
                if (isset($translation['language_code']) && (isset($translation['name']) || !empty($siteConf->name)) ) {
                    $siteConfTranslation = $siteConf->siteConfTranslation()->whereLangCode($translation['language_code'])->first();
                    if (empty($siteConfTranslation)) {
                        $siteConfTranslation = $siteConf->siteConfTranslation()->create(
                            [
                                'lang_code' => $translation['language_code'],
                                'name' => !empty($translation["name"]) ? $translation['name'] : "",
                                'description' => !empty($translation["description"]) ? $translation['description'] : "",
                            ]
                        );
                    } else {
                        $siteConfTranslation->name = !empty($translation["name"]) ? $translation["name"] : "";
                        $siteConfTranslation->description = !empty($translation["description"]) ? $translation["description"] : "";
                        $siteConfTranslation->save();
                    }
                }
                $translationsNew[] = $siteConfTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $siteConf->siteConfTranslation()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }
            return response()->json($siteConf, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConf not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update SiteConf'], 500);
        }
    }

    /**
     * @SWG\Delete(
     *  path="/SiteConf/{site_configuration_key}",
     *  summary="Delete a Site Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration"},
     *
     * @SWG\Parameter(
     *      name="site_configuration_key",
     *      in="path",
     *      description="Site Configuration Key",
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
     *      @SWG\Schema(ref="#/definitions/siteConfigurationDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationErrorDefault")
     *  )
     * )
     */

    /**
     * Deletes the specified SiteConf.
     *
     * @param Request $request
     * @param $siteConfKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,$siteConfKey)
    {
        //ONE::verifyToken($request);

        try {
            $group = SiteConf::siteConfKey($siteConfKey)->firstOrFail();
            $group->siteSiteConfs();
            foreach ($group->siteSiteConfs as $siteSiteConf) {
                $siteSiteConf->destroy($request,$siteSiteConf->id);
            }

            $group->translations();
            foreach ($group->translations as $translation) {
                $translation->destroy($request,$translation->id);
            }

            SiteConf::destroy($group["id"]);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site Conf'], 500);
        }
    }

    /**
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     */
    public function edit($typeId)
    {
        try {
            $siteConf = SiteConf::siteConfKey($typeId)->firstOrFail();

            $siteConf->translations();

            return response()->json($siteConf, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
