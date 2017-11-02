<?php

namespace App\Http\Controllers;

use App\One\One;
use App\TopicReviewStatusType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class TopicReviewStatusTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="TopicReviewStatusType",
 *   description="Everything about Topic Review Status Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="topicReviewStatusTypeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="topicReviewStatusType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "position", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="translations", type="array",
 *              @SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="language_code", type="string"),
 *                          @SWG\Property(property="name", type="string"),
 *                          @SWG\Property(property="description", type="string"),
 *				)
 *          )
 *      )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="topicReviewStatusTypeResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"topic_review_status_type_key", "code", "position", "created_at", "updated_at"},
 *           @SWG\Property(property="topic_review_status_type_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *              @SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="language_code", type="string"),
 *                          @SWG\Property(property="topic_review_status_type_id", type="integer"),
 *                          @SWG\Property(property="name", type="string"),
 *                          @SWG\Property(property="description", type="string"),
 *                          @SWG\Property(property="created_at", type="string"),
 *                          @SWG\Property(property="updated_at", type="string")
 *
 *					    )
 *            )
 *      )
 *   }
 * )
 *
 */

class TopicReviewStatusTypesController extends Controller
{
    protected $required = [
        'store' => [
            'translations',
            'code',
            'position'
        ],
        'update' => [
            'translations',
            'code',
            'position'
        ]
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if(!is_null($request->get('action'))){

                $topicReviewStatusTypes = TopicReviewStatusType::where('code', '!=', 'open')->get();
            }else
                $topicReviewStatusTypes = TopicReviewStatusType::all();

            foreach ($topicReviewStatusTypes as $topicReviewStatusType) {
                if (!($topicReviewStatusType->translation($request->header('LANG-CODE')))) {
                    if (!$topicReviewStatusType->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$topicReviewStatusType->translation('en'))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }

            return response()->json($topicReviewStatusTypes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Status Types']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/topicReviewStatusTypes/{topicReviewStatusTypeKey}",
     *  summary="Show a Topic Review Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewStatusType"},
     *
     * @SWG\Parameter(
     *      name="topicReviewStatusTypeKey",
     *      in="path",
     *      description="Topic Review Status Type Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG_CODE",
     *      in="header",
     *      description="Language Code",
     *      required=true,
     *      type="string"
     *  ),
     *
     *     @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Topic Review Status Type data",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review Status Type not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Topic Review Status Type",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $topicReviewStatusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicReviewStatusTypeKey)
    {
        try {
            $topicReviewStatusType = TopicReviewStatusType::whereTopicReviewStatusTypeKey($topicReviewStatusTypeKey)->first();

            if (!($topicReviewStatusType->translation($request->header('LANG-CODE')))) {
                if (!$topicReviewStatusType->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($topicReviewStatusType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Status Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $topicReviewStatusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $topicReviewStatusTypeKey)
    {
        try {
            $topicReviewStatusType = TopicReviewStatusType::whereTopicReviewStatusTypeKey($topicReviewStatusTypeKey);

            $topicReviewStatusType->translations();

            return response()->json($topicReviewStatusType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Status Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/topicReviewStatusTypes",
     *  summary="Creation of a Topic Review Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewStatusType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Topic Review Status Type data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusType")
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
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Topic Review Status Type",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
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
            $topicReviewStatusType = TopicReviewStatusType::create(
                [
                    'code'      => $request->json('code'),
                    'position'  => $request->json('position')
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $topicReviewStatusTypeTranslation = $topicReviewStatusType->statusTypeTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['title'],
                            'description'   => isset($translation['description']) ? $translation['description'] : null
                        ]
                    );
                }
            }

            return response()->json($topicReviewStatusType, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Topic Review Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $topicReviewStatusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $topicReviewStatusTypeKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try{

            $translationsOld = [];
            $translationsNew = [];

            $topicReviewStatusType = TopicReviewStatusType::whereTopicReviewStatusTypeKey($topicReviewStatusTypeKey)->firstOrFail();

            $topicReviewStatusType->code     = $request->json('code');
            $topicReviewStatusType->position = $request->json('position');
            $topicReviewStatusType->save();

            $translationsId = $topicReviewStatusType->statusTypeTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $topicReviewStatusTypeTranslation = $topicReviewStatusType->statusTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($topicReviewStatusTypeTranslation)) {
                        $topicReviewStatusTypeTranslation = $topicReviewStatusType->statusTypeTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name'],
                                'description'   => isset($translation['description']) ? $translation['description'] : null
                            ]
                        );
                    }
                    else {
                        $topicReviewStatusTypeTranslation->name        = $translation['title'];
                        $topicReviewStatusTypeTranslation->description = isset($translation['description']) ? $translation['description'] : null;
                        $topicReviewStatusTypeTranslation->save();
                    }
                }
                $translationsNew[] = $topicReviewStatusTypeTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $topicReviewStatusType->statusTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($topicReviewStatusType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Status Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Topic Review Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteTopicReviewStatusType",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/topicReviewStatusTypes/{topicReviewStatusTypeKey}",
     *  summary="Delete a Topic Review Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"TopicReviewStatusType"},
     *
     * @SWG\Parameter(
     *      name="topicReviewStatusTypeKey",
     *      in="path",
     *      description="Topic Review Status Type Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTopicReviewStatusType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Topic Review Status Type not Found",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Topic Review Status Type",
     *      @SWG\Schema(ref="#/definitions/topicReviewStatusTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $topicReviewStatusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $topicReviewStatusTypeKey)
    {
        ONE::verifyToken($request);

        try{
            $topicReviewStatusType = TopicReviewStatusType::whereTopicReviewStatusTypeKey($topicReviewStatusTypeKey)->firstOrFail();
            $topicReviewStatusType->statusTypeTranslations()->delete();
            $topicReviewStatusType->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review Status Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Topic Review Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
