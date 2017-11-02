<?php

namespace App\Http\Controllers;

use App\PostCommentType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use One;

/**
 * Class PostCommentTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="PostCommentType",
 *   description="Everything about Post Comment Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="postCommentTypeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="postCommentType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="postCommentTypeResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_comment_type_key", "code", "created_at", "updated_at"},
 *           @SWG\Property(property="post_comment_type_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */

class PostCommentTypesController extends Controller
{
    protected $keysRequired = [
        'code'
    ];

    /**
     * Returns all Post Comment Types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $postCommentTypes = PostCommentType::all();
            return response()->json(['data' => $postCommentTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Post Comment Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/postCommentTypes/{postCommentTypeKey}",
     *  summary="Show a Post Comment Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostCommentType"},
     *
     * @SWG\Parameter(
     *      name="postCommentTypeKey",
     *      in="path",
     *      description="Post Comment Type Key",
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
     *      @SWG\Schema(ref="#/definitions/postCommentTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post Comment Type not Found",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Post Comment Type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Returns the specified Post Comment Type
     *
     * @param Request $request
     * @param $postCommentTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $postCommentTypeKey)
    {
        try {
            $postCommentType = PostCommentType::wherePostCommentTypeKey($postCommentTypeKey)->firstOrFail();
            return response()->json($postCommentType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Comment Type not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Comment Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/postCommentType",
     *  summary="Creation of a Post Comment Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostCommentType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Comment Type data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postCommentType")
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
     *      description="the newly created post comment type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Post Comment Type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Stores a new Post Comment Type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyLogin($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        do {
            $key = '';
            $rand = str_random(32);
            if (!($exists = PostCommentType::wherePostCommentTypeKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);

        try {
            $postCommentType = PostCommentType::create(
                [
                    'post_comment_type_key' => $key,
                    'code' => clean($request->json('code'))
                ]
            );
            return response()->json($postCommentType, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store the Post Comment Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/postCommentType/{postCommentTypeKey}",
     *  summary="Update a Post Comment Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostCommentType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Comment Type Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postCommentType")
     *  ),
     *
     * @SWG\Parameter(
     *      name="postCommentTypeKey",
     *      in="path",
     *      description="Post Comment Type Key",
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
     *      description="The updated Post Comment Type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Post Comment Type not Found",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Post Comment Type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates and returns the specified Post Comment Type
     *
     * @param Request $request
     * @param $postCommentTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $postCommentTypeKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $postCommentType = PostCommentType::wherePostCommentTypeKey($postCommentTypeKey)->firstOrFail();
            $postCommentType->code = clean($request->json('code'));
            $postCommentType->save();

            return response()->json($postCommentType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Comment Type not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Post Comment Type'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeletePostCommentType",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/postCommentType/{postCommentTypeKey}",
     *  summary="Delete a Post Comment Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostCommentType"},
     *
     * @SWG\Parameter(
     *      name="postCommentTypeKey",
     *      in="path",
     *      description="Post Comment Type Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeletePostCommentType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post Comment Type not Found",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Post Comment Type",
     *      @SWG\Schema(ref="#/definitions/postCommentTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Deletes the specified Post Comment Type
     *
     * @param Request $request
     * @param $postCommentTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $postCommentTypeKey)
    {
        ONE::verifyToken($request);

        try {
            PostCommentType::wherePostCommentTypeKey($postCommentTypeKey)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Comment Type not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Post Comment Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
