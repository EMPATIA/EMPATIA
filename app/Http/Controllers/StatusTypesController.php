<?php

namespace App\Http\Controllers;

use ONE;
use App\StatusType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

/**
 * Class StatusTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="StatusType",
 *   description="Everything about Sites",
 * )
 *
 *  @SWG\Definition(
 *      definition="statusTypeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="statusTypeResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"status_type_key", "code", "position", "created_at", "updated_at", "translations"},
 *           @SWG\Property(property="status_type_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *            @SWG\Property(property="translations", type="array",
 *              @SWG\Items(
 *                  @SWG\Property(property="status_type_id", format="string", type="integer"),
 *                  @SWG\Property(property="language_code", format="string", type="integer"),
 *                  @SWG\Property(property="name", format="string", type="integer"),
 *                  @SWG\Property(property="description", format="string", type="integer"),
 *                  @SWG\Property(property="created_at", format="string", type="integer"),
 *                  @SWG\Property(property="updated_at", format="string", type="integer"),
 *              )
 *          ),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="statusType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "position", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="translations", type="array",
 *              @SWG\Items(
 *                  @SWG\Property(property="status_type_id", format="string", type="integer"),
 *                  @SWG\Property(property="language_code", format="string", type="integer"),
 *                  @SWG\Property(property="name", format="string", type="integer"),
 *                  @SWG\Property(property="description", format="string", type="integer"),
 *                  @SWG\Property(property="created_at", format="string", type="integer"),
 *                  @SWG\Property(property="updated_at", format="string", type="integer"),
 *              )
 *          ),
 *       )
 *   }
 * )
 *
 */
class StatusTypesController extends Controller
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
            $statusTypes = StatusType::all();

            foreach ($statusTypes as $statusType) {
                if (!($statusType->translation($request->header('LANG-CODE')))) {
                    if (!$statusType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $statusType->statusTypeTranslations()->first();
                        if ($translation){
                            $statusType->translation($translation->language_code);
                        } else {
                            $statusType->setAttribute('name','no translation');
                            $statusType->setAttribute('description','no translation');
                        }
                    }
                }
            }

            return response()->json(['data' => $statusTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Status Types']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/statusTypes/{statusTypeKey}",
     *  summary="Show a Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"StatusType"},
     *
     * @SWG\Parameter(
     *      name="statusTypeKey",
     *      in="path",
     *      description="Status Type Key",
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
     *      description="Show the Status Type data",
     *      @SWG\Schema(ref="#/definitions/statusTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Status Type not Found",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $statusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $statusTypeKey)
    {
        try {
            $statusType = StatusType::whereStatusTypeKey($statusTypeKey);

            if (!($statusType->translation($request->header('LANG-CODE')))) {
                if (!$statusType->translation($request->header('LANG-CODE-DEFAULT'))){
                    if (!$statusType->translation('en')){
                        return response()->json(['error' => 'No translation found'], 404);
                    }
                }

            }

            return response()->json($statusType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $statusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $statusTypeKey)
    {
        try {
            $statusType = StatusType::whereStatusTypeKey($statusTypeKey);

            $statusType->translations();

            return response()->json($statusType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/statusTypes",
     *  summary="Creation of a Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"StatusType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Status Type data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/statusType")
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
     *      description="the newly created Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
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
            $statusType = StatusType::create(
                [
                    'code'      => $request->json('code'),
                    'position'  => $request->json('position')
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $statusTypeTranslation = $statusType->statusTypeTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['title'],
                            'description'   => isset($translation['description']) ? $translation['description'] : null
                        ]
                    );
                }
            }

            return response()->json($statusType, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/statusTypes/{statusTypeKey}",
     *  summary="Update a Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"StatusType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Status Type Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/statusType")
     *  ),
     *
     * @SWG\Parameter(
     *      name="statusTypeKey",
     *      in="path",
     *      description="Status Type Key",
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
     *      description="The updated Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Status Type not Found",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $statusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $statusTypeKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try{

            $translationsOld = [];
            $translationsNew = [];

            $statusType = StatusType::whereStatusTypeKey($statusTypeKey)->firstOrFail();

            $statusType->code     = $request->json('code');
            $statusType->position = $request->json('position');
            $statusType->save();

            $translationsId = $statusType->statusTypeTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $statusTypeTranslation = $statusType->statusTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($statusTypeTranslation)) {
                        $statusTypeTranslation = $statusType->statusTypeTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name'],
                                'description'   => isset($translation['description']) ? $translation['description'] : null
                            ]
                        );
                    }
                    else {
                        $statusTypeTranslation->name        = $translation['title'];
                        $statusTypeTranslation->description = isset($translation['description']) ? $translation['description'] : null;
                        $statusTypeTranslation->save();
                    }
                }
                $translationsNew[] = $statusTypeTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $statusType->statusTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($statusType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteStatusType",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/statusTypes/{statusTypeKey}",
     *  summary="Delete a Status Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"StatusType"},
     *
     * @SWG\Parameter(
     *      name="statusTypeKey",
     *      in="path",
     *      description="Status Type Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteStatusType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Status Type",
     *      @SWG\Schema(ref="#/definitions/statusTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $statusTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $statusTypeKey)
    {
        ONE::verifyToken($request);

        try{
            $statusType = StatusType::whereStatusTypeKey($statusTypeKey)->firstOrFail();
            $statusType->statusTypeTranslations()->delete();
            $statusType->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Status Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $language
     * @return bool|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\JsonResponse|static[]
     */
    public function getStatusTypes($language)
    {
        try {
            $statusTypes = StatusType::all();

            $response = [];
            foreach ($statusTypes as $statusType) {
                if (!($statusType->translation($language['langCode']))) {
                    if (!$statusType->translation($language['langCodeDefault'])){
                        if (!$statusType->translation('en'))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }
                $response[$statusType->code] = $statusType->name;
            }

            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
}
