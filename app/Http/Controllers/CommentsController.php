<?php

namespace App\Http\Controllers;

use App\Comment;
use App\One\One;
use App\Status;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class CommentsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Comments",
 *   description="Everything about Comments",
 * )
 *
 *  @SWG\Definition(
 *      definition="commentErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *
 * @SWG\Definition(
 *   definition="commentsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"comment_key", "title", "content"},
 *           @SWG\Property(property="comment_key", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="comments",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"public", "title", "content", "created_by"},
 *           @SWG\Property(property="public", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="commentsUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"public", "title", "content"},
 *           @SWG\Property(property="public", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="content", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */

class CommentsController extends Controller
{
    protected $required = [
        'store' => [
            'status_key',
            'public',
            'title',
            'content'
        ],
        'update' => [
            'public',
            'title',
            'content'
        ]
    ];

    public function index(Request $request)
    {
        try {
            $comments = Comment::all();

            foreach ($comments as $comment) {
                $comment->timezone($request);
            }

            return response()->json(['data' => $comments], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Comments']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/comments/{commentKey}",
     *  summary="Show a Comment",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Comments"},
     *
     * @SWG\Parameter(
     *      name="commentKey",
     *      in="path",
     *      description="Comment Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *     @SWG\Parameter(
     *      name="timezone",
     *      in="header",
     *      description="Timezone",
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
     *      description="Show the Comment data",
     *      @SWG\Schema(ref="#/definitions/commentsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Comment not Found",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Comment",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  )
     * )
     *
     */
    /**
     * @param Request $request
     * @param $commentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $commentKey)
    {

        try {
            $comment = Comment::whereCommentKey($commentKey)->firstOrFail()->timezone($request);

            return response()->json($comment, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Comment'], 500);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/comments",
     *  summary="Creation of a Comment",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Comments"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Comment data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/comments")
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
     *      description="the newly created user",
     *      @SWG\Schema(ref="#/definitions/commentsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Comment",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
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
            $status = Status::whereStatusKey('status_key')->firstOrFail();

            do {
                $rand = str_random(32);
                if (!($exists = Comment::whereCommentKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $comment = $status->comments()->create(
                [
                    'comment_key'   => $key,
                    'public'        => $request->json('public'),
                    'title'         => $request->json('title'),
                    'content'       => $request->json('content'),
                    'created_by'    => $userKey
                ]
            );

            return response()->json($comment, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status not found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Comment'], 500);
        }
    }

    /**
     *
     * @SWG\Put(
     *  path="/comments/{commentKey}",
     *  summary="Update a Comment",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Comments"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Comment Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/commentsUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="commentKey",
     *      in="path",
     *      description="Comment Key",
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
     *      description="The updated Comment",
     *      @SWG\Schema(ref="#/definitions/commentsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Comment not Found",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Comment",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  )
     * )
     *
     */
    /**
     * @param Request $request
     * @param $commentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $commentKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $comment = Comment::whereCommentKey($commentKey)->firstOrFail();

            $comment->public    = $request->json('public');
            $comment->title     = $request->json('title');
            $comment->content   = $request->json('content');
            $comment->save();

            return response()->json($comment, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update the Comment'], 500);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteComment",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/comments/{commentKey}",
     *  summary="Delete a Comment",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Comments"},
     *
     * @SWG\Parameter(
     *      name="commentKey",
     *      in="path",
     *      description="Comment Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteComment")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Comment not Found",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Comment",
     *      @SWG\Schema(ref="#/definitions/commentErrorDefault")
     *  )
     * )
     *
     */
    /**
     * @param Request $request
     * @param $commentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $commentKey)
    {
        ONE::verifyToken($request);

        try {
            $comment = Comment::whereCommentKey($commentKey)->firstOrFail();
            $comment->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Comment'], 500);
        }
    }
}
