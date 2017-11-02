<?php

namespace App\Http\Controllers;

use App\Site;
use App\SiteConf;
use App\SiteConfGroup;
use App\SiteConfGroupTranslation;
use App\SiteConfValue;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

/**
 * Class SiteConfGroupController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Site Configuration Group",
 *   description="Everything about Site configuration groups",
 * )
 *
 *  @SWG\Definition(
 *      definition="siteConfigurationGroupErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationGroupTranslations",
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
 *   definition="siteConfigurationGroupRequest",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="group", format="integer", type="integer"),
 *           @SWG\Property(
 *              property="translations",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/siteConfigurationGroupTranslations")
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationGroupCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"request"},
 *           @SWG\Property(
 *              property="request",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/siteConfigurationGroupRequest")
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="siteConfigurationGroupReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="site_conf_group_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="siteConfigurationGroupDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class SiteConfGroupController extends Controller
{
    protected $required = [
        'store' => [],
        'update' => []
    ];

    /**
     * Returns the list of Site Conf Groups.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if(isset($request['public']) && $request->public){
                $siteConfigurationGroups = $this->publicSite($request->header('X-SITE-KEY'));
            }
            else{
                if(isset($request['siteKey'])) {
                    $siteKey = $request['siteKey'];
                    $site = Site::where('key',$siteKey)->first();
                }

                $siteConfigurationGroups = SiteConfGroup::with(['siteConfigurations'])->get();

                foreach($siteConfigurationGroups as $siteConfigurationGroup){
                    $siteConfigurationGroup->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                    $siteConfigurationGroup->subgroup = $siteConfigurationGroup->siteConfigurations;
                    if(!empty($siteConfigurationGroup->siteConfigurations)){
                        foreach ($siteConfigurationGroup->siteConfigurations as $subGroup){
                            $subGroup->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                            if(isset($site)) {
                                $subGroup->siteConfValues($site->id);
                            }
                        }
                    }
                }
            }
            return response()->json(['data' => $siteConfigurationGroups], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Conf Group'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/siteConfGroup/{site_configuration_group_key}",
     *  summary="Show a Site Configuration Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration Group"},
     *
     *  @SWG\Parameter(
     *      name="site_configuration_group_key",
     *      in="path",
     *      description="Site Configuration Group Key",
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
     *      description="Show the Site Configuration Group data",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration Group not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site Configuration Group",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  )
     * )
     */

    /**
     * Returns the details of the specified SiteConfGroup.
     *
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     */
    public function show($typeId)
    {
        try {
            $type = SiteConfGroup::siteConfGroupKey($typeId)->firstOrFail();
            $type->translations();
            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConfGroup not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the SiteConfGroup']);
        }
    }

    /**
     * @SWG\Post(
     *  path="/siteConfGroup",
     *  summary="Create a Site Configuration Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration Group"},
     *
     *  @SWG\Parameter(
     *      name="Site Configuration Group",
     *      in="body",
     *      description="Site Configuration Group Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupCreate")
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
     *      description="the newly created Site Configuration Group",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration Group not found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Site Configuration Group",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
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

                if (!($exists = SiteConfGroup::whereSiteConfGroupKey($rand)->exists()))
                    $key = $rand;
            } while ($exists);

            $requestArray = $request->toArray()["request"];
            $siteConfGroup = SiteConfGroup::create([
                'site_conf_group_key' => $key,
                'code'  => $requestArray["code"],
            ]);

            $translationsArray = $request->toArray()["translations"];
            foreach ($translationsArray as $translation) {
                if (isset($translation['language_code']) && (isset($translation['name']) || !empty($siteConfGroup->name)) ) {
                    $siteConfGroupTranslation = SiteConfGroupTranslation::create([
                        'name' => $translation["name"],
                        'description' => !empty($translation["description"]) ? $translation["description"] : "",
                        'lang_code' => $translation["language_code"],
                        'site_conf_group_id' => $siteConfGroup->id,
                    ]);
                }
            }

            return response()->json($siteConfGroup, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Conf Group','e'=>$e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Put(
     *  path="/siteConfGroup/{site_configuration_group_key}",
     *  summary="Update a Site Configuration Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration Group"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Site Configuration Group Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="site_configuration_group_key",
     *      in="path",
     *      description="Site Configuration Group Key",
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
     *      description="The updated Site Configuration Group",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Site Configuration Group not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Site Configuration Group",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  )
     * )
     */

    /**
     * Updates the specified Site Config Group returning it afterwards.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     * @internal param $groupID
     */
    public function update(Request $request, $typeId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $translationsOld = [];
            $translationsNew = [];


            $siteConfGroup = SiteConfGroup::siteConfGroupKey($typeId)->firstOrFail();

            $requestArray = $request->toArray()["request"];

            $siteConfGroup->code                 = $requestArray["code"];
            $siteConfGroup->save();

            $translationsId = $siteConfGroup->siteConfGroupTranslation()->get();
            foreach ($translationsId as $translationId) {
                $translationsOld[] = $translationId->id;
            }

            $translationsArray = $request->toArray()["translations"];
            foreach ($translationsArray as $translation) {
                if (isset($translation['language_code']) && (isset($translation['name']) || !empty($siteConfGroup->name)) ) {
                    $siteConfTranslation = $siteConfGroup->siteConfGroupTranslation()->whereLangCode($translation['language_code'])->first();
                    if (empty($siteConfTranslation)) {
                        $siteConfTranslation = $siteConfGroup->siteConfGroupTranslation()->create(
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
                $deleteId = $siteConfGroup->siteConfGroupTranslation()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }
            return response()->json($siteConfGroup, 200);


            /*
            $type = SiteConfGroup::siteConfGroupKey($typeId)->firstOrFail();
            $type->code             = $request->toArray()["code"];
            $type->save();

            return response()->json($type, 200);*/
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConfGroup not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update SiteConfGroup'], 500);
        }
    }

    /**
     * @SWG\Delete(
     *  path="/siteConfGroup/{site_configuration_group_key}",
     *  summary="Delete a Site Configuration Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site Configuration Group"},
     *
     * @SWG\Parameter(
     *      name="site_configuration_group_key",
     *      in="path",
     *      description="Site Configuration Group Key",
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
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Site Configuration",
     *      @SWG\Schema(ref="#/definitions/siteConfigurationGroupErrorDefault")
     *  )
     * )
     */

    /**
     * Deletes the specified SiteConfGroup.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $typeId)
    {
        //ONE::verifyToken($request);

        try {
            $group = SiteConfGroup::siteConfGroupKey($typeId)->firstOrFail();
            $group->siteConfs();
            foreach ($group->siteConf as $siteConf) {
                $siteConf->siteSiteConfs();
                foreach ($siteConf->siteSiteConfs as $siteSiteConf) {
                    $siteSiteConf->destroy($request,$siteSiteConf->id);
                }
                $siteConf->translations();
                foreach ($siteConf->translations as $translation) {
                    $translation->destroy($request,$translation->id);
                }
                 $siteConf->destroy($request,$siteConf->id);
            }

            $group->translations();
            foreach ($group->translations as $translation) {
                $translation->destroy($request,$translation->id);
            }

            SiteConfGroup::destroy($group["id"]);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf Group not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site Conf Group Type','e'=>$e->getMessage()], 500);
        }
    }


    public function publicSite($siteKey)
    {
        try {
            $configurations = SiteConfValue::where(function($query) use ($siteKey){
                $query->where('site_id','=', Site::where('key',$siteKey)->first()->id);
            })
                ->join('site_confs', 'site_confs.id', '=', 'site_conf_values.site_conf_id')
                ->get()
                ->mapWithKeys(function ($item) {
                return [$item['code'] => $item['value']];
            });


            return $configurations;

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Conf Group'], 500);
        }
    }


}
