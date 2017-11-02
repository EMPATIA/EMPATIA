<?php

namespace App\Http\Controllers;

use App\One\One;
use App\TopicReview;
use App\TopicReviewStatus;
use App\TopicReviewStatusType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class TopicReviewStatusController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="TopicReviewStatus",
 *   description="Everything about Topic Review Status",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicReviewStatusErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="topicReviewStatus",
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
 *   definition="topicReviewStatusResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_review_status_key", "topic_review_status_type_id", "topic_review_id", "active", "created_by", "created_at", "updated_at", },
 *           @SWG\Property(property="topic_review_status_key", format="string", type="string"),
 *           @SWG\Property(property="topic_review_status_type_id", format="string", type="integer"),
 *           @SWG\Property(property="topic_review_id", format="string", type="integer"),
 *           @SWG\Property(property="active", format="string", type="integer"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */

class TopicReviewStatusController extends Controller
{

    protected $required = [
        'store' => [
            'topic_review_key',
            'code'
        ]
    ];

    /**
     *
     * Returns a list of Topic Review Statuses
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponses
     */
    public function index(Request $request)
    {
        try {
            $status = TopicReviewStatus::all();

            return response()->json(['data' => $status], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Topic Review Status']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/topicReviewStatus/{topicReviewStatusKey}",
     *  summary="Show a Topic Review Status",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewStatus"},
     *
     * @SWG\Parameter(
     *      name="topicReviewStatusKey",
     *      in="path",
     *      description="Topic Review Status Key",
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
     *      description="Show the Topic Review Status data",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review Status not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Topic Review Status",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusErrorDefault")
     *  )
     * )
     *
     */

    /**
     *
     * Shows a topic review status
     *
     * @param Request $request
     * @param $statusKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicReviewStatusKey)
    {

        try {
            $topicReviewStatus = TopicReviewStatus::whereTopicReviewStatusKey($topicReviewStatusKey)->firstOrFail();

            return response()->json($topicReviewStatus, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Status not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic Review Status'], 500);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteTopicReviewStatus",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/topicReviewStatus/{topicReviewStatusKey}",
     *  summary="Delete a Topic Review Status",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewStatus"},
     *
     * @SWG\Parameter(
     *      name="topicReviewStatusKey",
     *      in="path",
     *      description="Topic Review Status Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTopicReviewStatus")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review Status not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic Review Status",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusErrorDefault")
     *  )
     * )
     *
     */

    /**
     *
     * Deletes a TopicReview Status
     *
     * @param Request $request
     * @param $topicReviewKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $topicReviewStatusKey)
    {

        ONE::verifyToken($request);

        try{

            $topicReviewStatus = TopicReviewStatus::whereTopicReviewStatusKey($topicReviewStatusKey)->firstOrFail();

            $topicReviewStatus->delete();

            return response()->json('Ok', 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Topic Review Status'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
