<?php

namespace App\Http\Controllers;

use App\Cb;
use App\ComModules\Auth;
use App\Flag;
use App\Post;
use App\PostCommentType;
use App\PostLike;
use App\PostAbuse;
use App\One\One;
use App\One\OneCb;
use App\Topic;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use HttpClient;
use Symfony\Component\Translation\Dumper\IniFileDumper;

/**
 * Class PostsController
 * @package App\Http\Controllers
 */
/**
 * @SWG\Tag(
 *   name="Post",
 *   description="Everything about Posts",
 * )
 *
 *  @SWG\Definition(
 *      definition="postErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="post",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_key", "contents"},
 *           @SWG\Property(property="topic_key", format="string", type="string"),
 *           @SWG\Property(property="type_code", format="string", type="string"),
 *           @SWG\Property(property="parent_id", format="string", type="integer"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="enabled", format="string", type="integer"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="post_comment_type_id", format="string", type="integer"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="postResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"post_key", "version", "parent_id", "topic_id", "post_comment_type_id", "enabled", "blocked", "active", "created_by", "contents", "status_id", "created_at", "updated_at"},
 *           @SWG\Property(property="post_key", format="string", type="string"),
 *           @SWG\Property(property="version", format="string", type="integer"),
 *           @SWG\Property(property="parent_id", format="string", type="integer"),
 *           @SWG\Property(property="topic_id", format="string", type="integer"),
 *           @SWG\Property(property="post_comment_type_id", format="string", type="integer"),
 *           @SWG\Property(property="enabled", format="string", type="integer"),
 *           @SWG\Property(property="blocked", format="string", type="integer"),
 *           @SWG\Property(property="active", format="string", type="integer"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="contents", format="string", type="string"),
 *           @SWG\Property(property="status_id", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */
class PostsController extends Controller
{

    protected $required = [
        'store' => ['contents', 'topic_key'],
        'update' => ['contents', 'topic_key'],
        'addFile' => ['file_id', 'file_code', 'name', 'description', 'type_id'],
        'updateFile' => ['name', 'description'],
        'orderFile' => ['movement']
    ];


    /**
     * Requests a list of Posts.
     * Returns the list of Posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $posts = Post::with(["topic.cb","abuses"])->get();
            return response()->json(['data' => $posts], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Requests a list of Posts that need some admin iteration (Post that needs approval + Posts with an abuse report  ).
     * Returns the list of Posts.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postManagerList(Request $request)
    {
        try {
            $tableData = $request->json('tableData') ?? null;
            $cbKeys = $request->input('cbKeys');
            $showWithFlags = $request->input("showWithFlags");
            $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");

            $firstPostKeys = Topic::with("firstPost")
                ->whereHas("cb",function($q) use ($cbKeys) {
                    $q->whereIn("cb_key",$cbKeys);
                })->get()->pluck("firstPost.post_key");

            $posts = Post::with([
                "topic.cb.configurations",
                "abuses"
            ])
                ->whereHas("topic.cb",function($q) use ($cbKeys){
                    $q->whereIn("cb_key",$cbKeys);
                })
                ->whereNotIn("post_key",$firstPostKeys)
                ->withCount("abuses")
                ->whereEnabled("1");

            /* Filter per Flag if requested */
            if ($showWithFlags!=false) {
                $posts = $posts->whereHas("flags",function($q) use ($showWithFlags) {
                    $q->where("flag_id","=",$showWithFlags)->where("active","=",true);
                });
            }

            /* Filter Posts with abuses if requested */
            if ($request->input("showWithAbuses")==1)
                $posts = $posts->whereHas("abuses");

            if ($showCommentsNeedsAuth!=0)
                $posts = $posts->whereActive("0")->whereBlocked("0");

