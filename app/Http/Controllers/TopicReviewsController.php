<?php

namespace App\Http\Controllers;

use App\Cb;
use App\One\One;
use App\Topic;
use App\TopicReview;
use App\TopicReviewReviewer;
use App\TopicReviewStatus;
use App\TopicReviewStatusType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class TopicReviewsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="TopicReview",
 *   description="Everything about Topic Reviews",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicReviewErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="topicReviewResponse",
 *   type="object",
 *   allOf={
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
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topicReview",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_review_key", "created_by", "topic_id", "description", "subject"},
 *           @SWG\Property(property="topic_review_key", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="topic_id", format="string", type="string"),
 *           @SWG\Property(property="reviewer_key", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="subject", format="string", type="string"),
 *           @SWG\Property(property="topic_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="reviewers", type="array",
 *                  @SWG\Items(
 *                      type="object",
 *                      @SWG\Property(property="key", type="string"),
 *                      @SWG\Property(property="is_group", type="integer")
 *                  )
 *          )
 *       )
 *   }
 * )
 *
 */

class TopicReviewsController extends Controller
{
    protected $required = [

        'store' => ['topic_review_key', 'created_by', 'topic_id','reviewer_key', 'is_group', 'description', 'subject' ]
        /*'update' => ['code', 'description', 'subject']*/
    ];


    /**
     * Lists all Topic Reviews for given topic
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $topicKey)
    {
        //token verification
        ONE::verifyToken($request);

        try {

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            $topicReviews = TopicReview::whereTopicId($topic->id)->orderBy('created_at', 'desc')->with('TopicReviewReplies')->get();

            $reviews = [];
            foreach ($topicReviews as $topicReview) {

                if (count($topicReview->TopicReviewReviewers)>0){
                    $reviews[] = $topicReview;
                }
            }

            return response()->json(['data' => $reviews], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Topic Reviews list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *
     * returns a data array organized by reviewers (groups and users)
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByReviewerType(Request $request, $topicKey)
    {
        //token verification
        //ONE::verifyToken($request);


        try {
            $isGroup = isset($request->is_group) ? $request->is_group: 0;

            //get topic
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            //get Topic Reviews with reviewers = users
            $topicReviews = TopicReview::whereTopicId($topic->id)->with('TopicReviewReplies')
                ->with(['TopicReviewReviewers' =>
                            function($query) use ($isGroup){
                                $query->where('is_group', $isGroup);
                            }])
                ->get();



            //remove topic reviewers with empty TopicReviewReviewers
            $newArray = [];
            foreach ($topicReviews as $topicReview) {

                if (count($topicReview->TopicReviewReviewers)>0){
                    $newArray[] = $topicReview;
                }
            }


            $data = [];
            $data[] = $newArray;

            return response()->json(['data' => $newArray], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/topicReviews/{topicReviewKey}",
     *  summary="Show a Topic Review",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReview"},
     *
     * @SWG\Parameter(
     *      name="topicReviewKey",
     *      in="path",
     *      description="Topic Review Key",
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
     *      description="Show the Topic Review data",
     *      @SWG\Schema(ref="#/definitions/topicReviewResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Topic Review",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $topicReviewKey
     * @return Exception|ModelNotFoundException|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicReviewKey)
    {

        ONE::verifyToken($request);
        try {


            $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)
                ->with(['TopicReviewStatus' =>
                            function ($query) {
                                $query->where('active', '=', 1);
                            }, 'TopicReviewReviewers'])
                ->firstOrFail();

            $status = $topicReview->topicReviewStatus()->orderBy('created_at', 'desc')->first();
            $new = TopicReviewStatusType::whereId($status->topic_review_status_type_id)->first();
            $topicReview->status = $new->code;

            return response()->json($topicReview, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review not Found'], 404);
        }catch(Exception $e){
            return $e;
            return response()->json(['error' => 'Failed to retrieve Topic Review'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);


    }

    /**
     *
     * @SWG\Post(
     *  path="/topicReviews",
     *  summary="Creation of a Topic Review",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReview"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Review data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicReview")
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
     *      description="the newly created topic review",
     *      @SWG\Schema(ref="#/definitions/topicReviewResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new topic review",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  )
     * )
     *
     */

    /**
     *
     * Stores a new TopicReview
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

            /*----------- TOPIC REVIEW --------*/

