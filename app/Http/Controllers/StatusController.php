<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests;
use App\One\One;
use App\Status;
use App\StatusType;
use App\Topic;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class StatusController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Status",
 *   description="Everything about Status",
 * )
 *
 * @SWG\Definition(
 *      definition="statusErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 * @SWG\Definition(
 *   definition="status",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "topic_key"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="topic_key", format="string", type="string"),
 *           @SWG\Property(property="status_type_id", format="string", type="integer"),
 *           @SWG\Property(property="comment", format="string", type="string")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="statusResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"status_key", "status_type_id", "topic_id", "active", "created_at", "updated_at"},
 *           @SWG\Property(property="status_key", format="string", type="string"),
 *           @SWG\Property(property="status_type_id", format="string", type="integer"),
 *           @SWG\Property(property="topic_id", format="string", type="integer"),
 *           @SWG\Property(property="active", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */
class StatusController extends Controller
{
    protected $required = [
        'store' => [
            'topic_key',
            'code'
        ]
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $status = Status::all();

            return response()->json(['data' => $status], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Status']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/status/{statusKey}",
     *  summary="Show a Status",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Status"},
     *
     * @SWG\Parameter(
     *      name="statusKey",
     *      in="path",
     *      description="Status Key",
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
     *      description="Show the Status data",
     *      @SWG\Schema(ref="#/definitions/statusResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Status not Found",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Status",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $statusKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $statusKey)
    {

        try {
            $status = Status::whereStatusKey($statusKey)->firstOrFail();

            return response()->json($status, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Status'], 500);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/status",
     *  summary="Creation of a Status",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Status"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Status data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/status"),
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
     *      description="the newly created status",
     *      @SWG\Schema(ref="#/definitions/statusResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Status",
     *      @SWG\Schema(ref="#/definitions/statusErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            if ($request->json('code') != "0") {
                $statusType = StatusType::whereCode($request->json('code'))->firstOrFail();
            }
            $topic = Topic::whereTopicKey($request->json('topic_key'))->firstOrFail();

            if (!$topic->topicVersions()->whereActive(1)->exists()) {
                $topic->topicVersions()->update(["active" => 0, "active_by" => null]);
                $topicVersion = $topic->topicVersions()->orderByDesc("version")->first();
                $topicVersion->active = 1;
                $topicVersion->active_by = $userKey;
                $topicVersion->save();
            } else
                $topicVersion = $topic->topicVersions()->whereActive(1)->first();

            if (!empty($topicVersion))
                (new TopicsController())->updateTopicParametersCache($topic->topic_key, $topicVersion);

            do {
                $rand = str_random(32);
                if (!($exists = Status::whereStatusKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $statusUpdate = Status::whereTopicId($topic->id)->update(['active' => 0]);

            if ($request->json('code') != "0") {
                $status = $topic->status()->create(
                    [
                        'status_key' => $key,
                        'status_type_id' => $statusType->id,
                        'active' => 1,
                        'created_by' => $userKey
                    ]
                );
            } else {
                $status = null;
            }
            $comment = $request->json('comment');

            if (!empty($request->json('comment'))) {
                $comment = $request->json('comment');
                if ((!empty($comment['content']))) {

                    do {
                        $rand = str_random(32);
                        if (!($exists = Comment::whereCommentKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $newComment = $status->comments()->create(
                        [
                            'comment_key' => $key,
                            'title' => $request->json('code'),
                            'content' => $comment['content'],
                            'created_by' => $userKey
                        ]
                    );

                    /**GRAVAR COMENTARIO PUBLICO SE VIER NOS PARAMETROS INICIO**/
                    if (!empty($comment['public_content'])) {

                        do {
                            $rand = str_random(32);
                            if (!($exists = Comment::whereCommentKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $newComment = $status->comments()->create(
                            [
                                'comment_key' => $key,
                                'public' => 1,
                                'title' => $request->json('code'),
                                'content' => $comment['public_content'],
                                'created_by' => $userKey
                            ]
                        );
                    }

                    /**GRAVAR COMENTARIO PUBLICO SE VIER NOS PARAMETROS FIM**/
                }
            }

            // Notify Followers - BEGIN
//            $configurations = $topic->cb->configurations()->select('code')->pluck('code');
//
//            if (OneCb::checkCBsOption($configurations->toArray(), 'NOTIFICATION-STATUS-CHANGE')){
//                $tags = [
//                    'topic'         => $topic->title,
//                    'topic_status'  => $statusType->code,
//                ];
//                $response = One::notifyFollowers($request, $tags, 'notification_status_change');
//            }
            // Notify Followers - END

            $topicActiveVersion = $topic->topicVersions()->whereActive(1)->first();
            if (!empty($topicActiveVersion)) {
                $topicsController = new TopicsController();
                $topicsController->updateTopicParametersCache($topic->topic_key,$topicActiveVersion);
            }

            return response()->json($status, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Status'], 500);
        }
    }

    public function history(Request $request, $topicKey)
    {
        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $statuses = $topic->status()->orderBy('created_at', 'desc')->get();

            foreach ($statuses as $status) {

                $statusType = $status->statusType()->first();

                if (!($statusType->translation($request->header('LANG-CODE')))) {
                    if (!$statusType->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }

                $status['name'] = $statusType->name;
                $status['comments'] = $status->comments()->get();
            }

            return response()->json(['data' => $statuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Status History']);
        }
    }
}
