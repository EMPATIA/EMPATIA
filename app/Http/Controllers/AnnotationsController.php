<?php

namespace App\Http\Controllers;

use App\Annotation;
use App\AnnotationType;
use App\Cooperator;
use App\One\One;
use App\Post;
use App\Topic;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class AnnotationsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Annotations",
 *   description="Everything about Annotations",
 * )
 *
 *  @SWG\Definition(
 *      definition="annotationsErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="annotations",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"text", "quote"},
 *			 @SWG\Property(property="text", format="string", type="string"),
 *           @SWG\Property(property="quote", format="string", type="string"),
 *           @SWG\Property(property="ranges", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="start", type="string"),
 *                          @SWG\Property(property="end", type="string"),
 *                          @SWG\Property(property="start_offset", type="string"),
 *                          @SWG\Property(property="end_offset", type="string")
 *
 *					    )
 *          ),
 *          @SWG\Property(property="tags", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="code", type="string"),
 *
 *					    )
 *          )
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="annotationsUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"text"},
 *			 @SWG\Property(property="text", format="string", type="string"),
 *           @SWG\Property(property="tags", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="code", type="string"),
 *
 *					    )
 *          )
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="annotationsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"annotation_key"},
 *           @SWG\Property(property="annotation_key", format="string", type="string"),
 *           @SWG\Property(property="text", format="string", type="string"),
 *           @SWG\Property(property="quote", format="string", type="string"),
 *
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="annotationsRanges",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *			 @SWG\Property(property="ranges", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="start", type="string"),
 *                          @SWG\Property(property="end", type="string"),
 *                          @SWG\Property(property="start_offset", type="string"),
 *                          @SWG\Property(property="end_offset", type="string")
 *
 *					    )
 *          )
 *       )
 *   }
 * )
 *
 */

class AnnotationsController extends Controller
{
    protected $required = [
        'store' => ['topic_key', 'text', 'quote', 'ranges'],
        'update' => ['text'],
    ];

    /**
     *
     * @SWG\Get(
     *  path="/annotation/{key}",
     *  summary="Show Annotations",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Annotations"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Topic Key",
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
     *      description="Show Annotations data",
     *      @SWG\Schema(ref="#/definitions/annotationsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Annotations not Found",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Annotations",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicKey)
    {
        try {
            $post = Post::whereTopicId(Topic::whereTopicKey($topicKey)->firstOrFail()->id)
                ->whereEnabled(1)
                ->orderBy('created_at', 'asc')
                ->first();

            $annotations = $post->annotations()
                ->with('ranges')
                ->with('annotationTypes')
                ->with('cooperator')
                ->get();

            foreach ($annotations as $annotation) {
                foreach ($annotation->annotationTypes as $tag) {
                    if (!($tag->translation($request))) {
                        return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }

            return response()->json(['data' => $annotations], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Annotations'], 500);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/annotation",
     *  summary="Creation of an Annotation",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Annotations"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Annotation data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/annotations")
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
     *      description="the newly created Annotation",
     *      @SWG\Schema(ref="#/definitions/annotationsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Annotation",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Stores a new Annotation returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["store"], $request);

        try {
            $post = Post::whereTopicId(Topic::whereTopicKey($request->json('topic_key'))->firstOrFail()->id)
                ->whereEnabled(1)
                ->orderBy('created_at', 'asc')
                ->first();

            $cooperator = Topic::whereTopicKey($request->json('topic_key'))
                ->firstOrFail()
                ->cooperators()
                ->whereUserKey($userKey)
                ->firstOrFail();

            do {
                $key = '';
                $rand = str_random(32);
                if (!($exists = Annotation::whereAnnotationKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $annotation = $post->annotations()->create(
                [
                    'annotation_key'    => $key,
                    'cooperator_id'     => $cooperator->id,
                    'post_id'           => $post->id,
                    'text'              => clean($request->json('text')),
                    'quote'             => clean($request->json('quote'))
                ]
            );

            foreach ($request->json('ranges') as $range) {
                $annotation->ranges()->create(
                    [
                        'start'         => clean($range['start']),
                        'end'           => clean($range['end']),
                        'start_offset'  => clean($range['startOffset']),
                        'end_offset'    => clean($range['endOffset']),
                    ]
                );
            }

            if(!empty($request->json('tags'))){
                $tags = [];
                foreach ($request->json('tags') as $tag) {
                    $annotationType = AnnotationType::whereCode($tag)->first();
                    if($annotationType){
                        $tags[] = $annotationType->id;
                    }
                }
                $annotation->annotationTypes()->sync($tags);
            }

            return response()->json($annotation, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store Annotation'], 500);
        }
    }

    /**
     *
     * @SWG\Put(
     *  path="/annotation/{key}",
     *  summary="Update an Annotation",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Annotations"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Annotation Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Annotation Update text",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/annotationsUpdate")
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
     *      description="The updated User",
     *      @SWG\Schema(ref="#/definitions/annotationsResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update User",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  )
     * )
     *
     */
    /**
     * @param Request $request
     * @param $annotationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $annotationKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["update"], $request);

        try {
            $annotation = Annotation::whereAnnotationKey($annotationKey)->firstOrFail();

            if ($userKey == Cooperator::findOrFail($annotation->cooperator_id)->user_key){

                $annotation->update(['text' => clean($request->json('text'))]);

                if(!empty($request->json('tags'))){
                    $tags = [];
                    foreach ($request->json('tags') as $tag) {
                        $annotationType = AnnotationType::whereCode($tag)->first();
                        if($annotationType){
                            $tags[] = $annotationType->id;
                        }
                    }
                    $annotation->annotationTypes()->sync($tags);
                }
                return response()->json($annotation, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Annotation not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Annotation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteAnnotation",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/annotation/{key}",
     *  summary="Delete an Annotation",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Annotations"},
     *
     * @SWG\Parameter(
     *      name="key",
     *      in="path",
     *      description="Annotation Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteAnnotation")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Annotation not Found",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Annotation",
     *      @SWG\Schema(ref="#/definitions/annotationsErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $annotationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $annotationKey)
    {
        ONE::verifyToken($request);

        try {
            $annotation = Annotation::whereAnnotationKey($annotationKey)->firstOrFail();
            $annotation->ranges()->delete();
            $annotation->annotationTypes()->detach();
            $annotation->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Annotation not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Annotation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTags(Request $request)
    {
        try {
            $tags = AnnotationType::all();

            foreach ($tags as $tag) {
                if (!($tag->translation($request))) {
                    return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(['data' => $tags], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Annotation Types not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Annotation Types'], 500);
        }
    }
}
