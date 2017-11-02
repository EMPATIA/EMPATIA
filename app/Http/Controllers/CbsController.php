<?php

namespace App\Http\Controllers;

use App\Cb;
use App\Site;
use App\Post;
use App\User;
use App\Topic;
use Exception;
use App\CbVote;
use App\Status;
use App\CbType;
use App\One\One;
use App\FlagType;
use App\OrchUser;
use App\EntityCb;
use App\Parameter;
use Carbon\Carbon;
use App\PostAbuse;
use App\StatusType;
use App\CbTemplate;
use App\CbModerator;
use App\OperationType;
use App\Http\Requests;
use App\Configuration;
use App\ComModules\Vote;
use App\OperationAction;
use App\TechnicalAnalysis;
use App\VoteConfiguration;
use App\ConfigurationType;
use Illuminate\Http\Request;
use App\TechnicalAnalysisQuestion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;


/**
 * Class CbsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Cbs",
 *   description="Everything about Cbs",
 * )
 *
 *  @SWG\Definition(
 *      definition="cbErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="cb",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"title", "contents", "created_by", "blocked", "status_id", "layout_code", "parent_cb_id", "start_date", "end_date"},
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="date", type="string"),
 *           @SWG\Property(property="end_date", format="date", type="string")
 *       )
 *   }
 * )
 *
 *
 *
 *  @SWG\Definition(
 *   definition="cbStore",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"title", "contents", "start_date"},
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="tag", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="integer", type="integer"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="date", type="string"),
 *           @SWG\Property(property="end_date", format="date", type="string")
 *       )
 *   }
 * )
 *
 *
 *
 *
 *  @SWG\Definition(
 *   definition="cbNewsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="data", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="cb_id", format="string", type="integer"),
 *                      @SWG\Property(property="news_key", format="string", type="string"),
 *                      @SWG\Property(property="created_by", format="string", type="string"),
 *                      @SWG\Property(property="updated_by", format="string", type="string"),
 *                      @SWG\Property(property="created_at", format="date", type="string"),
 *                      @SWG\Property(property="updated_at", format="date", type="string"),
 *                  )
 *           ),
 *       )
 *   }
 * )
 *
 *
 *
 *
 *
 *  @SWG\Definition(
 *   definition="cbNewsStore",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="tag", format="string", type="string"),
 *           @SWG\Property(property="news_key", format="string", type="string")
 *       )
 *   }
 * )
 *
 *
 *
 * @SWG\Definition(
 *   definition="cbResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cbKey", "title", "contents", "created_by", "blocked", "status_id", "layout_code", "parent_cb_id", "start_date", "end_date"},
 *           @SWG\Property(property="cbKey", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="boolean", type="boolean"),
 *           @SWG\Property(property="end_date", format="boolean", type="boolean")
 *       )
 *   }
 * )
 *
 *
 *
 * @SWG\Definition(
 *   definition="cbChildrenResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="cbKey", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="boolean", type="boolean"),
 *           @SWG\Property(property="end_date", format="boolean", type="boolean"),
 *           @SWG\Property(property="children", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="cbKey", format="string", type="string"),
 *                      @SWG\Property(property="title", format="string", type="string"),
 *                      @SWG\Property(property="contents", format="string", type="string"),
 *                      @SWG\Property(property="created_by", format="string", type="string"),
 *                      @SWG\Property(property="blocked", format="string", type="string"),
 *                      @SWG\Property(property="status_id", format="string", type="string"),
 *                      @SWG\Property(property="layout_code", format="string", type="string"),
 *                      @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *                      @SWG\Property(property="start_date", format="boolean", type="boolean"),
 *                      @SWG\Property(property="end_date", format="boolean", type="boolean"),
 *                  )
 *           ),
 *       )
 *   }
 * ) *
 * @SWG\Definition(
 *   definition="cbTopicsTagResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="cbKey", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="boolean", type="boolean"),
 *           @SWG\Property(property="end_date", format="boolean", type="boolean"),
 *           @SWG\Property(property="topics", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="id", format="integer", type="integer"),
 *                      @SWG\Property(property="version", format="integer", type="integer"),
 *                      @SWG\Property(property="topic_key", format="string", type="string"),
 *                      @SWG\Property(property="cb_id", format="string", type="string"),
 *                      @SWG\Property(property="parent_topic_id", format="string", type="string"),
 *                      @SWG\Property(property="created_by", format="string", type="string"),
 *                      @SWG\Property(property="created_on_behalf", format="string", type="string"),
 *                      @SWG\Property(property="title", format="string", type="string"),
 *                      @SWG\Property(property="contents", format="string", type="string"),
 *                      @SWG\Property(property="tag", format="string", type="string"),
 *                      @SWG\Property(property="blocked", format="string", type="string"),
 *                      @SWG\Property(property="q_key", format="string", type="string"),
 *                      @SWG\Property(property="topic_number", format="string", type="string"),
 *                      @SWG\Property(property="start_date", format="date", type="string"),
 *                      @SWG\Property(property="end_date", format="date", type="string"),
 *                      @SWG\Property(property="summary", format="string", type="string"),
 *                      @SWG\Property(property="description", format="string", type="string"),
 *                      @SWG\Property(property="language_code", format="string", type="string"),
 *                      @SWG\Property(property="active", format="integer", type="integer"),
 *                      @SWG\Property(property="moderated", format="integer", type="integer"),
 *                      @SWG\Property(property="moderated_by", format="string", type="string"),
 *                      @SWG\Property(property="created_at", format="date", type="string"),
 *                      @SWG\Property(property="updated_at", format="date", type="string"), *                  )
 *           ),
 *       )
 *   }
 * )
 *
 *
 *
 *
 *  @SWG\Definition(
 *      definition="topicsByTagBody",
 *      type="object",
 *      required={"tag"},
 *      @SWG\Property( property="tag", type="string", format="string")
 *  )
 *
 *
 *
 *
 *
 */
class CbsController extends Controller
{

    protected $required = [
        'store' => ['title', 'contents', 'start_date'],
        'update' => ['title', 'contents', 'start_date'],
        'setConfigurations' => ['configurations'],
        'addVote' => ['vote_key', 'vote_method', 'name'],
        'updateVote' => ['configurations']
    ];

    /*
     * Requests statistics.
     * Returns statistics.
     *
     * @param type $cbKey
     * @var array
     */
    public function statistics(Request $request, $cbKey, $internalCall = false)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail()->timezone($request);

            $data = [];
            $data["cbKey"]= $cb->cb_key;
            $data["topics"] = $cb->topics()->count();
            $data["posts"] = $cb->posts()->whereEnabled(1)->where('posts.active',1)->count();
            $data["votes"] = $cb->votes()->count();

