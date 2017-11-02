<?php

namespace App\Http\Controllers;

use App\One\One;
use App\TopicReview;
use App\TopicReviewReply;
use App\TopicReviewStatus;
use App\TopicReviewStatusType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class TopicReviewRepliesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="TopicReviewReply",
 *   description="Everything about Topic Review Replies",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicReviewReplyErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="topicReviewReplyResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_review_reply_key", "topic_review_id", "content", "created_by", "created_at", "updated_at"},
 *           @SWG\Property(property="topic_review_reply_key", format="string", type="string"),
 *           @SWG\Property(property="topic_review_id", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property="topic_review",
 *              allOf={
 *       @SWG\Schema(
 *           required={"topic_review_key", "topic_id", "description", "subject", "created_by", "created_at", "updated_at"},
 *           @SWG\Property(property="topic_review_key", format="string", type="string"),
 *           @SWG\Property(property="topic_id", format="string", type="integer"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="subject", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   })
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topicReviewReply",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_review_key", "content", "code"},
 *           @SWG\Property(property="topic_review_key", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topicReviewReplyUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"content", "code"},
 *           @SWG\Property(property="content", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */
class TopicReviewRepliesController extends Controller
{


    /**
     *
     * Lists all TopicReviewReplies for given TopicReview
     *
     * @param Request $request
     * @param $topicReviewKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $topicKey
     */
    public function index(Request $request, $topicReviewKey)
    {
        //token verification
        ONE::verifyToken($request);

        try {

            $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)->firstOrFail();
            $topicReviewsReplies = TopicReviewReply::whereTopicReviewId($topicReview->id)->with('TopicReview')->get();
            return response()->json($topicReviewsReplies, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic ReviewReply list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/topicReviewReplies/{topicReviewReplyKey}",
     *  summary="Show a Topic Review Reply",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewReply"},
     *
     * @SWG\Parameter(
     *      name="topicReviewReplyKey",
     *      in="path",
     *      description="Topic Review Reply Key",
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
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  )
     * )
     *
     */

    /**
     *
     * Shows TopicReviewReply details
     *
     * @param Request $request
     * @param $topicReviewReplyKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicReviewReplyKey)
    {

//        ONE::verifyToken($request);
        try {
            //$topicReviewReply = TopicReviewReply::whereTopicReviewReplyKey($topicReviewReplyKey)->with('TopicReviewStatus')->with('TopicReview')->firstOrFail();
            $topicReviewReply = TopicReviewReply::whereTopicReviewReplyKey($topicReviewReplyKey)->with('TopicReview')->firstOrFail();
            return response()->json($topicReviewReply, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Reply  not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Topic ReviewReply '], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/topicReviewReplies",
     *  summary="Creation of a Topic Review Reply",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewReply"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Review Reply data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicReviewReply")
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
     *      description="the newly created Topic Review Reply",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Topic Review Reply",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  )
     * )
     *
     */
    /**
     *
     * Stores a new TopicReviewReply
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //token verification
        $userKey = ONE::verifyToken($request);
        try{

            //ONE::verifyKeysRequest($this->required["store"], $request); //TODO: implement this

            //receives $topicKey from request
            $topicReviewKey = $request->json('topic_review_key');
            $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)->firstOrFail();


            // topic_review_reply_key generation
            $key = '';
            do {
                $rand = str_random(32);

                if (!($exists = TopicReviewStatus::whereTopicReviewStatusKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);


            //TopicReviewReplyCreation
            $topicReviewReply = $topicReview->topicReviewReplies()->create([
                'topic_review_reply_key' => $key,
                'content' => $request->json('content'),
                'created_by' => $userKey,
            ]);


            /*----------- TOPIC REVIEW STATUS --------*/

            //Get topic Type from code
            $topicReviewStatusType = TopicReviewStatusType::whereCode($request->json('code'))->firstOrFail();

            //"disable" previous statuses
            $statusUpdate = TopicReviewStatus::whereTopicReviewId($topicReview->id)->update(['active' => 0]);

            //Topic Review Status key generation

            do {
                $rand = str_random(32);
                if (!($exists = TopicReviewStatus::whereTopicReviewStatusKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            //Topic Review Status creation
            $topicReviewStatus = $topicReview->topicReviewStatus()->create(
                [
                    'topic_review_status_key' => $key,
                    'topic_review_status_type_id' => $topicReviewStatusType->id,
                    'active' => 1,
                    'created_by' => $userKey
                ]
            );

            return response()->json($topicReviewReply, 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Reply not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store Topic Review Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
     * @SWG\Put(
     *  path="/topicReviewReplies/{topicReviewReplyKey}",
     *  summary="Update a Topic Review Reply",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewReply"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Review Reply Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="topicReviewReplyKey",
     *      in="path",
     *      description="Topic Review Reply Key",
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
     *      description="The updated Topic Review Reply",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Topic Review Reply not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Topic Review Reply",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates TopicReviewReply
     *
     * @param Request $request
     * @param $topicReviewReplyKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $topicReviewReplyKey)
    {
        //token verification
        $userKey = ONE::verifyToken($request);

        try{
            // ONE::verifyKeysRequest($this->required["update"], $request);      //TODO: implement keys verification

            $topicReviewReply = TopicReviewReply::whereTopicReviewReplyKey($topicReviewReplyKey)->firstOrFail();
            //get topic review status


            /*----------- TOPIC REVIEW STATUS --------*/

            //Get topic Type from code
            $topicReviewStatusType = TopicReviewStatusType::whereCode($request->json('code'))->firstOrFail();


            //"disable" previous statuses
            $statusUpdate = TopicReviewStatus::whereTopicReviewId($topicReviewReply->topic_review_id)->update(['active' => 0]);

            //Topic Review Status key generation

            do {
                $rand = str_random(32);
                if (!($exists = TopicReviewStatus::whereTopicReviewStatusKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            //Topic Review Status creation
            TopicReviewStatus::create(
                [
                    'topic_review_status_key' => $key,
                    'topic_review_id' => $topicReviewReply->topic_review_id,
                    'topic_review_status_type_id' => $topicReviewStatusType->id,
                    'active' => 1,
                    'created_by' => $userKey
                ]
            );

            //TopicReviewReply update
            $topicReviewReply->created_by = $userKey;
            $topicReviewReply->content = $request->json('content');
            $topicReviewReply->save();

            return response()->json($topicReviewReply, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Reply not Found'], 404);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to store Topic Review Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteTopicReviewReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/topicReviewReplies/{topicReviewReplyKey}",
     *  summary="Delete a Topic Review Reply",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewReply"},
     *
     * @SWG\Parameter(
     *      name="topicReviewReplyKey",
     *      in="path",
     *      description="Topic Review Reply Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTopicReviewReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review Reply not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic Review Reply",
     *      @SWG\Schema(ref="#/definitions/topicReviewReplyErrorDefault")
     *  )
     * )
     *
     */

    /**
     *
     * Deletes TopicReviewReply
     *
     * @param Request $request
     * @param $topicReviewReplyKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $topicReviewReplyKey)
    {

        ONE::verifyToken($request);

        try{

            $topicReviewReply = TopicReviewReply::whereTopicReviewReplyKey($topicReviewReplyKey)->firstOrFail();

            $topicReviewReply->delete();

            return response()->json('Ok', 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Reply not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Topic Review Reply'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
