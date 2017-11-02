<?php

namespace App\Http\Controllers;

use App\Layout;
use App\Site;
use App\Entity;
use App\One\One;
use App\SiteAdditionalUrl;
use App\SiteEthic;
use App\SiteEthicType;
use App\SiteSiteConfs;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class SitesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Site",
 *   description="Everything about Sites",
 * )
 *
 *  @SWG\Definition(
 *      definition="siteErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *@SWG\Definition(
 *   definition="use_terms",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"language_code", "content"},
 *           @SWG\Property(property="site_id", format="integer", type="integer"),
 *           @SWG\Property(property="language_code", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="site",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="cm_key", format="string", type="string"),
 *           @SWG\Property(property="site_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="link", format="string", type="string"),
 *           @SWG\Property(property="partial_link", format="boolean", type="boolean"),
 *           @SWG\Property(property="active", format="boolean", type="boolean"),
 *           @SWG\Property(property="no_reply_email", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="date", type="string"),
 *           @SWG\Property(property="end_date", format="date", type="string")
 *      )
 *   }
 * )
 *
 *
 * @SWG\Definition(
 *   definition="siteCreateReply",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="cm_key", format="string", type="string"),
 *           @SWG\Property(property="site_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="link", format="string", type="string"),
 *           @SWG\Property(property="partial_link", format="boolean", type="boolean"),
 *           @SWG\Property(property="active", format="boolean", type="boolean"),
 *           @SWG\Property(property="no_reply_email", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="date", type="string"),
 *           @SWG\Property(property="end_date", format="date", type="string"),
 *           @SWG\Property(
 *              property="use_terms",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/use_terms")
 *           ),
 *           @SWG\Property(
 *              property="layout",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/layoutReply")
 *           ),
 *      )
 *   }
 * )
 */
class SitesController extends Controller
{
    protected $keysRequired = [
        'name',
        'description',
        'link',
    ];

