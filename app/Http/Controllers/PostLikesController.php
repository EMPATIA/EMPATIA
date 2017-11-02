<?php

namespace App\Http\Controllers;

use App\PostLike;
use App\One\One;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class PostLikesController
 * @package App\Http\Controllers
 */
/**
 * @SWG\Tag(
 *   name="PostLike",
 *   description="Everything about Post Likes",
 * )
 *
 *  @SWG\Definition(
 *      definition="postLikeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="postLikeResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_id", "like", "created_at", "updated_at"},
 *           @SWG\Property(property="post_id", format="string", type="integer"),
 *           @SWG\Property(property="like", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="postLike",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_id", "like"},
 *           @SWG\Property(property="post_id", format="string", type="integer"),
 *           @SWG\Property(property="like", format="string", type="string")
 *       )
 *   }
 * )
 *
 */
class PostLikesController extends Controller
{
    
    protected $keysRequired = [
        'like'
    ];  
    
    /**
     * Requests a list of Post Likes.
     * Returns the list of Post Likes.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $postlikes = PostLike::all();
            return response()->json(['postlikes' => $postlikes], 200);  
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Likes list'], 500);
        }            
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }

    /**
     *
     * @SWG\Get(
     *  path="/postlike/{id}",
     *  summary="Show a Post Like",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostLike"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Like Id",
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
     *      description="Show the Post Like data",
     *      @SWG\Schema(ref="#/definitions/postLikeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post Like not Found",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Post Like",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific Post Like.
     * Returns the details of a specific Post Like.
     * 
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $postlike = PostLike::findOrFail($id);
            return response()->json(['postlike' => $postlike], 200);                    
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Like not Found'], 404);
        }
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }

    /**
     *
     * @SWG\Post(
     *  path="/postlike",
     *  summary="Creation of a Post Like",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostLike"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Like data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postLike")
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
     *      description="the newly created post like",
     *      @SWG\Schema(ref="#/definitions/postLikeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Post Like",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created Post Like in storage. 
     * Returns the details of the newly created Post Like.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);                

        try {
            $postlike = PostLike::create(['post_id' => $request->json('post_id'), 
                                          'like' => $request->json('like'),
                                          'created_by' => $userKey]);
            return response()->json($postlike, 201);             
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Post Like'], 500);
        }   
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }

    /**
     *
     * @SWG\Put(
     *  path="/postlike/{id}",
     *  summary="Update a Post Like",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostLike"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Like Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/postLike")
     *  ),
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Like Id",
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
     *      description="The updated Post Like",
     *      @SWG\Schema(ref="#/definitions/postLikeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Post Like not Found",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Post Like",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the Post Like in storage.
     * Returns the details of the updated Post Like.
     * 
     * @param Request $request
     * @param type $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);                        

        try {        
            $postlike = PostLike::findOrFail($id);

            $postlike->post_id = $request->json('post_id');        
            $postlike->like = $request->json('like');

            $postlike->save();

            return response()->json($postlike, 200);            
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update an Post Like'], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Like not found'], 404);
        }     
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeletePostLike",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/postlike/{id}",
     *  summary="Delete a Post Like",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PostLike"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Post Like Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeletePostLike")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post Like not Found",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Post Like",
     *      @SWG\Schema(ref="#/definitions/postLikeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified Post Like from storage.
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);

        try {          
            $postlike = PostLike::findOrFail($id);
            $postlike->delete();
            return response()->json('OK', 200);
        } 
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Post Like'], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not Found'], 404);
        }   
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }
    
    /**
     * Returns the details of a specif Post Like.
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request, $post_id)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $countliked = PostLike::where("post_id" , "=" , $post_id)->where("like", "=", 1)->count();
            $countdisliked = PostLike::where("post_id" , "=" , $post_id)->where("like","=", 0)->count();
            $postlike = PostLike::where("post_id" , "=" , $post_id)->where("created_by","=",$userKey);
            return response()->json(['postlike' => $postlike , 'liked' => $countliked, 'disliked' => $countdisliked ], 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post Like not Found'], 404);
        }    
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }
    
    /**
     * Function to make Like. 
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(Request $request)
    {
        $userKey = ONE::verifyToken($request);    
        return PostLikesController::storeLike($request->json('post_id') ,1,$userKey);       
    }   
    
    /**
     * Function that make a Dislike to a Post
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dislike(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        return PostLikesController::storeLike($request->json('post_id') ,0, $userKey);
    }       
    
    /**
     * Function used to store a Like or a DisLike in storage.
     * 
     * @param type $post_id
     * @param type $like
     * @param type $userKey
     * @param type $postlike_id
     * @return \Illuminate\Http\JsonResponse
     */ 
    private static function storeLike($post_id,$like,$userKey){
        try {
            $postlike = PostLike::where("post_id", "=" , $post_id)->where("created_by", "=" , $userKey)->get()->first();
            
            if( $postlike === null ){
                $postlike = PostLike::create(['post_id' => $post_id, 
                                              'like' => $like,
                                              'created_by' => $userKey]);
                return response()->json($postlike, 201);             
            } else {
                $postlike = PostLike::findOrFail($postlike->id);

                $postlike->post_id = $post_id;        
                $postlike->like = $like;

                $postlike->save();
                return response()->json($postlike, 200);             
            }
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to Like a Post'], 500);
        }   
        
        return response()->json(['error' => 'Unauthorized' ], 401);                        
    }
    
}