            $recordsTotal = $posts->count();
            /* Use datatable data if present */
            if (!empty($tableData)) {
                $posts = $posts
                    ->where(function ($q) use ($tableData) {
                        $q
                            ->where("contents", "like", "%" . $tableData["search"]["value"] . "%")
                            ->orWhereHas("topic", function ($q) use ($tableData) {
                                $q->where("title", "like", "%" . $tableData["search"]["value"] . "%");
                            });
                    })
                    ->orderBy($tableData['order']['value'], $tableData['order']['dir'])
                    ->skip($tableData['start'])
                    ->take($tableData['length']);
            }

            $posts = $posts->get();
            $cbNeedsAuth = [];

            foreach ($posts as $post) {
                if (!array_key_exists($post->topic->cb->cb_key, $cbNeedsAuth)) {
                    $cbConfigurations = $post->topic->cb->configurations->pluck("code")->toArray();
                    $cbNeedsAuth[$post->topic->cb_key] = OneCb::checkCBsOption( $cbConfigurations, 'COMMENT-NEEDS-AUTHORIZATION');
                    unset($cbConfigurations);
                }

                $post->cb = $post->topic->cb;
                $post->commentNeedsAuth = $cbNeedsAuth[$post->topic->cb_key];
                unset($post->topic->cb);
                unset($post->cb->configurations);
            }

