<?php

namespace App\Http\Controllers;

use App\FieldType;
use App\One\One;
use App\ParamAddField;
use App\ParameterType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ParameterTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="ParameterType",
 *   description="Everything about Parameter Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterTypeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */

class ParameterTypesController extends Controller
{
    protected $required = [
        'store' => ['name', 'code'],
        'update' => ['name', 'code']
    ];

    /**
     * Returns the list of Parameter Types.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $types = ParameterType::with('paramAddFields')->get();

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Parameter Types'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/parameterTypes/{typeId}",
     *  summary="Show a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterType"},
     *
     * @SWG\Parameter(
     *      name="typeId",
     *      in="path",
     *      description="Type Id",
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
     *      description="Show the Parameter Type data",
     *      @SWG\Schema(ref="#/definitions/parameterType")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Returns the details of the specified Parameter Type.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $typeId)
    {

        try {
            $type = ParameterType::with('paramAddFields')->findOrFail($typeId);
            foreach($type->paramAddFields as $fields){
                $fields->translations();
            }
            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'ParameterType not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameter Type']);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/parameterTypes",
     *  summary="Creation of a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Type data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterType")
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
     *      description="the newly created parameter type",
     *      @SWG\Schema(ref="#/definitions/parameterType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Stores a new Parameter Type returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $type = ParameterType::create([
                'name' => $request->json('name'),
                'code' => $request->json('code'),
                'options' => $request->json('options')
            ]);

            if(isset($request->types) and !empty($request->types)){
                foreach($request->types as $fieldType){

                    $paramAddField = ParamAddField::create([
                        'field_type_id' => $fieldType['id'],
                        'parameter_type_id' => $type->id,
                        'code' => $fieldType['code'],
                        'value' => $fieldType['value']
                    ]);

                    foreach($request->translations[$paramAddField->code] as $translation){
                        if (isset($translation['language_code']) && isset($translation['name'])){
                            $parameterTranslation = $paramAddField->paramAddFieldTranslations()->create([
                                'language_code' => $translation['language_code'],
                                'name' => $translation['name'],
                                'description' => $translation['description']
                            ]);
                        }

                    }
                }
            }

            return response()->json($type, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error storing parameter type'], 500);
        }
    }
    /**
     *
     * @SWG\Put(
     *  path="/parameterTypes/{typeId}",
     *  summary="Update a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Type Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterType")
     *  ),
     *
     * @SWG\Parameter(
     *      name="typeId",
     *      in="path",
     *      description="Parameter Type Id",
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
     *      description="The updated parameterType",
     *      @SWG\Schema(ref="#/definitions/parameterType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Parameter Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates the specified Parameter Type returning it afterwards.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $typeId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);


        try {

            $type = ParameterType::findOrFail($typeId);
            $type->name = $request->json('name');
            $type->code = $request->json('code');
            $type->options = $request->json('options');
            $type->save();

            foreach($request->json('types') as $types) {
                $paramAddField = ParamAddField::whereFieldTypeId($types['id'])->whereParameterTypeId($type->id)->first();

                if (!is_null($paramAddField)) {
                    $paramAddField->code = $types['code'];
                    $paramAddField->value = $types['value'];
                    $paramAddField->save();

                    $translationsOld = $paramAddField->paramAddFieldTranslations()->pluck('id');
                    $translationsNew = [];

                    foreach ($request->translations[$paramAddField->code] as $translation) {

                        if (isset($translation['language_code']) && isset($translation['name'])) {
                            $paramAddFieldTranslation = $paramAddField->paramAddFieldTranslations()->whereLanguageCode($translation['language_code'])->first();

                            if (empty($paramAddFieldTranslation)) {
                                $paramAddFieldTranslation = $paramAddField->paramAddFieldTranslations()->create(
                                    [
                                        'language_code' => $translation['language_code'],
                                        'name'          => $translation['name'],
                                        'description'   => $translation['description']
                                    ]
                                );
                            } else {

                                $paramAddFieldTranslation->name = $translation['name'];
                                $paramAddFieldTranslation->description = $translation['description'];
                                $paramAddFieldTranslation->save();
                            }


                        }
                        $translationsNew[] = $paramAddFieldTranslation->id;

                    }


                    $deleteTranslations = $translationsOld->diff($translationsNew);

                    if (!empty($deleteTranslations->toArray())) {
                        $paramAddField->paramAddFieldTranslations()->whereIn('id', [$deleteTranslations])->delete();
                    }
                }else{
                    $paramAddField = ParamAddField::create([
                        'field_type_id' => $types['id'],
                        'parameter_type_id' => $type->id,
                        'code' => $types['code'],
                        'value' => $types['value']
                    ]);

                    foreach($request->translations[$paramAddField->code] as $translation){
                        if (isset($translation['language_code']) && isset($translation['name'])){
                            $parameterTranslation = $paramAddField->paramAddFieldTranslations()->create([
                                'language_code' => $translation['language_code'],
                                'name' => $translation['name'],
                                'description' => $translation['description']
                            ]);
                        }

                    }
                }

            }

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Parameter Type'], 500);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteParameterType",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/parameterTypes/{typeId}",
     *  summary="Delete a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterType"},
     *
     * @SWG\Parameter(
     *      name="typeId",
     *      in="path",
     *      description="Parameter Type Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteParameterType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter Type not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Deletes the specified Parameter Type.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $typeId)
    {
        ONE::verifyToken($request);

        try {
            ParameterType::destroy($typeId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter Type'], 500);
        }
    }
}
