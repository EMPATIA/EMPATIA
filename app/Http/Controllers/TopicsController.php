<?php

namespace App\Http\Controllers;

use App\Cb;
use App\User;
use App\Post;
use Exception;
use App\Topic;
use App\Entity;
use App\Status;
use App\CbType;
use App\One\One;
use App\PostLike;
use App\EntityCb;
use App\OrchUser;
use App\One\OneCb;
use Carbon\Carbon;
use App\PostAbuse;
use App\Parameter;
use App\Cooperator;
use App\StatusType;
use App\CbTemplate;
use App\Http\Requests;
use App\OperationType;
use App\Configuration;
use App\ParameterType;
use App\TopicAlliance;
use App\TopicFollower;
use App\CooperatorType;
use App\OperationAction;
use App\ComModules\Vote;
use App\PostCommentType;
use App\ComModules\Auth;
use App\ComModules\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class TopicsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Topic",
 *   description="Everything about Topics",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *
 * @SWG\Definition(
 *   definition="configurationsTopic",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cm_key"},
 *           @SWG\Property(property="cm_key", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="cbTopic",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key", "parent_cb_id", "title", "contents", "created_by", "blocked", "status_id", "layout_code", "start_date", "end_date", "created_at", "updated_at"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="parent_cb_id", format="string", type="integer"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="integer"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="layout_code", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="string", type="string"),
 *           @SWG\Property(property="end_date", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 *
 *
 * @SWG\Definition(
 *   definition="topicStore",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key", "title", "contents"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="parent_topic_id", format="integer", type="integer"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_on_behalf", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="integer"),
 *           @SWG\Property(property="status_id", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="date", type="string"),
 *           @SWG\Property(property="end_date", format="date", type="string"),
 *           @SWG\Property(property="q_key", format="string", type="string"),
 *           @SWG\Property(property="tag", format="string", type="string"),
 *           @SWG\Property(property="parameters", ref="#/definitions/parametersTopic"),
 *       )
 *   }
 * )
 *
 *
 *
 *
 *
 * @SWG\Definition(
 *   definition="firstPost",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_key", "version", "parent_id", "topic_id", "post_comment_type_id", "created_by", "contents", "status_id", "created_at", "updated_at", "enabled", "blocked", "active"},
 *           @SWG\Property(property="post_key", format="string", type="string"),
 *           @SWG\Property(property="version", format="string", type="integer"),
 *           @SWG\Property(property="parent_id", format="string", type="integer"),
 *           @SWG\Property(property="topic_id", format="string", type="integer"),
 *           @SWG\Property(property="post_comment_type_id", format="string", type="integer"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property="enabled", format="string", type="integer"),
 *           @SWG\Property(property="blocked", format="string", type="integer"),
 *           @SWG\Property(property="active", format="string", type="integer"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topicResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_key", "cb_id", "created_by", "title", "contents", "blocked", "q_key", "topic_number", "start_date", "end_date", "created_at", "updated_at"},
 *           @SWG\Property(property="topic_key", format="string", type="string"),
 *           @SWG\Property(property="cb_id", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="blocked", format="string", type="string"),
 *           @SWG\Property(property="q_key", format="string", type="string"),
 *           @SWG\Property(property="topic_number", format="string", type="string"),
 *           @SWG\Property(property="start_date", format="string", type="string"),
 *           @SWG\Property(property="end_date", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property = "first_post", ref="#/definitions/firstPost"),
 *           @SWG\Property(property = "cb", ref="#/definitions/cbTopic"),
 *           @SWG\Property(property = "configurations", ref="#/definitions/configurationsTopic"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="parametersTopic",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_id", "value"},
 *           @SWG\Property(property="parameter_id", format="string", type="integer"),
 *           @SWG\Property(property="value", format="string", type="string")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topic",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key", "title", "contents"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="parameters", ref="#/definitions/parametersTopic"),
 *
 *       )
 *   }
 * )
 *
 *
 *
 *
 * @SWG\Definition(
 *   definition="topicFollowersResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *          @SWG\Property(
 *              property="data",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/topicFollower")
 *           )
 *       )
 *   }
 * )
 *
 *
 *
 *  @SWG\Definition(
 *   definition="topicNewsStore",
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
 *  @SWG\Definition(
 *   definition="topicNewsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="data", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="topic_id", format="string", type="integer"),
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
 *  @SWG\Definition(
 *   definition="topicCbsStore",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="cb_key", format="string", type="string")
 *       )
 *   }
 * )
 *
 *
 *
 *
 *  @SWG\Definition(
 *   definition="topicCbsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="data", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property = "cb", ref="#/definitions/cbTopic"),
 *                  )
 *           ),
 *       )
 *   }
 * )
 *
 *
 */

class TopicsController extends Controller
{

    protected $required = [
        'store' => ['cb_key', 'title', 'contents'],
        'update' => ['title', 'contents'],
        'updateStatus' => ['blocked']
    ];

    /**
     * Requests statistics.
     * Returns statistics.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request, $topicKey)
    {
        try {
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;

            $posts = Topic::findOrFail($topicId)->posts()->whereEnabled(1)->count();
            $likes = Topic::findOrFail($topicId)->likes()->whereLike(1)->count();
            $dislikes = Topic::findOrFail($topicId)->likes()->whereLike(0)->count();

            return response()->json(['like_counter' => $likes, 'dislike_counter' => $dislikes, 'posts_counter' => $posts], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic data not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic statistics'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Topics.
     * Returns the list of Topics with the last Post of each Topic.
     *
     * @deprecated The list should be obtained from the CB
     *
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $cbKey)
    {
        try {
            $cbId = Cb::whereCbKey($cbKey)->firstOrFail()->id;

            $topics = Topic::with('lastPost')->whereCbId($cbId)->get();

            foreach ($topics as $topic) {
                $topic->timezone($request);
            }

            return response()->json(['data' => $topics], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Topics.
     * Returns the list of Topics with the first Post of each Topic.
     *
     * @deprecated The list should be obtained from the CB
     *
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexWithFirst(Request $request, $cbKey)
    {
        try {
            $cbId = Cb::whereCbKey($cbKey)->firstOrFail()->id;

            $topics = Topic::with('firstPost', 'parameters', 'parameters.options')->whereCbId($cbId)->get();

            foreach ($topics as $topic) {
                $topic->timezone($request);
            }

            return response()->json(['data' => $topics], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/topic/{topicKey}",
     *  summary="Show a Topic",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     * @SWG\Parameter(
     *      name="topicKey",
     *      in="path",
     *      description="Topic Key",
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
     *      description="Show the Topic data",
     *      @SWG\Schema(ref="#/definitions/topicResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Topic",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Request a specific Topic.
     * Returns the details of a specific Topic.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function show(Request $request, $topicKey)
    {
        try {
            //GET THE TOPIC
            $topic = $this->parameters($request,$topicKey,true);

            //THIS NEEDS TO BE CLARIFIED
            $topicForStatistics = Topic::findOrFail($topic->id);

            //GET THE FIRST POST CONTENT
            $firstPost = Post::whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first()
                ->timezone($request);
            $topic['first_post'] = $post;

            //GET TOPIC FIRST POST FILES
            $topicFirstPostFiles = $this->organizeFilesByType($firstPost->files);

            //GET THE NUMBER OF POSTS
            $posts = $topicForStatistics->posts()->whereEnabled(1)->count();
            //GET THE NUMBER OF LIKES
            $likes = $topicForStatistics->likes()->whereLike(1)->count();
            //GET THE NUMBER OF DISLIKES
            $dislikes = $topicForStatistics->likes()->whereLike(0)->count();


            //GET THE CB
            $cb = $topic->cb()->first();
            $cb['parameters'] = $this->dealWithCbParameters($request,$cb);


            $publishConfiguration = Configuration::whereCode('publish_needed')->first();
            $topicNeedsToBePublished = (!is_null($publishConfiguration) && !is_null($cb->configurations()->whereConfigurationId($publishConfiguration->id)->first()));


            //GET THE CB MODERATORS
            $cb['moderators'] = $cb->moderators()->get();

            //GET THE CB VOTES
            $votes = $cb->votes()->get();
            foreach ($votes as $vote) {
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
            }

            //GET THE CB CONFIGURATIONS
            $configurations = $cb->configurations()->select('code')->pluck('code');


            /* Create Operation Schedules object */
            $operationSchedules = array();
            $operationTypes = OperationType::all();
            $operationActions = OperationAction::all();
            $cbOperationSchedules = $cb->operationSchedules()->get();
            foreach ($operationTypes as $operationType) {
                $operationSchedules[$operationType->code] = array();

                foreach ($operationActions as $operationAction) {
                    $cbOperationSchedule = $cbOperationSchedules->where("operation_type_id",$operationType->id)->where("operation_action_id",$operationAction->id);
                    $operationSchedules[$operationType->code][$operationAction->code] = $cbOperationSchedule->isEmpty();
                }
            }

