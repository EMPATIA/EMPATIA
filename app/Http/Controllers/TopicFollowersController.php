<?php

namespace App\Http\Controllers;

use App\One\One;
use App\Topic;
use App\TopicFollower;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


/**
 * @SWG\Tag(
 *   name="TopicFollower",
 *   description="Everything about TopicFollowers",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicFollowerErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *
 *  @SWG\Definition(
 *     definition="topicFollowerDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 *
 *
 * @SWG\Definition(
 *   definition="topicFollower",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="follower_key", format="string", type="string"),
 *           @SWG\Property(property="user_key", format="string", type="string"),
 *           @SWG\Property(property="topic_id", format="integer", type="integer"),
 *       )
 *   }
 * )
 *
 *
 * @SWG\Definition(
 *   definition="topicFollowerStore",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_key"},
 *           @SWG\Property(property="topic_key", format="string", type="string")
 *
 *       )
 *   }
 * )
 *
 *
 *
 *
 *
 */


class TopicFollowersController extends Controller
{
    protected $required = [
        'store' => ['topic_key']
    ];


    /**
     * @SWG\Post(
     *  path="/topicFollowers",
     *  summary="Store a Topic Follower",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicFollower"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Follower Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicFollowerStore")
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
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The stored Topic Follower",
     *      @SWG\Schema(ref="#/definitions/topicFollower")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Topic not Found",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Operator",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *  )
     * )
     */

    public function index(Request $request)
    {
        //ONE::verifyKeysRequest($this->required["index"], $request);

        try {
            $followingsDB = TopicFollower::whereUserKey($request->get('user_key'))->get();
            $topics = [];
            foreach ($followingsDB as $following) {
                if ($following->topic()->count()==1)
                    $topics[] = $following->topic()->first();
            }

            return response()->json($topics,200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Followings not found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retreive followings'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["store"], $request);

        try {
            do {
                $key = '';
                $rand = str_random(32);
                if (!($exists = TopicFollower::whereTopicFollowerKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $topic = Topic::whereTopicKey($request->json('topic_key'))->firstOrFail();
            /** Verify if user already follow topic*/
            $topicFollower = TopicFollower::whereTopicId($topic->id)->whereUserKey($userKey)->first();
            if(empty($topicFollower)){
                $topicFollower = $topic->followers()->create(
                    [
                        'topic_follower_key' => $key,
                        'user_key' => $userKey,
                    ]

                );
            }

            return response()->json($topicFollower, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Topic Follower'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }


    /**
     * @SWG\Delete(
     *  path="/topicFollowers/{topic_key}",
     *  summary="Delete a Topic follower",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicFollower"},
     *
     * @SWG\Parameter(
     *      name="topic_key",
     *      in="path",
     *      description="Topic Key",
     *      required=true,
     *      type="integer"
     *  ),
     *
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
     *      @SWG\Schema(ref="#/definitions/topicFollowerDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Follower not Found",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic Follower",
     *      @SWG\Schema(ref="#/definitions/topicFollowerErrorDefault")
     *  )
     * )
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $topicKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();
            $topicFollower = TopicFollower::whereTopicId($topic->id)->whereUserKey($userKey)->firstOrFail();
            $topicFollower->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Follower not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Topic Follower'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
