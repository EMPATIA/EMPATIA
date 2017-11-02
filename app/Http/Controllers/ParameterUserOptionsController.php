<?php

namespace App\Http\Controllers;

use App\One\One;
use App\ParameterUserOption;
use App\ParameterUserType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ParameterUserOptionsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Parameter User Option",
 *   description="Everything about Parameter User Options",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterUserOptionErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *      definition="parameterUserTranslation",
 *      @SWG\Property(property="language_code", type="string", format="string"),
 *      @SWG\Property(property="name", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterUserOptionCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_user_type_key", "translations"},
 *           @SWG\Property(property="parameter_user_type_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="translations",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/parameterUserTranslation")
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="parameterUserOptionShowReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_option_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="parameterUserOptionReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_user_option_key", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="parameterUserOptionDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class ParameterUserOptionsController extends Controller
{
    protected $keysRequired = [
        'translations'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $parameterUserOptions = ParameterUserOption::all();

            foreach ($parameterUserOptions as $parameterUserOption) {
                if (!($parameterUserOption->translation($request->header('LANG-CODE')))) {
                    if (!$parameterUserOption->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            return response()->json(['data' => $parameterUserOptions], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameters User Options list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Get(
     *  path="/parameterUserOption/{parameter_user_option_key}",
     *  summary="Show a Parameter User Option",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Option"},
     *
     *  @SWG\Parameter(
     *      name="parameter_user_option_key",
     *      in="path",
     *      description="Parameter User Option key",
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
     *      description="Show the Parameter User Option data",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Option not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Parameter User Option",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserOptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $parameterUserOptionKey)
    {
        try {
            $parameterUserOption = ParameterUserOption::whereParameterUserOptionKey($parameterUserOptionKey)->firstOrFail();

            if (!($parameterUserOption->translation($request->header('LANG-CODE')))) {
                if (!$parameterUserOption->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($parameterUserOption, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $parameterUserOptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $parameterUserOptionKey)
    {
        try {
            $parameterUserOption = ParameterUserOption::whereParameterUserOptionKey($parameterUserOptionKey)->firstOrFail();

            $parameterUserOption->translations();

            return response()->json($parameterUserOption, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/parameterUserOption",
     *  summary="Create a Parameter User Option",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Option"},
     *
     *  @SWG\Parameter(
     *      name="ParameterUserOption",
     *      in="body",
     *      description="Parameter User Option Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionCreate")
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
     *      response=201,
     *      description="the newly createdParameter User Option",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Option not found",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Parameter User Option",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
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
            $parameterUserType = ParameterUserType::whereParameterUserTypeKey($request->json('parameter_user_type_key'))->firstOrFail();
            $key = '';

            do {
                $rand = str_random(32);
                if (!($exists = ParameterUserOption::whereParameterUserOptionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $parameterUserOption = $parameterUserType->parameterUserOptions()->create([
                'parameter_user_option_key' => $key,
            ]);

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name']
                        ]
                    );
                }
            }

            return response()->json($parameterUserOption, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Type not Found'], 404);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Parameter User Option'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/parameterUserOption/{parameter_user_option_key}",
     *  summary="Update a Parameter User Option",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Option"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter User Option Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="parameter_user_option_key",
     *      in="path",
     *      description="Parameter User Option Key",
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
     *      description="The updated Parameter User Option",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Parameter User Option not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Parameter User Option",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserOptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $parameterUserOptionKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $translationsOld = [];
            $translationsNew = [];

            $parameterUserOption = ParameterUserOption::whereParameterUserOptionKey($parameterUserOptionKey)->firstOrFail();

            $translationsId = $parameterUserOption->parameterUserOptionTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($parameterUserOptionTranslations)) {
                        $parameterUserOptionTranslations = $parameterUserOption->parameterUserOptionTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name']
                            ]
                        );
                    }
                    else {
                        $parameterUserOptionTranslations->name = $translation['name'];
                        $parameterUserOptionTranslations->save();
                    }
                }
                $translationsNew[] = $parameterUserOptionTranslations->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $parameterUserOption->parameterUserOptionTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($parameterUserOption, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update a Parameter User Option'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/parameterUserOption/{parameter_user_option_key}",
     *  summary="Delete a parameter User Option",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter User Option"},
     *
     * @SWG\Parameter(
     *      name="parameter_user_option_key",
     *      in="path",
     *      description="parameter User Option Key",
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
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter User Option not Found",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete mMdule Type",
     *      @SWG\Schema(ref="#/definitions/parameterUserOptionErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $parameterUserOptionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $parameterUserOptionKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $parameterUserOption = ParameterUserOption::whereParameterUserOptionKey($parameterUserOptionKey)->firstOrFail();
            $parameterUserOption->parameterUserOptionTranslations()->delete();
            $parameterUserOption->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete Parameter User Option'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter User Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