    /**
     * Request the list of Ideas
     * Returns the list of all Ideas
     * @param Request $request
     * @internal param $
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                $sites = Site::whereEntityId($entity->id)->get();
            } else {
                $sites = Site::all();
            }
            return response()->json(['data' => $sites], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Sites'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/site/{site_key}",
     *  summary="Show a Site",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site"},
     *
     * @SWG\Parameter(
     *      name="site_key",
     *      in="path",
     *      description="Site Key",
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
     *      description="Show the Site data",
     *      @SWG\Schema(ref="#/definitions/siteCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of a Site
     * Returns the attributes of the Site
     * @param $siteKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show($siteKey)
    {
        try{
            $site = Site::with('layout')->where('key', $siteKey)->firstOrFail();
            $siteEthics = $site->siteEthics()->whereActive(1)->with('siteEthicType')->get()->keyBy('siteEthicType.code');
            $siteEthicsWithTranslations = [];
            foreach ($siteEthics as $key => $siteEthic){
                $siteEthic->translations();
                $siteEthicsWithTranslations[$key] = $siteEthic->translations;
            }

            $site['site_ethics'] = $siteEthicsWithTranslations;
            $site['use_terms'] = $site->useTerms()->get()->keyBy('language_code');
            $site['social_networks'] = $site->socialNetworks();

            return response()->json($site, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Return the site's additional Urls
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteAdditionalUrls($siteKey)
    {

        try{
            $site = Site::with('layout')->where('key', $siteKey)->firstOrFail();
            $additionalUrls = $site->additionalUrls()->get();
            return response()->json($additionalUrls, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site additional urls'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * Return the site's additional Urls
     * @param $urlId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdditionalUrl($urlId)
    {
        try{
            $siteAdditionalUrl = SiteAdditionalUrl::find($urlId);
            return response()->json($siteAdditionalUrl, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site additional url'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    public function deleteAdditionalUrl($urlId)
    {
        try{
            SiteAdditionalUrl::destroy($urlId);
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Additional link not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the additional url'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function addAdditionalLink(Request $request)
    {
        try{
            $site = Site::where('key',$request->json('site_id'))->first();
            SiteAdditionalUrl::create([
                'site_id' => $site->id,
                'link' => $request->json('link'),
                'partial_link' => true,

            ]);
            return response()->json('Ok', 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Additional link not Found'], 404);
        }catch (Exception $e) {
            return $e;
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function updateAdditionalLink(Request $request)
    {
        try{
            $additionalLink = SiteAdditionalUrl::find($request->json('url_id'));
            $additionalLink->link = $request->json('link');
            $additionalLink->save();
            return response()->json('Ok', 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Additional link not Found'], 404);
        }catch (Exception $e) {
            return $e;
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     * Return the site's additional Urls
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteById($siteId)
    {
        try{
            $site = Site::find($siteId);
            return response()->json($site, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * @SWG\Post(
     *  path="/site",
     *  summary="Create a Site",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site"},
     *
     *  @SWG\Parameter(
     *      name="Site",
     *      in="body",
     *      description="Site data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteCreateReply")
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
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created Site",
     *      @SWG\Schema(ref="#/definitions/siteCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Site",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Site in the database
     * Return the Attributes of the Site created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $key = null;
            do {
                $rand = str_random(32);

                if (!($exists = Site::where('key',$rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            if (is_null($request->json('entity_key'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            } else {
                $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            }

            if (!empty($request->json('layout_key',"")))
                $layout = Layout::whereLayoutKey($request->json('layout_key'))->firstOrFail();
            else
                $layout = Layout::whereReference($request->json('layout_reference'))->firstOrFail();

            $site = $entity->sites()->create(
                [
                    'key'           => $key,
                    'layout_id'     => $layout->id,
                    'cm_key'        => is_null($request->json('cm_key')) ? 0 : $request->json('cm_key'),
                    'name'          => $request->json('name'),
                    'description'   => $request->json('description'),
                    'link'          => $request->json('link'),
                    'partial_link'  => true,
                    'no_reply_email'  => $request->json('no_reply_email'),
                    'active'        => $request->json('active'),
                    'start_date'    => !empty($request->json('start_date')) ? $request->json('start_date'): Carbon::now()->toDateString(),
                    'end_date'      => !empty($request->json('end_date')) ? $request->json('end_date'): null
                ]
            );

            if (!is_null($request->json('use_terms'))){
                foreach ($request->json('use_terms') as $useTerm) {
                    if (isset($useTerm['language_code']) && (isset($useTerm['content']))) {
                        $siteUseTerm = $site->useTerms()->create(
                            [
                                'language_code' => $useTerm['language_code'],
                                'content' => htmlentities($useTerm['content'], ENT_QUOTES, "UTF-8")
                            ]
                        );
                    }
                }
            }

            return response()->json($site, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Site'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/site/{site_key}",
     *  summary="Update a Site",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Site Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/siteCreateReply")
     *  ),
     *
     * @SWG\Parameter(
     *      name="site_key",
     *      in="path",
     *      description="Site Key",
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
     *      description="The updated Site",
     *      @SWG\Schema(ref="#/definitions/siteCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Site",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Site
     * Return the Attributes of the Site Updated
     * @param Request $request
     * @param $siteKey
     * @return mixed
     */
    public function update(Request $request, $siteKey)
    {
        ONE::verifyToken($request);

        try{
            $site = Site::where('key',$siteKey)->firstOrFail();
            $layout = Layout::whereLayoutKey($request->json('layout_key'))->firstOrFail();

            $site->cm_key = empty($request->json('cm_key')) ? 0 : $request->json('cm_key');
            $site->layout_id = $layout->id;
            $site->name = $request->json('name');
            $site->description = $request->json('description');
            $site->link = $request->json('link');
            $site->partial_link = true;
            $site->no_reply_email = $request->json('no_reply_email');
            $site->active = $request->json('active');
            $site->start_date = $request->json('start_date');
            $site->end_date = !empty($request->json('end_date')) ? $request->json('end_date') : null;
            $site->save();

            if (!is_null($request->json('use_terms'))) {
                $useTermsOld = $site->useTerms()->pluck('id');
                $useTermsNew = [];
                foreach ($request->json('use_terms') as $useTerm) {
                    if (isset($useTerm['language_code']) && (isset($useTerm['content']))) {
                        $siteUseTerm = $site->useTerms()->whereLanguageCode($useTerm['language_code'])->first();
                        if (empty($siteUseTerm)) {
                            $siteUseTerm = $site->useTerms()->create(
                                [
                                    'language_code' => $useTerm['language_code'],
                                    'content' => htmlentities($useTerm['content'], ENT_QUOTES, "UTF-8")
                                ]
                            );
                        } else {
                            $siteUseTerm->content = $useTerm['content'];
                            $siteUseTerm->save();
                        }
                        $useTermsNew[] = $siteUseTerm->id;
                    }
                }

                $deleteUseTerms = array_diff($useTermsOld->toArray(), $useTermsNew);
                foreach ($deleteUseTerms as $deleteUseTerm) {
                    $deleteId = $site->useTerms()->whereId($deleteUseTerm)->first();
                    $deleteId->delete();
                }
            }

            if (!is_null($request->json('siteConfs'))) {
                $siteConfs = $request->json('siteConfs');
                foreach ($siteConfs as $siteConf) {
                    if ($siteConf["config_value"]==0) {
                        /* Caso seja pedido para desactivar, apaga o registo (softDelete) caso exista; caso não exista, ignora*/
                        $currentConf = SiteSiteConfs::where("site_id","=",$request->header("X-SITE-KEY"))
                            ->where("site_conf_id","=",$siteConf["config_id"])->first();
                        if (!is_null($currentConf))
                            SiteSiteConfs::destroy($request, $currentConf->id);
                    } else {
                        /*Caso seja pedido para activar, procura se já existe um registo, se existir, ignora; caso contrário, cria novamente*/
                        $currentConf = SiteSiteConfs::where("site_id","=",$request->header("X-SITE-KEY"))
                            ->where("site_conf_id","=",$siteConf["config_id"])->first();
                        if (count($currentConf)==0) {
                            $type = SiteSiteConfs::create([
                                'site_id' => $request->header("X-SITE-KEY"),
                                'site_conf_id' => $siteConf["config_id"],
                                'parameter_value' => $siteConf["config_value"],
                            ]);
                        }
                    }
                }
            }
            return response()->json($site, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found','e'=>$e->getMessage()], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Site','e'=>$e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteSite",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/site/{site_key}",
     *  summary="Delete a Site",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Site"},
     *
     * @SWG\Parameter(
     *      name="site_key",
     *      in="path",
     *      description="Site Key",
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
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteSite")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Site",
     *      @SWG\Schema(ref="#/definitions/siteErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Delete existing Site
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $siteKey)
    {
        ONE::verifyToken($request);
        try{
            $site = Site::where('key',$siteKey)->firstOrFail();
            $site->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteEntity(Request $request)
    {
        try {
            $site = Site::whereLink($request->json('url'))
                ->wherePartialLink(1)
                ->orWhere(function ($query) use ($request) {
                    $query->whereLink("http://" . $request->json('url'))
                        ->orWhere("link", '=', "https://" . $request->json('url'))
                        ->wherePartialLink(0);
                })
                ->first();


            if(!$site){
                $additionalUrl = SiteAdditionalUrl::whereLink($request->json('url'))->orWhere("link", '=', $request->json('url'))->firstOrFail();
                $site = Site::find($additionalUrl->site_id);
            }


            $siteEthics = $site->siteEthics()->whereActive(1)->with('siteEthicType')->get()->keyBy('siteEthicType.code');
            $siteEthicsWithTranslations = [];
            foreach ($siteEthics as $key => $siteEthic){
                $siteEthic->translations();
                $siteEthicsWithTranslations[$key] = $siteEthic->translations;
            }


            $entity = $site->entity()->first();
            $timezone = $entity->timezone()->first();

            $social = $site->socialNetworks()->get();

            // Layout
            $siteLayout = $site->layout()->get();

            $layout = (!empty($siteLayout) && count($siteLayout) > 0) ? $siteLayout[0]->reference : null;


            $data = [];
            $data['site_key'] = $site->key;
            $data['entity_id'] = $entity->entity_key;
            $data['layout'] = $layout;
            $data['timezone'] = $timezone;
            $data['social_network'] = $social;
            $data['site_ethics'] = $siteEthicsWithTranslations;


            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Entity not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Site Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SiteEntityKey(Request $request)
    {
        try {
            $path = parse_url($request->url());

            $site = Site::whereLink($path['host'])

                ->wherePartialLink(1)
                ->orWhere(function ($query) use ($request) {
                    $query->whereLink("http://" . $request->json('url'))
                        ->orWhere("link", '=', "https://" . $request->json('url'))
                        ->wherePartialLink(0);
                })
                ->firstOrFail();

            $entity = $site->entity()->first();

            return response()->json(["site_key" => $site->key, "entity_key" => $entity->entity_key], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Entity not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Site Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function SiteUseTerms(Request $request, $siteKey)
    {
        try{
            $site = Site::where('key',$siteKey)->firstOrFail();

            $useTerms = $site->useTerms()->whereLanguageCode($request->header('LANG-CODE'))->first();

            if (is_null($useTerms)){
                $useTerms = $site->useTerms()->whereLanguageCode($request->header('LANG-CODE-DEFAULT'))->first();
            }

            if (is_null($useTerms)){
                return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($useTerms, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Use Terms'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUseTerms(Request $request)
    {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            $useTerms = $site->useTerms()->whereLanguageCode($request->header('LANG-CODE'))->first();

            if (is_null($useTerms)){
                $useTerms = $site->useTerms()->whereLanguageCode($request->header('LANG-CODE-DEFAULT'))->first();
            }

            if (is_null($useTerms)){
                return response()->json(['error' => 'No translation found'], 404);
            }

            $site['use_terms'] = $useTerms;

            return response()->json($site, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Use Terms'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function setUseTerms(Request $request, $siteKey)
    {
        try{
            $site = Site::where('key',$siteKey)->firstOrFail();

            $useTermsOld = $site->useTerms()->pluck('id');
            $useTermsNew = [];
            foreach ($request->json('use_terms') as $useTerm) {
                if (isset($useTerm['language_code']) && (isset($useTerm['content']))) {
                    $siteUseTerm = $site->useTerms()->whereLanguageCode($useTerm['language_code'])->first();
                    if (empty($siteUseTerm)) {
                        $siteUseTerm = $site->useTerms()->create(
                            [
                                'language_code' => $useTerm['language_code'],
                                'content' => htmlentities($useTerm['content'], ENT_QUOTES, "UTF-8")
                            ]
                        );
                    } else {
                        $siteUseTerm->content = $useTerm['content'];
                        $siteUseTerm->save();
                    }
                    $useTermsNew[] = $siteUseTerm->id;
                }
            }

            $deleteUseTerms = array_diff($useTermsOld->toArray(), $useTermsNew);
            foreach ($deleteUseTerms as $deleteUseTerm) {
                $deleteId = $site->useTerms()->whereId($deleteUseTerm)->first();
                $deleteId->delete();
            }

            return response()->json($site, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Use Terms'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteEthicTypeCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteEthics(Request $request,$siteEthicTypeCode)
    {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

            $siteEthicType = SiteEthicType::whereCode($siteEthicTypeCode)->first();

            if(empty($siteEthicType)){
                return response()->json(['error' => 'Site Ethic Type not Found'], 404);
            }

            /** If version not found get active version*/
            if(empty($siteEthic)){
                $siteEthic = $site->siteEthics()
                    ->whereSiteEthicTypeId($siteEthicType->id)
                    ->whereActive(1)
                    ->first()
                    ->translations()
                    ->where("language_code","=",$request->header('LANG-CODE'))
                    ->first();
            }

            if(empty($siteEthic)){
                $siteEthic = $site->siteEthics()
                    ->whereSiteEthicTypeId($siteEthicType->id)
                    ->whereActive(1)
                    ->first()
                    ->translations()
                    ->where("language_code","=",$request->header('LANG-CODE-DEFAULT'))
                    ->first();
            }

            if(empty($siteEthic)){
                $siteEthic = $site->siteEthics()
                    ->whereSiteEthicTypeId($siteEthicType->id)
                    ->whereActive(1)
                    ->first()
                    ->translations()
                    ->where("content","!=","")
                    ->first();
            }

            if(empty($siteEthic)){
                return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($siteEthic, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteEthic(Request $request,$siteKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest(['site_ethic_type_code'], $request);
        try{
            $version = $request->json('version');
            $site = Site::where('key',$siteKey)->firstOrFail();
            $siteEthicType = SiteEthicType::whereCode($request->json('site_ethic_type_code'))->first();
            if(empty($siteEthicType)){
                return response()->json(['error' => 'Site Ethic Type not Found'], 404);
            }

            if(!empty($version)){
                $siteEthic = $site->siteEthics()->whereSiteEthicTypeId($siteEthicType->id)->whereVersion($version)->first();

            }
            /** If version not found get active version*/
            if(empty($siteEthic)){
                $siteEthic = $site->siteEthics()->whereSiteEthicTypeId($siteEthicType->id)->whereActive(1)->first();
            }

            if(!empty($siteEthic)){
                $siteEthic->translations();
            }
            $versions = $site->siteEthics()->whereSiteEthicTypeId($siteEthicType->id)->orderBy('version','desc')->get();
            $data= [];
            $data['site_ethic'] = $siteEthic;
            $data['site_ethic_versions'] = $versions;

            return response()->json(['data' => $data], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Use Terms'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entitiesSites(Request $request)
    {
        try{
            $authorizedModules = ['logs'];
            One::verifyModulesAccess($request, $authorizedModules);

            $entities = Entity::with('sites')->get();

            $response = [];
            foreach ($entities as $entity){
                foreach ($entity['sites'] as $site){
                    $response[$entity->entity_key][] = $site->key;
                }
            }

            return response()->json($response, 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Entities with Sites'], 500);
        }
    }
}