            if (!$internalCall)
                return response()->json(['data' => $data], 200);
            else
                return $data;
        } catch (ModelNotFoundException $e) {
            return ['error' => 'CB data not Found'];
        } catch (Exception $e) {
            return ['error' => 'Failed to retrieve the CB statistics'];
        }
    }

    /**
     * Requests a list of CBs.
     * Returns the list of CBs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $cbs = Cb::with("configurations","news")->get();

            foreach ($cbs as $cb)
                $cb->timezone($request);

            return response()->json(['data' => $cbs], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/cb/{key}",
     *  summary="Show a Cb",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Cb Key",
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
     *      description="Show the Cb data",
     *      @SWG\Schema(ref="#/definitions/cb")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Cb not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Cb",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific CB.
     * Returns the details of a specific CB.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $cbKey)
    {
        try {
            $cb = Cb::with(array('moderators', 'configurations', 'parameters' => function ($query) {
                $query->with('type', 'options','options.parameterOptionFields');
            },'news'))->whereCbKey($cbKey)
                ->firstOrFail()
                ->timezone($request);

            foreach ($cb->configurations as $configuration) {
                $configuration->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
            }

            foreach ($cb->parameters as $parameter) {

                $parameter->translations();

                if (!($parameter->translation($request->header('LANG-CODE')))) {
                    if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $parameter->parameterTranslations()->first();
                        $parameter->translation($translation->language_code);
                    }
                }

                foreach ($parameter->options as $option) {

                    $option->translations();

                    if (!($option->translation($request->header('LANG-CODE')))) {
                        if (!$option->translation($request->header('LANG-CODE-DEFAULT'))){
                            $translation = $option->parameterOptionTranslations()->first();
                            $option->translation($translation->language_code);
                        }
                    }
                }
            }

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request a specific CB.
     * Returns the details of a specific CB.
     *
     * @param Request $request
     * @param $cbId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbById(Request $request, $cbId)
    {
        try {
            $cb = Cb::whereId($cbId)->firstOrFail()->timezone($request);
            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     * Request a specific CB with configurations.
     * Returns the details of a specific CB with configurations.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function configurations(Request $request, $cbKey)
    {
        try {
            $cb = Cb::with('configurations')->whereCbKey($cbKey)->firstOrFail()->timezone($request);

            foreach ($cb->configurations as $configuration) {
                if (!($configuration->translation($request->header('LANG-CODE')))) {
                    if (!$configuration->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$configuration->translation('en')) {
                            $configuration->title = 'No translation found for title';
                            $configuration->description = 'No translation found for description';
                        }

                    }
                }
            }

            $cb->statistics = $this->getStatistics($cb);

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the configurations'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/cb",
     *  summary="Creation of a Cb",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Cb data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/cbStore")
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
     *     @SWG\Schema(ref="#/definitions/cb"),
     *      description="the newly created Cb"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Cb",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created CB in storage.
     * Returns the details of the newly created CB.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required["store"], $request);

        try {
            do {
                $rand = str_random(32);
                if (!($exists = Cb::whereCbKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $cb = Cb::create(
                [
                    'cb_key' => $key,
                    'title' => $request->json('title'),
                    'contents' => $request->json('contents'),
                    'tag' => $request->json('tag') ?? null,
                    'created_by' => $userKey,
                    'blocked' => is_null($request->json('blocked')) ? 0 : $request->json('blocked'),
                    'status_id' => is_null($request->json('status_id')) ? 0 : $request->json('status_id'),
                    'layout_code' => is_null($request->json('layout_code')) ? 0 : $request->json('layout_code'),
                    'parent_cb_id' => is_null($request->json('parent_cb_id')) ? 0 : $request->json('parent_cb_id'),
                    'start_date' => Carbon::createFromFormat('Y-m-d', $request->json('start_date'))->toDateTimeString(),
                    'end_date' => !empty($request->json('end_date')) ? Carbon::createFromFormat('Y-m-d', $request->json('end_date'))->toDateTimeString() : null
                ]
            );

            return response()->json($cb, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Sets the configurations for a specific CB in storage.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $cbKey
     */
    public function setConfigurations(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required["setConfigurations"], $request);
        $configurations = $request->json("configurations");
        $configGroups = $request->json("configGroups");
        $type = $request->json("type");

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $oldConfigs = $cb->configurations()->get();

            $oldNormalConfigs = [];
            $oldNotifyConfigs = [];

            foreach ($oldConfigs as $oldConfig){
                $configType = ConfigurationType::find($oldConfig->configuration_type_id)->code;
                if($configType == 'notifications' || $configType == 'notifications_topic' || $configType == 'notifications_owners' || $configType == 'notification_deadline' ){
                    $oldNotifyConfigs[] = $oldConfig->id;
                }else{
                    $oldNormalConfigs[] = $oldConfig->id;
                }
            }

            if(!empty($configurations)){
                if($type == 1){
                    $cb->configurations()->detach($oldNotifyConfigs);
                }elseif($type == 0){
                    $cb->configurations()->detach($oldNormalConfigs);
                }
                $temp = [];
                $count = 0;
                foreach ($configurations as $configurationType => $configurationsArray){
                    if($type == 1){
                        $count ++;
                        if($configurationType == 'notifications' || $configurationType == 'notifications_topic' || $configurationType == 'notifications_owners' || $configurationType == 'notification_deadline'){
                            $cb->configurations()->detach($configurationsArray);
                            switch ($configurationType) {
                                case 'notifications_topic':
                                    foreach ($configGroups as $configId => $groupId){
                                        $cb->configurations()->attach($configId, ['value' => json_encode($groupId)]);
                                    }
                                    break;
                                case 'notification_deadline':

                                    //values needed for notification send
                                    $siteKey = $request->header('X-SITE-KEY') ?? null;
                                    $entityKey = $request->header('X-ENTITY-KEY') ?? null;
                                    $site = Site::where('key', $siteKey)->first();

                                    $valueArray = [];
                                    $valueArray['deadline'] = $request->json('deadline');
                                    $valueArray['entityKey'] = $entityKey;
                                    $valueArray['siteNoReplyEmail'] = $site->no_reply_email ?? null;
                                    $valueArray['siteName'] = $site->name ?? null;

                                    $EntityCb = EntityCb::where('cb_key', $cb->cb_key)->first();
                                    $cbTypeCode = CbType::whereId($EntityCb->cb_type_id)->first()->code;

                                    $valueArray['cbTypeCode'] = $cbTypeCode ?? null;

                                    $cb->configurations()->attach($configurationsArray, ['value' => json_encode($valueArray)]);
                                    break;
                                default :
                                    $cb->configurations()->attach($configurationsArray);
                                    break;
                            }
                            $temp[] = $configurationsArray;
                        }
                    }
                    if($type == 0){
                        $cb->configurations()->attach($configurationsArray);
                    }
                }

            }else{
                if($type == 0){
                    $cb->configurations()->detach($oldNormalConfigs);
                }
                if($type == 1){
                    $cb->configurations()->detach($oldNotifyConfigs);
                }
            }


            return response()->json("OK", 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     *
     * @SWG\Put(
     *  path="/cb/{key}",
     *  summary="Update a Cb",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Cb Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/cbStore")
     *  ),
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Cb Key",
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
     *      description="The updated Cb",
     *      @SWG\Schema(ref="#/definitions/cbResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Cb not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Cb",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Update the CB in storage.
     * Returns the details of the updated CB.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function update(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required["update"], $request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cb->title = $request->json('title');
            $cb->contents = $request->json('contents');
            $cb->tag = $request->json('tag') ?? null;
            $cb->blocked = empty($request->json('blocked')) ? 0 : $request->json('blocked');
            $cb->status_id = empty($request->json('status_id')) ? 0 : $request->json('status_id');
            $cb->layout_code = empty($request->json('layout_code')) ? 0 : $request->json('layout_code');
            $cb->parent_cb_id = empty($request->json('parent_cb_id')) ? 0 : $request->json('parent_cb_id');
            $cb->start_date = Carbon::createFromFormat('Y-m-d', $request->json('start_date'))->toDateTimeString();
            $cb->end_date = !empty($request->json('end_date')) ? Carbon::createFromFormat('Y-m-d', $request->json('end_date'))->toDateTimeString() : null;

            $cb->save();
            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update a CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteCb",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/cb/{key}",
     *  summary="Delete a Cb",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Cb Key",
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
     *      description="Authentication Token",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteCb")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Cb not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Cb",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified CB from storage.
     *
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cb->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Requests a specific list of CBs.
     * Returns the list of CBs.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCBs(Request $request)
    {
        try {
            if (isset($_GET['list']) && !empty($_GET['list'])) {
                switch ($_GET['list']) {
                    case 'orderby':
                        $cbs = Cb::whereIn('cb_key', $request->json('cbList'))->orderBy("id", "desc")->get();
                        break;
                    case 'random':
                        $cbs = Cb::whereIn('cb_key', $request->json('cbList'))->orderByRaw("RAND()")->get();
                        break;
                    default:
                        $cbs = Cb::whereIn('cb_key', $request->json('cbList'))->get();
                        break;
                }
            } else {
                $cbs = Cb::whereIn('cb_key', $request->json('cbList'))->get();
            }

            if (empty($cbs[0]))
                return response()->json(['data' => []], 200);

            $results = [];
            foreach ($cbs as $cb) {
                $cb->timezone($request);

                $topicIds = [];
                $topics = Topic::where('cb_id', "=", $cb->id)->get();
                foreach ($topics as $topic) {
                    $topicIds[] = $topic->id;
                }

                if (count($topicIds)) {
                    $post = Post::whereIn('topic_id', $topicIds)->orderBy("id", "desc")->first();
                    $cb["lastpost"] = $post;
                    $cb["lasttopic"] = (!empty($post->topic_id)) ? Topic::findOrFail($post->topic_id) : [];
                } else {
                    $cb["lastpost"] = [];
                    $cb["lasttopic"] = [];
                }

                $cb["statistics"] = $this->statistics($request, $cb->cb_key, true);
                $cb["configurations"] = $cb->configurations()->select('code')->pluck('code');
                $results[] = $cb;
            }
            return response()->json(['data' => $results], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Change the status of a specific CB in storage.
     * Returns the details of the updated CB.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function changeStatus(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail()->timezone($request);
            $cb->status_id = $request->json('status_id');
            $cb->save();
            return response()->json(['cb' => $cb], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to change CB status'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Change the status of a specific CB in storage.
     * Returns the details of the updated CB.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function block(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail()->timezone($request);
            $cb->blocked = 1;
            $cb->save();
            return response()->json(['cb' => $cb], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to change CB status'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Change the status of a specific CB in storage.
     * Returns the details of the updated CB.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function unblock(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail()->timezone($request);
            $cb->blocked = 0;
            $cb->save();
            return response()->json(['cb' => $cb], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to change CB status'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Post Abuses by CB.
     * Returns the list of Post Abuses by CB.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAllAbuses(Request $request)
    {
        try {
            $cbIds = [];
            $topicIds = [];
            $postIds = [];
            $cbs = Cb::all();
            foreach ($cbs as $cb) {
                $cbIds[] = $cb->id;
            }

            $topics = Topic::whereIn('cb_id', $cbIds)->get();
            foreach ($topics as $topic) {
                $topicIds[] = $topic->id;
            }

            $posts = Post::whereIn('topic_id', $topicIds)->get();
            foreach ($posts as $post) {
                $postIds[] = $post->id;
            }

            $postAbuses = PostAbuse::whereIn('post_id', $postIds)->get();
            return response()->json(['data' => $postAbuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Abuses list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAbuses(Request $request, $cbKey)
    {
        try {
            $cbData = Cb::with("topics.posts.abuses")->whereCbKey($cbKey)->firstOrFail()->timezone($request);
            return response()->json(['data' => $cbData->topics], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Abuses'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Community Building Moderators.
     * Returns the list of Community Building Moderators.
     *
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function moderatorList($cbKey)
    {
        try {
            $cbModerators = Cb::whereCbKey($cbKey)->firstOrFail()->moderators;
            return response()->json(['data' => $cbModerators], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB Moderator list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function isModerator(Request $request, $cbKey, $userKey)
    {
        ONE::verifyToken($request);

        try {
            $moderator = Cb::whereCbKey($cbKey)->firstOrFail()->moderators()->whereUserKey($userKey)->first();
            return response()->json($moderator ? 'True' : 'False', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Community Building Moderator not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Community Building Moderator'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created Community Building Moderator in storage.
     * Returns the details of the newly created Community Building Moderator.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addModerator(Request $request, $cbKey)
    {
        $createdBy = ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $moderators = [];
            foreach ($request->json('moderators') as $mod) {
                $cbModerator = CbModerator::create(
                    [
                        'cb_id' => $cb->id,
                        'user_key' => $mod['user_key'],
                        'type_id' => $mod['type_id'],
                        'created_by' => $createdBy
                    ]
                );

                $moderators[] = $cbModerator;
            }

            return response()->json(['data' => $moderators], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Community Building Moderator'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created Community Building Moderator in storage.
     * Returns the details of the newly created Community Building Moderator.
     *
     * @param Request $request
     * @param $cbKey
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeModerator(Request $request, $cbKey, $userKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbModerator = Cb::findOrFail($cb->id)->moderators()->whereUserKey($userKey)->firstOrFail();
            $cbModerator->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Community Building Moderator not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Community Building Moderator'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Topics.
     * Returns the list of Topics.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsWithLastPost(Request $request, $cbKey)
    {

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            if ($request->has("parameters.vote_event")) {
                $eventKey = $request->get("parameters")["vote_event"] ?? "";
                if (!empty($eventKey) && $cb->votes()->whereVoteKey($eventKey)->exists()) {
                    if (!Cache::has("vote-event-" . $eventKey)) {
                        $voteResults = Vote::allVoteResults([$eventKey]);
                        $voteEventVotes = collect();
                        foreach ($voteResults as $voteEvent) {
                            foreach ($voteEvent->total_votes ?? [] as $vote) {
                                $voteEventVotes[$vote->vote_key] = ($vote->positive - $vote->negative);
                            }
                            break;
                        }
                        Cache::put("vote-event-" . $eventKey, json_encode($voteEventVotes), 15);
                    } else
                        $voteEventVotes = collect(json_decode(Cache::get("vote-event-" . $eventKey)));
                }
            }

            $tableData = $request->input('tableData') ?? null;
            $recordsTotal = $cb->topics()->count();

            $query = $cb->topics();

            $parameters = $request->parameters;

            if(!empty($parameters) ) {
                if(isset($parameters['phases'])){
                    $parameters[$parameters['phases']] = 1;
                    unset($parameters['phases']);
                }

                if(isset($parameters['vote_event']))
                    unset($parameters['vote_event']);
            }
            $query = $query
                ->with('parameters.type', 'lastPost', 'parameters.options', 'parameters.options.padPermissions','parameters.options.parameterOptionFields', 'technicalAnalysis');

            if ($tableData["order"]["value"]=="votes" && isset($voteEventVotes)) {
                $voteKeysOrdered = $voteEventVotes->sort()->keys();
                $databaseOrder = ($tableData["order"]["dir"]=="asc") ? "DESC" : "ASC";

                $query = $query->orderByRaw("FIELD(topic_key,'". $voteKeysOrdered->implode("','") ."') " . $databaseOrder . ", title");
            } else if ($tableData["order"]["value"]!="votes")
                $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);


            if(!empty($tableData['search']['value'])) {
                $query = $query->where('title', 'like', '%'.$tableData['search']['value'].'%');
            }


            if(!empty($parameters) ){
                foreach($parameters as $key => $parameter){

                    $query = $query->whereHas('parameters',function ($q) use ($key,$parameter){
                        $q->where('id', $key)->where(function($q)use ($parameter) {
                            $q->where('topic_parameters.value', '=', $parameter)
                                ->orWhere("topic_parameters.value", "LIKE", "%," . $parameter)
                                ->orWhere("topic_parameters.value", "LIKE", $parameter . ",%")
                                ->orWhere("topic_parameters.value", "LIKE", "%," . $parameter . ",%");
                        });
                    });

                }
            }

            $filters_static = $request->filters_static;
            if(!empty($filters_static)){
                $startdate = $enddate ='';
                foreach ($filters_static as $key => $value) {
                    if($key == 'start_date')
                        $startdate =$value;
                    else if($key == 'end_date')
                        $enddate = $value;
                    else if($key == 'author')
                        $query = $query->where('created_by', $value);
                    else if($key == 'status')
                        $query = $query->join('status', 'topics.id', '=', 'status.topic_id')->where('status.active', '=',1)->join('status_types', 'status_types.id', '=', 'status.status_type_id')->where('status_types.code',$value)->select('topics.*', 'status_types.code');
                }
                if(!empty($startdate) && !empty($enddate)){
                    $query = $query->whereBetween('topics.created_at', [$startdate,$enddate]);
                }
            }

            $recordsFiltered = $query->count();

            if ($tableData['length'] >= 0) {
                $query = $query
                    ->skip($tableData['start'])
                    ->take($tableData['length']);
            }
            $topics = $query->get();

            $allTopics = array();
            foreach ($topics as $topic) {

                $lastVersion = '';
                if(!($topic->topicVersions()->get())->isEmpty()){
                    if ($topic->topicVersions()->whereActive(1)->count()==1) {
                        $lastVersion = $topic->topicVersions()->whereActive(1)->firstOrFail();
                        $topic->title = $lastVersion->title;
                    }
                    else {
                        $lastVersion = $topic->topicVersions()->orderBy("version", "desc")->firstOrFail();
                        $topic->title = $lastVersion->title;
                    }
                }

                $posts = $topic->posts()->whereEnabled(1)->count();
                $likes = $topic->likes()->whereLike(1)->count();
                $dislikes = $topic->likes()->whereLike(0)->count();


                $status = $topic->status()->whereActive(1)->first();
                $statusType = !empty($status) ? $status->statusType()->firstOrFail() : null;

                if (!empty($status) && !($statusType->translation($request->header('LANG-CODE')))) {
                    if (!$statusType->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$statusType->translation('en')){
                            return response()->json(['error' => 'No translation found'], 404);
                        }
                    }

                }

                $topic['statistics'] = ['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts];
                $topic['status'] = $statusType;

                $topic["balance_votes"] = $voteEventVotes[$topic->topic_key] ?? 0;
                $allTopics[] = $topic;
            }

            $data['topics'] = $allTopics;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json(['data' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTopics(Request $request, $cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->first();
            $data = array();
            if($cb){
                $topics = Topic::whereCbId($cb->id)->get();
                if($topics) {
                    foreach ($topics as $topic) {
                        if(!Status::whereTopicId($topic->id)->exists()) {
                            $data[] = $topic;
                        }
                    }
                }
            }

            return response()->json(['data' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsWithParameters($cbKey)
    {
        try {
            $topics = Topic::with('parameters.type', 'parameters.options', 'lastPost')->where('cb_key', '=', $cbKey)->get();

            $data = array();
            foreach ($topics as $topic) {
                $posts = $topic->posts()->whereEnabled(1)->count();
                $likes = $topic->likes()->whereLike(1)->count();
                $dislikes = $topic->likes()->whereLike(0)->count();

                $topic['statistics'] = ['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts];
                $data[] = $topic;
            }

            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsKey($cbKey)
    {
        try {
            $topics = Cb::whereCbKey($cbKey)->firstOrFail()->topics()->get();

            $data = [];
            foreach ($topics as $topic) {
                $data[] = $topic['topic_key'];
            }

            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic Keys'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsWithFirstPost($cbKey)
    {
        try {
            $topics = Cb::whereCbKey($cbKey)->firstOrFail()->topics()->with('firstPost', 'parameters')->get();

            $data = array();
            foreach ($topics as $topic) {
                $posts = $topic->posts()->whereEnabled(1)->count();
                $likes = $topic->likes()->whereLike(1)->count();
                $dislikes = $topic->likes()->whereLike(0)->count();

                $topic['statistics'] = ['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts];
                $data[] = $topic;
            }

            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVote(Request $request, $cbKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['addVote'], $request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $vote = $cb->votes()->create(
                [
                    'vote_key' => $request->json('vote_key'),
                    'vote_method' => $request->json('vote_method'),
                    'name' => $request->json('name'),
                    'created_by' => $userKey
                ]
            );

            if (!empty($request->json('configurations'))) {
                $voteConfigurations = $request->json('configurations');
                foreach ($voteConfigurations as $voteConfiguration) {
                    $vote->voteConfigurations()->attach(VoteConfiguration::whereVoteConfigurationKey($voteConfiguration['vote_configuration_key'])->firstOrFail()->id, ['value' => $voteConfiguration['value']]);
                }
            }
            return response()->json($vote, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed while trying to add a Vote instance to Cb'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function updateVote(Request $request, $cbKey, $voteKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['updateVote'], $request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $vote = $cb->votes()->whereVoteKey($voteKey)->first();

            $vote->vote_method = empty($request->json('vote_method')) ? $vote->vote_method : $request->json('vote_method');
            $vote->name = empty($request->json('name')) ? $vote->name : $request->json('name');
            $vote->save();

            if (!empty($request->json('configurations'))) {
                $voteConfigurationId = [];
                $voteConfigurations = $request->json('configurations');
                foreach ($voteConfigurations as $voteConfiguration) {
                    $id = VoteConfiguration::whereVoteConfigurationKey($voteConfiguration['vote_configuration_key'])->firstOrFail()->id;
                    $voteConfigurationId[$id]['value'] = $voteConfiguration['value'];
                }
                $vote->voteConfigurations()->sync($voteConfigurationId);
            }

            return response()->json($vote, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed while trying to update a Vote instance to Cb'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @param $voteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function showVote(Request $request, $cbKey, $voteKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $vote = $cb->votes()->whereVoteKey($voteKey)->first();

            $voteConfigurations = $vote->voteConfigurations()->get();

            if (!empty($voteConfigurations)) {
                foreach ($voteConfigurations as $voteConfiguration) {
                    if (!($voteConfiguration->translation($request->header('LANG-CODE')))) {
                        if (!$voteConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                            $voteConfiguration->translation('en');
                    }
                    $voteConfiguration['value'] = $voteConfiguration->pivot->value;
                    $voteConfiguration = array_except($voteConfiguration, 'pivot');
                }
                $vote['vote_configurations'] = $voteConfigurations;
            }

            return response()->json($vote, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB Vote not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB Vote'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listVotes(Request $request, $cbKey)
    {
        try {
            $cb = Cb::with("votes.voteConfigurations")->whereCbKey($cbKey)->firstOrFail();

            foreach ($cb->votes as $vote) {
                foreach ($vote->voteConfigurations as $voteConfiguration) {
                    if (!($voteConfiguration->translation($request->header('LANG-CODE')))) {
                        if (!$voteConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                            $voteConfiguration->translation('en');
                    }
                    $voteConfiguration->value = $voteConfiguration->pivot->value;
                    unset($voteConfiguration->pivot);
                }
            }

            return response()->json(['data' => $cb->votes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed while listing CbVotes'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @param $voteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeVote(Request $request, $cbKey, $voteKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $vote = $cb->votes()->whereVoteKey($voteKey)->first();
            $voteConfigurations = $vote->voteConfigurations()->get();

            if (!empty($voteConfigurations)) {
                foreach ($voteConfigurations as $voteConfiguration) {
                    $vote->voteConfigurations()->detach(VoteConfiguration::whereVoteConfigurationKey($voteConfiguration->vote_configuration_key)->firstOrFail()->id);
                }
            }

            $cb->votes()->whereVoteKey($voteKey)->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed while trying to delete a Vote instance of the Cb']);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function setParameters(Request $request, $cbKey)
    {
        ONE::verifyToken($request);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            foreach ($request->json('parameters') as $parameter) {

                $parameterLastPosition = $cb->parameters()
                    ->orderBy('position', 'desc')
                    ->where('parameter_type_id', '=', $parameter['parameter_type_id'])
                    ->first();

                $lastPosition = !empty($parameterLastPosition) ? $parameterLastPosition->position + 1 : 0;

                $cb->parameters()->create(
                    [
                        'parameter_type_id' => $parameter['parameter_type_id'],
                        'cb_id' => $parameter['cb_id'],
                        'parameter' => $parameter['parameter'],
                        'description' => $parameter['description'],
                        'code' => $parameter['code'],
                        'parameter_code' => $parameter['parameter_code'],
                        'mandatory' => $parameter['mandatory'],
                        'value' => $parameter['value'],
                        'currency' => $parameter['currency'],
                        'position' => $lastPosition
                    ]);
            }

            //Updates the CB Cached Data
            $this->updateCachedData($cbKey);

            return response()->json(Cb::with('parameters')->findOrFail($cb->id), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to set CB Parameters'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeParameters(Request $request, $cbKey, $parameterId)
    {
        ONE::verifyToken($request);
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $parameter = $cb->parameters()->findOrFail($parameterId);
            $parameter->delete();

            //Updates the CB Cached Data
            $this->updateCachedData($cbKey);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete CB Parameters'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameters(Request $request, $cbKey)
    {
        try {
            $cb = Cb::with('parameters')->whereCbKey($cbKey)->firstOrFail();

            foreach ($cb->parameters as $parameter) {
                if (!($parameter->translation($request->header('LANG-CODE')))) {
                    if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){}

                    $firstLanguageFound = $parameter->parameterTranslations()->first();
                    $parameter->setAttribute('parameter',$firstLanguageFound->parameter);
                    $parameter->setAttribute('description',$firstLanguageFound->description);
                }
            }

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB with its parameters'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function setOptions(Request $request, $cbKey)
    {
        ONE::verifyToken($request);
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $optionsMod = array();

            foreach ($request->json('options') as $option) {
                $optionsMod[$option['parameter_option_id']] = $option;
            }

            $cb->options()->sync($optionsMod);

            //Updates the CB Cached Data
            $this->updateCachedData($cbKey);

            return response()->json(Cb::with(['parameters', 'options'])->findOrFail($cb->id), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to set CB Parameter Options'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $cbId
     */
    public function options(Request $request, $cbKey)
    {
        try {
            $cb = Cb::with(array('parameters' => function ($query) {
                $query->with('type', 'options')->orderBy("position");
            }))->whereCbKey($cbKey)->firstOrFail();

            if ($request->get("privateTopicsList",false)) {
                $userKey = ONE::verifyToken($request);
                $entity = ONE::getEntity($request);

                $user = OrchUser::with(["entityGroups" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                }])
                    ->whereUserKey($userKey)
                    ->firstOrFail();

                $padPermissions = $cb->padPermissions()
                    ->with("parameterOptions")
                    ->whereIn("group_key",$user->entityGroups->pluck("entity_group_key"))
                    ->orWhere("user_key",$userKey)
                    ->get();

                if (!$padPermissions->isEmpty()) {
                    $accessibleParametersIds = [];
                    foreach ($padPermissions as $padPermission) {
                        foreach ($padPermission->parameterOptions as $parameterOption) {
                            $accessibleParametersIds[$parameterOption->id] = $parameterOption->id;
                        }
                    }


                }
            }

            foreach ($cb->parameters as $parameter) {
                if (!($parameter->translation($request->header('LANG-CODE')))) {
                    if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $parameter->parameterTranslations()->first();

                        if (!empty($translation)){
                            $parameter->translation($translation->language_code);
                        } else {
                            return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }

                foreach ($parameter->options as $option) {
                    if (!($option->translation($request->header('LANG-CODE')))) {
                        if (!$option->translation($request->header('LANG-CODE-DEFAULT'))){
                            $translation = $option->parameterOptionTranslations()->first();

                            if (!empty($translation)){
                                $option->translation($translation->language_code);
                            } else {
                                return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }
            }

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB with its parameters'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllInformation(Request $request, $cbKey)
    {
        $filterList = $request->input('filter_list');

        try {
            $userKey = ONE::verifyLogin($request);
            $response = [];
            $cb = Cb::whereCbKey($cbKey)->with(['parameters.type', 'parameters.options'])->firstOrFail();
            /** get cb parameters with  option fields and translations*/
            foreach ($cb->parameters as $parameter){
                foreach ($parameter->options as $option){
                    $parameterOptionFields = $option->parameterOptionFields()->get();
                    foreach ($parameterOptionFields as $parameterOptionField){
                        $option[$parameterOptionField->code] = $parameterOptionField->value;
                    }
                }
            }
            foreach ($cb->parameters as $parameter) {
                if (!($parameter->translation($request->header('LANG-CODE')))) {
                    if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                        $translation = $parameter->parameterTranslations()->first();
                        if(!empty($translation)){
                            $parameter->translation($translation->language_code);
                        }
                        else{
                            return response()->json(['error' => 'No translation found'], 404);
                        }

                    }
                }

                $parameter->translations();

                foreach ($parameter->options as $option) {
                    if (!empty($option)) {
                        if (!($option->translation($request->header('LANG-CODE')))) {
                            if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                $translation = $option->parameterOptionTranslations()->first();
                                if(!empty($translation)){
                                    $option->translation($translation->language_code);
                                }
                                else{
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                    }
                    $option->translations();
                }
            }



            $topicsCollection = $cb->topics()->with(['parameters.type', 'parameters.options', 'lastPost', 'firstPost',
                'status' => function ($q) {
                    $q->where('active', '=', 1);
                }, 'status.statusType', 'followers'])
                ->get();

            foreach ($topicsCollection as $topic){
                foreach ($topic->parameters as $parameter){
                    foreach ($parameter->options as $option){
                        $parameterOptionFields = $option->parameterOptionFields()->get();
                        foreach ($parameterOptionFields as $parameterOptionField){
                            $option[$parameterOptionField->code] = $parameterOptionField->value;
                        }
                    }
                }
                $topic->alliances = $topic->originAllyRequest()->whereAccepted(1)->get();
                $topic->alliances->merge($topic->destinyAllyRequest()->whereAccepted(1)->get());
                $topic->posts_count = $topic->posts()->whereEnabled(1)->count();
            }

            $topics = [];
            foreach ($topicsCollection as $key => $topic) {

                //Removes from collection Topics without Status
                if ($topic->status->isEmpty()) {
                    $topicsCollection->forget($key);
                    continue;
                }

                //Get topic active status
                $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();

                if(!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated'){
                    $topic->closed = true;
                }else{
                    $topic->closed = false;
                }

                //---------------------------- Filter/Order Topics ---------------------------------

                if(!empty($filterList)) {
                    $filterParameter = '';
                    $filterOption = '';

                    foreach ($filterList as $itemKey => $value) {
                        if (preg_match("/filter_.*/", $itemKey)) {
                            $filterParameter = trim($itemKey, "filter_");;
                            $filterOption = $value;
                            break;
                        }
                    }

                    if (!empty($filterParameter) && !empty($filterOption)) {
                        //check if topic have the parameter selected in the filter
                        if (!$topic->parameters->contains('id', $filterParameter)) {
                            $topicsCollection->forget($key);
                            continue;
                        } else {
                            //check if parameter have the option selected in the filter
                            $topicParameter = $topic->parameters->where('id', $filterParameter)->first();
                            if ($topicParameter->pivot->value != $filterOption) {
                                $topicsCollection->forget($key);
                                continue;
                            }
                        }
                    }

                    if(!empty($filterList['status'])){

                        $statusType = StatusType::whereCode($filterList['status'])->first();

                        //Check if topic status is the same as the one used in the filter
                        if(empty($topic->active_status) || $topic->active_status->status_type_id != $statusType->id){
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }

                    //search the topic for the provided search word
                    if(!empty($filterList['search'])){
                        if (stripos($topic->title, $filterList['search']) === false && stripos($topic->contents, $filterList['search']) === false  && stripos($topic->firstPost->contents, $filterList['search']) === false) {
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }

                    if (!empty($filterList['orderBy'])) {
                        switch ($filterList['orderBy']) {
                            case 'rand':
                                $topics = (collect($topics)->shuffle())->toArray();
                                break;
                            case 'asc':
                                $topics = (collect($topics)->sortBy('created_at'))->toArray();
                                break;
                            case 'desc':
                                $topics = (collect($topics)->sortByDesc('created_at'))->toArray();
                                break;
                            default:
                                break;
                        }
                    }
                }

                //----------------------------------------------------------------------------------------------

                if (!empty($topic->active_status)) {
                    if (!($topic->active_status->statusType->translation($request->header('LANG-CODE')))) {
                        if (!$topic->active_status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                            $translation = $topic->active_status->statusType->statusTypeTranslations()->first();
                            if(!empty($translation)){
                                $topic->active_status->statusType->translation($translation->language_code);
                            }
                            else{
                                return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }

                $following = false;
                if (!empty($userKey)) {
                    $following = $topic->followers()->whereUserKey($userKey)->exists();
                }
                $topic->following = $following;
                foreach ($topic->parameters as $parameter) {
                    if (!($parameter->translation($request->header('LANG-CODE')))) {
                        if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                            $translation = $parameter->parameterTranslations()->first();
                            if(!empty($translation)){
                                $parameter->translation($translation->language_code);
                            }
                            else{
                                return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }

                    $parameter->translations();

                    foreach ($parameter->options as $option) {
                        if (!empty($option)) {
                            if (!($option->translation($request->header('LANG-CODE')))) {
                                if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    $translation = $option->parameterOptionTranslations()->first();
                                    if(!empty($translation)){
                                        $option->translation($translation->language_code);
                                    }
                                    else{
                                        return response()->json(['error' => 'No translation found'], 404);
                                    }
                                }
                            }
                        }
                        $option->translations();
                    }
                }

                foreach ($topic->status as $status) {
                    if (!empty($status->statusType)) {
                        if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                            if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                $translation = $status->statusType->statusTypeTranslations()->first();
                                if(!empty($translation)){
                                    $status->statusType->translation($translation->language_code);
                                }
                                else{
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                    }
                }

                $posts = $topic->posts()->whereEnabled(1)->whereActive(1)->count();
                $likes = $topic->likes()->whereLike(1)->count();
                $dislikes = $topic->likes()->whereLike(0)->count();

                $topic['statistics'] = ['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts];

                $topic['accesses'] = $topic->topicAccesses()->count();
                $topics[] = $topic;
            }

            $statusTypeItem = new StatusTypesController();

            $response['cb'] = $cb;
            $response['topics'] = $topics;
            $response['moderators'] = $cb->moderators()->get();
            $response['configurations'] = $cb->configurations()->select('code')->pluck('code');
            $response['votes'] = $cb->votes()->get()->keyBy('vote_key');
            $response['statusTypes'] = $statusTypeItem->getStatusTypes(['langCode' => $request->header('LANG-CODE'), 'langCodeDefault' => $request->header('LANG-CODE-DEFAULT')]);
            $response['voteKeys'] = $cb->votes()->get()->pluck('vote_key');

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve CB Information '], 500);
        }
    }

    /**
     * CREATE CB WITH CONFIGURATIONS, PARAMETERS AND MODERATORS
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAdvanced(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["store"], $request);

        try {
            do {
                $key = '';
                $rand = str_random(32);
                if (!($exists = Cb::whereCbKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            //CREATE CB
            $cb = Cb::create(
                [
                    'cb_key' => $key,
                    'title' => $request->json('title'),
                    'contents' => $request->json('contents'),
                    'created_by' => $userKey,
                    'blocked' => is_null($request->json('blocked')) ? 0 : $request->json('blocked'),
                    'status_id' => is_null($request->json('status_id')) ? 0 : $request->json('status_id'),
                    'layout_code' => is_null($request->json('layout_code')) ? 0 : $request->json('layout_code'),
                    'parent_cb_id' => is_null($request->json('parent_cb_id')) ? 0 : $request->json('parent_cb_id'),
                    'start_date' => Carbon::createFromFormat('Y-m-d', $request->json('start_date'))->toDateTimeString(),
                    'end_date' => !empty($request->json('end_date')) ? Carbon::createFromFormat('Y-m-d', $request->json('end_date'))->toDateTimeString() : null
                ]
            );

            //CONFIGURATIONS
            if(!empty($request->json('configurations'))){
                $configurations = $request->json("configurations");
                $cb->configurations()->sync($configurations);
            }

            //PARAMETERS
            if(!empty($request->json('parameters'))) {
                $parameters = $request->json('parameters');
                if(!ParametersController::storeParametersAdvance($parameters, $key)){
                    throw new Exception('Failed to store Parameter');
                }
            }

            //MODERATORS
            if(!empty($request->json('moderators'))) {
                foreach ($request->json('moderators') as $moderator) {
                    $cbModerator = $cb->moderators()->create(
                        [
                            'user_key'      => $moderator['user_key'],
                            'type_id'       => $moderator['type_id'],
                            'created_by'    => $userKey
                        ]
                    );
                }
            }

            //Updates the CB Cached Data
            $this->updateCachedData($cb->cb_key);

            return response()->json($cb, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new CB'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * UPDATE CB AND CB CONFIGURATIONS, CB PARAMETERS AND CB MODERATORS
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAdvanced(Request $request, $cbKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            //CB
            $cb = $this->updateStatic($request, $cbKey);

            //CONFIGURATIONS
            $configurationsArray = $request->json('configurations');
            if(!empty($request->json("configurations"))){
                $configurations = $request->json("configurations");
                $this->updateConfigurationAdvance($configurations, $cbKey);
            }elseif (isset($configurationsArray)){
                $cb->configurations()->detach();
            }

            //PARAMETERS
            $parameterArray = $request->json('parameters');
            if(!empty($parameterArray)) {
                ParametersController::updateParametersAdvance($parameterArray, $cbKey);
            } elseif (isset($parameterArray)){
                $cb->parameters()->delete();
            }

            //MODERATORS
            $moderatorsArray = $request->json('moderators');
            if(!empty($moderatorsArray)) {
                $this->updateModeratorsAdvance($moderatorsArray, $cbKey, $userKey);
            }elseif (isset($moderatorsArray)){
                $cb->moderators()->delete();
            }

            //Updates the CB Cached Data
            $this->updateCachedData($cbKey);

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    //STATIC FUNCTIONS FOR CB -> UPDATE ADVANCED

    /**
     * UPDATE CB
     * @param $data
     * @param $cbKey
     * @throws Exception
     */
    public static function updateStatic($data, $cbKey)
    {
        $item = new CbsController();
        ONE::verifyKeysRequest($item->required["update"], $data);

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cb->title = $data->json('title');
            $cb->contents = $data->json('contents');
            $cb->blocked = empty($data->json('blocked')) ? 0 : $data->json('blocked');
            $cb->status_id = empty($data->json('status_id')) ? 0 : $data->json('status_id');
            $cb->layout_code = empty($data->json('layout_code')) ? 0 : $data->json('layout_code');
            $cb->parent_cb_id = empty($data->json('parent_cb_id')) ? 0 : $data->json('parent_cb_id');
            $cb->start_date = Carbon::createFromFormat('Y-m-d', $data->json('start_date'))->toDateTimeString();
            $cb->end_date = !empty($data->json('end_date')) ? Carbon::createFromFormat('Y-m-d', $data->json('end_date'))->toDateTimeString() : null;

            $cb->save();
            return $cb;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('CB not Found');
        }catch (Exception $e) {
            throw new Exception('Error in updateStatic');
        }
    }

    /**
     * UPDATE CB CONFIGURATIONS
     * @param $data
     * @param $cbKey
     * @return bool
     */
    public static function updateConfigurationAdvance($data, $cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cb->configurations()->sync($data);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * UPDATE CB MODERATORS
     * @param $data
     * @param $cbKey
     * @param $userKey
     * @return bool
     * @throws Exception
     */
    public static function updateModeratorsAdvance($data, $cbKey, $userKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $oldModerators = $cb->moderators()->pluck('id')->toArray();
            $newModerators = [];

            foreach ($data as $moderator) {
                $cbModerator = $cb->moderators()->whereUserKey($moderator['user_key'])->first();

                if(empty($cbModerator)){
                    $cbModerator = $cb->moderators()->create(
                        [
                            'user_key'      => $moderator['user_key'],
                            'type_id'       => $moderator['type_id'],
                            'created_by'    => $userKey
                        ]
                    );
                }
                $newModerators[] = $cbModerator->id;
            }

            $deleteModerators = array_diff($oldModerators,$newModerators);
            CbModerator::destroy($deleteModerators);

            return true;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('CB not Found');
        }catch (Exception $e) {
            throw new Exception('Error in updateModeratorsAdvance');
        }
    }


    /**
     *
     * Returns an events keys array for given CB
     *
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventsKeyList ($cbKey) {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $cbVotes = CbVote::whereCbId($cb->id)->get()->pluck('vote_key');

            return response()->json(['data' => $cbVotes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB Events Key list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * verify if the given user created any topic in the given CB
     *
     * @param Request $request
     * @param $cbKey
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserTopics(Request $request, $cbKey, $userKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            if($cb->topics()->whereCreatedBy($userKey)->exists()){
                return response()->json(true, 200);
            }

            return response()->json(false, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to vetrify user topics'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * General Analytics
     */
    public function getCbAnalytics ($cbKey) {
        try {
            $cb = Cb::whereCbKey($cbKey)->first();
            $totalSupports = 0;
            $analytics=[];

            if(!empty($cb)){
                $analytics['total_topics'] = $cb->topics()->count();
                $topics = $cb->topics()->get();
                foreach ($topics as $topic){
                    $total= $topic->followers()->count();
                    $totalSupports = $totalSupports + $total;
                }
            }else{
                $analytics['total_topics']= 0;
            }
            $analytics['total_followers'] = $totalSupports;


            return response()->json(['data' => $analytics], 200);
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }


    /**
     *
     * retrieves all topics created by given user
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserTopics(Request $request, $cbKey)
    {
        try {
            $userKey = ONE::verifyLogin($request);
            $response = [];
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $topicsCollection = $cb->topics()->with(['parameters.type', 'parameters.options', 'lastPost', 'firstPost',
                'status' => function ($q) {
                    $q->where('active', '=', 1);
                }, 'status.statusType', 'followers'])
                ->where('created_by', $userKey)
                ->get();

            $topics = [];
            foreach ($topicsCollection as $key => $topic) {



                if (!empty($topic->active_status)) {
                    if (!($topic->active_status->statusType->translation($request->header('LANG-CODE')))) {
                        if (!$topic->active_status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$topic->active_status->statusType->translation('en'))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }

                $following = false;
                if (!empty($userKey)) {
                    $following = $topic->followers()->whereUserKey($userKey)->exists();
                }
                $topic->following = $following;
                foreach ($topic->parameters as $parameter) {
                    if (!($parameter->translation($request->header('LANG-CODE')))) {
                        if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$parameter->translation('en'))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                    }

                    $parameter->translations();

                    foreach ($parameter->options as $option) {
                        if (!empty($option)) {
                            if (!($option->translation($request->header('LANG-CODE')))) {
                                if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    if (!$option->translation('en'))
                                        return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                        $option->translations();
                    }
                }

                foreach ($topic->status as $status) {
                    if (!empty($status->statusType)) {
                        if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                            if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                if (!$status->statusType->translation('en'))
                                    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }


                //Get topic active status
                $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();

                if(!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated'){
                    $topic->closed = true;
                }else{
                    $topic->closed = false;
                }
                $topics[] = $topic;
            }

            $response['cb'] = $cb;
            $response['topics'] = $topics;

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve CB Information '], 500);
        }
    }

    /**
     *
     * retrieves all topics followed by given user
     *
     * @param Request $request
     * @param $cbKey
     * @return Exception|ModelNotFoundException|\Illuminate\Http\JsonResponse
     */
    public function getAllUserFollowingTopics(Request $request, $cbKey)
    {

        try {
            $userKey = ONE::verifyLogin($request);
            $response = [];
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $topicsCollection = $cb->topics()->with(['parameters.type', 'parameters.options', 'lastPost', 'firstPost',
                'status', 'status.statusType', 'followers' => function ($query) use  ($userKey) {
                    $query->whereUserKey($userKey);
                }])->get();


            $topics = [];
            foreach ($topicsCollection as $key => $topic) {

                if ($topic->followers->isEmpty()) {

                    $topicsCollection->forget($key);
                    continue;
                }

                if (!empty($topic->active_status)) {
                    if (!($topic->active_status->statusType->translation($request->header('LANG-CODE')))) {
                        if (!$topic->active_status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$topic->active_status->statusType->translation('en'))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }

                $following = false;
                if (!empty($userKey)) {
                    $following = $topic->followers()->whereUserKey($userKey)->exists();
                }
                $topic->following = $following;
                foreach ($topic->parameters as $parameter) {
                    if (!($parameter->translation($request->header('LANG-CODE')))) {
                        if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$parameter->translation('en'))
                                return response()->json(['error' => 'No translation found'], 404);
                        }
                    }

                    $parameter->translations();

                    foreach ($parameter->options as $option) {
                        if (!empty($option)) {
                            if (!($option->translation($request->header('LANG-CODE')))) {
                                if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    if (!$option->translation('en'))
                                        return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                        $option->translations();
                    }
                }

                foreach ($topic->status as $status) {
                    if (!empty($status->statusType)) {
                        if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                            if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                if (!$status->statusType->translation('en'))
                                    return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }


                //Get topic active status
                $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();

                if(!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated'){
                    $topic->closed = true;
                }else{
                    $topic->closed = false;
                }
                $topics[] = $topic;
            }

            $response['cb'] = $cb;
            $response['followingTopics'] = $topics;

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve CB Information '], 500);
        }
    }


    /**
     * For given array of Cbs Keys lists CBs with Statistics
     * Possible Open/Closed CBs Filter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCbsWithStatistics(Request $request)
    {
        try {

            //Query with filters for open and closed CBs
            switch ($request->json('cbsStatus')) {
                case 'open':
                    $cbs = Cb::with(['votes' => function ($query) {
                        $query->where('vote_method', '=', 'likes');
                    }])->whereIn('cb_key', $request->json('cbList'))
                        ->where(function ($query) {
                            $query->where('end_date', '>', Carbon::now()->toDateString())
                                ->orWhere('end_date', '=', NULL);
                        })
                        ->orderBy("created_at", "asc")->orderBy("end_date", "asc")->get();
                    break;
                case 'closed':
                    $cbs = Cb::with(['votes' => function ($query) {
                        $query->where('vote_method', '=', 'likes');
                    }])->whereIn('cb_key', $request->json('cbList'))
                        ->where(function ($query) {
                            $query->where('end_date', '<', Carbon::now()->toDateString());
                        })
                        ->orderBy("created_at", "asc")->orderBy("end_date", "asc")->get();
                    break;
                default:

                    $cbs = Cb::with(['votes' => function ($query) {
                        $query->where('vote_method', '=', 'likes');
                    }])->whereIn('cb_key', $request->json('cbList'))->orderBy("created_at", "asc")->orderBy("end_date", "asc")->get();
                    break;
            }

            if (empty($cbs[0]))
                return response()->json(['data' => []], 200);

            $closedCbs = 0;
            $openedCbs = 0;
            $totalComments= 0;
            $results = [];
            $cbsArray = [];
            $votesArray = [];
            $cbsStatistics = [];
            foreach ($cbs as $cb) {
                $cb->timezone($request);

                //count opened and closed CBs
                if($cb->end_date){
                    ($cb->end_date >= Carbon::now())? $openedCbs++:$closedCbs++;
                }else{
                    $openedCbs++;
                }

                $topicIds = [];
                $topics = Topic::where('cb_id', "=", $cb->id)->get();
                foreach ($topics as $topic) {
                    $topicIds[] = $topic->id;
                }

                if (count($topicIds)) {
                    $post = Post::whereIn('topic_id', $topicIds)->orderBy("id", "desc")->first();
                    $cb["lastpost"] = $post;
                    $cb["lasttopic"] = (!empty($post->topic_id)) ? Topic::findOrFail($post->topic_id) : [];
                } else {
                    $cb["lastpost"] = [];
                    $cb["lasttopic"] = [];
                }

                $cb["statistics"] = $this->getStatistics($cb);

                if(!empty($cb["statistics"])){
                    $totalComments += $cb["statistics"]["posts"] - $cb["statistics"]["topics"];
                }

                $cb["configurations"] = $cb->configurations()->select('code')->pluck('code');

                $votesEventsKeys = $cb->votes()->pluck('vote_key')->toArray();
                if(!empty($votesEventsKeys)){
                    $votesArray = array_merge($votesArray,$votesEventsKeys);
                }
                $cbsArray[] = $cb;
            }


            $results['cbs'] = $cbsArray;
            //Total comments/open/closed CBs
            $cbsStatistics['totalClosedCbs'] = $closedCbs;
            $cbsStatistics['totalOpenCbs'] = $openedCbs;
            $cbsStatistics['totalComments'] = $totalComments;
            //array with Likes vote events keys
            $cbsStatistics['votesArray'] = $votesArray;
            $results['cbsStatistics'] = $cbsStatistics;

            return response()->json(['data' => $results], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB list with Statistics'], 500);
        }

    }



    /**
     *
     * @SWG\Post(
     *  path="/cb/{cb_key}/addCbNews",
     *  summary="Add cb News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Cb news data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/cbNewsStore")
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="cb_key",
     *      in="path",
     *      description="Cb Key",
     *      required=true,
     *      type="string"
     *  ),
     *
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
     *
     *  @SWG\Response(
     *      response=201,
     *     @SWG\Schema(ref="#/definitions/cbNewsResponse"),
     *      description="The associated news"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store news to the cb
     * Returns the cb news
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCbNews(Request $request, $cbKey)
    {
        $createdBy = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['news_key'], $request);
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $newsKey = $request->json('news_key');
            $cbNews = $cb->news()->whereNewsKey($newsKey)->first();
            if(empty($cbNews)){
                $cbNews = $cb->news()->create(
                    [
                        'cb_id' => $cb->id,
                        'news_key' => $newsKey,
                        'tag' => $request->json('tag') ?? null,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy
                    ]
                );
            }

            $cbNews = $cb->news()->get();

            return response()->json(['data' => $cbNews], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store cb News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     *
     * @SWG\Get(
     *  path="/cb/{cb_key}/getCbNews",
     *  summary="Get Cb News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="cb_key",
     *      in="path",
     *      description="Cb Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="tag",
     *      in="query",
     *      description="Tag of news",
     *      required=false,
     *      type="string"
     *  ),
     *
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
     *
     *  @SWG\Response(
     *      response=200,
     *     @SWG\Schema(ref="#/definitions/cbNewsResponse"),
     *      description="The cb news"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Get Cb news
     * Returns the cb news
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbNews(Request $request, $cbKey)
    {
        $createdBy = ONE::verifyToken($request);
        try {

            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $tag = $request->get('tag');
            if(!empty($tag)){
                $cbNews = $cb->news()->whereTag($tag)->get();
            }else{
                $cbNews = $cb->news()->get();
            }

            return response()->json(['data' => $cbNews], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteCbNews",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *
     * @SWG\Delete(
     *  path="/cb/{cb_key}/deleteCbNews/{news_key}",
     *  summary="Delete Cb News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="cb_key",
     *      in="path",
     *      description="Cb Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="news_key",
     *      in="path",
     *      description="News Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
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
     *
     *  @SWG\Response(
     *      response=200,
     *     @SWG\Schema(ref="#/definitions/replyDeleteCbNews"),
     *      description="Ok"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete Cb news
     *
     * @param Request $request
     * @param $cbKey
     * @param $newsKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCbNews(Request $request, $cbKey,$newsKey)
    {
        $userKey = ONE::verifyToken($request);
        try {

            try {
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Cb not Found'], 404);
            }

            $cbNews = $cb->news()->whereNewsKey($newsKey)->firstOrFail();
            $cbNews->updated_by = $userKey;
            $cbNews->save();
            $cbNews->delete();

            return response()->json('ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb News not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     *
     * @SWG\Get(
     *  path="/cb/{cb_key}/getCbChildren",
     *  summary="Get Cb Children",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="cb_key",
     *      in="path",
     *      description="Cb Key",
     *      required=true,
     *      type="string"
     *  ),
     *
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
     *
     *  @SWG\Response(
     *      response=200,
     *     @SWG\Schema(ref="#/definitions/cbChildrenResponse"),
     *      description="The cb with children"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Get Cb news
     * Returns the cb news
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbChildren(Request $request, $cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbChildren = Cb::whereParentCbId($cb->id)->get();
            $cb->children = $cbChildren;

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     *
     * @SWG\Post(
     *  path="/cb/{cb_key}/getCbTopicsByTag",
     *  summary="Get Cb topics by Tag",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Cbs"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="cb_key",
     *      in="path",
     *      description="Cb Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Cb topics by tag data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicsByTagBody")
     *  ),
     *
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
     *
     *  @SWG\Response(
     *      response=200,
     *     @SWG\Schema(ref="#/definitions/cbTopicsTagResponse"),
     *      description="The cb with children"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/cbErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Get Cb topics by tag
     * Returns the cb with topics
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbTopicsByTag(Request $request,$cbKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest(['tag'], $request);
        try {
            $tag = $request->json('tag');
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbTopics = $cb->topics()->whereTag($tag)->get();
            $cb->topics = $cbTopics;
            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb with topics'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithPagination(Request $request, $cbKey)
    {
        try {
            $filterList = $request->input('filter_list');
            $numberOfTopicsToShow = $request->input("numberOfTopicsToShow",6);

            $userKey = ONE::verifyLogin($request);
            $response = [];
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $cb->statistics = $this->getStatistics($cb);
            $pageToken = $request->pageToken ?? null;
            $idSended = [];
            if(empty($pageToken)){
                do{
                    $pageToken = str_random(32);
                }while(Cache::has($pageToken));
            }else{
                if(empty(Cache::has($pageToken))){
                    Cache::put($pageToken, json_encode([]), 60);
                }
                $idSended = json_decode(Cache::get($pageToken));
            }

            $topicsCollection = $cb->topics()->whereNotIn('id', $idSended)->with(['lastPost', 'firstPost',
                'status' => function ($q) {
                    $q->where('active', '=', 1)->whereHas(
                        'statusType',
                        function ($query){
                            $query->where('code', '<>', 'not_accepted');
                        }
                    );
                }, 'status.statusType', 'followers','posts'])
                ->get();

            foreach($topicsCollection as $topic){
                $versions = $topic->topicVersions()->get();

                if(!$versions->isEmpty()){
                    if ($topic->topicVersions()->whereActive(1)->count()==1) {
                        $lastVersion = $topic->topicVersions()->whereActive(1)->firstOrFail();
                    }
                    else {
                        $lastVersion = $topic->topicVersions()->orderBy("version", "desc")->firstOrFail();
                    }

                    if(!empty($lastVersion)) {
                        $topic->title = $lastVersion->title;
                        $topic->subject = $lastVersion->subject;
                        $topic->contents = $lastVersion->contents;
                        $topic->version = $lastVersion->version;

                        if (!is_null($topic->firstPost)){
                            $topic->firstPost->setAttribute('contents', $lastVersion->contents);
                        }
                    }

                    $parameters = $lastVersion->topicParameters()->get();
                    $param = [];

                    foreach ($parameters as $parameter) {
                        $topicParameter = $topic->parameters()->with(['type', 'options'])->find($parameter->parameter_id);

                        if (!empty($topicParameter)) {
                            $topicParameter->pivot->value = $parameter->value;
                            $param[] = $topicParameter;
                        }
                    }

                    $topic->parameters = $param;
                    foreach($topic->parameters as $parameter){
                        $parameter->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
                    }

                    foreach ($topic->parameters as $parameter) {
                        if (!($parameter->translation($request->header('LANG-CODE')))) {
                            if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                                $translation = $parameter->parameterTranslations()->first();
                                if(!empty($translation)){
                                    $parameter->translation($translation->language_code);
                                }
                                else{
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                        foreach ($parameter->options as $option) {
                            if(!empty($option)){
                                if (!($option->translation($request->header('LANG-CODE')))) {
                                    if (!$option->translation($request->header('LANG-CODE-DEFAULT'))){
                                        $translation = $option->parameterOptionTranslations()->first();
                                        if(!empty($translation)){
                                            $option->translation($translation->language_code);
                                        }
                                        else{
                                            return response()->json(['error' => 'No translation found'], 404);
                                        }
                                    }
                                }

                                if(!empty($option->parameterOptionFields())){
                                    foreach($option->parameterOptionFields()->get() as $optionParam){
                                        if($optionParam->code == 'color'){
                                            $option->color = $optionParam->value;
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
                else{
                    $topic->parameters = $topic->parameters()->with(['type', 'options'])->get();
                    foreach($topic->parameters as $parameter){
                        $parameter->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
                    }

                    foreach ($topic->parameters as $parameter) {
                        if (!($parameter->translation($request->header('LANG-CODE')))) {
                            if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                                $translation = $parameter->parameterTranslations()->first();
                                if(!empty($translation)){
                                    $parameter->translation($translation->language_code);
                                }
                                else{
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }
                        foreach ($parameter->options as $option) {
                            if(!empty($option)){
                                if (!($option->translation($request->header('LANG-CODE')))) {
                                    if (!$option->translation($request->header('LANG-CODE-DEFAULT'))){
                                        $translation = $option->parameterOptionTranslations()->first();
                                        if(!empty($translation)){
                                            $option->translation($translation->language_code);
                                        }
                                        else{
                                            return response()->json(['error' => 'No translation found'], 404);
                                        }
                                    }
                                }

                                if(!empty($option->parameterOptionFields())){
                                    foreach($option->parameterOptionFields()->get() as $optionParam){
                                        if($optionParam->code == 'color'){
                                            $option->color = $optionParam->value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            /* Add Topic Moderation Date to it's attributes */
            $moderatedStatusTypeId = StatusType::whereCode("moderated")->get();
            if ($moderatedStatusTypeId->count()==1) {
                $moderatedStatusTypeId = $moderatedStatusTypeId->first()->id;
                foreach ($topicsCollection as $topic) {
                    $topicStatus = $topic->status()->get();
                    if ($topicStatus->count() > 0) {
                        $moderatedStatus = $topic->status()->whereStatusTypeId($moderatedStatusTypeId)->get();
                        if ($moderatedStatus->count()>0)
                            $topic->moderated_at = $moderatedStatus->first()->created_at->toDateTimeString();
                    };
                }
            }

            if ($request->has("filter_list.sort_order"))
                $sortOrder = $request->filter_list["sort_order"];
            else if (Cache::has($pageToken."_sortOrder"))
                $sortOrder = Cache::get($pageToken."_sortOrder");

            if (isset($sortOrder) && !empty($sortOrder)) {
                Cache::put($pageToken . "_sortOrder",$sortOrder,60);

                switch ($sortOrder) {
                    case "order_by_recent":
                        $topicsCollection = $topicsCollection->sortByDesc('created_at');
                        break;
                    case "order_by_popular":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return count($topic->followers);
                        });
                        break;

                    case "order_by_likes":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) use ($request) {
                            if($request->has("filter_list.votesPerTopic.".$topic->topic_key)){
                                return $request->get("filter_list")['votesPerTopic'][$topic->topic_key];

                            }else{
                                return 0;
                            }
                        });
                        break;

                    case "order_by_popular_as_parameter":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            $followersTypes = collect($topic->parameters)->where('code','=','numeric');
                            $followers = 0;
                            if(!empty($followersTypes)){
                                foreach($followersTypes as $followersType){
                                    $followers += $followersType->pivot->value;
                                }
                            }
                            return $followers;
                        });
                        break;
                    case "order_by_post_count":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return $topic->statistics->posts_counter;
                        });
                        break;
                    case "order_by_comments":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return $topic->posts()->whereActive(1)->count();
                        });
                        break;
                    case "order_by_random":
                        $topicsCollection = $topicsCollection->shuffle();
                        break;
                    default:
                        $topicsCollection = $topicsCollection->sortBy(function ($topic, $key) {
                            if (isset($topic->moderated_at) && !empty($topic->moderated_at))
                                return $topic->moderated_at;
                            else
                                return $topic->created_at;
                        });
                }
            } else {
                $topicsCollection = $topicsCollection->sortBy(function ($topic, $key) {
                    if (isset($topic->moderated_at) && !empty($topic->moderated_at))
                        return $topic->moderated_at;
                    else
                        return $topic->created_at;
                });
            }



            foreach ($topicsCollection as $topic) {
                $topic->alliances = $topic->originAllyRequest()->whereAccepted(1)->get();
                $topic->alliances->merge($topic->destinyAllyRequest()->whereAccepted(1)->get());
                $topic->posts_count = $topic->posts()->whereEnabled(1)->count();
            }


            $topics = [];
            foreach ($topicsCollection as $key => $topic) {

                //Removes from collection Topics without Status
                if ($topic->status->isEmpty()) {
                    $idSended[] = $topic->id;
                    $topicsCollection->forget($key);
                    continue;
                }

                if($topic->status->where("active",1)->where("statusType.code","published")->count()>0) {
                    $idSended[] = $topic->id;
                    $topicsCollection->forget($key);
                    continue;
                }

                if(!empty($topic->active_status) && $topic->active_status->statusType->code == 'not_accepted') {
                    $idSended[] = $topic->id;
                    $topicsCollection->forget($key);
                    continue;
                }

                //Get topic active status
                $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();

                if (!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated') {
                    $topic->closed = true;
                } else {
                    $topic->closed = false;
                }


                //---------------------------- Filter/Order Topics ---------------------------------



                if (!empty($filterList)) {
                    $filterParameters = [];
                    $filterOption = [];

                    foreach ($filterList as $itemKey => $value) {
                        if (preg_match("/filter_.*/", $itemKey)) {
                            $filterParameters[$itemKey] = trim($itemKey, "filter_");;
                            $filterOption[$itemKey] = $value;
                        }
                    }
                    $continue = false;
                    foreach ($filterParameters as $key=>$filterParameter) {
                        if (!empty($filterParameter) && !empty($filterOption[$key])) {
                            //check if topic have the parameter selected in the filter
                            if (!$topic->parameters->contains('id', $filterParameter)) {
                                $idSended[] = $topic->id;
                                $topicsCollection->forget($key);
                                $continue = true;
                            } else {
                                //check if parameter have the option selected in the filter
                                $topicParameter = $topic->parameters->where('id', $filterParameter)->first();
                                if ($topicParameter->pivot->value != $filterOption[$key]) {
                                    $options = explode(",",$topicParameter->pivot->value);
                                    $keepParameter = false;
                                    if(count($options)>0){
                                        foreach ($options as $option){
                                            if($option == $filterOption[$key]){
                                                $keepParameter = true;
                                                break;
                                            }
                                        }
                                    }
                                    if(!$keepParameter) {
                                        $topicsCollection->forget($key);
                                        $idSended[] = $topic->id;
                                        $continue = true;
                                    }
                                }
                            }
                        }
                    }
                    if(isset($filterList['archive']) && $filterList['archive'] != 0 ){
                        if (empty($archive = $topic->parameters->where('id', '=',$filterList['archive'])->first())) {
                            $topicsCollection->forget($key);
                            $idSended[] = $topic->id;
                            continue;
                        }
                    }

                    if(isset($filterList['archive']) && $filterList['archive'] == 0 ){
                        if (!empty($archive = $topic->parameters->where('id', '=',$filterList['archive'])->first())) {
                            $topicsCollection->forget($key);
                            $idSended[] = $topic->id;
                            continue;
                        }
                    }


                    if ($continue)
                        continue;

                    if (!empty($filterList['status'])) {

                        $statusType = StatusType::whereCode($filterList['status'])->first();

                        //Check if topic status is the same as the one used in the filter
                        if (empty($topic->active_status) || $topic->active_status->status_type_id != $statusType->id) {
                            $idSended[] = $topic->id;
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }

                    //search the topic for the provided search word
                    if (!empty($filterList['search'])) {
                        if (stripos($topic->title, $filterList['search']) === false && stripos($topic->contents, $filterList['search']) === false && stripos($topic->firstPost->contents, $filterList['search']) === false) {
                            $idSended[] = $topic->id;
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }



                }
                $topics[] = $topic;
                //----------------------------------------------------------------------------------------------
            }
            if (!empty($filterList['orderBy']))
                $filterListOrder = $filterList["orderBy"];
            else if(!empty($filterList['byVotes']))
                $filterListOrder = $filterList['byVotes'];
            else if (Cache::has($pageToken."_order"))
                $filterListOrder = Cache::get($pageToken."_order");

            if (isset($filterListOrder) && !empty($filterListOrder)) {
                Cache::put($pageToken . "_order",$filterListOrder,60);

                if(!empty($filterList['orderBy'])){
                    switch ($filterList['orderBy']) {
                        case 'rand':
                            $topics = collect($topics)->shuffle();
                            break;
                        case 'asc':
                            $topics = collect($topics)->sortBy('created_at');
                            break;
                        case 'desc':
                            $topics = collect($topics)->sortByDesc('created_at');
                            break;
                        default:
                            break;
                    }
                }

                if(!empty($filterList['byVotes'])){
                    switch($filterList['byVotes']) {
                        case 'desc':
                            $topics = collect($topics)->sortByDesc(function($topic, $key) use ($request){
                                if($request->has("filter_list.votesPerTopic.".$topic->topic_key)) {
                                    return $request->get("filter_list")['votesPerTopic'][$topic->topic_key];
                                }else{
                                    return 0;
                                }
                            });
                            break;
                        case 'asc':
                            $topics = collect($topics)->sortBy(function($topic, $key) use ($request){
                                if($request->has("filter_list.votesPerTopic.".$topic->topic_key)) {
                                    return $request->get("filter_list")['votesPerTopic'][$topic->topic_key];
                                }else{
                                    return 0;
                                }
                            });
                            break;
                    }
                }
            }

            $filteredTopicsCount = count($topics);
            $topics = collect($topics)->take($numberOfTopicsToShow);
            $statusTrans = [];
            $parameterTrans = [];
            $statusTrans = [];
            foreach ($topics as $key => $topic) {

                $topic->_cached_data = json_decode($topic->_cached_data);

                $idSended[] = $topic->id;
                foreach ($topic->active_status as $status) {
                    if (!empty($status->statusType)) {
                        if(!empty($statusTrans[$status->statusType->id])){
                            $status->statusType->translationByArray($statusTrans[$status->statusType->id]);
                        }else {
                            if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                                if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    $status->statusType = $status->statusType->statusTypeTranslations()->first();
                                }
                            }

                            $statusTrans[$status->statusType->id] = ['name'=> $status->statusType->name , 'description'=> $status->statusType->description];
                        }
                    }
                }


                $following = false;
                if (!empty($userKey)) {
                    $following = $topic->followers()->whereUserKey($userKey)->exists();
                }
                $topic->following = $following;

                foreach ($topic->status as $status) {
                    if (!empty($status->statusType)) {
                        if(!empty($statusTrans[$status->statusType->id])){
                            $status->statusType->translationByArray($statusTrans[$status->statusType->id]);
                        }else {
                            if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                                if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    $firstTranslationFound = $status->statusType->statusTypeTranslations()->first();
                                    $status->statusType->setAttribute("name",$firstTranslationFound->name);
                                    $status->statusType->setAttribute("description",$firstTranslationFound->description);
                                }
                            }
                            $statusTrans[$status->statusType->id] = ['name'=> $status->statusType->name , 'description'=> $status->statusType->description];
                        }
                    }
                }

                $posts = $topic->posts()->whereEnabled(1)->whereActive(1)->count();

                $topic['statistics'] = ['posts_counter' => $posts];

                $topic['accesses'] = $topic->topicAccesses()->count();

            }
            if ($topics->count()==0) {
                Cache::forget($pageToken);
                $pageToken = null;
            } else
                Cache::put($pageToken,json_encode($idSended),60);

            $statusTypeItem = new StatusTypesController();

            $response['cb'] = $cb;
            $response['topics'] = $topics;
            $response['moderators'] = $cb->moderators()->get();
            $response['configurations'] = $cb->configurations()->select('code')->pluck('code');
            $response['votes'] = $cb->votes()->get()->keyBy('vote_key');
            $response['statusTypes'] = $statusTypeItem->getStatusTypes(['langCode' => $request->header('LANG-CODE'), 'langCodeDefault' => $request->header('LANG-CODE-DEFAULT')]);
            $response['voteKeys'] = $cb->votes()->get()->pluck('vote_key');
            $response['pageToken'] = $pageToken;
            $response['statistics'] = $this->getStatistics($cb);
            $response['filteredTopicsCount'] = $filteredTopicsCount;


            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /*
    * Requests statistics.
    * Returns statistics.
    *
    * @param type $cbKey
    * @var array
    */
    private function getStatistics($cb)
    {
        try {
            $data = [];
            $data["topics"] = $cb->topics()->count();
            $data["posts"] = $cb->posts()->whereEnabled(1)->where('posts.active',1)->count();
            $data["votes"] = $cb->votes()->count();
            $data["topics_with_status"] = $cb->topics()->whereHas('status',
                function ($query){
                    $query->whereActive(1)->whereHas('statusType',
                        function ($query){
                            $query->where('code', '<>', 'not_accepted');
                        }
                    );
                }
            )->count();

            $data["user_participants"] = $cb->topics()->groupBy('created_by')->get()->count();


            $topicsCountByActiveStatus = array(
                "no_status" => 0
            );
            foreach ($cb->topics()->get() as $topic) {
                $currentIncrementIndex = "no_status";

                $activeStatus = $topic->status()->whereActive(1)->get();
                if ($activeStatus->count()>0) {
                    $activeStatusType = $activeStatus->first()->statusType()->get();
                    if ($activeStatusType->count()>0) {
                        if (!array_has($topicsCountByActiveStatus,$activeStatusType->first()->code))
                            $topicsCountByActiveStatus[$activeStatusType->first()->code] = 0;

                        $currentIncrementIndex = $activeStatusType->first()->code;
                    }
                }

                $topicsCountByActiveStatus[$currentIncrementIndex]++;
            }
            $data["topics_count_by_active_status"] = $topicsCountByActiveStatus;

            return $data;
        } catch (ModelNotFoundException $e) {
            return [];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function exportProposalsToProjects()
    {
        try {
            $proposalsCb = Cb::whereCbKey('BXrIvhd6E5fFcT4p8rHbd1NY5twtTu6z')->firstOrFail();
            $proposalTopics = Topic::with('firstPost', 'parameters', 'parameters.options')->whereCbId($proposalsCb->id)->get();
            $topicsToExport = [];
            $i = 0;
            foreach ($proposalTopics as $topic) {

                if (!empty($evaluation = collect($topic->parameters)->where('id', '=', 24)->first())) { // EVALUATION DROP DOWN
                    if (!empty($option = collect($evaluation->options)->where('id', '=', $evaluation->pivot->value)->first())) {
                        if ($option->translation('it')) {
                            if (strtoupper($option->label) == 'FATTIBILE' || strtoupper($option->label) == 'PARZIALMENTE FATTIBILE') { //ONLY WITH THIS EVALUATION
                                if (!empty($category = collect($topic->parameters)->where('id', '=', 15)->first())) { //CATEGORY
                                    if (!empty($categoryOption = collect($category->options)->where('id', '=', $category->pivot->value)->first())) {
                                        if ($categoryOption->translation('it')) {
                                            $topic['category'] = strtoupper($categoryOption->label);
                                        }
                                    }
                                }
                                if (!empty($district = collect($topic->parameters)->where('id', '=', 29)->first())) { //DISTRICT
                                    if (!empty($districtOption = collect($district->options)->where('id', '=', $district->pivot->value)->first())) {
                                        if ($districtOption->translation('it')) {
                                            $topic['district'] = strtoupper($districtOption->label);
                                        }
                                    }
                                }
                                if (!empty($coordinates = collect($topic->parameters)->where('id', '=', 11)->first())) { //MAP
                                    $topic['coordinates'] = $coordinates->pivot->value;
                                }
                                if (!empty($post = $topic->posts()->first())) {
                                    if (count($files = $post->files()->get()) > 0) {
                                        foreach ($files as $file) {
                                            $topic['files'] = $file;
                                        }

                                    }

                                }
                                // ADD PARENT TOPIC
                                $topic['parent_topic'] = $topic->id;

                                $topicsToExport[] = $topic;
                                $i++;
                            }
                        }
                    }
                }
                /*if ($i == 10)
                    break;*/
            }

            $projectsCb = Cb::whereCbKey('qilNAFMfO3t6eBRokS9EuQ4II9DOrO3z')->firstOrFail();

            $projectsCbCategoryParameter = $projectsCb->parameters()->whereCode('category')->first();
            $projectsCbCategoryParameterOptions = $projectsCbCategoryParameter->options()->get();
            if(!empty($projectsCbCategoryParameterOptions)){
                foreach ($projectsCbCategoryParameterOptions as $option){
                    if ($option->translation('it')) {
                        $categories[strtoupper($option->label)] = $option->id;
                    }
                }
            }

            $projectsCbDistrictParameter = $projectsCb->parameters()->whereCode('district')->first();
            $projectsCbDistrictParameterOptions = $projectsCbDistrictParameter->options()->get();
            if(!empty($projectsCbDistrictParameterOptions)){
                foreach ($projectsCbDistrictParameterOptions as $option){
                    if ($option->translation('it')) {
                        $districts[strtoupper($option->label)] = $option->id;
                    }
                }
            }
            $i = 0;
            foreach ($topicsToExport as $topic) {

                do {
                    $key = '';
                    $rand = str_random(32);
                    if (!($exists = Topic::whereTopicKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);
                $lastId = Topic::withTrashed()->max('id');
                $lastTopic = Topic::whereCbId($projectsCb->id)->orderBy('created_at', 'desc')->first();

                $newId = empty($lastId) ? 1 : $lastId + 1;
                $newTopic = $projectsCb->topics()->create(
                    [
                        'id' => $newId,
                        'topic_key' => $key,
                        'parent_topic_id' => $topic['parent_topic'] ?? 0,
                        'created_by' => 'defaultUSERprojectEMPATIA2016JAN',
                        'created_on_behalf' => null,
                        'title' => clean($topic['title']),
                        'blocked' => 0,
                        'contents' => clean($topic['contents']),
                        'status_id' => 0,
                        'q_key' => 0,
                        'topic_number' => !isset($lastTopic->topic_number) ? 1 : $lastTopic->topic_number + 1,
                        'start_date' => null,
                        'end_date' =>  null,
                        'tag' => null,
                    ]
                );
                //CATEGORY 398
                //DISTRICT 400
                //MAP 401
                $parameters = [];
                if(isset($topic['category'])){
                    $parameters[38] = $categories[strtoupper($topic['category'])];
                }
                if(isset($topic['district'])){
                    $parameters[46] = $districts[strtoupper($topic['district'])];
                }
                if(isset($topic['coordinates'])){
                    $parameters[34] = $topic['coordinates'];
                }
                foreach ($parameters as $id => $value){
                    $newTopic->parameters()->attach($id, [
                        'value' => $value
                    ]);
                }

                do {
                    $rand = str_random(32);
                    if (!($exists = Post::wherePostKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);
                $contents = '';
                if (!empty($topic['contents'])){
                    $contents = clean($topic['contents']);
                }

                $post = Post::create(
                    [
                        'post_key' => $key,
                        'topic_id' => $newTopic->id,
                        'created_by' => 'defaultUSERprojectEMPATIA2016JAN',
                        'enabled' => 1,
                        'contents' => $contents
                    ]
                );
                if(isset($topic['files'])){

                    $last = $post->files()->whereTypeId($topic['files']->type_id)->orderBy('position', 'desc')->first();
                    $position = $last ? $last->position + 1 : 0;
                    $post->files()->create(
                        [
                            'file_id' => $topic['files']->file_id,
                            'file_code' => $topic['files']->file_code,
                            'name' => $topic['files']->name,
                            'description' => $topic['files']->description,
                            'position' => $position,
                            'type_id' => $topic['files']->type_id
                        ]
                    );
                }




                //check topic needs moderation
                $config = Configuration::whereCode('topic_need_moderation')->first();
                if(!is_null($config)){
                    //check pivot table if topics need config in current CB
                    $cb_config = $projectsCb->configurations()->whereConfigurationId($config->id)->first();

                    if(is_null($cb_config)){
                        $statusType = StatusType::whereCode('moderated')->first();
                        if (!is_null($statusType)){

                            //"disable" previous statuses
                            $statusUpdate = Status::whereTopicId($newTopic->id)->update(['active' => 0]);

                            //new key for status
                            do {
                                $rand = str_random(32);
                                if (!($exists = Status::whereStatusKey($rand)->exists())) {
                                    $key = $rand;
                                }
                            } while ($exists);

                            $statusType->status()->create(
                                [
                                    'status_key' => $key,
                                    'status_type_id' => $statusType->id,
                                    'topic_id' => $newTopic->id,
                                    'active' => 1,
                                    'created_by' => 'defaultUSERprojectEMPATIA2016JAN'
                                ]
                            );
                        }
                    }
                }
                $i++;
                /*if ($i == 10)
                    break;*/
            }
            return response()->json("OK", 200);
        }catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }


    }


    public function getWithTopicKeys(Request $request, $cbKey)
    {
        try {
            $filterList = $request->input('filter_list');
            $numberOfTopicsToShow = $request->input("numberOfTopicsToShow",6);

            $topicKeys = $request->input('topicKeys');

            $userKey = ONE::verifyLogin($request);
            $response = [];
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $pageToken = $request->pageToken ?? null;
            $idSended = [];
            if(empty($pageToken)){
                do{
                    $pageToken = str_random(32);
                }while(Cache::has($pageToken));
            }else{
                if(empty(Cache::has($pageToken))){
                    Cache::put($pageToken, json_encode([]), 60);
                }
                $idSended = json_decode(Cache::get($pageToken));
            }

            $topicsCollection = $cb->topics()->whereIn('topic_key', $topicKeys)->whereNotIn("id",$idSended)->with(['parameters.type', 'parameters.options', 'lastPost', 'firstPost',
                'status' => function ($q) {
                    $q->where('active', '=', 1);
                }, 'status.statusType', 'followers'])
                ->get();


            if ($request->has("filter_list.sort_order"))
                $sortOrder = $request->filter_list["sort_order"];
            else if (Cache::has($pageToken."_sortOrder"))
                $sortOrder = Cache::get($pageToken."_sortOrder");

            if (isset($sortOrder) && !empty($sortOrder)) {
                Cache::put($pageToken . "_sortOrder",$sortOrder,60);

                switch ($sortOrder) {
                    case "order_by_recent":
                        $topicsCollection = $topicsCollection->sortByDesc('created_at');
                        break;
                    case "order_by_popular":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return count($topic->followers);
                        });
                        break;

                    case "order_by_popular_as_parameter":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            $followersTypes = collect($topic->parameters)->where('code','=','numeric');
                            $followers = 0;
                            if(!empty($followersTypes)){
                                foreach($followersTypes as $followersType){
                                    $followers += $followersType->pivot->value;
                                }
                            }
                            return $followers;
                        });
                        break;
                    case "order_by_post_count":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return $topic->statistics->posts_counter;
                        });
                        break;
                    case "order_by_comments":
                        $topicsCollection = $topicsCollection->sortByDesc(function ($topic, $key) {
                            return isset($topic->posts_count) ? $topic->posts_count-1 : 0;
                        });
                        break;
                    default:
                        $topicsCollection = $topicsCollection->shuffle();
                }
            }else{
                $topicsCollection = $topicsCollection->shuffle();
            }



            foreach ($topicsCollection as $topic){
                $topic->alliances = $topic->originAllyRequest()->whereAccepted(1)->get();
                $topic->alliances->merge($topic->destinyAllyRequest()->whereAccepted(1)->get());
                $topic->posts_count = $topic->posts()->whereEnabled(1)->count();
            }



            $topics = [];
            foreach ($topicsCollection as $key => $topic) {

                //Removes from collection Topics without Status
                if ($topic->status->isEmpty()) {
                    $idSended[] = $topic->id;
                    $topicsCollection->forget($key);
                    continue;
                }

                //Get topic active status
                $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();

                if (!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated') {
                    $topic->closed = true;
                } else {
                    $topic->closed = false;
                }

                //---------------------------- Filter/Order Topics ---------------------------------

                if (!empty($filterList)) {
                    $filterParameters = [];
                    $filterOption = [];

                    foreach ($filterList as $itemKey => $value) {
                        if (preg_match("/filter_.*/", $itemKey)) {
                            $filterParameters[$itemKey] = trim($itemKey, "filter_");;
                            $filterOption[$itemKey] = $value;
                        }
                    }
                    $continue = false;
                    foreach ($filterParameters as $key=>$filterParameter) {
                        if (!empty($filterParameter) && !empty($filterOption[$key])) {
                            //check if topic have the parameter selected in the filter
                            if (!$topic->parameters->contains('id', $filterParameter)) {
                                $idSended[] = $topic->id;
                                $topicsCollection->forget($key);
                                $continue = true;
                            } else {
                                //check if parameter have the option selected in the filter
                                $topicParameter = $topic->parameters->where('id', $filterParameter)->first();
                                if ($topicParameter->pivot->value != $filterOption[$key]) {
                                    $topicsCollection->forget($key);
                                    $idSended[] = $topic->id;
                                    $continue = true;
                                }
                            }
                        }
                    }

                    if ($continue)
                        continue;

                    if (!empty($filterList['status'])) {

                        $statusType = StatusType::whereCode($filterList['status'])->first();

                        //Check if topic status is the same as the one used in the filter
                        if (empty($topic->active_status) || $topic->active_status->status_type_id != $statusType->id) {
                            $idSended[] = $topic->id;
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }

                    //search the topic for the provided search word
                    if (!empty($filterList['search'])) {
                        if (strpos($topic->title, $filterList['search']) === false && strpos($topic->contents, $filterList['search']) === false && strpos($topic->firstPost->contents, $filterList['search']) === false) {
                            $idSended[] = $topic->id;
                            $topicsCollection->forget($key);
                            continue;
                        }
                    }


                }
                $topics[] = $topic;
                //----------------------------------------------------------------------------------------------
            }

            if (!empty($filterList['orderBy']))
                $filterListOrder = $filterList["orderBy"];
            else if (Cache::has($pageToken."_order"))
                $filterListOrder = Cache::get($pageToken."_order");

            if (isset($filterListOrder) && !empty($filterListOrder)) {
                switch ($filterList['orderBy']) {
                    case 'rand':
                        $topics = (collect($topics)->shuffle())->toArray();
                        break;
                    case 'asc':
                        $topics = (collect($topics)->sortBy('created_at'))->toArray();
                        break;
                    case 'desc':
                        $topics = (collect($topics)->sortByDesc('created_at'))->toArray();
                        break;
                    default:
                        break;
                }
            }

            $topics = collect($topics)->take($numberOfTopicsToShow);
            $parameterTrans = [];
            $statusTrans = [];
            foreach ($topics as $key => $topic) {
                $idSended[] = $topic->id;
                foreach ($topic->active_status as $status) {
                    if (!empty($status->statusType)) {
                        if(!empty($statusTrans[$status->statusType->id])){
                            $status->statusType->translationByArray($statusTrans[$status->statusType->id]);
                        }else {
                            if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                                if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    $status->statusType = $status->statusType->statusTypeTranslations()->first();
                                }
                            }

                            $statusTrans[$status->statusType->id] = ['name'=> $status->statusType->name , 'description'=> $status->statusType->description];
                        }
                    }
                }


                $following = false;
                if (!empty($userKey)) {
                    $following = $topic->followers()->whereUserKey($userKey)->exists();
                }
                $topic->following = $following;
                foreach ($topic->parameters as $parameter) {
                    if(!empty($parameterTrans[$parameter->id])){
                        $parameter->translationByArray($parameterTrans[$parameter->id]);
                    }else{
                        if (!($parameter->translation($request->header('LANG-CODE')))) {
                            if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                                $firstTranslationFound = $parameter->parameterTranslations()->first();
                                $parameter->setAttribute("parameter",$firstTranslationFound->parameter);
                                $parameter->setAttribute("description",$firstTranslationFound->description);
                            }
                        }
                        $parameterTrans[$parameter->id] = ['parameter'=> $parameter->parameter, 'description'=>$parameter->description];
                    }


                    //$parameter->translations();
                    foreach ($parameter->options as &$option) {
                        if (!empty($option)) {

                            if(!empty($parameterOptionTrans[$option->id])){
                                $parameter->translationByArray($parameterOptionTrans[$option->id]);
                            }else {
                                if (!($option->translation($request->header('LANG-CODE')))) {
                                    if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                        $firstTranslationFound = $option->parameterOptionTranslations()->first();
                                        $option->setAttribute("label",$firstTranslationFound->label);
                                    }
                                }
                                $parameterOptionTrans[$option->id] = ['label'=> $option->label];
                            }

                            $parameterOptionFields = $option->parameterOptionFields()->get();
                            foreach ($parameterOptionFields as $parameterOptionField){
                                $option[$parameterOptionField->code] = $parameterOptionField->value;
                            }
                        }
                        //$option->translations();
                    }
                }
                foreach ($topic->status as $status) {
                    if (!empty($status->statusType)) {
                        if(!empty($statusTrans[$status->statusType->id])){
                            $status->statusType->translationByArray($statusTrans[$status->statusType->id]);
                        }else {
                            if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                                if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    $firstTranslationFound = $status->statusType->statusTypeTranslations()->first();
                                    $status->statusType->setAttribute("name",$firstTranslationFound->name);
                                    $status->statusType->setAttribute("description",$firstTranslationFound->description);
                                }
                            }
                            $statusTrans[$status->statusType->id] = ['name'=> $status->statusType->name , 'description'=> $status->statusType->description];
                        }
                    }
                }

                $posts = $topic->posts()->whereEnabled(1)->whereActive(1)->count();

                $topic['statistics'] = ['posts_counter' => $posts];

                $topic['accesses'] = $topic->topicAccesses()->count();

            }

            if ($topics->count()==0) {
                Cache::forget($pageToken);
                $pageToken = null;
            } else
                Cache::put($pageToken,json_encode($idSended),60);

            $statusTypeItem = new StatusTypesController();

            $response['cb'] = $cb;
            $response['topics'] = $topics;
            $response['moderators'] = $cb->moderators()->get();
            $response['configurations'] = $cb->configurations()->select('code')->pluck('code');
            $response['votes'] = $cb->votes()->get()->keyBy('vote_key');
            $response['statusTypes'] = $statusTypeItem->getStatusTypes(['langCode' => $request->header('LANG-CODE'), 'langCodeDefault' => $request->header('LANG-CODE-DEFAULT')]);
            $response['voteKeys'] = $cb->votes()->get()->pluck('vote_key');
            $response['pageToken'] = $pageToken;
            $response['statistics'] = $this->getStatistics($cb);

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getTopicsByNumber(Request $request)
    {
        try {
            $cbKey = $request->get("cbKey");
            $topicsNumbers = $request->get("topicsNumbers");
            $cb = CB::whereCbKey($cbKey)->first();
            $topics = [];

            if($cb){
                foreach ($topicsNumbers as $topicNumber){
                    if( Topic::whereCbId($cb->id)->whereTopicNumber($topicNumber)->exists()){
                        $topics[] =  Topic::whereCbId($cb->id)->whereTopicNumber($topicNumber)->first();
                    }else{
                        return response()->json(['error' => 'No topic with that number', 'topic_number' => $topicNumber], 408);
                    }
                }
                return response()->json(['data' => $topics], 200);
            }else{
                return response()->json(['error' => 'Failed to get the cb'], 409);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get topics by number'], 500);
        }
    }



    /**
     * Requests a list of Topics.
     * Returns the list of Topics.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataToExport(Request $request, $cbKey)
    {
        try {
            $data = [];
            $cb = Cb::with('votes')->whereCbKey($cbKey)->firstOrFail();

            if ($request->withVotes) {
                $voteEvents = $cb->votes->pluck('vote_key');

                $allTotalVotes = Vote::allVoteResults($voteEvents);
                $eventNames = [];

                foreach ($allTotalVotes as $key => $item){
                    $totalVotes[$key] = collect($item->total_votes);
                    $eventNames[$key]['name'] = $cb->votes->where('vote_method',$key)->first()->name;
                }
            }

            $exportIds = $request->input('exportIds');

            if (!empty($exportIds)) {
                $exportIds = json_decode($exportIds);
                $orderFields = implode(',', $exportIds);

                $topics = $cb->topics()->with([
                    'topicVersions',
                    'lastPost',
                    'status',
                    'status.statusType',
                    'posts',
                    'likes'
                ])
                    ->orderByRaw('FIELD(id,'.$orderFields.')')
                    ->find($exportIds);

            } else {
                $topics = Topic::with([
                    'topicVersions',
                    'lastPost',
                    'status',
                    'status.statusType',
                    'posts',
                    'likes'
                ])->whereCbId($cb->id)->get();
            }

            $topicsData = array();
            foreach ($topics as $topic) {

                if (isset($topic->topicVersions) && !empty($topic->topicVersions)){
                    $version = $topic->topicVersions->where('active',1)->first();

                    if ($version) {
                        $topic->title = $version->title ?? $topic->title;
                        $topic->contents = $version->contents ?? $topic->contents;
                        $topic->summary = $version->summary ?? $topic->summary;
                        $topic->created_by = $version->created_by ?? $topic->created_by;
                        $topic->active_by = $version->active_by ?? $topic->active_by;
                        $topic->created_at = $version->created_at ?? $topic->created_at;
                        $topic->updated_at = $version->updated_at ?? $topic->updated_at;
                        $topic->topicVersionId = $version->id;
                    }
                }

                $parameters = [];
                $dropDownOptions = [];

                if ($request->withVotes) {

                    $voteData = [];
                    foreach ($totalVotes as $key => $totalEventVotes) {
                        if (isset($totalEventVotes[$topic->topic_key])) {
                            $voteData[$key]['votes'] = $totalEventVotes->get($topic->topic_key)->positive;
                        } else {
                            $voteData[$key]['votes'] = 0;
                        }
                        if  (isset($eventNames[$key])) {
                            $voteData[$key]['name'] = $eventNames[$key]['name'];
                        }
                    }
                }
                $topic['voteData'] = $voteData;

                $posts = $topic->posts->where("enabled", 1)->count();
                $likes = $topic->likes->where("like", 1)->count();
                $dislikes = $topic->likes->where("like", 0)->count();

                if (!empty($parameters = json_decode($topic->_cached_data))) {
                    if(!empty($parameters->parameters)){
                        foreach ($parameters->parameters as $parameter) {
                            if (!empty($parameterTrans[$parameter->id])) {
                                $parameter->parameter = $parameterTrans[$parameter->id]['parameter'];
                                $parameter->description = $parameterTrans[$parameter->id]['description'];
                            } else {
                                $langCode = $request->header('LANG-CODE');
                                $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                                if (!($parameter->translations->$langCode)) {
                                    if (!$parameter->translations->$langCodeDefault) {
                                        $firstTranslationFound = $parameter->translations->first();
                                        $parameter->parameter = $firstTranslationFound->parameter;
                                        $parameter->description = $firstTranslationFound->description;
                                    }
                                } else {
                                    $parameter->parameter = $parameter->translations->$langCode->parameter;
                                    $parameter->description = $parameter->translations->$langCode->description;
                                }
                                $parameterTrans[$parameter->id] = ['parameter' => $parameter->parameter, 'description' => $parameter->description];

                            }
                            if (!empty($options = $parameter->options)) {
                                foreach ($options as $option) {
                                    if (!empty($option)) {
                                        $langCode = $request->header('LANG-CODE');
                                        $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                                        if (!empty($parameterOptionTrans[$option->id])) {
                                            $option->label = $parameterOptionTrans[$option->id]['label'];
                                        } else {
                                            if (!($option->translations->$langCode)) {
                                                if (!$option->translations->$langCodeDefault) {
                                                    $firstTranslationFound = $option->translations->first();
                                                    $option->label = $firstTranslationFound->label;
                                                }
                                            } else {
                                                $option->label = $option->translations->$langCode->label;
                                            }
                                            $parameterOptionTrans[$option->id] = ['label' => $option->translations->$langCode->label];
                                        }

                                        $parameterOptionFields = $option->fields;
                                        if (!empty($parameterOptionFields)) {
                                            foreach ($parameterOptionFields as $key => $parameterOptionField) {
                                                $option->code = $parameterOptionField;
                                            }
                                        }
                                    }
                                }
                            }

                        }

                        $topic->parameters = $parameters->parameters;
                    }else{
                        $topic->parameters = [];
                    }
                }

                $status = $topic->status->where("active", 1)->first();
                if (!empty($status->statusType)) {
                    if (!empty($statusTrans[$status->statusType->id])) {
                        $status->statusType->translationByArray($statusTrans[$status->statusType->id]);
                    } else {
                        if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                            if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))) {
                                $firstTranslationFound = $status->statusType->statusTypeTranslations()->first();
                                $status->statusType->setAttribute("name", $firstTranslationFound->name);
                                $status->statusType->setAttribute("description", $firstTranslationFound->description);
                            }
                        }
                        $statusTrans[$status->statusType->id] = ['name' => $status->statusType->name, 'description' => $status->statusType->description];
                    }
                }

                $topic->statistics = ['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts];
                $topic['status'] = $status;
//                $topic['parameters'] = $parameters->parameters;


                $topic['dropDownOptions'] = $dropDownOptions;
                $topic['posts'] = $posts;

                $topicsData[] = $topic;
            }

            $data["cb"] = $cb;
            $data["topics"] = $topicsData;

            return response()->json(['data' => $data], 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Export list of topics to new cb.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportTopics(Request $request, $cbKey)
    {
        try {
            ONE::verifyKeysRequest(['topic_keys','cb_key_export'], $request);
            $topicKeys = $request->json('topic_keys');
            $parametersMapping = $request->json('mapping_parameters');
            $optionsMapping = $request->json('mapping_options');
            $cbKeyExport = $request->json('cb_key_export');
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbExport = Cb::whereCbKey($cbKeyExport)->firstOrFail();
            $topics = $cb->topics()->with(['parameters.type', 'parameters.options','firstPost','firstPost.files'])->whereIn('topic_key',$topicKeys)->get();

            foreach ($topics as $topic){
                /** *************** Topic exported Creation *************** */
                do {
                    $key = '';
                    $rand = str_random(32);
                    if (!($exists = Topic::whereTopicKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $lastTopic = Topic::whereCbId($cbExport->id)->orderBy('created_at', 'desc')->first();

                $lastId = Topic::withTrashed()->max('id');
                $newId = empty($lastId) ? 1 : $lastId + 1;

                /** Create new topic export */
                $topicExported = $cbExport->topics()->create(
                    [
                        'id' => $newId,
                        'topic_key' => $key,
                        'parent_topic_id' => 0,
                        'created_by' => $topic->created_by,
                        'created_on_behalf' => $topic->created_on_behalf,
                        'title' => $topic->title,
                        'description' => $topic->first_post->contents ?? $topic->description,
                        'blocked' => $topic->blocked,
                        'contents' => $topic->summary ?? '',
                        'status_id' => is_null($request->json('status_id')) ? 0 : clean($request->json('status_id')),
                        'q_key' => $topic->q_key,
                        'topic_number' => !isset($lastTopic->topic_number) ? 1 : $lastTopic->topic_number + 1,
                        'start_date' =>  null,
                        'end_date' => null,
                        'tag' => $topic->tag,
                    ]
                );


                $realFirstPost = Post::with("files")->whereTopicId($topic->id)
                    ->orderBy('id', 'asc')
                    ->first();

                /**
                 *  Create topic first post
                 */

                $this->createFirstPost($topic,$topicExported,$realFirstPost->files);

                /** Verify if are parameters to mapping
                 *  Create topic exported params mapping by topic params
                 */
                if (!empty($parametersMapping)) {
                    $this->mappingTopicParameters($topic,$topicExported,$parametersMapping,$optionsMapping);
                }

                /** *************** END Topic creation *************** */

            }

            return response()->json('ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /** Topic first post creation
     * @param $topic
     * @param $topicExported
     */
    private function createFirstPost($topic, $topicExported, $files)
    {
        do {
            $rand = str_random(32);
            if (!($exists = Post::wherePostKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);

        $post = Post::create(
            [
                'post_key' => $key,
                'topic_id' => $topicExported->id,
                'created_by' => $topic->firstPost->created_by,
                'enabled' => 1,
                'contents' => $topic->firstPost->contents
            ]
        );

        foreach ($files as $file) {
            $post->files()->create(
                [
                    'file_id' => $file->file_id,
                    'file_code' => $file->file_code,
                    'type_id' => $file->type_id,
                    'name' => $file->name,
                    'description' => $file->description,
                    'position' => $file->position
                ]
            );
        }
    }


    /** Mapping topic parameters and options to topic exported parameters and options
     * @param $parametersMapping
     * @param $topic
     * @param $topicExported
     * @param $optionsMapping
     */
    private function mappingTopicParameters($topic, $topicExported,$parametersMapping, $optionsMapping)
    {
        $manualSyncParameters = [];
        $parameters = [];
        foreach ($topic->parameters as $parameter) {
            if(!array_key_exists($parameter->id,$parametersMapping)){
                continue;
            }
            if(count($parameter->options) > 0){
                $values = explode(",",$parameter->pivot->value);
                $parameterOptionsExport = [];
                foreach ($values as $value){
                    if(array_key_exists($value,$optionsMapping)){
                        $parameterOptionsExport[] = $optionsMapping[$value];
                    }
                }
                $manualSyncParameters[$parametersMapping[$parameter->id]] = implode(",", $parameterOptionsExport);
            }
            else{
                $parameters[$parametersMapping[$parameter->id]] = ['parameter_id' => $parametersMapping[$parameter->id],'value' => $parameter->pivot->value];
            }
        }
        $topicExported->parameters()->sync($parameters);

        foreach ($manualSyncParameters as $id => $value){
            $topicExported->parameters()->attach($id, [
                'value' =>  clean($value)
            ]);
        }

    }

    public function verifyTemplate(Request $request){
        try {
            $template = CbTemplate::whereCbKey($request->cbKey)->whereConfigurationCode($request->configCode)->get();

            if($template == '[]'){
                $data['exists'] = false;
                $data['templateKey'] = null;
                return response()->json(['data'=> $data], 200);
            }
            else{
                $data['exists'] = true;
                foreach ($template as $value){
                    $data['templateKey'] = $value->template_key;
                }
                return response()->json(['data'=> $data], 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb template'], 500);
        }
    }

    public function setCbTemplate(Request $request){
        try {
            $cbKey = $request->cbKey;
            $configCode = $request->configCode;
            $emailTemplateKey = $request->emailTemplateKey;


            $cbTemplate = CbTemplate::create(
                [
                    'cb_key' => $cbKey,
                    'template_key' => $emailTemplateKey,
                    'configuration_code' => $configCode
                ]
            );

            return response()->json($cbTemplate, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to set cb template'], 500);
        }
    }

    public function getCbTemplates(Request $request){
        try{
            $cbKey = $request->cbKey;
            $templates = CbTemplate::whereCbKey($cbKey)->get();
            return response()->json($templates, 200);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to get cb templates'], 500);
        }
    }

    public function getCbWithFlags(Request $request,$cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail()->timezone($request);

            $flagType = FlagType::where('code','LIKE',$request['flagType'])->firstOrFail();

            $flags = $cb->flags()->whereFlagTypeId($flagType->id)->get();

            if($flags){
                foreach ($flags as $flag){
                    $flag = FlagsController::getTranslation($flag,$request);
                }
            }

            $cb->flags = $flags;

            return response()->json($cb, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function getCbTopicAuthors(Request $request,$cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $authors = $cb->topics()->get()->pluck('created_by');

            if($authors){
                $authors = $authors->unique();
            }

            return response()->json($authors, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB authors'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipationInformationForDataTable(Request $request)
    {
        try {
            if (isset($request['cbKeys'])) {
                foreach ($request['cbKeys'] as $cbKey) {
                    $tableData = $request->input('tableData') ?? null;
                    $cb = CB::whereCbKey($cbKey)->with('configurations')->firstOrFail();
                    $data['cb'] = $cb;

                    $query = $cb;

                    if ($request['withTopics']) {
                        $query = $query->topics();
                    }

                    if ($request['withPosts']) {
                        if($request['withFlags'] && isset($request['withFilters']['flags_filter'])){
                            $query =  $query->flags()->where('cb_flag.flag_id',$request['withFilters']['flags_filter'])->first()->posts()->with('topic');
                            $recordsTotal = $query->count();
                        }else{
                            $query = $query->posts()->with('flags')->with('topic');
                            $recordsTotal = $query->count();
                        }
                    }

                    if ($tableData) {
                        $data['query_result'] = $query
                            ->skip($tableData['start'])
                            ->take($tableData['length'])
                            //->orderBy($tableData['order']['value'],$tableData['order']['dir'])
                            ->get();
                        $data['recordsFiltered'] = count($data['query_result']);

                    }

                    foreach($data['query_result'] as $a){
                        foreach($a->flags as $flag){

                            if (!($flag->translation($request->header('LANG-CODE')))) {
                                if (!$flag->translation($request->header('LANG-CODE-DEFAULT'))){
                                    $translation = $flag->translations()->first();
                                    if(!empty($translation)){
                                        $flag->translation($translation->language_code);
                                    }
                                    else{
                                        return response()->json(['error' => 'No translation found'], 404);
                                    }
                                }
                            }
                        }
                    }
                    $data['recordsTotal'] = $recordsTotal;
                }
            }

            return response()->json(['data' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Information For DataTable'], 500);
        }
    }


    /**
     * GET INFORMATION FOR PARTICIPATION
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParticipationInformation(Request $request)
    {

        try {
            //CHECK IF REQUEST IS MADE FOR A TABLE
            if ($request['withTableData']) {
                $tableData = $request['withTableData'] ?? null;

            }
            $recordsTotal = 0;

            if (isset($request['cbKeys'])) {
                //GET THE CB
                $cbKey = $request['cbKeys'];
                $cb = CB::whereCbKey($cbKey)->with('configurations')->firstOrFail();
                $data['cb'] = $cb;
                $query = $cb;

                //GET WITH TOPICS
                if ($request['withTopics']) {
                    $query = $query->topics(); //GET ALL THE TOPICS
                    $recordsTotal = $query->count(); //SET THE TOTAL RECORDS

                    //GET TOPICS THAT NEED MODERATION [THE ONES THAT DON'T HAVE A ACTIVE STATUS]
                    if($request['withModeration']){
                        //ONLY DO THIS IF THE PAD NEEDS TOPICS MODERATION [CONFIGURATION : TOPICS_NEED_MODERATION]
                        if(!empty(collect($cb->configurations)->where('code','=','topic_need_moderation')->first())) {
                            $query = $query->whereDoesntHave('status',function ($q){
                                $q->where('active', '=', 1);
                            });
                        }
                    }

                    //GET THE TOPICS WITH A SPECIFIC STATUS
                    if($request['withStatus']){
                        $statusCode =$request['withStatus'];
                        $query = $query->whereHas('status',function ($q) use($statusCode){
                            $q->where('active', '=', 1) //GET THE ACTIVE STATUS
                            ->whereHas(
                                'statusType', function ($query) use ($statusCode) { //CHECK IF IT HAS THE REQUESTED CODE
                                $query->where('code', '=', $statusCode);
                            });
                        });
                    }
                }

                //GET WITH POSTS
                if ($request['withPosts']) {
                    //IF WE WANT POSTS, WE NEED TO REMOVE THE CONTENT OF EACH TOPIC WHICH ARE ALSO POSTS
                    $filterWithData = $cb->topics()->with('firstPost')->get();

                    $doNotFetchThisIds = $filterWithData->pluck('firstPost.id');
                    $fetchWithThisIds = $filterWithData->pluck('id');

                    $query = $cb->posts()->whereNotIn('posts.id',$doNotFetchThisIds)->whereIn('posts.topic_id',$fetchWithThisIds)->with('topic');


                    //IF THE PAD ALLOWS ABUSES TO BE REPORTED FETCH THE POSTS WITH ABUSES
                    if(!empty(collect($cb->configurations)->where('code','=','security_allow_report_abuses')->first())) {
                        $query = $query->with('abuses');
                    }

                    //THE USER ONLY SEES THE POSTS THAT MEETS THIS CONDITIONS
                    $query = $query->where('posts.enabled', '=', 1)->where('posts.blocked', '=', 0);


                    //SET THE RECORDS TOTAL
                    $recordsTotal = $query->count();

                    //GET WITH POST FLAGS TODO: WE NEED TO PREPARE THIS FOR CB,TOPIC FLAGS
                    if($request['withFlags']) {
                        //CHECK IF WE WANT POSTS WITH A SPECIFIC FLAG
                        if (isset($request['withFilters']['flags_filter'])) {
                            //SET THE FLAG TO FILTER BY
                            $flagToFilter = $request['withFilters']['flags_filter'];
                            $query = $query->whereHas('flags',function ($q) use($flagToFilter){
                                $q->where('flag_id', '=', $flagToFilter);
                            });
                        }

                    }else{ //WE DON'T WANT FLAGS
                        //GET THE POSTS THAT DON'T HAVE FLAGS
                        $query = $query->whereDoesntHave('flags');
                    }

                    if($request['withModeration']) { // TODO : WE NEED TO VERIFY THIS FOR TOPICS AND FOR POSTS
                        //CHECK IF THE PAD NEEDS THE COMMENTS TO BE MODERATED [CONFIGURATION SECURITY_COMMENT_AUTHORIZATION]
                        if(!empty(collect($cb->configurations)->where('code','=','security_comment_authorization')->first())) {

                            //IF SO WE NEED TO ATTACHED THIS TO THE QUERY
                            $query = $query->where('posts.active', '=', 0);
                        }
                    }
                }

                //GET WITH LIMIT OF RECORDS
                if($request['withLimit']){
                    $query = $query->take($request['withLimit']);
                }

                //GET WITH SPECIFIC SORT ORDER
                if($request['withSortOrder']){
                    if(strtoupper($request['withSortOrder']) == 'DESC'){

                        if ($request['withTopics']) {
                            $query = $query->orderByDesc('topics.created_at');
                        }
                        if ($request['withPosts']) {
                            $query = $query->orderByDesc('posts.created_at');
                        }

                    }else{
                        if ($request['withTopics']) {
                            $query = $query->orderBy('topics.created_at');
                        }

                        if ($request['withPosts']) {
                            $query = $query->orderBy('posts.created_at');
                        }
                    }
                }

                if (isset($tableData)) {
                    $data['query_result'] = $query
                        ->skip($tableData['start'])
                        ->take($tableData['length'])
                        ->get();
                }

                $data['recordsFiltered'] = $query->count();
                $data['recordsTotal'] = $recordsTotal;
                $data['query_result'] = $query->get();
                return response()->json(['data' => $data], 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Information For DataTable'], 500);
        }
    }

    /**
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicsWithBasicData($cbKey)
    {
        try {
            $topics = Cb::whereCbKey($cbKey)->firstOrFail()->topics()->get();

            $data = [];
            foreach ($topics as $topic) {
                $data[] = array(
                    "topic_key" => $topic->topic_key,
                    "created_by" => $topic->created_by,
                    "title" => $topic->title,
                );
            }

            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CB Topics'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $query
     * @param $sortOrder
     * @param $request
     * @return mixed
     */
    public function reorderResultsAccordingToGivenOrder($query, $sortOrder, $request, $cb){
        switch (!empty($sortOrder) ? $sortOrder : "") {
            case "order_by_recent":
                $query->orderBy('created_at', 'DESC');
                break;
            case "order_by_popular":
                $query->orderBy('followers_count', 'DESC');
                break;
            case "order_by_post_count":
            case "order_by_comments":
                $query->orderBy('posts_count', 'DESC');
                break;
            case "order_by_random":
                $query->inRandomOrder();
                break;
            case "order_by_likes":
                $this->orderByGivenVoteMethod($request, $cb, $query, "likes");
                break;
            case "order_by_multi_vote":
                $this->orderByGivenVoteMethod($request, $cb, $query, "multi_voting");
                break;

            default:
                $query->orderBy('created_at', 'DESC');
//            default:
//                $query
//                    ->join('status as my_status', 'my_status.topic_id', '=', 'topics.id')
//                    ->where('my_status.active', 1)
//                    ->where('my_status.status_type_id', 6)
//                    ->orderBy('my_status.created_at', 'desc');
        }

        return $query;
    }


    /**
     * @param $request
     * @param $cb
     * @param $query
     * @param $voteMethod
     */
    private function orderByGivenVoteMethod($request, &$cb, &$query, $voteMethod) {
        if ($request->has("filter_list.votesPerTopic")) {
            $orderKeys = $request->get("filter_list")['votesPerTopic'];
        } else if(isset($cb->votes)) {
            $voteKey = collect($cb->votes)->where("vote_method","=",$voteMethod)->first()->vote_key ?? "";

            if (!empty($voteKey)) {
                $response = One::get([
                    'component' => 'vote',
                    'api' => 'event',
                    'api_attribute' => $voteKey,
                    'method' => 'voteResults'
                ]);

                $totalVotes = $response->json()->total_votes;
                $orderKeys = [];
                foreach ($totalVotes as $key => $totalVote) {
                    $orderKeys[$key] = $totalVote->positive - $totalVote->negative;
                }
            }
        }

        if (isset($orderKeys)) {
            asort($orderKeys);
            $str = "";
            foreach ($orderKeys as $orderKey=>$orderValue) {
                $str .= '"' . $orderKey . '",';
            }
            $str = rtrim($str, ",");
            $query->orderByRaw('FIELD(topic_key,' . $str . ') DESC');
        }
    }


    /**
     * @param $query
     * @param $sortOrder
     * @param $request
     * @param $cb
     * @return mixed
     */
    public function reorderResultsAccordingToGivenOrderSecondPhase($query, $sortOrder, $request, $cb){
        if (isset($sortOrder) && !empty($sortOrder)) {
            switch ($sortOrder) {
//                case "order_by_random":
//                    $query = $query->inRandomOrder();
//                    break;
            }
        }
        return $query;
    }

    /**
     * @param $configurations
     * @param $code
     * @return bool
     */
    public function hasConfiguration($configurations, $code)
    {
        if($configurations->contains(strtolower($code))){
            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicPadInformation(Request $request, $cbKey)
    {
        //if this method is changed make the same change in the getMultiplePublicPadsInformation, if it applies there
        try{
            //REQUESTED LANGUAGEs
            $languageCode = $request->header('LANG-CODE');
            $users = [];
            //GET THE CURRENT USER
            $userKey = ONE::verifyLogin($request);

            //INITIALIZE THE RESPONSE
            $response = [];

            //GET THE FILTERS
            $filterParameters = [];
            $filterList = $request->input('filter_list');

            foreach ($filterList as $itemKey => $value) {
                if (preg_match("/filter_.*/", $itemKey)) {
                    $filterParameters[trim($itemKey, "filter_")] = $value;
                }
            }

            //GET THE LIMIT OF TOPICS TO GET IN EACH REQUEST
            $limit = $request->input("numberOfTopicsToShow",6);

            //GET THE SORT ORDER
            if ($request->has("filter_list.sort_order"))
                $sortOrder = $filterList["sort_order"];


            $currentSent = [];
            $pageToken = $request->pageToken ?? null;

            if(empty($pageToken)){
                do{
                    $pageToken = str_random(32);
                }while(Cache::has($pageToken));
            }else{
                if(empty(Cache::has($pageToken))){
                    Cache::put($pageToken, json_encode([]), 60);
                }
                $currentSent = json_decode(Cache::get($pageToken),true);
            }
            //GET THE CB AND STORE IT

            if (Cache::has($pageToken)){
                $cb = CB::whereCbKey($cbKey)->firstOrFail();
            }else{
                $cb = CB::whereCbKey($cbKey)
                    ->with([
                        'votes',
                        'configurations',
                        'moderators',
                        'parameters.parameterTranslations' => function ($q) use ($languageCode){
                            $q->where('language_code', '=', $languageCode);
                        },
                        'parameters.options.parameterOptionTranslations' => function ($q) use ($languageCode){
                            $q->where('language_code', '=', $languageCode);
                        },
                        'parameters.parameterFields',
                        'operationSchedules' => function($q) {
                            $q
                                ->where("active","=","1")
                                ->where(function($q) {
                                    $q
                                        ->whereDate("start_date",">",Carbon::now())
                                        ->orWhereDate("end_date","<",Carbon::now());
                                });
                        }
                    ])
                    ->firstOrFail();
            }

            /* Create Operation Schedules object */
            $operationSchedules = array();
            $operationTypes = OperationType::all();
            $operationActions = OperationAction::all();
            foreach ($operationTypes as $operationType) {
                $operationSchedules[$operationType->code] = array();

                foreach ($operationActions as $operationAction) {
                    $cbOperationSchedule = $cb->operationSchedules->where("operation_type_id",$operationType->id)->where("operation_action_id",$operationAction->id);
                    $operationSchedules[$operationType->code][$operationAction->code] = $cbOperationSchedule->isEmpty();
                }
            }

            if($request->has('filter_list.only_basic_information')){
                $response = array(
                    'cb'                  => $cb,
                    'operationSchedules'  => $operationSchedules
                );

                return response()->json($response, 200);
            }

            //GET THE CONFIGURATIONS
            $configurations = collect($cb->configurations)->pluck('code');

            //INITIALIZE THE QUERY
            $query = $cb;

            //RUN THE QUERY TO GET THE TOPICS
            $query = $query->topics()->whereNotNull("_cached_data");

            if (!empty($filterList['search'])) {
                $search = $filterList["search"];
                $query = $query->where(function ($q) use ($search) {
                    $q->where("topics.title", "LIKE", "%" . $search . "%")->orWhere("topics.contents", "LIKE", "%" . $search . "%");
                });
            }

            if(isset($filterList['profile']) and $filterList['profile']){
                $query = $query
                    ->with([
                        'lastPost',
                        'firstPost',
                        'status.statusType',
                        'posts.files',
                        'moderation_date'
                    ])
                    ->whereNotIn("topic_key",$currentSent);
            }else{
                $query = $query
                    ->with([
                        'lastPost',
                        'firstPost',
                        'status.statusType',
                        'posts.files',
                        'moderation_date'
                    ])
                    ->whereRaw('topics.id in (select topic_id from status where active = 1 AND status_type_id <> ?)', [StatusType::whereCode('not_accepted')->first()->id])
                    ->whereNotIn("topic_key",$currentSent);
            }

            if($request->has("filter_list.parentTopicKey")){
                $parentTopic =  Topic::whereTopicKey($filterList['parentTopicKey'])->first();
                if(!empty($parentTopic)) {
                    $query = $query->whereParentTopicId($parentTopic->id);
                }
            }
            //REORDER THE TOPICS COLLECTION
            $query = $this->reorderResultsAccordingToGivenOrder($query,isset($sortOrder) ? $sortOrder : '',$request,$cb);

            if($request->has("filter_list.filter_phases")){
                $filterParameters[$request->get("filter_list")['filter_phases']] = 1;
                unset($filterParameters['phases']);

            }
            //dd($filterParameters);
            if(!empty($filterParameters)){

                foreach ($filterParameters as $parameter => $parameterValue){

                    $query = $query->where(function($q) use ($parameter,$parameterValue) {
                        $q->whereHas('topicVersions',function ($q) use ($parameter,$parameterValue) {
                            $q->where('topic_versions.active', '=', 1)
                                ->whereHas('topicParametersPivot',function ($q) use ($parameter,$parameterValue){
                                    $q->where('id', $parameter)->where(function($q)use ($parameterValue) {
                                        $q->where('topic_parameters.value', '=', $parameterValue)
                                            ->orWhere("topic_parameters.value", "LIKE", "%," . $parameterValue)
                                            ->orWhere("topic_parameters.value", "LIKE", $parameterValue . ",%")
                                            ->orWhere("topic_parameters.value", "LIKE", "%," . $parameterValue . ",%");
                                    });
                                });
                        })->orWhereHas('parameters',function ($q) use ($parameter,$parameterValue){
                            $q->where('id', $parameter)->where(function($q)use ($parameterValue) {
                                $q
                                    ->where("topic_parameters.topic_version_id","=",0)
                                    ->where(function($q) use ($parameterValue) {
                                        $q
                                            ->where('topic_parameters.value', '=', $parameterValue)
                                            ->orWhere("topic_parameters.value", "LIKE", "%," . $parameterValue)
                                            ->orWhere("topic_parameters.value", "LIKE", $parameterValue . ",%")
                                            ->orWhere("topic_parameters.value", "LIKE", "%," . $parameterValue . ",%");
                                    });
                            });
                        });
                    });
                }
            }

            if($this->hasConfiguration($configurations,'ALLOW_ALLIANCE')) {
                $query->with([
                    "originAllyRequest" => function($q) {
                        $q->where("accepted","=","1");
                    },"destinyAllyRequest" => function($q) {
                        $q->where("accepted","=","1");
                    }
                ]);
            }

            $filteredTopicsCount = $query->count();
            $topicsCollection = $query->take($limit)->get();

            $currentSent = array_merge($currentSent,$topicsCollection->pluck("topic_key","topic_key")->toArray());

            if($topicsCollection) {
                $userKeys = collect($topicsCollection)->pluck('created_by')->unique();

                $entityKey = $request->header('X-ENTITY-KEY','');

                $users = User::whereHas('orchUser.entities', function ($q) use ($entityKey) {
                    $q->where("entity_key","=",$entityKey);
                })->whereIn('user_key', $userKeys)
                    ->select('user_key', 'name', 'public_name','surname','public_surname','photo_id', 'photo_code')
                    ->get()
                    ->keyBy('user_key');


                foreach ($users as $user) {
                    if ($user->public_name!=1)
                        $user->name = "";

                    if ($user->public_surname!=1)
                        $user->surname = "";
                }
            }

            $parameterTrans = [];
            $statusTrans = [];

            foreach ($topicsCollection as $key => $topic) {
                $validation = json_decode($topic->_cached_data);
                if (isset($validation->parameters) && !empty($parameters = $validation->parameters)) {
                    foreach ($parameters as $parameter) {
                        if (!empty($parameterTrans[$parameter->id])) {
                            $parameter->parameter =   $parameterTrans[$parameter->id]['parameter'];
                            $parameter->description =  $parameterTrans[$parameter->id]['description'];
                        } else {
                            $langCode = $request->header('LANG-CODE');
                            $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                            if (!isset($parameter->translations->$langCode)) {
                                if (!isset($parameter->translations->$langCodeDefault)) {
                                    $firstTranslationFound = collect($parameter->translations)->first();
                                    $parameter->parameter =  $firstTranslationFound->parameter;
                                    $parameter->description =  $firstTranslationFound->description;
                                } else {
                                    $parameter->parameter =  $parameter->translations->$langCodeDefault->parameter;
                                    $parameter->description =  $parameter->translations->$langCodeDefault->description;
                                }
                            }else{
                                $parameter->parameter =  $parameter->translations->$langCode->parameter;
                                $parameter->description =  $parameter->translations->$langCode->description;
                            }
                            $parameterTrans[$parameter->id] = ['parameter' => $parameter->parameter, 'description' => $parameter->description];

                        }
                        if (!empty($options = $parameter->options)) {
                            foreach ($options as $option) {
                                if (!empty($option)) {
                                    $langCode = $request->header('LANG-CODE');
                                    $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                                    if(!empty($parameterOptionTrans[$option->id])){
                                        $option->label = $parameterOptionTrans[$option->id]['label'];
                                    }else {
                                        if (!isset($option->translations->$langCode)) {
                                            if (!isset($option->translations->$langCodeDefault)) {
                                                $firstTranslationFound = collect($option->translations)->first();
                                                $option->label = $firstTranslationFound->label;
                                            } else {
                                                $option->label = $option->translations->$langCodeDefault->label;
                                            }
                                        }else{
                                            $option->label = $option->translations->$langCode->label;
                                        }
                                        $parameterOptionTrans[$option->id] = [
                                            'label'=> $option->label
                                        ];
                                    }

                                    $parameterOptionFields = $option->fields;
                                    if(!empty($parameterOptionFields)) {
                                        foreach ($parameterOptionFields as $key => $parameterOptionField) {
                                            $option->code = $parameterOptionField;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $topic->parameters = $parameters;
                }
            }

            Cache::put($pageToken,json_encode($currentSent),60);

            if($this->hasConfiguration($configurations,'ALLOW_ALLIANCE')){
                foreach($topicsCollection  as $key => $topic){
                    $topic->alliances = $topic->originAllyRequest;
                    $topic->alliances->merge($topic->destinyAllyRequest);

                    unset($topic->originAllyRequest);
                    unset($topic->destinyAllyRequest);
                }
            }
            $cbStatistics = json_decode($cb->_statistics);
            $statistics['topics'] = $cbStatistics->counts->topics??0;
            $statistics['posts'] = $cbStatistics->counts->posts??0;

            //PREPARE RESPONSE TO SEND
            $response = array([
                'pageToken'           => (count($topicsCollection) > 0) ? $pageToken: null,
                'cb'                  => $cb,
                'configurations'      => $configurations,
                'moderators'          => $cb->moderators,
                'topics'              => $topicsCollection,
                'votes'               => collect($cb->votes)->pluck('vote_key'),
                'statistics'          => null,
                'filteredTopicsCount' => $filteredTopicsCount,
                'users'               => $users,
                'statistics'          => $statistics,
                'operationSchedules'  => $operationSchedules
            ]);

            return response()->json($response, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMultiplePublicPadsInformation(Request $request)
    {
        //if this method is changed make the same change in the getPublicPadInformation, if it applies there
        try{
            //REQUESTED LANGUAGEs
            $entityCbs = ONE::getEntity($request)->entityCbs()->with('cbType')->get();

            $cbsKeys = [];

            if(!$entityCbs->isEmpty()){
                $cbsTypes = $entityCbs->pluck('cbType.code', 'cb_key');
                $cbsKeys = $entityCbs->pluck('cb_key');
            }

            $languageCode = $request->header('LANG-CODE');
            $users = [];
            //GET THE CURRENT USER
            $userKey = ONE::verifyLogin($request);

            //GET THE LIMIT OF TOPICS TO GET IN EACH REQUEST
            $limit = $request->input("numberOfTopicsToShow",6);

            $currentSent = [];
            $pageToken = $request->pageToken ?? null;

            if(empty($pageToken)){
                do{
                    $pageToken = str_random(32);
                }while(Cache::has($pageToken));
            }else{
                if(empty(Cache::has($pageToken))){
                    Cache::put($pageToken, json_encode([]), 60);
                }
                $currentSent = json_decode(Cache::get($pageToken),true);
            }

            //GET THE CB AND STORE IT
            $cbs = CB::whereIn('cb_key', $cbsKeys)
                ->with([
                    'votes',
                    'configurations'
                ])->whereHas('topics')->get();

            $cbsIds = [];
            $topicsCollection = [];
            if(isset($cbs) && !empty($cbs)){
                $cbs = collect($cbs);
                foreach($cbs as $cb){
                    $cbsIds[] = $cb->id;
                }

                $topicsCollection = Topic::whereIn('cb_id', $cbsIds)
                    ->with([
                        'lastPost',
                        'firstPost',
                        'status.statusType',
                        'posts.files',
                        'moderation_date'
                    ])
                    ->whereNotNull("_cached_data")
                    ->whereCreatedBy($userKey)
                    ->whereNotIn("topic_key", $currentSent)
                    ->take($limit)
                    ->get();

                $userKeys = collect($topicsCollection)->pluck('created_by')->unique();

                $entityKey = $request->header('X-ENTITY-KEY','');

                $users = User::whereHas('orchUser.entities', function ($q) use ($entityKey) {
                    $q->where("entity_key","=",$entityKey);
                })->whereIn('user_key', $userKeys)
                    ->select('user_key', 'name', 'public_name','surname','public_surname','photo_id', 'photo_code')
                    ->get()
                    ->keyBy('user_key');


                foreach ($users as $user) {
                    if ($user->public_name!=1)
                        $user->name = "";

                    if ($user->public_surname!=1)
                        $user->surname = "";
                }


                $parameterTrans = [];
                foreach($topicsCollection as $key => $topic){
                    $cbKey = $cbs->where('id', $topic->cb_id)->first()->cb_key;
                    $topic->type = isset($cbsTypes[$cbKey]) ? $cbsTypes[$cbKey] : '';
                    $topic->cbKey = $cbKey;
                    $topic->configurations = $cbs->where('id', $topic->cb_id)->first()->configurations;

                    $topic->status = $topic->status->first();
                    if(!empty($topic->status)){
                        $topic->status->statusType->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
                    }

                    $validation = json_decode($topic->_cached_data);
                    if (isset($validation->parameters) && !empty($parameters = $validation->parameters)) {
                        foreach ($parameters as $parameter) {
                            if (!empty($parameterTrans[$parameter->id])) {
                                $parameter->parameter =   $parameterTrans[$parameter->id]['parameter'];
                                $parameter->description =  $parameterTrans[$parameter->id]['description'];
                            } else {
                                $langCode = $request->header('LANG-CODE');
                                $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                                if (!isset($parameter->translations->$langCode)) {
                                    if (!isset($parameter->translations->$langCodeDefault)) {
                                        $firstTranslationFound = collect($parameter->translations)->first();
                                        $parameter->parameter =  $firstTranslationFound->parameter;
                                        $parameter->description =  $firstTranslationFound->description;
                                    } else {
                                        $parameter->parameter =  $parameter->translations->$langCodeDefault->parameter;
                                        $parameter->description =  $parameter->translations->$langCodeDefault->description;
                                    }
                                }else{
                                    $parameter->parameter =  $parameter->translations->$langCode->parameter;
                                    $parameter->description =  $parameter->translations->$langCode->description;
                                }
                                $parameterTrans[$parameter->id] = ['parameter' => $parameter->parameter, 'description' => $parameter->description];

                            }
                            if (!empty($options = $parameter->options)) {
                                foreach ($options as $option) {
                                    if (!empty($option)) {
                                        $langCode = $request->header('LANG-CODE');
                                        $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                                        if(!empty($parameterOptionTrans[$option->id])){
                                            $option->label = $parameterOptionTrans[$option->id]['label'];
                                        }else {
                                            if (!isset($option->translations->$langCode)) {
                                                if (!isset($option->translations->$langCodeDefault)) {
                                                    $firstTranslationFound = collect($option->translations)->first();
                                                    $option->label = $firstTranslationFound->label;
                                                } else {
                                                    $option->label = $option->translations->$langCodeDefault->label;
                                                }
                                            }else{
                                                $option->label = $option->translations->$langCode->label;
                                            }
                                            $parameterOptionTrans[$option->id] = [
                                                'label'=> $option->label
                                            ];
                                        }

                                        $parameterOptionFields = $option->fields;
                                        if(!empty($parameterOptionFields)) {
                                            foreach ($parameterOptionFields as $key => $parameterOptionField) {
                                                $option->code = $parameterOptionField;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $topic->parameters = $parameters;
                    }
                }
                $currentSent = array_merge($currentSent,$topicsCollection->pluck("topic_key","topic_key")->toArray());

            }

            $response = [
                'pageToken'           => (count($topicsCollection) > 0) ? $pageToken : null,
                'topics'              => $topicsCollection,
                'users'               => $users,
            ];

            Cache::put($pageToken,json_encode($currentSent),60);
            return response()->json($response, 200);


        }catch(Exception $e){
            dd($e->getMessage(), $e->getLine());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishPhase($cbKey, $topicCheckpointNewId) {
        try {
            $cb = CB::with("topics.parameters","votes")->whereCbKey($cbKey)->firstOrFail();

            $response = One::get([
                'component' => 'vote',
                'api' => 'event',
                'api_attribute' => $cb->votes->first()->vote_key,
                'method' => 'voteResults'
            ]);
            if($response->statusCode() != 200)
                throw new Exception(trans("comModulesVote.errorRetrievingCbTotalVotes"));


            $totalVotes = collect($response->json()->total_votes);
            $topicCheckpointBooleanParameter = Parameter::whereCode("topic_checkpoints_boolean")->first();
            $topicCheckpointParameter = Parameter::whereCode("topic_checkpoints")->first();

            foreach ($cb->topics as $topic) {
                if (!$topic->parameters->contains("code","topic_checkpoints_boolean") || $topic->parameters->where("code","topic_checkpoints_boolean")->first()->pivot->value!=1) {
                    /* Passar para nova fase */
                    if (!$topic->parameters->contains("code", "topic_checkpoints"))
                        $topic->parameters()->attach($topicCheckpointParameter->id, ["version" => 1, "value" => $topicCheckpointNewId]);
                    else if ($topic->parameters->where("code", "topic_checkpoints")->first()->pivot->value != 1)
                        $topic->parameters()->updateExistingPivot($topicCheckpointParameter->id, ["value" => $topicCheckpointNewId]);

                    /* Se não teve os votos mínimos, mudar a checkbox */
                    if (empty($totalVotes->get($topic->topic_key,"")) || $totalVotes->get($topic->topic_key)->positive<7) {
                        if (!$topic->parameters->contains("code", "topic_checkpoints_boolean"))
                            $topic->parameters()->attach($topicCheckpointBooleanParameter->id, ["version" => 1, "value" => 1]);
                        else if ($topic->parameters->where("code", "topic_checkpoints_boolean")->first()->pivot->value != 1)
                            $topic->parameters()->updateExistingPivot($topicCheckpointBooleanParameter->id, ["value" => 1]);
                    }
                }
            }

            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['exception' => $e->getMessage(),'e-line'=>$e->getLine()], 500);
        }
    }

    /**
     * @param $cbKey
     * @param $topicCheckpointNewId
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishPhase2($cbKey, $topicCheckpointNewId) {
        try {
            $cb = CB::with("topics.parameters")->whereCbKey($cbKey)->firstOrFail();

            $topicCheckpointBooleanParameter = Parameter::whereCode("topic_checkpoints_boolean")->first();
            $topicCheckpointParameter = Parameter::whereCode("topic_checkpoints")->first();

            foreach ($cb->topics as $topic) {
                if (!$topic->parameters->contains("code","topic_checkpoints_boolean") || $topic->parameters->where("code","topic_checkpoints_boolean")->first()->pivot->value!=1) {
                    if (!$topic->parameters->contains("code", "topic_checkpoints"))
                        $topic->parameters()->attach($topicCheckpointParameter->id, ["version" => 1, "value" => $topicCheckpointNewId]);
                    else if ($topic->parameters->where("code", "topic_checkpoints")->first()->pivot->value != 1)
                        $topic->parameters()->updateExistingPivot($topicCheckpointParameter->id, ["value" => $topicCheckpointNewId]);

                    if (!$topic->parameters->contains("code", "going_to_pass") || $topic->parameters->where("code","going_to_pass")->first()->pivot->value!=1) {
                        if (!$topic->parameters->contains("code", "topic_checkpoints_boolean"))
                            $topic->parameters()->attach($topicCheckpointBooleanParameter->id, ["version" => 1, "value" => 1]);
                        else if ($topic->parameters->where("code", "topic_checkpoints_boolean")->first()->pivot->value != 1)
                            $topic->parameters()->updateExistingPivot($topicCheckpointBooleanParameter->id, ["value" => 1]);
                    }
                }
            }
            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['exception' => $e->getMessage(),'e-line'=>$e->getLine()], 500);
        }
    }

    /**
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicsbyVotes(Request $request, $cbKey){
        try{
            $cb = CB::with("topics.parameters","votes")->whereCbKey($cbKey)->firstOrFail();

            $response = One::get([
                'url' => 'http://luismonteiro.empatia-dev.onesource.pt:5011',
                'component' => 'vote',
                'api' => 'event',
                'method' => 'voteResults',
                'api_attribute' => $cb->votes->first()->vote_key
            ]);

            if($response->statusCode() != 200)
                throw new Exception(trans("comModulesVote.errorRetrievingCbTotalVotes"));

            $totalVotes = collect($response->json()->total_votes);
            $topics = [];
            foreach ($cb->topics as $topic) {
                if (!$topic->parameters->contains("code", "topic_checkpoints_boolean") || $topic->parameters->where("code", "topic_checkpoints_boolean")->first()->pivot->value != 1) {

                    foreach ($topic->parameters as $parameter) {
                        if (!($parameter->translation($request->header('LANG-CODE')))) {
                            if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                                $translation = $parameter->parameterTranslations()->first();
                                if(!empty($translation)){
                                    $parameter->translation($translation->language_code);
                                }
                                else{
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                        }

                        foreach ($parameter->options as $option) {
                            if(!empty($option)){
                                if (!($option->translation($request->header('LANG-CODE')))) {
                                    if (!$option->translation($request->header('LANG-CODE-DEFAULT'))){
                                        $translation = $option->parameterOptionTranslations()->first();
                                        if(!empty($translation)){
                                            $option->translation($translation->language_code);
                                        }
                                        else{
                                            return response()->json(['error' => 'No translation found'], 404);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($totalVotes[$topic->topic_key]) && $totalVotes->get($topic->topic_key)->positive > 6) {
                        $topics [] = $topic;
                    }
                }
            }

            $topics = collect($topics)->sortByDesc(function ($topic) use ($totalVotes) {
                return $totalVotes->get($topic->topic_key)->positive;
            });

            $data = [];
            $data['topics'] = $topics;
            $data['totalVotes'] = $totalVotes;

            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
            return response()->json(['exception' => $e->getMessage(),'e-line'=>$e->getLine()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchToNewParameter(Request $request, $cbKey)
    {
        try {
            $cb = CB::with("topics.parameters")->whereCbKey($cbKey)->firstOrFail();
            $status = $cb->parameters()->where('code','=','topic_checkpoints')->first();
            /*
             * Noch nicht geprüft => 55 [196]
             * Kriteriencheck bestanden => 56 [197 | 198(Archive)]
             * Teil der TOP 100 => 57 [199 | 200(Archive)]
             * Gemeinwohl-Check bestanden: Teil der TOP 30 => 58 [201 | 202(Archive)]
             * Detailprüfung durch Verwaltung bestanden: Zur finalen Abstimmung freigegeben => 59 [ 203 | 204(Archive)]
             * Gewinner-Idee der finalen Abstimmung! Wird umgesetzt! => 60  [ 205 | 206(Archive)]
             * Umsetzung gestartet => 61 [ 207 | 208(Archive)]
             * Umgesetzt! => 83 [ 209 | 210(Archive)]
            */
            $topics = [];
            $topicsNew = [];
            foreach ($cb->topics as $topic){
                $newParameters = [];
                if(!empty($parameter = $topic->parameters()->where('id','62')->first())){

                    if($parameter->pivot->value == "56") { //INITIAL CRITERIA
                        if(!$topic->parameters()->whereParameterId('197')->exists() && !$topic->parameters()->whereParameterId('198')->exists()){


                            if(!empty($archiveParameter = $topic->parameters()->whereParameterId('68')->first())) {
                                if($archiveParameter->pivot->value == "1") {
                                    $topics[] = $topic;
                                    $newParameters[198] = array(
                                        "parameter_id"  => 198,
                                        "value"         => 1,
                                    );
                                }else{
                                    $topicsNew[] = $topic;
                                    $newParameters[197] = array(
                                        "parameter_id"  => 197,
                                        "value"         => 1,
                                    );
                                }
                            }else{
                                $topicsNew[] = $topic;
                                $newParameters[197] = array(
                                    "parameter_id"  => 197,
                                    "value"         => 1,
                                );
                            }

                            $topic->parameters()->attach($newParameters);
                        }
                    }

                    if($parameter->pivot->value == "57") { //TOP 100
                        if(!$topic->parameters()->whereParameterId('197')->exists() && !$topic->parameters()->whereParameterId('199')->exists() && !$topic->parameters()->whereParameterId('200')->exists()){

                            $newParameters[197] = array(
                                "parameter_id"  => 197,
                                "value"         => 1,
                            );

                            if(!empty($archiveParameter = $topic->parameters()->whereParameterId('68')->first())) {
                                if($archiveParameter->pivot->value == "1") {
                                    $topics[] = $topic;
                                    $newParameters[200] = array(
                                        "parameter_id"  => 200,
                                        "value"         => 1,
                                    );
                                }else{
                                    $topicsNew[] = $topic;
                                    $newParameters[199] = array(
                                        "parameter_id"  => 199,
                                        "value"         => 1,
                                    );
                                }
                            }else{
                                $topicsNew[] = $topic;
                                $newParameters[199] = array(
                                    "parameter_id"  => 199,
                                    "value"         => 1,
                                );
                            }

                            $topic->parameters()->attach($newParameters);
                        }
                    }

                    if($parameter->pivot->value == "58"){ //TOP 30
                        if(!$topic->parameters()->whereParameterId('197')->exists() && !$topic->parameters()->whereParameterId('199')->exists() && !$topic->parameters()->whereParameterId('201')->exists()){

                            $newParameters[197] = array(
                                "parameter_id"  => 197,
                                "value"         => 1,
                            );
                            $newParameters[199] = array(
                                "parameter_id"  => 199,
                                "value"         => 1,
                            );

                            if(!empty($archiveParameter = $topic->parameters()->whereParameterId('68')->first())) {
                                if($archiveParameter->pivot->value == "1") {
                                    $topics[] = $topic;
                                    $newParameters[202] = array(
                                        "parameter_id"  => 202,
                                        "value"         => 1,
                                    );
                                }else{
                                    $topicsNew[] = $topic;
                                    $newParameters[201] = array(
                                        "parameter_id"  => 201,
                                        "value"         => 1,
                                    );
                                }
                            }else{
                                $topicsNew[] = $topic;
                                $newParameters[201] = array(
                                    "parameter_id"  => 201,
                                    "value"         => 1,
                                );
                            }

                            $topic->parameters()->attach($newParameters);
                        }
                    }
                }
            }
            $archive = $cb->parameters()->where('code','=','topic_checkpoints_boolean')->first();
        } catch (Exception $e) {
            return response()->json(['exception' => $e->getMessage(), 'e-line' => $e->getLine()], 500);
        }
    }


    /**
     * @param $cbKey
     * @return bool
     */
    public static function updateCachedData($cbKey){
        try{
            $cb = Cb::with(array('parameters.parameterTranslations', 'parameters' => function ($query) {
                $query->with('type', 'options.parameterOptionTranslations')->orderBy("position");
            }))->whereCbKey($cbKey)->firstOrFail();

            $parameters = $cb->parameters;

            foreach ($parameters as $parameter){
                $parameterTranslations = [];
                foreach ($parameter->parameterTranslations as $translation){
                    $parameterTranslations[$translation->language_code] = array('parameter' => $translation->parameter, 'description' => $translation->description);
                }

                foreach ($parameter->options as $option){
                    $optionTranslations = [];
                    foreach ($option->parameterOptionTranslations as $optTranslations){
                        $optionTranslations[$optTranslations->language_code] = array('label' => $optTranslations->label);
                    }
                    unset($option->parameterOptionTranslations);
                    $option->translations = $optionTranslations;
                }

                unset($parameter->parameterTranslations);
                $parameter->translations = $parameterTranslations;
            }
            $cb->_cached_data = json_encode($parameters);
            $cb->save();

            return true;
        } catch (Exception $e){
            return false;
        }
    }

    public function getTopicsByCbKey($cbKey)
    {
        try {
            $cb = CB::whereCbKey($cbKey)->firstOrFail();
            $topics = $cb->topics()->get();
            return response()->json(['data' => $topics]);
        }catch (Exception $e){
            return response()->json(['exception' => $e->getMessage(), 'e-line' => $e->getLine()], 500);
        }
    }

    public function publishTechnicalAnalysisResults(Request $request, $cbKey) {
        $userKey = ONE::verifyToken($request);

        try {
            $questionKey = $request->get("question");
            $parameterId = $request->get("parameter");
            $passedStatusKey = $request->get("passed");
            $failedStatusKey = $request->get("failed");
            $simulate = $request->get("simulate",true);

            $cb = CB::whereCbKey($cbKey)->firstOrFail();
            $question = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($questionKey)->first();
            $parameter = Parameter::whereId($parameterId)->first();
            $passedStatus = StatusType::whereStatusTypeKey($passedStatusKey)->first();
            $failedStatus = StatusType::whereStatusTypeKey($failedStatusKey)->first();

            $data = array();
            if (!empty($questionKey) && empty($question))
                $data["errors"][] = "questionNotDefined";
            if (!empty($parameterId) && empty($parameter))
                $data["errors"][] = "parameterNotDefined";
            if (!empty($passedStatusKey) && empty($passedStatus))
                $data["errors"][] = "passedStatusNotDefined";
            if (!empty($failedStatusKey) && empty($failedStatus))
                $data["errors"][] = "failedStatusNotDefined";

            if (!isset($data["errors"])) {
                /* Check If there are technical analysis where the question is empty counts */
                $question->translation($request->header('LANG-CODE'));
                $baseQuestionAnswersQuery = $question
                    ->technicalAnalysisQuestionAnswers()
                    ->whereHas("technicalAnalysis", function ($q) {
                        $q->where("active", "=", 1);
                    })
                    ->where(function ($q) {
                        $q->where("value", "=", "")->orWhereNull("value");
                    });
                $data["data"]["question"] = array(
                    "question" => $question,
                    "empty" => $baseQuestionAnswersQuery->where(function ($q) {
                        $q->where("value", "=", "")->orWhereNull("value");
                    })->count(),
                    "nonempty" => $baseQuestionAnswersQuery->where(function ($q) {
                        $q->where("value", "!=", "")->orWhereNotNull("value");
                    })->count(),
                );

                /* Check If there are Topics with the Parameter filled */
                $baseParametersQuery = $parameter->topics();
                $data["data"]["parameter"] = array(
                    "parameter" => $parameter,
                    "empty" => $baseParametersQuery->where(function ($q) {
                        $q->where("value", "=", "")->orWhereNull("value");
                    })->count(),
                    "nonempty" => $baseParametersQuery->where(function ($q) {
                        $q->where("value", "!=", "")->orWhereNotNull("value");
                    })->count(),
                );

                /* Get Passed Status and Topics Info */
                $passedStatus->translation($request->header('LANG-CODE'));
                $passingTopicsIds = TechnicalAnalysis::whereDecision("1")->whereHas("topic", function ($q) use ($cb) {
                    $q->where("cb_id", "=", $cb->id);
                })->pluck("topic_id");
                $data["data"]["passing"] = array(
                    "status" => $passedStatus,
                    "topics" => Topic::whereIn("id", $passingTopicsIds)->get()
                );

                /* Get Failed Status and Topics Info */
                $failedStatus->translation($request->header('LANG-CODE'));
                $failingTopicsIds = TechnicalAnalysis::whereDecision("0")->whereHas("topic", function ($q) use ($cb) {
                    $q->where("cb_id", "=", $cb->id);
                })->pluck("topic_id");
                $data["data"]["failing"] = array(
                    "status" => $failedStatus,
                    "topics" => Topic::whereIn("id", $failingTopicsIds)->get()
                );

                /* Get Topics without Analysis */
                $data["data"]["noAnalysis"] = $cb->topics()->whereDoesntHave("technicalAnalysis")->get();

                /* If it's not simulating the process */
                if (!$simulate) {
                    /* Get instance of TopicsController (needed to update the parameters cache) */
                    $topicsController = new TopicsController();

                    $data["result"] = array(
                        "passing" => array(
                            "count" => 0,
                            "topics" => array(),
                            "failure" => array(),
                            "errors" => array()
                        ), "failing" => array(
                            "count" => 0,
                            "topics" => array(),
                            "failure" => array(),
                            "errors" => array()
                        )
                    );

                    /* Change the data for Passing Topics */
                    $passingTopics = $data["data"]["passing"]["topics"]->load([
                        "topicVersions",
                        "status",
                        "technicalAnalysis" => function($q) {
                            $q->where("active","=","1")->with("technicalAnalysisQuestionsAnswers");
                        }
                    ]);
                    foreach ($passingTopics as $topic) {
                        try {
                            $activeVersion = null;
                            $lastVersionIndex = 0;
                            foreach ($topic->topicVersions as $topicVersion) {
                                /* Get the active version */
                                if ($topicVersion->active == 1)
                                    $activeVersion = $topicVersion;

                                /* Get the bigger version index */
                                if ($topicVersion->version > $lastVersionIndex)
                                    $lastVersionIndex = $topicVersion->version;

                                /* Deactivate version, since the new one is going to be the active one */
                                $topicVersion->active = 0;
                                $topicVersion->active_by = null;
                                $topicVersion->save();
                            }

                            /* If there isn't an active version, grab the last one */
                            if (empty($activeVersion))
                                $activeVersion = $topic->topicVersions->last();

                            if (!empty($activeVersion)) {
                                /* Create new Topic Version */
                                $newTopicVersion = $topic->topicVersions()->create([
                                    "version" => $lastVersionIndex + 1,
                                    "active" => 1,
                                    "title" => $activeVersion->title,
                                    "contents" => $activeVersion->contents,
                                    "summary" => !empty($activeVersion->summary) ? $activeVersion->summary : null,
                                    "description" => !empty($activeVersion->description) ? $activeVersion->description : null,
                                    "created_by" => $userKey,
                                    "active_by" => $userKey
                                ]);

                                /* Duplicate the version parameters */
                                $activeVersion->load("topicParameters");

                                $newTopicParameters = [];
                                foreach ($activeVersion->topicParameters as $topicParameter) {
                                    $newTopicParameters[$topicParameter->parameter_id] = array(
                                        "topic_id" => $topic->id,
                                        "topic_version_id" => $newTopicVersion->id,
                                        "version" => 1,
                                        "value" => $topicParameter->value ?? null
                                    );
                                }

                                /* Populate the Public Description parameter */
                                $technicalAnalysisPublicDescription = $topic->technicalAnalysis->first()
                                    ->technicalAnalysisQuestionsAnswers
                                    ->where("technical_analysis_question_id", $question->id)
                                    ->first();

                                $newTopicParameters[$parameter->id] = array(
                                    "topic_id" => $topic->id,
                                    "topic_version_id" => $newTopicVersion->id,
                                    "version" => 1,
                                    "value" => $technicalAnalysisPublicDescription->value ?? null
                                );

                                $topic->parameters()->attach($newTopicParameters);

                                /* Update the topic Status */
                                Status::whereTopicId($topic->id)->update(['active' => 0]);
                                do {
                                    $rand = str_random(32);
                                    if (!($exists = Status::whereStatusKey($rand)->exists())) {
                                        $key = $rand;
                                    }
                                } while ($exists);
                                $topic->status()->create([
                                    'status_key' => $key,
                                    'status_type_id' => $passedStatus->id,
                                    'active' => 1,
                                    'created_by' => $userKey
                                ]);

                                /* Update topic parameters Cache */
                                $topicsController->updateTopicParametersCache($topic->topic_key,$newTopicVersion);

                                $data["result"]["passing"]["count"]++;
                                $data["result"]["passing"]["topics"][] = $topic;
                            } else {
                                $data["result"]["passing"]["failure"][] = $topic;
                            }
                        } catch (Exception $e) {
                            $data["result"]["passing"]["failure"][] = $topic;
                            $data["result"]["passing"]["errors"][$topic->topic_key] = $e->getMessage();
                        }
                    }

                    /* Change the data for Failing Topics */
                    $failingTopics = $data["data"]["failing"]["topics"]->load([
                        "topicVersions",
                        "status",
                        "technicalAnalysis" => function($q) {
                            $q->where("active","=","1")->with("technicalAnalysisQuestionsAnswers");
                        }
                    ]);
                    foreach ($failingTopics as $topic) {
                        try {
                            $activeVersion = null;
                            $lastVersionIndex = 0;
                            foreach ($topic->topicVersions as $topicVersion) {
                                /* Get the active version */
                                if ($topicVersion->active == 1)
                                    $activeVersion = $topicVersion;

                                /* Get the bigger version index */
                                if ($topicVersion->version > $lastVersionIndex)
                                    $lastVersionIndex = $topicVersion->version;

                                /* Deactivate version, since the new one is going to be the active one */
                                $topicVersion->active = 0;
                                $topicVersion->active_by = null;
                                $topicVersion->save();
                            }

                            /* If there isn't an active version, grab the last one */
                            if (empty($activeVersion))
                                $activeVersion = $topic->topicVersions->last();

                            if (!empty($activeVersion)) {
                                /* Create new Topic Version */
                                $newTopicVersion = $topic->topicVersions()->create([
                                    "version" => $lastVersionIndex + 1,
                                    "active" => 1,
                                    "title" => $activeVersion->title,
                                    "contents" => $activeVersion->contents,
                                    "summary" => !empty($activeVersion->summary) ? $activeVersion->summary : null,
                                    "description" => !empty($activeVersion->description) ? $activeVersion->description : null,
                                    "created_by" => $userKey,
                                    "active_by" => $userKey
                                ]);

                                /* Duplicate the version parameters */
                                $activeVersion->load("topicParameters");

                                $newTopicParameters = [];
                                foreach ($activeVersion->topicParameters as $topicParameter) {
                                    $newTopicParameters[$topicParameter->parameter_id] = array(
                                        "topic_id" => $topic->id,
                                        "topic_version_id" => $newTopicVersion->id,
                                        "version" => 1,
                                        "value" => $topicParameter->value ?? null
                                    );
                                }

                                /* Populate the Public Description parameter */
                                $technicalAnalysisPublicDescription = $topic->technicalAnalysis->first()
                                    ->technicalAnalysisQuestionsAnswers
                                    ->where("technical_analysis_question_id", $question->id)
                                    ->first();

                                $newTopicParameters[$parameter->id] = array(
                                    "topic_id" => $topic->id,
                                    "topic_version_id" => $newTopicVersion->id,
                                    "version" => 1,
                                    "value" => $technicalAnalysisPublicDescription->value ?? null
                                );

                                $topic->parameters()->attach($newTopicParameters);

                                /* Update the topic Status */
                                Status::whereTopicId($topic->id)->update(['active' => 0]);
                                do {
                                    $rand = str_random(32);
                                    if (!($exists = Status::whereStatusKey($rand)->exists())) {
                                        $key = $rand;
                                    }
                                } while ($exists);
                                $topic->status()->create([
                                    'status_key' => $key,
                                    'status_type_id' => $failedStatus->id,
                                    'active' => 1,
                                    'created_by' => $userKey
                                ]);

                                /* Update topic parameters Cache */
                                $topicsController->updateTopicParametersCache($topic->topic_key,$newTopicVersion);

                                $data["result"]["failing"]["count"]++;
                                $data["result"]["failing"]["topics"][] = $topic;
                            } else {
                                $data["result"]["failing"]["failure"][] = $topic;
                            }
                        } catch (Exception $e) {
                            $data["result"]["failing"]["failure"][] = $topic;
                            $data["result"]["failing"]["errors"][$topic->topic_key] = $e->getMessage();
                        }
                    }
                }
            }

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Publish Technical Analysis results'], 500);
        }
    }
}