            //receives $topicKey from request
            $topicKey = $request->json('topic_key');
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            //Get topic Type from code
            $topicReviewStatusType = TopicReviewStatusType::whereCode($request->json('code'))->firstOrFail();

            // topic_review_key generation
            $key = '';
            do {
                $rand = str_random(32);

                if (!($exists = TopicReview::whereTopicReviewKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);


            //TopicReview creation
            $topicReview = TopicReview::create(
                [
                    'topic_review_key' => $key,
                    'created_by' => $userKey,
                    'description' => $request->json('description'),
                    'subject' => $request->json('subject'),
                    'topic_id' => $topic->id
                ]
            );

            /*----------- TOPIC REVIEW STATUS --------*/

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

            /*----------- TOPIC REVIEW REVIEWERS --------*/

            //reviewers_keys
            $reviewers = $request->json('reviewers');


            //Reviewers create
            foreach ($reviewers as $reviewer){

                $topicReview->topicReviewReviewers()->create([
                        'reviewer_key' => $reviewer['key'],
                        'is_group' => $reviewer['is_group'],
                    ]
                );
            }

            $topicReviewReviewers = $topicReview->topicReviewReviewers()->get();

            return response()->json([$topicReviewReviewers, $topicReview], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store Topic Review'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
     * @SWG\Put(
     *  path="/topicReviews/{topicReviewKey}",
     *  summary="Update a Topic Review",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReview"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Review Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicReview")
     *  ),
     *
     * @SWG\Parameter(
     *      name="topicReviewKey",
     *      in="path",
     *      description="Topic Review Key",
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
     *      description="The updated Topic Review",
     *      @SWG\Schema(ref="#/definitions/topicReviewResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Topic Review not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Topic Review",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates a TopicReview - only adds reviewers
     *
     * @param Request $request
     * @param $topicReviewKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $topicReviewKey)
    {
        //token verification
        $userKey = ONE::verifyToken($request);

        //verify if is admin
        $role = ONE::verifyRoleAdmin($request, $userKey);

        //gets topic review
        $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)->with('TopicReviewReviewers')->firstOrFail();

        //only allows update to Topic Review Author or Admin
        if($topicReview->created_by == $userKey || $role = 'admin'){

            try{
                // ONE::verifyKeysRequest($this->required["update"], $request);      //TODO: implement this

                //update description
                $topicReview->description = $request->json('description');
                $topicReview->subject = $request->json('subject');

                $topicReview->save();

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

                /*----------- TOPIC REVIEW REVIEWERS    --------*/

                //reviewers_keys
                $reviewers = $request->json('reviewers');

                //Reviewers create
                if (count($reviewers) > 0) {
                    foreach ($reviewers as $reviewer) {

                        //verifies if entry doesn't already exist
                        if (!TopicReviewReviewer::where([

                            ['reviewer_key', '=', $reviewer['key']],
                            ['topic_review_id', '=', $topicReview->id]

                        ])->exists()
                        ) {
                            //adds reviewers
                            $topicReview->topicReviewReviewers()->create([
                                'reviewer_key' => $reviewer['key'],
                                'is_group' => $reviewer['is_group'],
                            ]);
                        }
                    }
                }
                //retrieves topicReview with new reviewers
                $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)->with('TopicReviewReviewers')->firstOrFail();

                return response()->json($topicReview, 200);

            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Topic Review not Found'], 404);
            } catch (Exception $e) {

                return response()->json(['error' => 'Failed to store Topic Review'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
    /**
     *  @SWG\Definition(
     *     definition="replyDeleteTopicReview",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/topicReviews/{topicReviewKey}",
     *  summary="Delete a Topic Review",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReview"},
     *
     * @SWG\Parameter(
     *      name="topicReviewKey",
     *      in="path",
     *      description="Topic Review Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTopicReview")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic Review",
     *      @SWG\Schema(ref="#/definitions/topicReviewErrorDefault")
     *  )
     * )
     *
     */


    /**
     *
     * Deletes a TopicReview
     *
     * @param Request $request
     * @param $topicReviewKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $topicReviewKey)
    {

        ONE::verifyToken($request);

        try{

            $topicReview = TopicReview::whereTopicReviewKey($topicReviewKey)->firstOrFail();

            $topicReview->delete();

            return response()->json('Ok', 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Topic Review'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
