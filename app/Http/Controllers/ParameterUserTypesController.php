<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\OrchParameterType;
use App\ParameterUserOption;
use App\ParameterUserType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ParameterUserTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Parameter User Type",
 *   description="Everything about Parameter User Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterUserTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterUserTypeCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_type_code", "mandatory", "translations", "parameter_user_options"},
 *           @SWG\Property(property="parameter_type_code", format="string", type="string"),
 *           @SWG\Property(property="mandatory", format="boolean", type="boolean"),
 *           @SWG\Property(
 *              property="translations",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/parameterUserTranslation")
 *           ),
 *           @SWG\Property(
 *              property="parameter_user_options",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/parameterUserOptionCreate")
 *           ),
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="parameterUserTypeShowReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_type_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(
 *              property="parameter_user_options",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/parameterUserOptionShowReply")
 *           ),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="parameterUserTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_type_key", format="string", type="string"),
 *           @SWG\Property(property="parameter_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="mandatory", format="boolean", type="boolean"),
 *           @SWG\Property(property="level_parameter_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="parameterUserTypeDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class ParameterUserTypesController extends Controller
{
    protected $keysRequired = [
        'mandatory',
        'parameter_unique',
        'parameter_type_code',
        'translations'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $parameterUserTypes = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail()->parameterUserTypes()->with('parameterType')->get();

            $primaryLanguage = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            foreach ($parameterUserTypes as $parameterUserType) {
                $parameterUserType->newTranslation($primaryLanguage,$defaultLanguage);

                $parameterUserOptions = $parameterUserType->parameterUserOptions()->get();
                foreach ($parameterUserOptions as $parameterUserOption) {
                    $parameterUserOption->newTranslation($primaryLanguage,$defaultLanguage);
                }

                $parameterUserType['parameter_user_options'] = $parameterUserOptions;
            }

            return response()->json(['data' => $parameterUserTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameters User Types list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Get(
     *  path="/parameterUserType/{parameter_user_type_key}",
     *  summary="Show a Parameter User Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Type"},
     *
     *  @SWG\Parameter(
     *      name="parameter_user_type_key",
     *      in="path",
     *      description="Parameter User Type key",
     *      required=true,
     *      type="integer"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE",
     *      in="header",
     *      description="User Language",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE-DEFAULT",
     *      in="header",
     *      description="Entity default Language",
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
     *      description="Show the Parameter User Type data",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Parameter User Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $parameterUserTypeId)
    {
        try {
            /* Someplaces use IDs and someother use Key, this makes everything work */
            if (is_numeric($parameterUserTypeId))
                $parameterUserType = ParameterUserType::whereId($parameterUserTypeId);
            else
                $parameterUserType = ParameterUserType::whereParameterUserTypeKey($parameterUserTypeId);

            $parameterUserType = $parameterUserType->with('parameterType')->firstOrFail();

            if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            $parameterUserOptions = $parameterUserType->parameterUserOptions()->get();

            foreach ($parameterUserOptions as $parameterUserOption) {
                if (!($parameterUserOption->translation($request->header('LANG-CODE')))) {
                    if (!$parameterUserOption->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            $parameterUserType['parameter_user_options'] = $parameterUserOptions;

            return response()->json($parameterUserType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameters User Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $parameterUserTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $parameterUserTypeKey)
    {
        try {
            $parameterUserType = ParameterUserType::whereParameterUserTypeKey($parameterUserTypeKey)->firstOrFail();
            $parameterUserType->translations();

            $parameterUserOptions = $parameterUserType->parameterUserOptions()->get();

            foreach ($parameterUserOptions as $parameterUserOption) {
                if (!($parameterUserOption->translations($request->header('LANG-CODE')))) {
                    if (!$parameterUserOption->translations($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            $parameterUserType['parameter_user_options'] = $parameterUserOptions;

            return response()->json($parameterUserType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameters User Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/parameterUserTypes",
     *  summary="Create a Parameter User Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Type"},
     *
     *  @SWG\Parameter(
     *      name="ParameterUserType",
     *      in="body",
     *      description="Parameter User Type Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeCreate")
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
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="ENtity Key",
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
     *      response=201,
     *      description="the newly createdParameter User Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Type not found",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Parameter User Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $parameterType = OrchParameterType::whereCode($request->json('parameter_type_code'))->firstOrFail();
            $key = '';

            do {
                $rand = str_random(32);

                if (!($exists = ParameterUserType::whereParameterUserTypeKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $parameterUserType = $entity->parameterUserTypes()->create(
                [
                    'parameter_user_type_key'   =>$key,
                    'code'                      =>$request->json('parameter_code'),
                    'parameter_type_id'         =>$parameterType->id,
                    'mandatory'                 =>$request->json('mandatory'),
                    'parameter_unique'          =>$request->json('parameter_unique')
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (!empty($translation['language_code']) && !empty($translation['name'])){
                    $parameterUserTypeTranslations = $parameterUserType->parameterUserTypeTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name'],
                            'description'   => $translation['description']
                        ]
                    );
                }
            }

            if (!is_null($request->json('parameter_user_options'))){

                foreach ($request->json('parameter_user_options') as $parameterOption){
                    $parameter_user_option_key = '';

                    do {
                        $rand = str_random(32);

                        if (!($exists = ParameterUserOption::whereParameterUserOptionKey($rand)->exists())) {
                            $parameter_user_option_key = $rand;
                        }
                    } while ($exists);

                    $parameterUserOption = $parameterUserType->parameterUserOptions()->create(
                        [
                            'parameter_user_option_key' => $parameter_user_option_key,
                        ]
                    );

                    foreach ($parameterOption['translations'] as $translation){
                        if (isset($translation['language_code']) && isset($translation['name'])){
                            $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->create(
                                [
                                    'language_code' => $translation['language_code'],
                                    'name'          => $translation['name']
                                ]
                            );
                        }
                    }
                }
            }

            return response()->json($parameterUserType, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Parameter User Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/parameterUserTypes/{parameter_user_type_key}",
     *  summary="Update a Parameter User Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Type"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter User Type Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="parameter_user_type_key",
     *      in="path",
     *      description="Parameter User Type Key",
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
     *      description="The updated Parameter User Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Parameter User Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Parameter User Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $parameterUserTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $translationsOld = [];
            $translationsNew = [];

            $parameterUserType = ParameterUserType::whereParameterUserTypeKey($parameterUserTypeKey)->firstOrFail();
            $parameterUserType->code = $request->json('parameter_code');
            $parameterUserType->mandatory = $request->json('mandatory');
            $parameterUserType->parameter_unique = $request->json('parameter_unique');
            $parameterUserType->save();

            $translationsId = $parameterUserType->parameterUserTypeTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $parameterUserTypeTranslations = $parameterUserType->parameterUserTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($parameterUserTypeTranslations)) {
                        $parameterUserTypeTranslations = $parameterUserType->parameterUserTypeTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name'],
                                'description'   => $translation['description']
                            ]
                        );
                    }
                    else {
                        $parameterUserTypeTranslations->name    = $translation['name'];
                        $parameterUserTypeTranslations->description = $translation['description'];
                        $parameterUserTypeTranslations->save();
                    }
                    $translationsNew[] = $parameterUserTypeTranslations->id;
                }
            }

            $parameterUserOptionsOld = $parameterUserType->parameterUserOptions->pluck('id');
            $parameterUserOptionsNew = [];

            if (!is_null($request->json('parameter_user_options'))){
                foreach ($request->json('parameter_user_options') as $parameterOption) {

                    if(!isset($parameterOption['parameter_user_option_key'])){

                        $parameter_user_option_key = '';
                        do {
                            $rand = str_random(32);

                            if (!($exists = ParameterUserOption::whereParameterUserOptionKey($rand)->exists())) {
                                $parameter_user_option_key = $rand;
                            }
                        } while ($exists);

                        $parameterUserOption = $parameterUserType->parameterUserOptions()->create(
                            [
                                'parameter_user_option_key' => $parameter_user_option_key,
                            ]
                        );

                        foreach ($parameterOption['translations'] as $translation){
                            if (isset($translation['language_code']) && isset($translation['name'])){
                                $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->create(
                                    [
                                        'language_code' => $translation['language_code'],
                                        'name'          => $translation['name']
                                    ]
                                );
                            }
                        }

                        $parameterUserOptionsNew[] = $parameterUserOption->id;

                    } else {
                        $optionTranslationsOld = [];
                        $optionTranslationsNew = [];


                        $parameterUserOption = ParameterUserOption::whereParameterUserOptionKey($parameterOption['parameter_user_option_key'])->firstOrFail();

                        $optionTranslationsId = $parameterUserOption->parameterUserOptionTranslations()->get();
                        foreach ($optionTranslationsId as $optionTranslationId) {
                            $optionTranslationsOld[] = $optionTranslationId->id;
                        }

                        foreach ($parameterOption['translations'] as $translation) {
                            if (isset($translation['language_code']) && isset($translation['name'])) {
                                $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                                if (empty($parameterUserOptionTranslations)) {
                                    $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->create(
                                        [
                                            'language_code' => $translation['language_code'],
                                            'name' => $translation['name'],
                                            'description' => $translation['description']
                                        ]
                                    );
                                } else {
                                    $parameterUserOptionTranslations->name = $translation['name'];
                                    $parameterUserOptionTranslations->description = $translation['description'];
                                    $parameterUserOptionTranslations->save();
                                }
                                $optionTranslationsNew[] = $parameterUserOptionTranslations->id;
                            }
                        }

                        $deleteTranslations = array_diff($optionTranslationsOld, $optionTranslationsNew);
                        foreach ($deleteTranslations as $deleteTranslation) {
                            $deleteId = $parameterUserOption->parameterUserOptionTranslations()->whereId($deleteTranslation)->first();
                            $deleteId->delete();
                        }
                        $parameterUserOptionsNew[] = $parameterUserOption->id;
                    }
                }
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $parameterUserType->parameterUserTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            $deleteParameterUserOptions = array_diff($parameterUserOptionsOld->toArray(), $parameterUserOptionsNew);
            foreach ($deleteParameterUserOptions as $deleteParameterUserOption) {
                $deleteId = $parameterUserType->parameterUserOptions()->whereId($deleteParameterUserOption)->first();
                $deleteId->delete();
            }

            return response()->json($parameterUserType, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update a Parameter User Type'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/parameterUserTypes/{parameter_user_type_key}",
     *  summary="Delete a parameter User Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Type"},
     *
     * @SWG\Parameter(
     *      name="parameter_user_type_key",
     *      in="path",
     *      description="parameter User Type Key",
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
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete mMdule Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $parameterUserTypeKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $parameterUserType = ParameterUserType::whereParameterUserTypeKey($parameterUserTypeKey)->firstOrFail();
            $parameterUserType->parameterUserTypeTranslations()->delete();
            $parameterUserType->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete Parameter User Type'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParameterUserTypesList(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $parameterTypeIds = OrchParameterType::whereIn('code', $request->parameterTypeCodes)->select(['id'])->get();

            $parameterUserTypes = ParameterUserType::whereIn('parameter_type_id', $parameterTypeIds)->get();

            foreach ($parameterUserTypes as $parameterUserType) {
                if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                    if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(['data' => $parameterUserTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Parameter User Types List'], 500);
        }
    }


    /** Verify Unique Parameter User Types
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUniqueParameterUserTypes(Request $request)
    {
        ONE::verifyKeysRequest(['parameter_user_type_keys'], $request);
        try {
            $parameterUserTypeKeys = $request->json('parameter_user_type_keys');
            if(!is_array($parameterUserTypeKeys)){
                $parameterUserType = ParameterUserType::whereParameterUserTypeKey($parameterUserTypeKeys)->first();
                $parameterUnique = 0;
                if(!empty($parameterUserType)){
                    $parameterUnique = $parameterUserType->parameter_unique;
                }
                return response()->json($parameterUnique, 200);

            }else{
                $parameterUserTypes = ParameterUserType::whereIn('parameter_user_type_key',$parameterUserTypeKeys)->get()->pluck('parameter_unique','parameter_user_type_key');
                return response()->json(['data' => $parameterUserTypes], 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to verify Unique Parameter User Type'], 500);
        }
    }


}