            return response()->json(['data' => $posts, 'recordsTotal' => $recordsTotal]);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function postManagerListLastly(Request $request)
    {
        try {
            // All requested CBs (topics and configurations)
            $cbs = Cb::with(["topics","configurations"])->whereIn("cb_key",$request->input('cbKeys'))->get();

            $showWithAbuses = $request->input("showWithAbuses");
            $showCommentsNeedsAuth = $request->input("showCommentsNeedsAuth");


            $data = [];
            foreach($cbs as $cb){

                // Getting CB configurations
                $cbConfigurations = [];
                foreach($cb->configurations as $configuration){
                    $cbConfigurations[] = $configuration->code;
                }

                // Topics
                foreach($cb->topics as $topic){

                    // Getting First Post
                    $firstPost = Post::whereTopicId($topic->id)
                        ->orderBy('id', 'asc')
                        ->first();

                    // Posts
                    $posts = Topic::findOrFail($topic->id)->posts()
                        ->with("abuses")
                        /*
                        ->whereBlocked(0)->whereActive(0)
                        */
                        ->where('post_key','!=',$firstPost->post_key)
                        ->orderby('created_at', 'desc')
                        ->take(15)
                        ->get();


                    foreach($posts as $post){

                        $post["cb"] = $cb;
                        $post["topic"] = $topic;
                        $data[] = $post;


                    }
                }
            }

            return response()->json(['data' => $data], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function listThatNeedsApproval(Request $request)
    {

        try {
            try{
                $cbs = Cb::with(["topics","configurations"])->whereIn("cb_key",$request->input('cb_keys'))->get();
            }catch(Exception $e){
                return response()->json(['data' => []], 200);
            }

            $data = [];
            foreach($cbs as $cb){

                // Getting CB configurations
                $cbConfigurations = [];
                foreach($cb->configurations as $configuration){
                    $cbConfigurations[] = $configuration->code;
                }

                // Topics
                foreach($cb->topics as $topic){

                    // Getting First Post
                    try{
                        $firstPost = Post::whereTopicId($topic->id)
                            ->orderBy('id', 'asc')
                            ->first();
                    }catch(Exception $e){
                        return response()->json(['data' => []], 200);
                    }

                    if(!empty($firstPost)){

                        try{
                            // Posts
                            $posts = Topic::find($topic->id)->posts()
                                ->whereActive(0)->where('post_key','!=',$firstPost->post_key)->whereBlocked(0)
                                ->orderby('created_at', 'desc')
                                ->take(10)
                                ->get();
                        }catch(Exception $e){
                            return response()->json(['data' => []], 200);
                        }

                        if(!empty($posts)){
                            foreach($posts as $post){
                                $commentsNeedsAuth = OneCb::checkCBsOption( $cbConfigurations, 'COMMENT-NEEDS-AUTHORIZATION');
                                if($commentsNeedsAuth == 1){
                                    $post["cb"] = $cb;
                                    $post["topic"] = $topic;
                                    $post["commentsNeedsAuth"] = $commentsNeedsAuth;
                                    $data[] = $post;
                                }

                            }
                        }else
                            return response()->json(['data' => $data], 200);
                    }else
                        return response()->json(['data' => $data], 200);
                }
            }

            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/post",
     *  summary="Creation of a Post",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Post"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/post")
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
     *      description="the newly created post",
     *      @SWG\Schema(ref="#/definitions/postResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Post",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created Post in storage.
     * Returns the details of the newly created Post.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyLogin($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $contents = $request->json('contents') ?? null;
            if ($contents && (strlen($contents) > 0 && strlen(trim($contents)) > 0)) {

                $topic = Topic::whereTopicKey($request->json('topic_key'))->firstOrFail();

                // Configurations
                $configurations = $topic->cb->configurations()->select('code')->pluck('code');

                /* Verify if user can vote */
                if (OneCb::checkCBsOption($configurations->toArray(),"ALLOW-COMMENTS") && (!empty($userKey) || OneCb::checkCBsOption($configurations->toArray(), 'COMMENTS-ANONYMOUS'))) {
                    $commentType = PostCommentType::whereCode($request->json('type_code'))->first();

                    $key = '';
                    do {
                        $rand = str_random(32);
                        if (!($exists = Post::wherePostKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);


                    $active = 1;
                    if (OneCb::checkCBsOption($configurations->toArray(), 'COMMENT-NEEDS-AUTHORIZATION'))
                        $active = 0;

                    $post = $topic->posts()->create(
                        [
                            'post_key' => $key,
                            'parent_id' => clean($request->json('parent_id')),
                            'created_by' => is_null($userKey) ? 'anonymous' : $userKey,
                            'active' => $active,
                            'enabled' => 1,
                            'contents' => clean($request->json('contents')),
                            'post_comment_type_id' => $commentType ? $commentType->id : 0
                        ]
                    );

//                Notify Followers - BEGIN
//                if (OneCb::checkCBsOption($configurations->toArray(), 'NOTIFICATION-NEW-COMMENTS')){
//                    $tags = [
//                        'topic'     => $topic->title,
//                        'contents'  => $request->json('contents'),
//                        'link'      => $request->json('link'),
//                    ];
//                    $response = One::notifyFollowers($request, $tags, 'notification_new_comments');
//                }
//                Notify Followers - END

                    return response()->json($post, 201);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Post'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/post/{postKey}",
     *  summary="Show a Post",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Post"},
     *
     * @SWG\Parameter(
     *      name="postKey",
     *      in="path",
     *      description="Post Key",
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
     *      description="Show the Post data",
     *      @SWG\Schema(ref="#/definitions/postResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Site",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific Post.
     * Returns the details of a specific Post.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $postKey)
    {
        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();
            return response()->json(['post' => $post], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/post/{postKey}",
     *  summary="Update a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Post"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Post Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/post")
     *  ),
     *
     * @SWG\Parameter(
     *      name="postKey",
     *      in="path",
     *      description="Post Key",
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
     *      description="The updated Post",
     *      @SWG\Schema(ref="#/definitions/postResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Post not Found",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Post",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the Post in storage.
     * Returns the details of the updated Post.
     *
     * @param Request $request
     * @param $post_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function update(Request $request, $post_key)
    {

        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            // Disabled other posts
            $otherPosts = Post::wherePostKey($post_key)->where('enabled','=',1)->update(array("enabled" => 0));

            $post = Post::wherePostKey($post_key)->orderBy('version', 'desc')->firstOrFail();
            $version = $post->version;
            $topic = Topic::whereTopicKey($request->json('topic_key'))->firstOrFail();

            $newPost = $topic->posts()->create(
                [
                    'post_key' => $post->post_key,
                    'parent_id' => $post->parent_id,
                    'version' => ++$version,
                    'enabled' => 1,
                    'created_by' => $post->created_by,
                    'updated_by' => $userKey,
                    'status_id' => clean($request->json('status_id')) ?: $post->status_id,
                    'contents' => clean($request->json('contents')),
                    'post_comment_type_id' => $post->post_comment_type_id,
                ]
            );

            $newPost->created_at = $post->created_at;
            $newPost->save();

            return response()->json($newPost, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Post'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeletePost",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/post/{postKey}",
     *  summary="Delete a Post",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Post"},
     *
     * @SWG\Parameter(
     *      name="postKey",
     *      in="path",
     *      description="Post Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeletePost")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Post not Found",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Post",
     *      @SWG\Schema(ref="#/definitions/postErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified Post from storage.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $postKey)
    {
        ONE::verifyToken($request);

        try {
            $posts = Post::wherePostKey($postKey)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Post'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the status of a Post in storage.
     * Returns the details of the updated Post.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $postKey)
    {
        ONE::verifyToken($request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();
            $post->status_id = clean($request->json('status_id'));
            $post->save();
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Post'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Post Abuses by Topic.
     * Returns the list of Post Abuses by Topic.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAbuses(Request $request, $postKey)
    {
        ONE::verifyToken($request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            $postabuses = PostAbuse::wherePostId($post->id)->get();
            return response()->json(['postabuses' => $postabuses], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Abuse list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function postHistory(Request $request, $postKey)
    {
        try {
            $posts = Post::wherePostKey($postKey)->orderBy('version')->get();
            return response()->json(['data' => $posts], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post history'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param null $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function files(Request $request, $postKey, $typeId = null)
    {

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            if (!is_null($typeId))
                $files = $post->files()->whereTypeId($typeId)->orderBy('position')->get();
            else
                $files = $post->files;

            return response()->json(['data' => $files], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Files list'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFile(Request $request, $postKey, $fileId)
    {
        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            try {
                $file = $post->files()->whereFileId($fileId)->firstOrFail();

                return response()->json($file, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'File not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Post File'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param null $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function filesByType(Request $request, $postKey, $typeId = null)
    {
        $filesData = [];

        $fileTypes = [];
        $fileTypes["images"] = array("gif","jpg","png","bmp");
        $fileTypes["videos"] = array("avi","mpg","mp4","avi","asf","qt","flv","swf","wmv","webm","vob","ogv","ogg","mpeg","3gp");
        $fileTypes["docs"]   = array("pdf","doc","docx","rtf");

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            if (!is_null($typeId))
                $files = $post->files()->whereTypeId($typeId)->orderBy('position')->get();
            else
                $files = $post->files;


            foreach($files as $file){
                $array = explode('.',$file->name);
                $extension = strtolower(end($array));

                foreach($fileTypes as $key => $value) {
                    if (in_array($extension,$value)) {
                        $filesData[$key][] = $file;
                    }
                }
            }

            return response()->json(['data' => $filesData], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Post Files list'], 500);
        }
    }

    public function addFile(Request $request, $postKey)
    {
//        with this security is impossible for an anonymous user to upload files when creating a public topic
//        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['addFile'], $request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            $last = $post->files()->whereTypeId($request->json('type_id'))->orderBy('position', 'desc')->first();
            $position = $last ? $last->position + 1 : 0;

            $post->files()->create(
                [
                    'file_id' => clean($request->json('file_id')),
                    'file_code' => clean($request->json('file_code')),
                    'name' => clean($request->json('name')),
                    'description' => clean($request->json('description')),
                    'position' => $position,
                    'type_id' => clean($request->json('type_id'))
                ]
            );

            return response()->json('OK', 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add File to Post'], 500);
        }
    }


    public function addFiles(Request $request, $postKey)
    {
//        with this security is impossible for an anonymous user to upload files when creating a public topic
//        ONE::verifyToken($request);

        // ONE::verifyKeysRequest($this->required['addFile'], $request);

        try {

            foreach($request->json('files') as $file){
                $post = Post::wherePostKey($postKey)->firstOrFail();

                $last = $post->files()->whereTypeId($request->json('type_id'))->orderBy('position', 'desc')->first();
                $position = $last ? $last->position + 1 : 0;

                $post->files()->create(
                    [
                        'file_id' => $file['file_id'],
                        'file_code' => $file['file_code'],
                        'name' =>  $file['name'],
                        'description' => $file['description'],
                        'position' => $position,
                        'type_id' => $file['type_id']
                    ]
                );
            }
            return response()->json('OK', 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add File to Post'], 500);
        }
    }




    public function updateFiles(Request $request, $postKey)
    {
//        with this security is impossible for an anonymous user to upload files when creating a public topic
//        ONE::verifyToken($request);

        // ONE::verifyKeysRequest($this->required['addFile'], $request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();
            $post->files()->delete();

            foreach($request->json('files') as $file){
                $last = $post->files()->whereTypeId($request->json('type_id'))->orderBy('position', 'desc')->first();
                $position = $last ? $last->position + 1 : 0;
                $post->files()->create(
                    [
                        'file_id' => $file['file_id'],
                        'file_code' => $file['file_code'],
                        'name' =>  $file['name'],
                        'description' => $file['description'],
                        'position' => $position,
                        'type_id' => $file['type_id']
                    ]
                );
            }
            return response()->json('OK', 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add File to Post'], 500);
        }
    }


    /**
     * @param Request $request
     * @param $postKey
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFile(Request $request, $postKey, $fileId)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['updateFile'], $request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            try {
                $file = $post->files()->whereFileId($fileId)->firstOrFail();
                $file->name = clean($request->json('name'));
                $file->description = clean($request->json('description'));
                $file->save();

                return response()->json('OK', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'File not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Post File details'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request, $postKey, $fileId)
    {
        ONE::verifyToken($request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            try {
                $post->files()->whereFileId($fileId)->delete();

                return response()->json('OK', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'File not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Post File'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderFile(Request $request, $postKey, $fileId)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['orderFile'], $request);

        try {
            $post = Post::wherePostKey($postKey)->firstOrFail();

            try {
                $file = $post->files()->whereFileId($fileId)->firstOrFail();
                $files = $post->files()->whereTypeId($file->type_id)->orderBy('position')->pluck('file_id', 'position');

                if ($request->json('movement') > 0 && $file->position <= (sizeof($files) - 1 - $request->json('movement'))) {
                    for ($i = $file->position + 1; $i <= $file->position + $request->json('movement'); $i++) {
                        $tmp = $post->files()->whereFileId($files[$i])->firstOrFail();
                        $tmp->position -= 1;
                        $tmp->save();
                    }
                    $file->position += $request->json('movement');
                    $file->save();

                } elseif ($request->json('movement') < 0 && $file->position >= -$request->json('movement')) {
                    for ($i = $file->position - 1; $i >= $file->position + $request->json('movement'); $i--) {
                        $tmp = $post->files()->whereFileId($files[$i])->firstOrFail();
                        $tmp->position += 1;
                        $tmp->save();
                    }
                    $file->position += $request->json('movement');
                    $file->save();
                }

                return response()->json('OK', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'File not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Post File position'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postTimeline(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $postsDB = Post::withTrashed()->whereCreatedBy($userKey)->groupBy("topic_id")->get();
            $posts = [];
            foreach ($postsDB as $post) {
                $topic = Topic::whereId($post->topic_id)->get();
                if ($topic->count()==1) {
                    $topic = $topic->first();
                    if ($topic->cb()->count()==1) {
                        $cbKey = $topic->cb()->first()->cb_key;
                        $topicPosts = $topic->posts()->whereCreatedBy($userKey)->get();
                        $iteration = 0;
                        foreach ($topicPosts as $topicPost) {
                            $aditionalData = [
                                "topic_key" => $topic->topic_key,
                                "cb_key"    => $cbKey,
                                "topic_title" => $topic->title ?? null,
                                "edition"   => ($iteration>0) ?? false
                            ];
                            $posts[] = array_merge($topicPost->toArray(), $aditionalData, ["order_date" => $post->created_at]);
                            if (!is_null($topicPost->deleted_at))
                                $posts[] = array_merge($topicPost->toArray(), $aditionalData, ["order_date" => $post->deleted_at]);

                            $iteration++;
                        }
                    }
                }
            }
            $posts = collect($posts)->sortByDesc("order_date");

            return response()->json(['data' => $posts], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to show the Vote','e'=>$e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $postKey
     * @param $version
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertPost(Request $request, $postKey, $version)
    {
        ONE::verifyToken($request);
        try {
            $lastVersion = Post::wherePostKey($postKey)->orderBy('version', 'desc')->first()->version;
            Post::wherePostKey($postKey)->update(['enabled' => 0]);

            $post = Post::wherePostKey($postKey)->whereVersion($version)->firstOrFail();

            $newPost = $post->replicate();
            $newPost->enabled = 1;
            $newPost->version = $lastVersion+1;
            $newPost->save();

            return response()->json(['post' => $newPost], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to revert the Post'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function acceptPost(Request $request, $postKey, $version)
    {
        ONE::verifyToken($request);
        try {
            if(Post::wherePostKey($postKey)->whereVersion($version)->exists()){
                if(Post::wherePostKey($postKey)->whereEnabled('1')->firstOrFail()->version < $version){
                    Post::wherePostKey($postKey)->update(['enabled' => 0]);
                    Post::wherePostKey($postKey)->whereVersion($version)->update(['enabled' => '1']);
                }
            }

            $post = Post::wherePostKey($postKey)->whereVersion($version)->firstOrFail();

            return response()->json(['post' => $post], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to accept the Post'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Block a Post.
     * Returns the details of the updated Post.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBlocked(Request $request, $postKey)
    {
        ONE::verifyToken($request);

        try {
            Post::wherePostKey($postKey)->update(['blocked' => 1]);
            $post = Post::wherePostKey($postKey)->firstOrFail();
            $post->blocked = clean($request->json('blocked'));
            $post->active = 0;
            $post->save();
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to block Post'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Store active status.
     * Returns the details of the updated Post.
     *
     * @param Request $request
     * @param $postKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeActive(Request $request, $postKey)
    {
        ONE::verifyToken($request);
        try {
            Post::wherePostKey($postKey)->update(['active' => 0]);

            $post = Post::wherePostKey($postKey)->orderBy('version', 'desc')->firstOrFail();
            $post->active = clean($request->json('active'));
            $post->blocked = 0;
            $post->save();
            return response()->json($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store Post active status'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function getTopicsFiles(Request $request)
    {

        $fileTypes = [];
        $fileTypes["images"] = array("gif","jpg","png","bmp");
        $fileTypes["videos"] = array("avi","mpg","mp4","avi","asf","qt","flv","swf","wmv","webm","vob","ogv","ogg","mpeg","3gp");
        $fileTypes["docs"]   = array("pdf","doc","docx","rtf");
        $filesByType = [];
        try {
            if(isset($request->topics)){
                foreach ($request->topics as $topic){
                    if(isset($topic['first_post']['post_key'])) {
                        $post = Post::wherePostKey($topic['first_post']['post_key'])->firstOrFail();
                        $files = $post->files;
                        $filesData = [];

                        foreach ($files as $file) {
                            $array = explode('.', $file->name);
                            $extension = strtolower(end($array));

                            foreach ($fileTypes as $key => $value) {
                                if (in_array($extension, $value)) {
                                    $filesData[$key][] = $file;
                                }
                            }
                        }
                        $filesByType[$topic['topic_key']] = $filesData;
                    }
                }
            }
            return response()->json(['data' => $filesByType], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Post files'], 400);

        }
    }
}