            return response()->json(['topic' => $topic,
                'configurations' => $configurations,
                'cb' => $cb,
                'like_counter' => $likes,
                'dislike_counter' => $dislikes,
                'posts_counter' => $posts,
                'cb_votes' => $votes,
                'topicsKeys' => [],
                'firstPostFiles' => $topicFirstPostFiles,
                'operationSchedules' => $operationSchedules
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found', 'e' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function dealWithCbParameters(Request $request, $cb)
    {

        try{
            if (!empty($parameters = json_decode($cb->_cached_data))) {
                foreach ($parameters as $parameter) {
                    if (!empty($parameterTrans[$parameter->id])) {
                        $parameter->parameter =   $parameterTrans[$parameter->id]['parameter'];
                        $parameter->description =  $parameterTrans[$parameter->id]['description'];
                    } else {
                        $langCode = $request->header('LANG-CODE');
                        $langCodeDefault = $request->header('LANG-CODE-DEFAULT');
                        if (!($parameter->translations->$langCode)) {
                            if (!$parameter->translations->$langCodeDefault) {
                                $firstTranslationFound = $parameter->translations->first();
                                $parameter->parameter =  $firstTranslationFound->parameter;
                                $parameter->description =  $firstTranslationFound->description;
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
                                    if (!($option->translations->$langCode)) {
                                        if (!$option->translations->$langCodeDefault) {
                                            $firstTranslationFound = $option->translations->first();
                                            $option->label = $firstTranslationFound->label;
                                        }
                                    }else{
                                        $option->label = $option->translations->$langCode->label;
                                    }
                                    $parameterOptionTrans[$option->id] = ['label'=> $option->translations->$langCode->label];
                                }
                            }
                        }
                    }
                }
                $cb->parameters = $parameters;
            }

            return $cb->parameters;
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic'], 500);
        }


    }

    /**
     * @param $files
     * @return array
     */
    public function organizeFilesByType($files){
        $fileTypes = [];
        $fileTypes["images"] = array("gif","jpg","png","bmp");
        $fileTypes["videos"] = array("avi","mpg","mp4","avi","asf","qt","flv","swf","wmv","webm","vob","ogv","ogg","mpeg","3gp");
        $fileTypes["docs"]   = array("pdf","doc","docx","rtf");

        $filesData = [];

        if(count($files) > 0) {
            foreach ($files as $file) {
                $array = explode('.', $file->name);
                $extension = end($array);
                foreach ($fileTypes as $key => $value) {
                    if (in_array(strtolower($extension), $value)) {
                        $filesData[$key][] = $file;
                    }
                }
            }
        }
        return $filesData;
    }



    /**
     *
     * @SWG\Post(
     *  path="/topic",
     *  summary="Creation of a Topic",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicStore")
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
     *      description="the newly created topic",
     *      @SWG\Schema(ref="#/definitions/topicResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Topic",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created Topic in storage.
     * Returns the details of the newly created Topic.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try{
            if (!CbOperationScheduleController::verifyScheduleInternal($request->header('X-ENTITY-KEY'), $request->json('cb_key'), 'topic', 'create')){
                return response()->json(['error' => 'Outside Permitted Creation Data'], 500);
            }
        } catch (Exception $e){
            //
        }

        $userKey = ONE::verifyLogin($request);
        ONE::verifyKeysRequest($this->required["store"], $request);

        do {
            $key = '';
            $rand = str_random(32);
            if (!($exists = Topic::whereTopicKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);

        $cb = Cb::whereCbKey(clean($request->json('cb_key')))->firstOrFail();

        $lastTopic = $topics = Topic::whereCbId($cb->id)->orderBy('created_at', 'desc')->first();

        $summary = '';
        if (!empty($request->json('summary'))){
            $summary = clean($request->json('summary'));
        }
        $contents = '';
        if (!empty($request->json('contents'))){
            $contents = clean($request->json('contents'));
        }

        $canCreateTopics = false;

        //If anonymous, verifies if the CB allows it
        try {
            if (is_null($userKey)) {
                $config = Configuration::whereCode('security_create_topics_anonymous')->first();
                if (!is_null($config)) {
                    $cb_config = $cb->configurations()->whereConfigurationId($config->id)->first();
                    if (!is_null($cb_config))
                        $canCreateTopics = true;
                }
            } else
                $canCreateTopics = true;
        } catch (Exception $e) {
            $canCreateTopics = false;
        }


        if ($canCreateTopics) {
            try {
                $isPrivateRequest = $request->json("private",false);

                if ($isPrivateRequest && !empty($request->get("topic_creator")))
                    $userKey = $request->get("topic_creator");

                if (!empty($request->json("parent_topic_key", ""))) {
                    try {
                        if (!$isPrivateRequest)
                            $parentTopicId = Topic::whereTopicKey($request->json("parent_topic_key"))->whereCreatedBy($userKey)->firstOrFail()->id;
                        else
                            $parentTopicId = Topic::whereTopicKey($request->json("parent_topic_key"))->firstOrFail()->id;
                    } catch (Exception $e) {
                        $parentTopicId = 0;
                    }
                } else
                    $parentTopicId = $request->json('parent_topic_id') ?? 0;

                $lastId = Topic::withTrashed()->max('id');
                $newId = empty($lastId) ? 1 : $lastId + 1;

                $topic = $cb->topics()->create(
                    [
                        'id' => $newId,
                        'topic_key' => $key,
                        'parent_topic_id' => $parentTopicId,
                        'created_by' => is_null($userKey) ? 'anonymous' : $userKey,
                        'created_on_behalf' => $request->json('created_on_behalf') ?? null,
                        'title' => clean($request->json('title')),
                        'blocked' => is_null($request->json('blocked')) ? 0 : clean($request->json('blocked')),
                        'summary' => $summary,
                        'contents' => $contents,

                        'status_id' => is_null($request->json('status_id')) ? 0 : clean($request->json('status_id')),
                        'q_key' => is_null($request->json('q_key')) ? 0 : clean($request->json('q_key')),
                        'topic_number' => !isset($lastTopic->topic_number) ? 1 : $lastTopic->topic_number + 1,
                        'start_date' => !empty($request->json('start_date')) ? Carbon::createFromFormat('Y-m-d', clean($request->json('start_date')))->toDateTimeString() : null,
                        'end_date' => !empty($request->json('end_date')) ? Carbon::createFromFormat('Y-m-d', clean($request->json('end_date')))->toDateTimeString() : null,
                        'tag' => $request->json('tag') ?? null,
                    ]
                );

                //create topic version
                $topicVersion = $topic->topicVersions()->create([
                    'topic_id' => $topic->id,
                    'title' => $topic->title,
                    'summary' => !empty($topic->summary) ? $topic->summary : null,
                    'contents' => $topic->contents,
                    'description' => !empty($topic->description) ? $topic->description : null,
                    'created_by' => $topic->created_by
                ]);

                $manualSyncParameters = [];
                if (!empty($request->json('parameters'))) {
                    $parameters = [];
                    foreach ($request->json('parameters') as $parameter) {
                        $canStoreParameter = false;
                        if (!$isPrivateRequest) {
                            $DBParameter = Parameter::whereId($parameter["parameter_id"])->get();
                            if ($DBParameter->count()==1 && $DBParameter->first()->private!=1)
                                $canStoreParameter = true;
                        } else
                            $canStoreParameter = true;

                        if ($canStoreParameter) {
                            if (!is_array($parameter['value'])) {
                                $parameters[$parameter['parameter_id']] = clean($parameter);
                                $parameters[$parameter['parameter_id']]['topic_version_id'] = $topicVersion->id;
                            } else {
                                //REMOVE OPTIONS FROM CHECKBOX WHEN EQUAL ZERO
                                foreach ($parameter['value'] as $value){
                                    if ($value != '0') {
                                        $newParameterValue[] = $value;
                                    }
                                }
                                if(isset($newParameterValue)){
                                    $parameter['value'] = $newParameterValue;
                                }

                                $manualSyncParameters[$parameter['parameter_id']]['value'] = implode(",", $parameter['value']);
                                $manualSyncParameters[$parameter['parameter_id']]['topic_version_id'] = $topicVersion->id;
                            }
                        }
                    }
                    $topic->parameters()->sync($parameters);
                };

                foreach ($manualSyncParameters as $id => $value) {
                    $topic->parameters()->attach($id, [
                        'value' => clean($value['value']),
                        'topic_version_id' => $value['topic_version_id']
                    ]);
                }

                do {
                    $rand = str_random(32);
                    if (!($exists = Post::wherePostKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $contents = '';
                if (!empty($request->json('contents'))) {
                    $contents = clean($request->json('contents'));
                }

                $post = Post::create(
                    [
                        'post_key' => $key,
                        'topic_id' => $topic->id,
                        'created_by' => is_null($userKey) ? 'anonymous' : $userKey,
                        'enabled' => 1,
                        'contents' => $contents
                    ]
                );

                //check topic needs moderation
                $config = Configuration::whereCode('topic_need_moderation')->first();
                if (!is_null($config)) {
                    //check pivot table if topics need config in current CB
                    $cb_config = $cb->configurations()->whereConfigurationId($config->id)->first();

                    if (is_null($cb_config)) {
                        $publishConfiguration = Configuration::whereCode('publish_needed')->first();

                        // If the topic don't need to be published, it can be automatically moderated
                        if (is_null($publishConfiguration) || is_null($cb->configurations()->whereConfigurationId($publishConfiguration->id)->first())) {
                            $statusType = StatusType::whereCode('moderated')->first();
                            if (!is_null($statusType)) {
                                //"disable" previous statuses
                                $statusUpdate = Status::whereTopicId($topic->id)->update(['active' => 0]);

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
                                        'topic_id' => $topic->id,
                                        'active' => 1,
                                        'created_by' => is_null($userKey) ? 'anonymous' : $userKey
                                    ]
                                );
                            }
                        }
                    }
                }

                if (!$isPrivateRequest) {
                    $topic->topicVersions()->update(["active" => 0, "active_by" => null]);
                    $topicVersion->active = 1;
                    $topicVersion->active_by = $topicVersion->created_by;
                    $topicVersion->save();

                    $this->updateTopicParametersCache($topic->topic_key, $topicVersion);
                }

                $data = Topic::with('parameters.type', 'firstPost')->findOrFail($topic->id);

                return response()->json(['topic' => $data], 201);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'CB not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to store new Topic'], 500);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/topic/{topicKey}",
     *  summary="Update a Topic",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicStore")
     *  ),
     *
     * @SWG\Parameter(
     *      name="topicKey",
     *      in="path",
     *      description="Topic Key",
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
     *      description="The updated Topic",
     *      @SWG\Schema(ref="#/definitions/topicResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Topic not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Topic",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the Topic in storage.
     * Returns the details of the updated Topic.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function update(Request $request, $topicKey)
    {

        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["update"], $request);

        try {
            $isPrivateRequest = $request->json("private",false);
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicLastVersion = $topic->topicVersions()->orderBy('version', 'desc')->first();

            //create new topic version
            $topicVersion = $topic->topicVersions()->create([
                'topic_id' => $topic->id,
                'title' => $request->input('title'),
                'summary' => !empty($request->input('summary')) ? $request->input('summary') : null,
                'contents' => $request->input('contents'),
                'description' => !empty($request->input('description')) ? $request->input('description') : null,
                'created_by' => $topic->created_by,
                'version' => isset($topicLastVersion) ? $topicLastVersion->version + 1 : '1'
            ]);

            $manualSyncParameters = [];


            if (!empty($request->json('parameters'))) {
                $parameters = [];
                foreach ($request->json('parameters') as $parameter) {
                    $canStoreParameter = false;
                    if (!$isPrivateRequest) {
                        $DBParameter = Parameter::whereId($parameter["parameter_id"])->get();
                        if ($DBParameter->count()==1 && $DBParameter->first()->private!=1)
                            $canStoreParameter = true;
                    } else
                        $canStoreParameter = true;

                    if ($canStoreParameter) {
                        if (!is_array($parameter['value'])) {
                            $parameters[$parameter['parameter_id']] = clean($parameter);
                            $parameters[$parameter['parameter_id']]['topic_version_id'] = $topicVersion->id;
                        } else {
                            //REMOVE OPTIONS FROM CHECKBOX WHEN EQUAL ZERO
                            foreach ($parameter['value'] as $value){
                                if ($value != '0') {
                                    $newParameterValue[] = $value;
                                }
                            }
                            if(isset($newParameterValue)){
                                $parameter['value'] = $newParameterValue;
                            }

                            $manualSyncParameters[$parameter['parameter_id']]['value'] = implode(",", $parameter['value']);
                            $manualSyncParameters[$parameter['parameter_id']]['topic_version_id'] = $topicVersion->id;
                        }
                    }
                }

                foreach ($parameters as $key => $parameter){
                    $topic->parameters()->attach([$key => ['topic_version_id' => $parameter['topic_version_id'], 'value' => $parameter['value']]]);
                }
            };



            foreach ($manualSyncParameters as $id => $value) {
                $topic->parameters()->attach($id, [
                    'value' => clean($value['value']),
                    'topic_version_id' => $value['topic_version_id']
                ]);
            }

            $firstPost = Post::whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first();

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first();

            $post->enabled = 0;
            $post->save();

            $newPost = $topic->posts()->create(
                [
                    'post_key' => $post->post_key,
                    'parent_id' => $post->parent_id,
                    'version' => ++$post->version,
                    'enabled' => 1,
                    'created_by' => $post->created_by,
                    'updated_by' => $userKey,
                    'status_id' => $post->status_id,
                    'contents' => clean($request->json('contents'))
                ]
            );

            $newPost->created_at = $firstPost->created_at;
            $newPost->save();

            // Notify Followers - BEGIN
//            $configurations = $topic->cb->configurations()->select('code')->pluck('code');
//            if (OneCb::checkCBsOption($configurations->toArray(), 'NOTIFICATION-CONTENT-CHANGE')){
//
//                $tags = [
//                    'topic' => $topic->title,
//                    'link'  => $request->json('link'),
//                ];
//
//                $response = One::notifyFollowers($request, $tags, 'notification_content_change');
//            }
            // Notify Followers - END

            if (!$isPrivateRequest) {
                $topic->topicVersions()->update(["active" => 0, "active_by" => null]);
                $topicVersion->active = 1;
                $topicVersion->active_by = $topicVersion->created_by;
                $topicVersion->save();

                $this->updateTopicParametersCache($topic->topic_key, $topicVersion);
            }

            $data = Topic::with('parameters.type')->findOrFail($topic->id);
            return response()->json(['topic' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to update Topic'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteTopic",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/topic/{topicKey}",
     *  summary="Delete a Topic",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     * @SWG\Parameter(
     *      name="topicKey",
     *      in="path",
     *      description="Topic Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTopic")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified Topic from storage.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param type $id
     */
    public function destroy(Request $request, $topicKey)
    {
        ONE::verifyToken($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topic->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests the first Post of a Topic.
     * Returns the details of the first Post.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFirstPost(Request $request, $topicKey)
    {
        try {
            $post = Topic::whereTopicKey($topicKey)->firstOrFail()->posts()->first()->timezone($request);;
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the status of a specific Topic in storage.
     * Returns the details of the updated CB.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function updateStatus(Request $request, $topicKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["updateStatus"], $request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail()->timezone($request);;
            $topic->blocked = clean($request->json('blocked'));
            $topic->save();
            return response()->json(['topic' => $topic], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to block Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Requests a list of Posts from a Topic in Tree View.
     * Returns the list of Posts.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function posts(Request $request, $topicKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $posts = Topic::withTrashed()->whereTopicKey($topicKey)->firstOrFail()->posts()->whereEnabled(1)->get();
            $data = $this->getRecursivePosts($request, $posts, $userKey);
            return response()->json(["data" => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Posts from a Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Requests an hierarquical structure of the posts.
     * Returns an hierarquical structure of the posts.
     * @param $request
     * @param $posts
     * @param $userKey
     * @return array
     */
    private function getRecursivePosts($request, $posts, $userKey)
    {
        $postArray = [];

        foreach ($posts as $post) {
            $post->timezone($request);
            $like = $post->likes()->whereCreatedBy($userKey)->first();
            $reportedAbuse = $post->abuses()->whereCreatedBy($userKey)->first();

            $post['liked'] = $like ? $like->like : -1;
            $post['reported_abuse'] = $reportedAbuse ? 1 : 0;
            $post['count_likes'] = $post->likes()->whereLike(1)->count();
            $post['count_dislikes'] = $post->likes()->whereLike(0)->count();
            $post["replies"] = array_reverse($this->getRecursiveReplies($request, $post->parent_id, $userKey));
            $postArray[] = $post;
        }

        return $postArray;
    }

    /**
     * @param $request
     * @param $parentId
     * @param $userKey
     * @param array $replies
     * @return array
     */
    private function getRecursiveReplies($request, $parentId, $userKey, $replies = [])
    {
        if ($parentId == 0) {
            return $replies;
        }

        $post = Post::withTrashed()->findOrFail($parentId)->timezone($request);;

        $like = $post->likes()->whereCreatedBy($userKey)->first();
        $reportedAbuse = $post->abuses()->whereCreatedBy($userKey)->first();

        $post['liked'] = $like ? $like->like : -1;
        $post['reported_abuse'] = $reportedAbuse ? 1 : 0;
        $post['count_likes'] = $post->likes()->whereLike(1)->count();
        $post['count_dislikes'] = $post->likes()->whereLike(0)->count();

        $replies[] = $post;

        return $this->getRecursiveReplies($post->parent_id, $userKey, $replies);
    }


    /**
     * Requests a list of Post Abuses by Topic.
     * Returns the list of Post Abuses by Topic.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAbuses(Request $request, $topicKey)
    {
        try {
            $abuses = Topic::whereTopicKey($topicKey)->firstOrFail()->abuses;
            return response()->json(['data' => $abuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Abuses from a Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Cooperators.
     * Returns the list of Cooperators.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function cooperatorList(Request $request, $topicKey)
    {
        try {
            $Cooperators = Cooperator::whereTopicKey($topicKey)->firstOrFail();
            return response()->json(['data' => $Cooperators], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic Cooperators list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created Topic Cooperator in storage.
     * Returns the details of the newly created Topic Cooperator.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $topicId
     */
    public function addCooperator(Request $request, $topicKey)
    {
        try {
            $createdBy = ONE::verifyToken($request);
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;

            $cooperatorTypeId = CooperatorType::first()->id;
            foreach ($request->input('cooperators') as $cooperator){
                if (!Cooperator::whereTopicId($topicId)->whereUserKey($cooperator)->exists()) {
                    Cooperator::create(
                        [
                            'topic_id' => $topicId,
                            'user_key' => $cooperator,
                            'type_id' => $cooperatorTypeId,
                            'created_by' => $createdBy
                        ]
                    );
                }
            }
            return response()->json('Ok', 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Topic Cooperator'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified Topic Cooperator from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCooperator(Request $request, $topicKey)
    {
        ONE::verifyToken($request);
        try {

            $userKey = $request->input('userKey');
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;

            $cooperator  = Cooperator::whereUserKey($userKey)->whereTopicId($topicId)->first();

            $cooperator->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cooperator not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Cooperator'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request, $topicKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;

            $posts = Topic::withTrashed()->findOrFail($topicId)->posts()->whereEnabled(1)->get();
            $data = $this->getRecursivePosts($posts, $userKey);

            $configurations = Topic::findOrFail($topicId)->cb->configurations()->select('code')->pluck('code');
            return response()->json(['posts' => $data, 'configurations' => $configurations], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic data'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * THIS FUNCTION RETURNS THE TOPIC POST COMMENTS WITH PAGINATION
     * @param Request $request
     * @param $topic
     * @param $firstTopicPost
     * @param $typeOfComment
     * @param $commentsNeedsAuth
     * @param $numberOfPostsToShow
     * @param $numberOfRepliesToShow
     * @param $orderBy
     * @param $sentPostsIds
     * @param null $postToLoadRepliesFrom
     * @return array
     */
    public function getTopicCommentsWithPagination($request,$topic,$firstTopicPost,$typeOfComment,$commentsNeedsAuth,$numberOfPostsToShow,$numberOfRepliesToShow,$orderBy, $sentPostsIds,$postToLoadRepliesFrom = null){

        $postsToReturn = [];

        //THIS APPLIES WHEN WE WANT TO LOAD ONLY POSTS REPLIES
        if($postToLoadRepliesFrom){
            $post = Post::findOrfail($postToLoadRepliesFrom);
            if ($post->version>0)
                $originalPostId = Post::wherePostKey($post->post_key)->orderBy("version")->first()->id;
            else
                $originalPostId = $post->id;

            if ($commentsNeedsAuth) {
                $post['replies'] = Topic::withTrashed()
                    ->findOrFail($topic->id)
                    ->posts()
                    ->whereNotIn('id', $sentPostsIds)
                    ->whereActive(1)
                    ->whereBlocked(0)
                    ->whereEnabled(1)
                    ->whereParentId($originalPostId)
                    ->limit($numberOfRepliesToShow)
                    ->get();
            } else {
                $post['replies'] = Topic::withTrashed()
                    ->findOrFail($topic->id)
                    ->posts()
                    ->whereNotIn('id', $sentPostsIds)
                    ->whereBlocked(0)
                    ->whereEnabled(1)
                    ->whereParentId($originalPostId)
                    ->limit($numberOfRepliesToShow)
                    ->get();
            }
            if ($post['replies']) {
                foreach ($post['replies'] as $reply) {
                    $reply->timezone($request);
                }
            }

            $countAbuses = $post->abuses()->count();
            $post['abuses'] = $countAbuses;

            $postsToReturn[] = $post;

            return $postsToReturn;
        }

        //GET THE PARENT POSTS [NO PARENT]
        if($commentsNeedsAuth){
            $posts = Topic::withTrashed()->findOrFail($topic->id)->posts()
                ->whereNotIn('id', $sentPostsIds)
                ->wherePostCommentTypeId($typeOfComment)
                ->whereActive(1)
                ->whereBlocked(0)
                ->whereEnabled(1)
                ->whereParentId(0)
                ->where('post_key','!=',$firstTopicPost->post_key)
                ->orderBy('created_at', $orderBy)
                ->limit($numberOfPostsToShow)
                ->get();
        }else{
            $posts = Topic::withTrashed()->findOrFail($topic->id)->posts()
                ->whereNotIn('id', $sentPostsIds)
                ->wherePostCommentTypeId($typeOfComment)
                ->whereBlocked(0)
                ->whereEnabled(1)
                ->whereParentId(0)
                ->where('post_key','!=',$firstTopicPost->post_key)
                ->orderBy('created_at', $orderBy)
                ->limit($numberOfPostsToShow)
                ->get();
        }

        //GET THE PARENT POSTS REPLIES
        if($posts) {
            foreach ($posts as $post) {
                $sentPostsIds[] = $post->id;

                $post->timezone($request);

                if ($post->version>1) {
                    $postVersions = Post::wherePostKey($post->post_key)->orderBy("version")->get();
                } else
                    $postVersions = array(
                        $post->id
                    );

                if ($commentsNeedsAuth) {
                    $post['replies'] = Topic::withTrashed()
                        ->findOrFail($topic->id)
                        ->posts()
                        ->whereNotIn('id', $sentPostsIds)
                        ->whereActive(1)
                        ->whereBlocked(0)
                        ->whereEnabled(1)
                        ->whereIn("id",$postVersions)
                        ->limit($numberOfRepliesToShow)
                        ->get();
                } else {
                    $post['replies'] = Topic::withTrashed()
                        ->findOrFail($topic->id)
                        ->posts()
                        ->whereNotIn('id', $sentPostsIds)
                        ->whereBlocked(0)
                        ->whereEnabled(1)
                        ->whereIn("id",$postVersions)
                        ->limit($numberOfRepliesToShow)
                        ->get();
                }
                if ($post['replies']) {
                    foreach ($post['replies'] as $reply) {
                        $reply->timezone($request);
                    }
                }

                $countAbuses = $post->abuses()->count();
                $post['abuses'] = $countAbuses;

                $postsToReturn[] = $post;
            }
        }
        return $postsToReturn;
    }


    /**
     * THIS FUNCTION RETURNS THE TOPIC POSTS INFORMATION WITH PAGINATION
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicPostsWithPagination(Request $request, $topicKey)
    {
        try {
            $postsWithProblems = 0;

            $posts = Post::where("version",">","1")->get();
            foreach ($posts as $post) {
                $replies = Topic::withTrashed()
                    ->findOrFail($post->topic_id)
                    ->posts()
                    ->whereActive(1)
                    ->whereBlocked(0)
                    ->whereEnabled(1)
                    ->whereParentId(0)
                    ->where('post_key','!=',$post->post_key)
                    ->get();

                if ($replies->count()>0)
                    $postsWithProblems++;
            }
        } catch (Exception $e) {
            dd($e);
        }


        $userKey = ONE::verifyLogin($request);

        try{
            $orderBy = strtolower($request['orderBy']);
            $typeOfComments = $request['typeOfComment'];
            $numberOfPostsToShow = $request['numberOfPostsToShow'];
            $numberOfRepliesToShow = $request['numberOfRepliesToShow'];
            $postToLoadRepliesFrom = $request['postToLoadRepliesFrom'];
            $postToModerate = [];
            $sentPostsIds = [];
            $postsToReturn = [];

            //CHECK TO SEE IF THIS FUNCTION IS ON LAZY LOAD
            $pageToken = $request['pageToken'] ?? null;
            if(empty($pageToken)){
                //GENERATE PAGE TOKEN
                do{
                    $pageToken = str_random(32);
                }while(Cache::has($pageToken));
            }else{
                //THERE IS SOMETHING IN CACHE
                $topic = Cache::get($pageToken . "_topic");
                $firstTopicPost = Cache::get($pageToken . "_firstTopicPost");
                $commentsNeedsAuth = Cache::get($pageToken . "_commentsNeedsAuth");
                $parentTopic = Cache::get($pageToken . "_parentTopic");
                $totalComments = Cache::get($pageToken . "_totalComments");
                $configurations = json_decode(Cache::get($pageToken . "_configurations"));

                //WE KNOW WHAT TOPICS HAVE BEEN SENT
                $sentPostsIds = json_decode(Cache::get($pageToken));
                if ($typeOfPostComment = PostCommentType::whereCode($typeOfComments)->first()) {
                    $typeOfComment = $typeOfPostComment->id;
                } else {
                    $typeOfComment = 0;
                }

                $postsToReturn = $this->getTopicCommentsWithPagination($request, $topic, $firstTopicPost, $typeOfComment, $commentsNeedsAuth, $numberOfPostsToShow, $numberOfRepliesToShow, $orderBy, $sentPostsIds,$postToLoadRepliesFrom);
                if($postsToReturn) {
                    foreach ($postsToReturn as $post) {
                        if (count($post['replies']) > 0) {
                            foreach ($post['replies'] as $reply) {
                                $sentPostsIds[] = $reply['id'];
                            }

                        }

                        $sentPostsIds[] = $post->id;

                    }
                }

                Cache::put($pageToken,json_encode($sentPostsIds),60);

                //CREATE THE RESPONSE OBJECT
                $response = [
                    'pageToken'       => $pageToken,
                    'topic'           => $topic,
                    'parentTopic'     => $parentTopic,
                    'posts'           => $postsToReturn,
                    'postsToModerate' => $postToModerate,
                    'configurations'  => $configurations,
                    'sentPostsIds'    => $sentPostsIds,
                    'totalComments'   => $totalComments,
                ];

                return response()->json($response, 200);

            }



            //GET THE TOPIC
            $topic = $this->parameters($request, $topicKey,true);
            //STORE THE TOPIC IN CACHE
            Cache::put($pageToken . "_topic",$topic,60);

            $totalComments = $topic->posts()->whereEnabled(1)->whereActive(1)->count();
            Cache::put($pageToken . "_totalComments",$totalComments,60);

            //GET THE PARENT TOPIC
            $parentTopic = $topic->parentTopic()->first();
            if (!is_null($parentTopic)) {
                $parentTopic->cb = $parentTopic->cb()->first();
                //STORE THE PARENT TOPIC IN CACHE
                Cache::put($pageToken . "_parentTopic",$topic,60);
            }

            //GET THE CONFIGURATIONS
            $configurations = Topic::findOrFail($topic->id)->cb->configurations()->select('code')->pluck('code');
            //STORE THE CONFIGURATIONS IN CACHE
            Cache::put($pageToken . "_configurations",json_encode($configurations),60);


            //CHECK AUTHORIZATION
            $commentsNeedsAuth = false;
            if(OneCb::checkCBsOption($configurations->toArray(), 'COMMENT-NEEDS-AUTHORIZATION')){
                $commentsNeedsAuth = true;
            }
            //STORE THE COMMENT-NEEDS-AUTHORIZATION IN CACHE
            Cache::put($pageToken . "_commentsNeedsAuth",json_encode($commentsNeedsAuth),60);

            //GET THE FIRST POST
            $firstTopicPost = Post::withTrashed()->whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);
            //STORE THE FIRST TOPIC POST IN CACHE
            Cache::put($pageToken . "_firstTopicPost",$firstTopicPost,60);


            $firstPost = Post::wherePostKey($firstTopicPost->post_key)->whereEnabled(1)->first()->timezone($request);

            //GET THE FIRST POST REPLIES
            $firstPost['replies'] = Topic::withTrashed()
                ->findOrFail($topic->id)
                ->posts()
                ->limit($numberOfRepliesToShow)
                ->whereEnabled(1)->whereParentId($firstTopicPost->id)->get();


            //STORE THE FIRST POST IN RESPONSE
//            $postsToReturn[] = $firstPost;

            //DEAL WITH NORMAL COMMENTS
            $normalComments = $this->getTopicCommentsWithPagination($request,$topic,$firstTopicPost,0,$commentsNeedsAuth,$numberOfPostsToShow,$numberOfRepliesToShow,$orderBy,[]);
            if($normalComments){
                foreach ($normalComments as $normalComment){
                    $postsToReturn[] = $normalComment;
                    $sentPostsIds[] = $normalComment->id;
                    if(count($normalComment['replies']) > 0){
                        foreach ($normalComment['replies'] as $reply)
                            $sentPostsIds[] = $reply->id;
                    }
                }
            }

            //GET THE POSTS OF THIS USER THAT NEED TO BE MODERATED
            //TODO this can and should be optimized
            $commentsNeedsAuth = false;
            if(OneCb::checkCBsOption($configurations->toArray(), 'COMMENT-NEEDS-AUTHORIZATION')){
                $commentsNeedsAuth = true;
            }

            $postToModerate = new Collection();

            if($commentsNeedsAuth && !empty($userKey)){
                $postToModerate = Topic::withTrashed()->findOrFail($topic->id)->posts()
                    ->with('postCommentType')
                    ->whereCreatedBy($userKey)
                    ->whereBlocked(0)
                    ->whereEnabled(1)
                    ->whereActive(0)
                    ->whereParentId(0)
                    ->where('post_key','!=',$firstPost->post_key)
                    ->orderBy('created_at', $orderBy)
                    ->get();
            }


            //CREATE THE RESPONSE OBJECT
            $response = [
                'topic'           => $topic,
                'parentTopic'     => $parentTopic,
                'posts'           => $postsToReturn,
                'postsToModerate' => $postToModerate,
                'configurations'  => $configurations,
                'sentPostsIds'    => $sentPostsIds,
                'totalComments'  => $totalComments
            ];

            //GET THE TYPES OF COMMENTS AVAILABLE
            $postCommentTypes = PostCommentType::all();

            //DEAL WITH POSITIVE, NEUTRAL, NEGATIVE COMMENTS
            if($postCommentTypes) {
                foreach ($postCommentTypes as $commentType) {
                    $postsByCommentType = $this->getTopicCommentsWithPagination($request, $topic, $firstTopicPost, $commentType->id, $commentsNeedsAuth, $numberOfPostsToShow, $numberOfRepliesToShow, $orderBy, []);
                    if($postsByCommentType) {
                        foreach ($postsByCommentType as $post) {
                            $sentPostsIds[] = $post->id;
                            if (count($post['replies']) > 0) {
                                foreach ($post['replies'] as $reply)
                                    $sentPostsIds[] = $reply->id;
                            }
                        }
                    }
                    $response[$commentType->code . '_comments'] = $postsByCommentType;
                }
            }

            //STORE POSTS IN CACHE
            Cache::put($pageToken,json_encode($sentPostsIds),60);
            $response['pageToken'] = $pageToken;

            //STORE TOPIC ACCESS
            $userToken = $request->header('X-AUTH-TOKEN') ?? null;
            $userKey = $userToken ? ONE::verifyToken($request) : 'anonymous';
            if (!$topic->topicAccesses()->whereUserToken($userToken)->exists() || $userToken == null) {
                $topic->topicAccesses()->create([
                    'user_token' => $userToken,
                    'user_key' => $userKey,
                ]);
            }


            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to retrieve the Topic data'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataWithChilds(Request $request, $topicKey)
    {
        try {

            $userKey = ONE::verifyLogin($request);

            // Topic
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topicId = $topic->id;
            $data = [];

            // Configurations
            $configurations = Topic::findOrFail($topicId)->cb->configurations()->select('code')->pluck('code');

            $canBeShown = false;
            if (ONE::verifyRoleAdmin($request, $userKey) != 'admin') {
                if ($topic->cb()->firstOrFail()->moderators()->whereUserKey($userKey)->count()==0) {
                    if ($configurations->search("publish_needed") !== false) {
                        if ($topic->status()->with("statusType")->get()->where("statusType.code", "=", "published")->count() == 0)
                            $canBeShown = ($topic->created_by == $userKey);
                        else
                            $canBeShown = true;
                    } else
                        $canBeShown = true;
                } else
                    $canBeShown = true;
            } else
                $canBeShown = true;

            if ($canBeShown) {
                $commentsNeedsAuth = false;
                if (OneCb::checkCBsOption($configurations->toArray(), 'COMMENT-NEEDS-AUTHORIZATION')) {
                    $commentsNeedsAuth = true;
                }

                $firstPost = Post::withTrashed()->whereTopicId($topicId)
                    ->orderBy('id', 'asc')
                    ->first()
                    ->timezone($request);

                $post = Post::wherePostKey($firstPost->post_key)->whereEnabled(1)->first()->timezone($request);

                $post['replies'] = Topic::withTrashed()
                    ->findOrFail($topicId)
                    ->posts()
                    ->whereEnabled(1)->whereParentId($firstPost->id)->get();
                /*foreach ($post['replies'] as $reply){
                    $reply->timezone($request);
                }*/

                $data[] = $post;
                $postToModerate = new Collection();

                if ((isset($_GET['orderBy']) && !empty($_GET['orderBy'])) && (strtolower($_GET['orderBy']) == 'asc' || strtolower($_GET['orderBy']) == 'desc')) {
                    $orderBy = strtolower($_GET['orderBy']);
                } else {
                    $orderBy = 'asc';
                }

                if ($commentsNeedsAuth) {
                    $posts = Topic::withTrashed()->findOrFail($topicId)->posts()
                        ->with('postCommentType')
                        ->whereActive(1)
                        ->whereBlocked(0)
                        ->whereEnabled(1)
                        ->whereParentId(0)
                        ->where('post_key', '!=', $firstPost->post_key)
                        ->orderBy('created_at', $orderBy)
                        ->get();

                    if (!empty($userKey)) {
                        $postToModerate = Topic::withTrashed()->findOrFail($topicId)->posts()
                            ->with('postCommentType')
                            ->whereCreatedBy($userKey)
                            ->whereBlocked(0)
                            ->whereEnabled(1)
                            ->whereActive(0)
                            ->whereParentId(0)
                            ->where('post_key', '!=', $firstPost->post_key)
                            ->orderBy('created_at', $orderBy)
                            ->get();
                    }
                } else {
                    $posts = Topic::withTrashed()->findOrFail($topicId)->posts()
                        ->with('postCommentType')
                        ->whereBlocked(0)
                        ->whereEnabled(1)
                        ->whereParentId(0)
                        ->where('post_key', '!=', $firstPost->post_key)
                        ->orderBy('created_at', $orderBy)
                        ->get();
                }

                foreach ($posts as $post) {

                    $post->timezone($request);

                    if ($commentsNeedsAuth) {
                        $post['replies'] = Topic::withTrashed()->findOrFail($topicId)
                            ->posts()->whereActive(1)->whereBlocked(0)->whereEnabled(1)->whereParentId($post->id)->get();
                    } else {
                        $post['replies'] = Topic::withTrashed()->findOrFail($topicId)
                            ->posts()->whereBlocked(0)->whereEnabled(1)->whereParentId($post->id)->get();
                    }

                    foreach ($post['replies'] as $reply) {
                        $reply->timezone($request);
                    }

                    $countAbuses = $post->abuses()->count();
                    $post['abuses'] = $countAbuses;

                    $data[] = $post;
                }

                $commentsGroup = New Collection();

                $newData = [];
                foreach ($data as $key => $item) {
                    if (!is_null($item['postCommentType'])) {
                        $commentsGroup->add($item);
                    } else {
                        $newData[] = $item;
                    }
                }

                //get previous and next CB Topic Key

                $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
                $parentTopic = $topic->parentTopic()->first();
                if (!is_null($parentTopic))
                    $parentTopic->cb = $parentTopic->cb()->first();

                $childTopics = $topic->childTopics()->get();
                if ($childTopics->count()>0) {
                    foreach ($childTopics as $childTopic) {
                        $childTopic->cb = $childTopic->cb()->first();
                    }
                }

                $topics = $topics = Topic::whereCbId($topic->cb_id)->get();
                $topics = $topics->sortByDesc('created_at')->pluck('topic_key');

                $summary = [];
                foreach ($topics as $i => $topicKey) {

                    if ($topicKey == $topic->topic_key) {
                        if ($i - 1 >= 0) {
                            $summary['previous'] = $topics[$i - 1];
                        }
                        if ($i + 1 < count($topics)) {
                            $summary['next'] = $topics[$i + 1];
                        }
                    }
                }


                $response = [
                    'topic' => $topic,
                    'parentTopic' => $parentTopic,
                    'childTopics' => $childTopics,
                    'posts' => $newData,
                    'postsToModerate' => $postToModerate,
                    'configurations' => $configurations,
                    'summary' => $summary
                ];

                foreach ($commentsGroup->groupBy('postCommentType.code') as $key => $value) {
                    $response[$key . '_comments'] = $value;
                }

                //          store topic access
                $userToken = $request->header('X-AUTH-TOKEN') ?? null;
                $userKey = $userToken ? ONE::verifyToken($request) : 'anonymous';
                if (!$topic->topicAccesses()->whereUserToken($userToken)->exists() || $userToken == null) {
                    $topic->topicAccesses()->create([
                        'user_token' => $userToken,
                        'user_key' => $userKey,
                    ]);
                }

                return response()->json($response, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic data'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function privateDataWithChilds(Request $request, $topicKey)
    {
        // check auth
        $userKey = ONE::verifyToken($request);

        try {
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;
            $data = [];

            $firstPost = Post::withTrashed()->whereTopicId($topicId)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first()
                ->timezone($request);

            $post['replies'] = Topic::withTrashed()->findOrFail($topicId)->posts()->whereEnabled(1)->whereParentId($firstPost->id)->get();
            $data[] = $post;

            if ((isset($_GET['orderBy']) && !empty($_GET['orderBy'])) && (strtolower($_GET['orderBy']) == 'asc' || strtolower($_GET['orderBy']) == 'desc')){
                $orderBy = strtolower($_GET['orderBy']);
            } else {
                $orderBy = 'asc';
            }

            $posts = Topic::withTrashed()->findOrFail($topicId)->posts()->whereEnabled(1)->whereParentId(0)->where('post_key','!=',$firstPost->post_key)->orderBy('created_at', $orderBy)->get();

            foreach ($posts as $post) {

                $post->timezone($request);

                $post['replies'] = Topic::withTrashed()->findOrFail($topicId)->posts()->whereEnabled(1)->whereParentId($post->id)->get();
                $countAbuses = $post->abuses()->count();
                $post['abuses'] = $countAbuses;

                foreach ($post['replies'] as $reply) {
                    $reply->timezone($request);
                }

                $data[] = $post;
            }

            $configurations = Topic::findOrFail($topicId)->cb->configurations()->select('code')->pluck('code');
            return response()->json(['posts' => $data, 'configurations' => $configurations], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic data'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(Request $request, $topicKey)
    {
        ONE::verifyToken($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topic->blocked = 1;
            $topic->save();

            return response()->json($topic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to block Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblock(Request $request, $topicKey)
    {
        ONE::verifyToken($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topic->blocked = 0;
            $topic->save();

            return response()->json($topic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to unblock Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function setParameters(Request $request, $topicKey)
    {
        ONE::verifyToken($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $parameters = [];
            foreach ($request->json('parameters') as $parameter) {
                $parameters[$parameter['parameter_id']] = clean($parameter);
            }
            $topic->parameters()->sync($parameters);

            $topic = Topic::with(['parameters.type', 'firstPost'])->findOrFail($topic->id)->timezone($request);

            return response()->json($topic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to set Topic Parameters'], 500);
        }
    }

    public function getTopicStatus(Request $request, $topicKey){


        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            return response()->json(Status::whereTopicId($topic->id)->exists(), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Email'], 500);
        }
    }

    public function getTopicsByParent(Request $request){


        try {
            $topic = Topic::whereTopicKey($request['topicKey'])->firstOrFail();
            $topics = Topic::whereParentTopicId($topic->id)->get();
            return response()->json(['data' => $topics], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Email'], 500);
        }
    }

    public function getTopicUserEmail(Request $request, $topicKey){


        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $parameter_type_email = ParameterType::whereCode('email')->first();

            if($parameter_type_email){

                $parameters = $topic->parameters()->get();

                if($parameters){

                    foreach($parameters as $parameter){

                        if($parameter->parameter_type_id ==  $parameter_type_email->id){
                            $email = $parameter->pivot->value;
                            break;
                        }

                    }
                }
            }

            return response()->json($email, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Email'], 500);
        }
    }


    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameters(Request $request, $topicKey, $internalCall = false)
    {
        try {
            $isPublicCall = $request->get("publicCall",false);
            if ($isPublicCall)
                $isPublicCall=true;

            $userKey = ONE::verifyLogin($request);
            $user = OrchUser::whereUserKey($userKey)->first();
            if (!$isPublicCall)
                $groups = $user->entityGroups()->get();


            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;
            $version = $request->input('topicVersion');



            $topic = Topic::with(['status' => function ($q){
                $q->where('active','=',1);
            }, 'status.statusType','followers','parentTopic.cb'])->findOrFail($topicId)->timezone($request);

            $cb = Cb::find($topic->cb_id);
            if(!$isPublicCall) {
                $padPermissions = $cb->padPermissions()->whereIn('group_key', collect($groups)->pluck('entity_group_key')->toArray())->orWhere('user_key', '=', $userKey)->get();

                $permissionOptions = [];
                if (!empty($padPermissions)) {
                    foreach ($padPermissions as $padPermission) {
                        $padPermissionOptions = $padPermission->parameterOptions()->get();
                        if (!empty($padPermissionOptions)) {
                            foreach ($padPermissionOptions as $option)
                                $permissionOptions[$option->parameter_id] = $option->pivot->parameter_option_id;
                        }
                    }
                }
            }

            $versions = $topic->topicVersions()->get()->unique("version")->map(function($item, $key) {
                return [
                    "version"       => $item->version,
                    "active"        => $item->active,
                    "created_at"    => $item->created_at
                ];
            });

            if(!$versions->isEmpty()){
                $topic["versions"] = $versions;
                $lastVersion = '';
                if (is_null($version)) {
                    if ($topic->topicVersions()->whereActive(1)->count()==1) {
                        $lastVersion = $topic->topicVersions()->whereActive(1)->firstOrFail();
                    }
                    else {
                        $lastVersion = $topic->topicVersions()->orderBy("version", "desc")->firstOrFail();
                    }
                } else{
                    $lastVersion = $topic->topicVersions()->whereVersion($version)->firstOrFail();
                }
                if(!empty($lastVersion)){
                    $topic->title = $lastVersion->title;
                    $topic->summary = $lastVersion->summary;
                    $topic->contents = $lastVersion->contents;
                    $topic->description = $lastVersion->description;
                    $topic->active = $lastVersion->active;
                    $topic->version = $lastVersion->version;

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
                }
            }else{
                $topic = Topic::with(['parameters.type', 'parameters.options', 'status' => function ($q){
                    $q->where('active','=',1);
                }, 'status.statusType','followers'])->findOrFail($topicId)->timezone($request);
                $topic["versions"] = null;
            }


            if(!$isPublicCall && !empty($permissionOptions) && ONE::verifyRoleAdmin($request, $userKey) != 'admin'){
                foreach ($permissionOptions as $key => $option){
                    if(collect($topic->parameters)->where('id','=',$key)->where('pivot.value','=',$option)->isEmpty()){
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }
                }
            }

            $topic->active_status = $topic->status()->with('statusType')->whereActive(1)->first();
            /** Verify if topic is closed */
            if(!empty($topic->active_status) && $topic->active_status->statusType->code != 'moderated'){
                $topic->closed = true;
            }else{
                $topic->closed = false;
            }

            if(!empty($topic->parameters)){
                foreach ($topic->parameters as $parameter){
                    foreach ($parameter->options as $option){
                        $parameterOptionFields = $option->parameterOptionFields()->get();
                        foreach ($parameterOptionFields as $parameterOptionField){
                            $option[$parameterOptionField->code] = $parameterOptionField->value;
                        }
                    }
                }
            }

            if(!empty($topic->active_status)){

                if (!($topic->active_status->statusType->translation($request->header('LANG-CODE')))) {
                    if (!$topic->active_status->statusType->translation($request->header('LANG-CODE-DEFAULT'))){
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
            if(!empty($userKey)){
                $following = $topic->followers()->whereUserKey($userKey)->exists();
            }
            $topic->following = $following;
            if(!empty($topic->parameters)){
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
            }

            foreach ($topic->status as $status) {
                if(!empty($status->statusType)){
                    if (!($status->statusType->translation($request->header('LANG-CODE')))) {
                        if (!$status->statusType->translation($request->header('LANG-CODE-DEFAULT'))){
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

            $topicAlliances = [];
            if ($topic->originAllyRequest->count()>0) {
                foreach ($topic->originAllyRequest as $alliance) {
                    if ($userKey==$topic->created_by || $alliance->accepted==1) {
                        $topicAlliances[$alliance->ally_key] = $alliance->destinyTopic;

                        if ((is_null($alliance->accepted) && $userKey == $topic->created_by))
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", -1);
                        else if ($alliance->accepted == 0 && $userKey == $topic->created_by)
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", -2);
                        else if ($userKey == $topic->created_by)
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", 3);

                        $topicAlliances[$alliance->ally_key]
                            ->setAttribute("ally_key", $alliance->ally_key)
                            ->setAttribute("request_date",Carbon::parse($alliance->created_at)->toDateTimeString())
                            ->setAttribute("request_description",$alliance->request_message)
                            ->setAttribute("response_date",Carbon::parse($alliance->updated_at)->toDateTimeString())
                            ->setAttribute("response_description",$alliance->response_message);
                    }
                }
            }

            if ($topic->destinyAllyRequest->count()>0) {
                foreach ($topic->destinyAllyRequest as $alliance) {
                    if ($userKey==$topic->created_by || $alliance->accepted==1) {
                        $topicAlliances[$alliance->ally_key] = $alliance->originTopic;

                        if ((is_null($alliance->accepted) && $userKey == $topic->created_by))
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", 1);
                        else if ($alliance->accepted == 0 && $userKey == $topic->created_by)
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", 2);
                        else if ($userKey == $topic->created_by)
                            $topicAlliances[$alliance->ally_key]->setAttribute("status", 3);

                        $topicAlliances[$alliance->ally_key]
                            ->setAttribute("ally_key", $alliance->ally_key)
                            ->setAttribute("request_date",Carbon::parse($alliance->created_at)->toDateTimeString())
                            ->setAttribute("request_description",$alliance->request_message)
                            ->setAttribute("response_date",Carbon::parse($alliance->updated_at)->toDateTimeString())
                            ->setAttribute("response_description",$alliance->response_message);
                    }
                }
            }


            $firstPost = Post::whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first()
                ->timezone($request);

            $topic['first_post'] = $post;
            $topic['alliances'] = $topicAlliances;
            if($internalCall){
                return $topic;
            }
            return response()->json($topic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic with its Parameters'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsWithFirstPost(Request $request)
    {
        try {
            $topics = Topic::with('firstPost', 'parameters', 'parameters.options')->whereIn('id', clean($request->json('topicList')))->get();

            foreach ($topics as $topic) {
                $topic->timezone($request);
            }

            return response()->json(['data' => $topics], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsWithModeration(Request $request)
    {
        try {
            $data = [];
            if(!empty($request->json('data'))){
                foreach ($request->json('data') as $item) {
                    $cb = Cb::with('configurations')->whereCbKey($item['cb_key'])->first();
                    if(!empty($cb->configurations)){
                        foreach ($cb->configurations as $configuration) {
                            if ($configuration->code == 'topic_need_moderation') {

                                $topics = $cb->topics()->get();
                                foreach ($topics as $topic) {
                                    if(!Status::whereTopicId($topic->id)->exists()){
                                        $topic['cb_key'] = $cb->cb_key;
                                        $topic['cb_title'] = $cb->title;
                                        $topic->timezone($request);
                                        $data[] = $topic;
                                    }
                                }
                            }
                        }
                    }
                }

            }
            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topics needing Moderation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @internal param $cbKey
     */
    public function topicsWithTechnicalEvaluation(Request $request){

        try{
            $data = [];
            if(!empty($request->json('data'))){
                foreach ($request->json('data') as $item) {
                    $cb = Cb::with(['topics.status' => function ($q) {
                        $q->where('active', '=', 1);}])->whereCbKey($item['cb_key'])->first();


                    if (!empty($cb['topics'])) {
                        foreach ($cb['topics'] as $topic) {

                            if (!empty($topic['status'])) {

                                foreach ($topic['status'] as $status) {

                                    if ($status->active == 1) {

                                        $s = StatusType::whereId($status->status_type_id)->first();

                                        if ($s->code == 'accepted') {

                                            $topic->accepted_at = $status->updated_at->toDateTimeString();
                                            $topic->cb_type = EntityCb::whereCbKey($cb->cb_key)->first()->cbType()->first()->code;
                                            $topic->cb_key = $cb->cb_key;
                                            $data[] = $topic;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data = collect($data)->sortByDesc(function ($topic, $key) {
                return $topic->accepted_at;
            })->take(10);




            return response()->json(["data" => $data ]);

        }catch(Exception $e){
            return response()->json($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function cooperatorPermissions(Request $request, $topicKey)
    {
        $userKey = ONE::verifyLogin($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $cooperator = $userKey ? $topic->cooperators()->whereUserKey($userKey)->exists() : null;

            $permissions = [];
            if ($cooperator){
                $permissions = $topic->cooperators()->whereUserKey($userKey)->firstOrFail()->permissions()->pluck('code');
            }
            return response()->json(['data' => $permissions], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Permissions'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopics(Request $request)
    {
        try {

            $topicsKeys = $request->json('topic_keys');

            if ($request->json("no_parameters",false)) {
                $topics = Topic::whereIn('topic_key', $topicsKeys)->get();
            } else {
                $topics = Topic::with('parameters', 'parameters.options')->whereIn('topic_key', $topicsKeys)->get();

                /** Get parameters and options translations */
                if (!empty($request->header('LANG-CODE')) && !empty($request->header('LANG-CODE-DEFAULT'))) {
                    foreach ($topics as $topic) {
                        foreach ($topic->parameters as $parameter) {
                            if (!($parameter->translation($request->header('LANG-CODE')))) {
                                if (!$parameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                                    return response()->json(['error' => 'No translation found'], 404);
                                }
                            }
                            foreach ($parameter->options as $option) {
                                if (!($option->translation($request->header('LANG-CODE')))) {
                                    if (!$option->translation($request->header('LANG-CODE-DEFAULT'))) {
                                        return response()->json(['error' => 'No translation found'], 404);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return response()->json(['data' => $topics], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topics'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * get topic parameters and options with all translations, specifically for the kiosk
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function kioskParameters(Request $request, $topicKey)
    {
        try {
            try {
                $topic = Topic::with(['parameters.type', 'parameters.options'])->whereTopicKey($topicKey)->firstOrFail()->timezone($request);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Topic not found'], 404);
            }



            $parameter->translations();

            foreach ($parameter->options as $option) {
                $option->translations();
            }

            $firstPost = Post::whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first()
                ->timezone($request);

            $topic['first_post'] = $post;

            return response()->json($topic, 200);
        }  catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic with its Parameters'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     *
     * @SWG\Get(
     *  path="/topic/{topic_key}/topicFollowers",
     *  summary="Show Topic Followers",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
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
     *      description="Show Topic Followers",
     *      @SWG\Schema(ref="#/definitions/topicFollowersResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Topic Followers",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicFollowers(Request $request, $topicKey){
        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topicFollowers = TopicFollower::whereTopicId($topic->id)->get();

            return response()->json(['data' => $topicFollowers], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic Followers'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    public function getUserTopics(Request $request, $userKey) {
        ONE::verifyToken($request);
        try {
            $topicsOfUser = Topic::whereCreatedBy($userKey)->get();
            return response()->json(["topics"=>$topicsOfUser],200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topics not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Topics'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getUserTopicsTimeline(Request $request, $userKey) {
        ONE::verifyToken($request);
        try {
            $userTopics = [];
            $entity = ONE::getEntity($request);
            if (!empty($entity)) {
                $entityCbs = $entity->entityCbs()
                    ->with([
                        "cbType",
                        "cb.topics" => function($q) use ($userKey) {
                            $q->where("created_by","=",$userKey);
                        }
                    ])->get();

                foreach ($entityCbs as $entityCb) {
                    $cb = $entityCb->cb;
                    foreach ($cb->topics as $topic) {
                        $currentTopic = $topic;
                        $currentTopic["cb_key"] = $cb->cb_key;
                        $currentTopic["cb_type"] = $entityCb->cbType->code;
                        $currentTopic["order_date"] = $topic->created_at;

                        if (!empty($topic->deleted_at))
                            $currentTopic["order_date"] = $topic->deleted_at;

                        $userTopics[] = $currentTopic;
                    }
                }
            }

            $userTopics = collect($userTopics)->sortByDesc("order_date");

            return response()->json(["topics"=>$userTopics],200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topics not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Topics'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getUserTopicsPaginated(Request $request, $userKey) {
        ONE::verifyToken($request);
        try {
            $cbKeys = ($request->cbKeys ?? []);
            if (count($cbKeys)>0) {
                $cbIds = Cb::whereIn("cb_key", $cbKeys)->get()->pluck("id");

                $currentPage = ($request->page ?? 1) - 1;
                $topicsPerPage = $request->topicsPerPage;

                $allTopics = Topic::with('cb')->whereCreatedBy($userKey)->whereIn("cb_id", $cbIds)->orderBy("created_at", "desc")->get();

                $totalTopics = $allTopics->count();
                $userTopics = $allTopics->slice(($currentPage * $topicsPerPage), $topicsPerPage);

                return response()->json(["topics" => $userTopics, "total" => $totalTopics], 200);
            } else
                return response()->json(["topics" => array(), "total" => 0], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topics not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Topics'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /* Alliances methods */

    /**
     * Allies two topics
     * @param Request $request
     * @param $firstTopicKey
     * @param $secondTopicKey
     */
    public function allyTopics(Request $request, $firstTopicKey, $secondTopicKey) {
        ONE::verifyToken($request);

        try {
            $firstTopic = Topic::whereTopicKey($firstTopicKey)->firstOrFail();
            $secondTopic = Topic::whereTopicKey($secondTopicKey)->firstOrFail();

            do {
                $key = '';
                $rand = str_random(32);
                if (!($exists = Topic::whereTopicKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $ally = TopicAlliance::create([
                "ally_key" => $key,
                "request_message" => $request->input("request_message") ?? "",
                "origin_topic_id" => $firstTopic->id,
                "destiny_topic_id" => $secondTopic->id,
            ]);

            return response()->json(['allyKey' => $ally->ally_key], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to ally Topic'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Processes Ally request response
     * @param Request $request
     * @param $allyKey
     */
    public function allyRequestResponse(Request $request, $allyKey) {
        ONE::verifyToken($request);

        try {
            $ally = TopicAlliance::whereAllyKey($allyKey)->firstOrFail();
            if (is_null($ally->accepted)) {
                $ally->response_message = $request->input("message") ?? "";
                $ally->accepted = ($request->has("response")) ? $request->input("response") : 0;
                $ally->save();

                return response()->json(['allyKey' => $ally->ally_key], 200);
            } else {
                return response()->json(["error" => "Already responded to this ally request"],400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ally not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Ally'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get ally data
     * @param Request $request
     * @param $allyKey
     */
    public function getAlly(Request $request, $allyKey) {
        ONE::verifyToken($request);
        $userKey = ONE::verifyToken($request);

        try {
            $ally = TopicAlliance::whereAllyKey($allyKey)->firstOrFail();
            $ally->originTopic;
            $ally->destinyTopic;

            if ($userKey==$ally->originTopic->created_by || $userKey==$ally->destinyTopic->created_by) {
                return response()->json(['ally' => $ally], 200);
            } else
                return response()->json(['error' => 'Ally not found'], 404);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ally not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Ally',"e"=>$e->getMessage()], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get User's related allies
     * @param Request $request
     * @param $allyKey
     */
    public function getAllies(Request $request) {
        $userKey = ONE::verifyToken($request);

        try {
            $topicsOfUser = Topic::whereCreatedBy($userKey)->get();
            $alliances = [];

            foreach ($topicsOfUser as $topic) {
                $alliancesTemp = [];
                if ($topic->originAllyRequest->count()>0)
                    $alliancesTemp = array_merge($alliancesTemp,$topic->originAllyRequest->toArray());
                if($topic->destinyAllyRequest->count()>0)
                    $alliancesTemp = array_merge($alliancesTemp,$topic->destinyAllyRequest->toArray());

                foreach ($alliancesTemp as $ally) {
                    $alliances[$ally["ally_key"]] = $ally;
                }
            }

            $alliances = collect($alliances)->sortByDesc(function ($item) {
                return Carbon::parse($item["created_at"])->getTimestamp();
            });

            return response()->json(['allies' => $alliances], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ally not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive Ally'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Request a specific Topic, including deleted (with a flag.) Originally made to use in user timeline
     * Returns the details of a specific Topic.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function getTopicForTimeline(Request $request, $topicKey)
    {
        try {
            $topic = Topic::withTrashed()
                ->whereTopicKey($topicKey)
                ->firstOrFail()
                ->timezone($request);

            $firstPost = Post::whereTopicId($topic->id)
                ->orderBy('id', 'asc')
                ->first()
                ->timezone($request);

            $post = Post::wherePostKey($firstPost->post_key)
                ->whereEnabled(1)
                ->first()
                ->timezone($request);

            $topic['first_post'] = $post;

            $cb = $topic->cb()->first();

            return response()->json(['topic' => $topic, 'cb' => $cb], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }







    /**
     *
     * @SWG\Post(
     *  path="/topic/{topic_key}/addTopicNews",
     *  summary="Add topic News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic news data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicNewsStore")
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
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
     *     @SWG\Schema(ref="#/definitions/topicNewsResponse"),
     *      description="The associated news"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store news to the topic
     * Returns the topic news
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTopicNews(Request $request, $topicKey)
    {
        $createdBy = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['news_key'], $request);
        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $newsKey = $request->json('news_key');
            $topicNews = $topic->news()->whereNewsKey($newsKey)->first();
            if(empty($topicNews)){
                $topicNews = $topic->news()->create(
                    [
                        'topic_id' => $topic->id,
                        'news_key' => $newsKey,
                        'tag' => $request->json('tag') ?? null,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy
                    ]
                );
            }

            $topicNews = $topic->news()->get();

            return response()->json(['data' => $topicNews], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store topic News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     *
     * @SWG\Get(
     *  path="/topic/{topic_key}/getTopicNews",
     *  summary="Get Topic News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
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
     *     @SWG\Schema(ref="#/definitions/topicNewsResponse"),
     *      description="The topic news"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Get Topic news
     * Returns the topic news
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicNews(Request $request, $topicKey)
    {
        $createdBy = ONE::verifyToken($request);
        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $tag = $request->get('tag');
            if(!empty($tag)){
                $topicNews = $topic->news()->whereTag($tag)->get();
            }else{
                $topicNews = $topic->news()->get();
            }

            return response()->json(['data' => $topicNews], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get topic News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteTopicNews",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *
     * @SWG\Delete(
     *  path="/topic/{topic_key}/deleteTopicNews/{news_key}",
     *  summary="Delete Topic News",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
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
     *     @SWG\Schema(ref="#/definitions/replyDeleteTopicNews"),
     *      description="Ok"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete Topic news
     *
     * @param Request $request
     * @param $topicKey
     * @param $newsKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTopicNews(Request $request, $topicKey,$newsKey)
    {
        $userKey = ONE::verifyToken($request);
        try {

            try {
                $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Topic not Found'], 404);
            }

            $topicNews = $topic->news()->whereNewsKey($newsKey)->firstOrFail();
            $topicNews->updated_by = $userKey;
            $topicNews->save();
            $topicNews->delete();

            return response()->json('ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic News not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete topic News'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /** ------------------- Methods about Topic cbs ------------------- */



    /**
     *
     * @SWG\Post(
     *  path="/topic/{topic_key}/addTopicCb",
     *  summary="Add Topic Cbs",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic cbs data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicCbsStore")
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
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
     *     @SWG\Schema(ref="#/definitions/topicCbsResponse"),
     *      description="The associated news"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store new Cb to Topic
     * Returns the topic cbs
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTopicCb(Request $request, $topicKey)
    {
        $createdBy = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['cb_key'], $request);
        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            try{
                $cb = Cb::whereCbKey($request->json('cb_key'))->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Cb not Found'], 404);
            }
            if(!$topic->topicCbs()->whereCbId($cb->id)->exists()){
                $topicCb = $topic->topicCbs()->create(
                    [
                        'cb_id' => $cb->id,
                        'created_by' => $createdBy,
                        'updated_by' => $createdBy,
                    ]
                );
            }
            $topicCbs = $topic->topicCbs()->with('cb')->get()->pluck('cb');

            return response()->json(['data' => $topicCbs], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store cb to topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteTopicCb",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *
     * @SWG\Delete(
     *  path="/topic/{topic_key}/deleteTopicCb/{cb_key}",
     *  summary="Delete Topic Cb",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
     *      required=true,
     *      type="string"
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
     *     @SWG\Schema(ref="#/definitions/replyDeleteTopicCb"),
     *      description="Ok"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Delete Topic cb
     *
     * @param Request $request
     * @param $topicKey
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTopicCb(Request $request, $topicKey,$cbKey)
    {
        $userKey = ONE::verifyToken($request);
        try {

            try{
                $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Topic not Found'], 404);
            }

            try{
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Cb not Found'], 404);
            }

            $topicCb = $topic->topicCbs()->whereCbId($cb->id)->firstOrFail();
            $topicCb->updated_by = $userKey;
            $topicCb->save();
            $topicCb->delete();

            return response()->json('ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Cb not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete topic cb'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     *
     * @SWG\Get(
     *  path="/topic/{topic_key}/getTopicCbs",
     *  summary="Get Topic Cbs",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Topic"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
     *      required=true,
     *      type="string"
     *  ),
     *
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
     *     @SWG\Schema(ref="#/definitions/topicCbsResponse"),
     *      description="The topic Cbs"
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *   ),
     *
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Not Found",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/topicErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Get Topic Cbs
     * Returns the topic Cbs
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopicCbs(Request $request, $topicKey)
    {
        ONE::verifyToken($request);
        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicCbs = $topic->topicCbs()->with('cb')->get()->pluck('cb');

            return response()->json(['data' => $topicCbs], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Topic Cbs'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function publishUserTopic(Request $request, $topicKey) {
        $userKey = ONE::verifyToken($request);
        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            if ($topic->created_by == $userKey && $topic->cb()->firstOrFail()->configurations()->whereCode("publish_needed")->count()>0) {
                $publishedStatusId = StatusType::whereCode("published")->firstOrFail()->id;

                do {
                    $rand = str_random(32);
                    if (!($exists = Status::whereStatusKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $topic->status()->create([
                    'status_key' => $key,
                    'status_type_id' => $publishedStatusId,
                    'active' => 1,
                    'created_by' => $userKey
                ]);

                /* Moderates the topic if no moderation is required */
                $config = Configuration::whereCode('topic_need_moderation')->first();
                if(!is_null($config)) {
                    //check pivot table if topics need config in current CB
                    $cb_config = $topic->cb()->firstOrFail()->configurations()->whereConfigurationId($config->id)->first();

                    if (is_null($cb_config)) {
                        $statusType = StatusType::whereCode('moderated')->first();
                        if (!is_null($statusType)) {
                            //"disable" previous statuses
                            $statusUpdate = Status::whereTopicId($topic->id)->update(['active' => 0]);

                            //new key for status
                            do {
                                $rand = str_random(32);
                                if (!($exists = Status::whereStatusKey($rand)->exists())) {
                                    $key = $rand;
                                }
                            } while ($exists);

                            $statusType->status()->create([
                                'status_key' => $key,
                                'status_type_id' => $statusType->id,
                                'topic_id' => $topic->id,
                                'active' => 1,
                                'created_by' => is_null($userKey) ? 'anonymous' : $userKey
                            ]);
                        }
                    }
                }

                return response()->json(['result' => "success"], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Publish Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     *
     * Displays all posts for given topic
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function privateDataWithChildsForModal(Request $request, $topicKey)
    {
        try {

            $userKey = ONE::verifyLogin($request);

            // Topic
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicId = $topic->id;
            $data = [];

            // Configurations
            $configurations = Topic::findOrFail($topicId)->cb->configurations()->select('code')->pluck('code');

            $canBeShown = false;
            if (ONE::verifyRoleAdmin($request, $userKey) != 'admin') {
                if ($topic->cb()->firstOrFail()->moderators()->whereUserKey($userKey)->count()==0) {
                    if ($configurations->search("publish_needed") !== false) {
                        if ($topic->status()->with("statusType")->get()->where("statusType.code", "=", "published")->count() == 0)
                            $canBeShown = ($topic->created_by == $userKey);
                        else
                            $canBeShown = true;
                    } else
                        $canBeShown = true;
                } else
                    $canBeShown = true;
            } else
                $canBeShown = true;

            if ($canBeShown) {
                $commentsNeedsAuth = false;
                if (OneCb::checkCBsOption($configurations->toArray(), 'COMMENT-NEEDS-AUTHORIZATION')) {
                    $commentsNeedsAuth = true;
                }

                $firstPost = Post::withTrashed()->whereTopicId($topicId)
                    ->orderBy('id', 'asc')
                    ->first()
                    ->timezone($request);

                $post = Post::wherePostKey($firstPost->post_key)->whereEnabled(1)->first()->timezone($request);

                $post['replies'] = Topic::withTrashed()
                    ->findOrFail($topicId)
                    ->posts()
                    ->whereEnabled(1)->whereParentId($firstPost->id)->get();

                $data[] = $post;
                $postToModerate = new Collection();

                if ((isset($_GET['orderBy']) && !empty($_GET['orderBy'])) && (strtolower($_GET['orderBy']) == 'asc' || strtolower($_GET['orderBy']) == 'desc')) {
                    $orderBy = strtolower($_GET['orderBy']);
                } else {
                    $orderBy = 'asc';
                }

                $posts = Topic::withTrashed()->findOrFail($topicId)->posts()
                    ->with('postCommentType')
                    ->whereBlocked(0)
                    ->whereEnabled(1)
                    ->whereParentId(0)
                    ->where('post_key', '!=', $firstPost->post_key)
                    ->orderBy('created_at', $orderBy)
                    ->get();

                foreach ($posts as $post) {

                    $post->timezone($request);

                    $post['replies'] = Topic::withTrashed()->findOrFail($topicId)
                        ->posts()->whereBlocked(0)->whereEnabled(1)->whereParentId($post->id)->get();

                    foreach ($post['replies'] as $reply) {
                        $reply->timezone($request);
                    }
                    $countAbuses = $post->abuses()->count();
                    $post['abuses'] = $countAbuses;
                    $data[] = $post;
                }

                $commentsGroup = New Collection();

                $newData = [];
                foreach ($data as $key => $item) {
                    if (!is_null($item['postCommentType'])) {
                        $commentsGroup->add($item);
                    } else {
                        $newData[] = $item;
                    }
                }

                $response = [
                    'topic' => $topic,
                    'posts' => $newData,
                    'postsToModerate' => $postToModerate,
                    'configurations' => $configurations,
                ];


                return response()->json($response, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic data'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * DEPRECATED - use 'getCooperatorsList' instead
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCooperators(Request $request){
        try {
            $tableData = $request->input('tableData') ?? null;
            $topic = Topic::whereTopicKey($request->topic_key)->firstOrFail();

            $permissions = CooperatorType::get();

            $primaryLanguage = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            foreach($permissions as $permission){
                $permission->newTranslation($primaryLanguage,$defaultLanguage);
            }

            $cooperators = $topic->cooperators()->get();

            $cooperatorsData = User::whereIn('user_key', $cooperators->pluck('user_key'))->get();

            foreach ($cooperators as $user){
                $user->name = $cooperatorsData->where('user_key', '=', $user->user_key)->first()->name;
            }

            return response()->json("cooperators", 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCooperatorsList(Request $request){
        try {
            $tableData = $request->input('tableData') ?? null;
            $topic = Topic::whereTopicKey($request->topic_key)->firstOrFail();

            $permissions = CooperatorType::get();

            $primaryLanguage = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            foreach($permissions as $permission){
                $permission->newTranslation($primaryLanguage,$defaultLanguage);
            }

            $query = $topic->cooperators();

            $recordsTotal = $query->count();

            if (!empty($tableData)){
                if (!empty($tableData['start']))
                    $query = $query->skip($tableData['start']);

                $cooperators = $query
                    ->take($tableData['length'])
                    ->get();
            } else {
                $cooperators = $query->get();
            }

            $cooperatorsData = User::whereIn('user_key', $cooperators->pluck('user_key'))->get();

            foreach ($cooperators as $user){
                $user->name = $cooperatorsData->where('user_key', '=', $user->user_key)->first()->name;
            }


            $recordsFiltered = $cooperators->count();

            $data['cooperators'] = $cooperators;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;
            $data['permissions'] = $permissions;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     *
     * Method to notify authors with topics closer to a deadline.
     * (Deadline defined in given Number of Days passed from moderation date)
     *
     * Method to run periodically based on defined job/automation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topicsNearVoteEndingAlert()
    {
        try {

            //Get currently active deadline notification configurations to run
            $configuration = Configuration::whereCode('notification_deadline')->first();
            $cbsConfigs = $configuration->cbs()->get();

            // foreach all CBs in configuration
            foreach ($cbsConfigs as $i=>$cbsConfig){

                //Get JSON Object stored in value
                $value = json_decode($cbsConfig->pivot->value);

                if(empty($value) || empty($value->deadline))
                    continue;

                //number of days passed since moderation // when the notification is triggered
                $nDaysAlert = (int) $value->deadline;

                //get values needed for Notify
                $entityKey = !empty($value->entityKey) ? $value->entityKey : null;
                $siteNoReplyEmail = !empty($value->siteNoReplyEmail) ? $value->siteNoReplyEmail : null;
                $siteName = !empty($value->siteName) ? $value->siteName : null;
                $cbTypeCode = !empty($value->cbTypeCode) ? $value->cbTypeCode : null;

                $cbKey = $cbsConfig->cb_key;

                //Set the date to be compared
                $dateToCompare = Carbon::now()->addDays($nDaysAlert)->subDays(60);

                //Get the CB
                $cb = CB::whereCbKey($cbKey)->first();

                //Get the email/notify template
                $template = CbTemplate::whereCbKey($cbKey)->whereConfigurationCode('notification_deadline')->first();

                if (!empty($template) || !empty($cb) ){
                    try {

                        //Get Status Id for moderated status code
                        $statusId = StatusType::where('code','moderated')->first()->id;

                        //get all the topics with currently active moderated status, in which the moderation date has passed the given number of days
                        $topics = $cb->topics()->with('status')->whereHas('status', function($q) use($dateToCompare, $statusId){
                            $q->where('active', 1)
                                ->where('status_type_id', $statusId)
                                ->whereDate('created_at', '=', $dateToCompare->toDateString());
                        })->get();

                        //notifications counter
                        $nNotifications = 0;
                        foreach ($topics as $topic) {

                            if ($topic->created_by != 'anonymous') {

                                //get user
                                $user = User::whereUserKey($topic->created_by)->first();
                                $usersEmail = $user->email ?? null;
                                $userKey = $user->user_key ?? null;

                                //URL + TAGS
                                //                          $url = "<a href='".action('TopicController@show', [$cbTypeCode, $cbKey, $topic->topic_key])."'>".$topic->title."</a>";
                                $url = '/#';  //TODO: Remove this dummy url and replace with the correct url for topic redirection.
                                $tags = ["topic" => $url, "title_topic" => $topic->title];

                                //SEND NOTIFICATION
                                $sendEmail = Notify::sendEmailForDeadlineNotification($template->template_key, $usersEmail, $userKey, $tags, $entityKey, $siteName, $siteNoReplyEmail);

                                if($sendEmail->statusCode() == 200){
                                    ++$nNotifications;
                                }

                            }
                        }

                        Log::info("[DEADLINE NOTIFICATION] CB - " . $cbKey . " - Total Notifications: " . $nNotifications);

                    } catch (Exception $e) {
                        return response()->json(['error' => 'Failed to retrieve the Topics List'], 500);
                    }
                }

            }
            //END - foreach all CBs
            return response()->json(['OK'], 200);

        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the CB'], 500);
        }
    }

    public function getTopicVersions($topicKey){
        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicVersions = $topic->topicVersions()->get();

            return response()->json($topicVersions, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve topic versions'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * THIS FUNCTION WAS CREATED TO CHECK IF WE NEED TO CHANGE
     * THE PARENT TOPIC ID OF A LIST OF TOPICS WHEN WE
     * CHANGE A CURRENT VERSION STATUS
     * @param $topic
     * @param $topicVersion
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTopicsParentTopicId($topic, $topicVersion, $status)
    {
        //TRY CATCH IS SET ON PARENT FUNCTION
        $attachedToVersionParameterIds = $topicVersion->topicParameters()->get();

        //DETACH BEFORE DOING ANYTHING
        Topic::whereParentTopicId($topic->id)->update(['parent_topic_id' => 0]);

        if(!$attachedToVersionParameterIds->isEmpty()){
            $associatedTopicsParameter = Topic::whereId($topicVersion->topic_id)->with(
                ['parameters' => function ($q) use ($attachedToVersionParameterIds,$topicVersion){
                    $q->whereIn('id',$attachedToVersionParameterIds->pluck('parameter_id')->toArray())
                        ->where('topic_version_id', '=',$topicVersion->id)
                        ->whereHas('type',function ($q){
                            $q->where('code', '=', 'associated_topics');
                        });
                    }
                ])->first(); //SHOULD BE JUST ONE

            if(!$associatedTopicsParameter->parameters->isEmpty()){
                $relationInformation = $associatedTopicsParameter->parameters->first()->pivot->value;
                $jsonObject = json_decode($relationInformation);
                if(isset($jsonObject->myTopics)) {
                    $topicsToDealWith = $jsonObject->myTopics;
                    if($status == 1){
                        foreach($topicsToDealWith as $topicKey){
                            Topic::whereTopicKey($topicKey)->update(['parent_topic_id' => $topic->id]);
                        }
                    }
                }
            }
        }
    }


    public function changeActiveVersionStatus(Request $request,$topicKey){
        try {
            $version = $request->input('version');
            $status = $request->input('status');
            $activeBy =  $request->input('activeBy');
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicVersion = $topic->topicVersions()->update(["active" => 0, "active_by" => null]);
            $topicVersion = $topic->topicVersions()->whereVersion($version)->first();

            $topicVersion->active_by = $activeBy;
            $topicVersion->active = $status;
            $topicVersion->save();

            $this->updateTopicParametersCache($topicKey, $topicVersion);

            if($request->input('checkParentTopics')){
                $this->updateTopicsParentTopicId($topic,$topicVersion,$status);
            }

            return response()->json($topicVersion, 200);
        } catch (Exception $e) {
            return response()->json(['error'=> 'Failed to update status'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $topicKey
     * @param $lastVersion
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTopicParametersCache($topicKey, $lastVersion)
    {
        $topic = Topic::whereTopicKey($topicKey)->first();

        //Get Topic Cached Data
        $cachedData = json_decode($topic->_cached_data);
        if (empty($cachedData))
            $cachedData = new \stdClass();

        $topic->title = $lastVersion->title;
        $topic->summary = $lastVersion->summary;
        $topic->contents = $lastVersion->contents;
        $topic->description = $lastVersion->description;
        $topic->active = $lastVersion->active;
        $topic->version = $lastVersion->version;

        $parameters = $lastVersion->topicParameters()->get();
        $param = [];
        $parametersCache = [];

        foreach ($parameters as $parameter) {
            $parameterTranslations = [];

            $topicParameter = $topic->parameters()
                ->wherePivot("topic_version_id","=",$lastVersion->id)
                ->with(['type', 'parameterTranslations', 'options.parameterOptionTranslations', 'options.parameterOptionFields'])->find($parameter->parameter_id);

            foreach ($topicParameter->parameterTranslations??[] as $parameterTranslation) {
                $parameterTranslations[$parameterTranslation->language_code] = array('parameter' => $parameterTranslation->parameter, 'description' => $parameterTranslation->description);
            }

            unset($topicParameter->parameterTranslations);

            $parametersCache[$parameter->parameter_id] = $topicParameter;
            $parametersCache[$parameter->parameter_id]['translations'] = $parameterTranslations;

            if (!empty($topicParameter)) {
                $topicParameter->pivot->value = $parameter->value;
                $param[] = $topicParameter;
            }


            foreach ($parametersCache[$parameter->parameter_id]->options??[] as $parameterOption) {

                $optionTranslations = [];
                $parameterOptionFields = [];

                foreach ($parameterOption->parameterOptionTranslations as $parameterOptionTranslation) {
                    $optionTranslations[$parameterOptionTranslation->language_code] = array('label' => $parameterOptionTranslation->label);
                }

                $parameterOption->translations = $optionTranslations;
                unset($parameterOption->parameterOptionTranslations);

                foreach ($parameterOption->parameterOptionFields as $parameterOptionField) {
                    $parameterOptionFields[] = array($parameterOptionField->code => $parameterOptionField->value);
                }

                $parameterOption->fields = $parameterOptionFields;
                unset($parameterOption->parameterOptionFields);
            }
        }

        //Cache Parameters
        $cachedData->parameters = json_decode(json_encode(array_values($parametersCache)));

        $following = false;
        if (!empty($userKey)) {
            $following = $topic->followers()->whereUserKey($userKey)->exists();
        }

        $cachedData->following = $following;

        $topic->_cached_data = json_encode($cachedData);
        $topic->save();
    }

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCooperatorPermission(Request $request, $topicKey){
        try {
            $topicId = Topic::whereTopicKey($topicKey)->firstOrFail()->id;

            $cooperator  = Cooperator::whereTopicId($topicId)->whereUserKey($request->input('userKey'))->firstOrFail();

            $cooperator->type_id = $request->input('permission');
            $cooperator->save();

            return response()->json('Ok', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update cooperator permission'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTopicVotesInfo(Request $request, $topicKey){
        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $cb = $topic->cb()->first();

            $newVotes   = $request->json('votes');
            $eventKey   = $request->json('event_key');
            $totalVotes = $request->json('total_votes');
            $totalUsers = $request->json('total_users');

            $cachedData = json_decode($topic->_cached_data);

//            if (isset($cachedData->votes)){
//                $cachedData->votes->{$eventKey} = $newVotes;
//                $topic->_cached_data = json_encode($cachedData);
//                $topic->save();
//            } else {
//                $topic->_cached_data->votes = collect([$eventKey => $newVotes]);
//                $topic->_cached_data = json_encode($topic->_cached_data);
//                $topic->save();
//            }

            $voteStatistics = json_decode($cb->_vote_statistics);

            if (!isset($voteStatistics->votes_by_event)) {
                if (is_null($voteStatistics)) {
                    $voteStatistics = ['votes_by_event' => collect([$eventKey => $totalVotes])];
                    $cb->_vote_statistics = json_encode($voteStatistics);
                } else {
                    $voteStatistics->votes_by_event = collect([$eventKey => $totalVotes]);
                    $cb->_vote_statistics = json_encode($voteStatistics);
                }
            } else {
                $voteStatistics->votes_by_event->{$eventKey} = $totalVotes;
                $cb->_vote_statistics = json_encode($voteStatistics);
            }

            if (!isset($voteStatistics->voters_by_event)) {
                if (is_null($voteStatistics)) {
                    $voteStatistics['voters_by_event'] = collect([$eventKey => $totalUsers]);
                    $cb->_vote_statistics = json_encode($voteStatistics);
                } else {
                    $voteStatistics->voters_by_event = collect([$eventKey => $totalUsers]);
                    $cb->_vote_statistics = json_encode($voteStatistics);
                }
            } else {
                $voteStatistics->voters_by_event->{$eventKey} = $totalUsers;
                $cb->_vote_statistics = json_encode($voteStatistics);
            }

            $cb->save();

            return response()->json(true, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Topic Vote Information'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}