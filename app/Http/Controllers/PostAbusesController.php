<?php

namespace App\Http\Controllers;

use App\PostAbuse;
use App\Post;
use App\Topic;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class PostAbusesController
 * @package App\Http\Controllers
 */
/**
 * @SWG\Tag(
 *   name="PostAbuse",
 *   description="Everything about Post Abuses",
 * )
 *
 *  @SWG\Definition(
 *      definition="postAbuseErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="postAbuse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_key", "type_id", "comment", "created_by"},
 *           @SWG\Property(property="post_key", format="string", type="string"),
 *           @SWG\Property(property="type_id", format="string", type="integer"),
 *           @SWG\Property(property="comment", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="postAbuseResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"id", "post_id", "type_id", "comment", "created_by", "processed", "created_at", "updated_at"},
 *           @SWG\Property(property="id", format="string", type="integer"),
 *           @SWG\Property(property="post_id", format="string", type="integer"),
 *           @SWG\Property(property="type_id", format="string", type="integer"),
 *           @SWG\Property(property="comment", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="processed", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */
class PostAbusesController extends Controller
{

    protected $keysRequired = [
        'post_key',
        'type_id',
        'comment'
    ];

    /**
     * Requests a list of Post Abuses.
     * Returns the list of Post Abuses.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $post_id)
    {
        try {
            $postabuses = PostAbuse::where("post_id", "=", $post_id)->get();
            return response()->json(['postabuses' => $postabuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Abuse list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/postabuse/{id}",
     *  summary="Show a Post Abuse",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostAbuse"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Abuse Id",
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
     *      @SWG\Schema(ref="#/definitions/postAbuseResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific Post Abuse.
     * Returns the details of a specific Post Abuse.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $postabuse = PostAbuse::findOrFail($id);
            return response()->json(['postabuse' => $postabuse], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Abuse not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/postabuse",
     *  summary="Creation of a Post Abuse",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostAbuse"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Abuse data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postAbuse")
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
     *      description="the newly created post abuse",
     *      @SWG\Schema(ref="#/definitions/postAbuseResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Post Abuse",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created Post Abuse in storage.
     * Returns the details of the newly created Post Abuse.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $post = Post::wherePostKey( $request->json('post_key'))->firstOrFail();
            $postabuse = PostAbuse::create(['post_id' => $post->id,
                'type_id' => $request->json('type_id'),
                'comment' => $request->json('comment'),
                'created_by' => $userKey]);
            return response()->json($postabuse, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Post Abuse'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/postabuse/{id}",
     *  summary="Update a Post Abuse",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostAbuse"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Abuse Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postAbuse")
     *  ),
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Abuse Id",
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
     *      description="The updated Post Abuse",
     *      @SWG\Schema(ref="#/definitions/postAbuseResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Post Abuse not Found",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Post Abuse",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the Post Abuse in storage.
     * Returns the details of the updated Post Abuse.
     *
     * @param Request $request
     * @param type $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userKey = (empty($request->header('X-AUTH-TOKEN'))) ? ONE::verifyToken($request) : "unknown";

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $postabuse = PostAbuse::findOrFail($id);

            $postabuse->post_key = $request->json('post_key');
            $postabuse->type_id = $request->json('type_id');
            $postabuse->comment = $request->json('comment');
            $postabuse->processed = $request->json('processed');

            $postabuse->save();

            return response()->json($postabuse, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update an Post Abuse'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Abuse not found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeletePostAbuse",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/postabuse/{id}",
     *  summary="Delete a Post Abuse",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostAbuse"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Abuse Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeletePostAbuse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post Abuse not Found",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Post Abuse",
     *      @SWG\Schema(ref="#/definitions/postAbuseErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified Post Abuse from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);

        try {
            $postabuse = PostAbuse::findOrFail($id);

            $postabuse->delete();
            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Post Abuse'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Abuse not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Post Abuses by CB.
     * Returns the list of Post Abuses by CB.
     *
     * @param Request $request
     *
     * @param Request $request
     * @param Integer $cbId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByCb(Request $request, $cbId)
    {
        try {
            $topicIds = [];
            $postIds = [];
            $topics = Topic::where('cb_id', "=", $cbId)->get();
            foreach ($topics as $topic) {
                $topicIds[] = $topic->id;
            }
            $posts = Post::whereIn('topic_id', $topicIds)->get();
            foreach ($posts as $post) {
                $postIds[] = $post->id;
            }
            $postabuses = PostAbuse::whereIn('post_id', $postIds)->get();
            return response()->json(['postabuses' => $postabuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Post Abuses by Topic.
     * Returns the list of Post Abuses by Topic.
     *
     * @param Request $request
     * @param Integer $topicId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByTopic(Request $request, $topicId)
    {
        try {
            $posts = Post::where('topic_id', "=", $topicId)->get();
            foreach ($posts as $post) {
                $postIds[] = $post->id;
            }
            $postabuses = PostAbuse::whereIn('post_id', $postIds)->get();
            return response()->json(['postabuses' => $postabuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
