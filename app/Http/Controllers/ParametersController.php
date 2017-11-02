<?php

namespace App\Http\Controllers;

use App\Cb;
use App\One\One;
use App\Parameter;
use App\ParameterField;
use App\ParameterOption;
use App\ParameterOptionField;
use App\Topic;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class ParametersController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Parameter",
 *   description="Everything about Parameters",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "mandatory", "value", "currency", "position", "use_filter", "visible_in_list", "visible"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="mandatory", format="string", type="integer"),
 *           @SWG\Property(property="parameter", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="value", format="string", type="string"),
 *           @SWG\Property(property="currency", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="use_filter", format="string", type="integer"),
 *           @SWG\Property(property="visible_in_list", format="string", type="integer"),
 *           @SWG\Property(property="visible", format="string", type="integer"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="parameter",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_type_id", "code", "mandatory", "position", "use_filter", "visible_in_list", "visible"},
 *           @SWG\Property(property="parameter_type_id", format="string", type="integer"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="mandatory", format="string", type="integer"),
 *           @SWG\Property(property="parameter", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="value", format="string", type="string"),
 *           @SWG\Property(property="currency", format="string", type="string"),
 *           @SWG\Property(property="position", format="string", type="integer"),
 *           @SWG\Property(property="use_filter", format="string", type="integer"),
 *           @SWG\Property(property="visible_in_list", format="string", type="integer"),
 *           @SWG\Property(property="visible", format="string", type="integer"),
 *       )
 *   }
 * )
 *
 */

class ParametersController extends Controller
{
    protected $required = [
        'store' => ['cb_key', 'parameter_type_id', 'mandatory', 'translations'],
        'update' => ['mandatory', 'translations'],
        'addOption' => ['label'],
        'editOption' => ['label']
    ];

    /**
     * @param $parameterId
     * @param $options
     * @return \Illuminate\Http\JsonResponse
     */
    private function syncParameterTopic($parameterId, $options)
    {
        try {
            $parameter = Parameter::findOrFail($parameterId);

            foreach ($options as $option) {
                $parameterPivots = $parameter->topics()->wherePivot('value', $option)->get();
                foreach ($parameterPivots as $parameterPivot){
                    $parameterPivot->parameters()->updateExistingPivot($parameterId, ["value" => ""]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update the Pivot Table'], 500);
        }
    }

    /**
     * Returns the list of Parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        try {
            $parameters = Parameter::with('type')->get();

            return response()->json(['data' => $parameters], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Parameters']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/parameters/{parameterId}",
     *  summary="Show a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter"},
     *
     * @SWG\Parameter(
     *      name="parameterId",
     *      in="path",
     *      description="Parameter Id",
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
     *      description="Show the Parameter data",
     *      @SWG\Schema(ref="#/definitions/parameterResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter not Found",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Returns the details of the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $parameterId)
    {

        try {
            $parameter = Parameter::findOrFail($parameterId);

            return response()->json($parameter, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameter'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $parameterId)
    {
        try {

            $parameter = Parameter::with('options', 'parameterFields')->findOrFail($parameterId);
            $parameter->translations();

            foreach ($parameter->options as $option) {
                $option->translations();
                $option->fields = ParameterOptionField::whereParameterOptionId($option->id)->get();
            }


            return response()->json($parameter, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/parameters",
     *  summary="Creation of a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameter")
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
     *      description="the newly created parameter",
     *      @SWG\Schema(ref="#/definitions/parameterResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Stores a new Parameter returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $cb = Cb::whereCbKey($request->json('cb_key'))->firstOrFail();

            $parameterLastPosition = $cb->parameters()
                ->orderBy('position', 'desc')
                ->where('parameter_type_id', '=', $request->json('parameter_type_id'))
                ->first();

            $lastPosition = !empty($parameterLastPosition) ? $parameterLastPosition->position + 1 : 0;

            $parameter = $cb->parameters()->create(
                [
                    'parameter_type_id' => $request->json('parameter_type_id'),
                    'code' => $request->json('code'),
                    'parameter_code' => $request->json('parameter_code'),
                    'mandatory' => $request->json('mandatory'),
                    'visible' => $request->json('visible'),
                    'visible_in_list' => $request->json('visible_in_list'),
                    'value' => $request->json('value'),
                    'currency' => $request->json('currency'),
                    'position' => $lastPosition,
                    'use_filter' => $request->json('use_filter'),
                    'private' => $request->json('private')

                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['parameter'])){
                    $parameterTranslation = $parameter->parameterTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'parameter'     => $translation['parameter'],
                            'description'   => empty($translation['description']) ? null : $translation['description']
                        ]
                    );
                }
            }

            if (isset($request->options) and !empty($request->json('options'))) {
                foreach ($request->json('options') as $option) {
                    $parameterOption = $parameter->options()->create([
                        "code" => $option["code"] ?? ""
                    ]);
                    if(!empty($option['optionFields'])) {
                        foreach($option['optionFields'] as $field){
                            $parameterOptionField = $parameterOption->parameterOptionFields()->create([
                                'value' => $field['value'],
                                'code'  => $field['code']
                            ]);
                        }
                    }

                    if (!empty($option['translations'])) {
                        foreach ($option['translations'] as $translation) {
                            if (isset($translation['language_code']) && isset($translation['label'])) {

                                $parameterOptionTranslation = $parameterOption->parameterOptionTranslations()->create(
                                    [
                                        'language_code' => $translation['language_code'],
                                        'label' => $translation['label']
                                    ]
                                );

                            }
                        }
                    }
                }
            }

            if(isset($request->fields) and !empty($request->fields)){
                foreach ($request->fields as $parameterField) {
                    $field = $parameter->parameterFields()->create(
                        [
                            'value' => $parameterField['value'],
                            'code'  => $parameterField['code']
                        ]

                    );
                }
            }

            $response = Parameter::with('options')->find($parameter->id);

            return response()->json($response, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter'], 500);
        }
    }

    /**
     *
     * @SWG\Put(
     *  path="/parameters/{parameterId}",
     *  summary="Update a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameter")
     *  ),
     *
     * @SWG\Parameter(
     *      name="parameterId",
     *      in="path",
     *      description="Parameter Id",
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
     *      description="The updated Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Parameter not Found",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Updates the specified Parameter returning it afterwards.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $parameterId)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $parameter = Parameter::findOrFail($parameterId);

            $translationsOld = $parameter->parameterTranslations()->pluck('id');
            $translationsNew = [];

            $parameter->parameter_type_id = $request->json('parameter_type_id');
            $parameter->code = $request->json('code');
            $parameter->parameter_code = $request->json('parameter_code');
            $parameter->mandatory = $request->json('mandatory');
            $parameter->visible = $request->json('visible');
            $parameter->visible_in_list = $request->json('visible_in_list');
            $parameter->value = $request->json('value');
            $parameter->currency = $request->json('currency');
//            $parameter->position = $request->json('position');
            $parameter->use_filter = $request->json('use_filter');
            $parameter->private = $request->json('private');

            $parameter->save();

            foreach ($request->json('translations') as $translation) {
                $parameterTranslation = '';
                if (isset($translation['language_code']) && isset($translation['parameter'])) {
                    $parameterTranslation = $parameter->parameterTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($parameterTranslation)) {
                        $parameterTranslation = $parameter->parameterTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'parameter' => $translation['parameter'],
                                'description' => empty($translation['description']) ? null : $translation['description']
                            ]
                        );
                    } else {
                        $parameterTranslation->parameter = $translation['parameter'];
                        $parameterTranslation->description = $translation['description'];
                        $parameterTranslation->save();
                    }
                }
                $translationsNew[] = $parameterTranslation->id;
            }

            $deleteTranslations = $translationsOld->diff($translationsNew);

            if (!empty($deleteTranslations->toArray())) {
                $parameter->parameterTranslations()->whereIn('id', [$deleteTranslations])->delete();
            }

            if (!empty($request->json('options'))) {

                $optionsOld = [];
                $optionsNew = [];

                foreach ($parameter->options as $option) {
                    $optionsOld[] = $option['id'];
                }

                foreach ($request->json('options') as $option) {
                    if (empty($option['option_id'])) {
                        $parameterOption = $parameter->options()->create([
                            "code" => $option["code"] ?? ""
                        ]);

                        if (!empty($option['optionFields'])){
                            foreach ($option['optionFields'] as $field) {

                                $parameterOptionField = $parameterOption->parameterOptionFields()->create([
                                    'value' => $field['value'],
                                    'code'  => $field['code']
                                ]);

                            }
                        }
                        foreach ($option['translations'] as $translation) {
                            if (isset($translation['language_code']) && isset($translation['label'])) {
                                $parameterOptionTranslation = $parameterOption->parameterOptionTranslations()->create(
                                    [
                                        'language_code' => $translation['language_code'],
                                        'label'         => $translation['label']
                                    ]
                                );
                            }
                        }
                    } else {
                        $parameterOption = $parameter->options()->whereId($option['option_id'])->first();
                        $parameterOption->code = $option["code"] ?? "";
                        $parameterOption->save();
                        $parameterOptionFields = $parameterOption->parameterOptionFields()->whereParameterOptionId($option['option_id'])->get();
                        foreach($option['optionFields'] as $fields){

                            $parameterOptionFields = $parameterOption->parameterOptionFields()->whereParameterOptionId($option['option_id'])->whereCode($fields['code'])->first();
                            $parameterOptionFields1 = $parameterOption->parameterOptionFields()->whereParameterOptionId($option['option_id'])->whereCode($fields['code'])->first();

                            if(empty($parameterOptionFields)){
                                $parameterOptionField = $parameterOption->parameterOptionFields()->create([
                                    'value' => $fields['value'],
                                    'code'  => $fields['code']
                                ]);
                            }else{
                                $parameterOptionFields->code = $fields['code'];
                                $parameterOptionFields->value = $fields['value'];
                                $parameterOptionFields->save();
                            }
                        }

                        $parameterOptionFields = $parameterOption->parameterOptionFields()->whereParameterOptionId($option['option_id'])->get();

                        $optionsNew[] = $option['option_id'];
                        $optionUpdate = $parameter->options()->whereId($option['option_id'])->first();

                        $optionTranslationsOld = $optionUpdate->parameterOptionTranslations()->pluck('id');
                        $optionTranslationsNew = [];

                        foreach ($option['translations'] as $translation) {
                            $parameterOptionTranslation = '';
                            if (isset($translation['language_code']) && isset($translation['label'])) {
                                $parameterOptionTranslation = $optionUpdate->parameterOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                                if (empty($parameterOptionTranslation)) {
                                    $parameterOptionTranslation = $optionUpdate->parameterOptionTranslations()->create(
                                        [
                                            'language_code' => $translation['language_code'],
                                            'label' => $translation['label']
                                        ]
                                    );
                                } else {
                                    $parameterOptionTranslation->label = $translation['label'];
                                    $parameterOptionTranslation->save();
                                }
                                $optionTranslationsNew[] = $parameterOptionTranslation->id;
                            }
                        }

                        $deleteTranslations = $optionTranslationsOld->diff($optionTranslationsNew);

                        if (!empty($deleteTranslations->toArray())) {
                            $optionUpdate->parameterOptionTranslations()->whereIn('id', [$deleteTranslations])->delete();
                        }

                    }
                }

                $deleteOptions = array_diff($optionsOld, $optionsNew);

                foreach ($deleteOptions as $deleteId) {
                    $option = $parameter->options()->whereId($deleteId)->first();
                    $option->delete();
                }
                $this->syncParameterTopic($parameter['id'], $deleteOptions);
            }

            if(!empty($request->fields)){
                $parameterField = ParameterField::whereParameterId($parameterId)->get();

                foreach($parameterField as $field){
                    if($field->code == 'color'){
                        $field->value = $request->fields['color'];
                    }

                    if($field->code == 'min_value'){
                        $field->value = $request->fields['min_value'];
                    }

                    if($field->code == 'max_value'){
                        $field->value = $request->fields['max_value'];
                    }

                    if($field->code == 'pin'){
                        $field->value = $request->fields['pin'];
                    }

                    if($field->code == 'icon'){
                        $field->value = $request->fields['icon'];
                    }

                    $field->save();
                }
            }

            $response = $parameter->with('options', 'parameterFields')->get();

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Parameter'], 500);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteParameter",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/parameters/{parameterId}",
     *  summary="Delete a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter"},
     *
     * @SWG\Parameter(
     *      name="parameterId",
     *      in="path",
     *      description="Parameter Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteParameter")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter not Found",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Deletes the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $parameterId)
    {
        ONE::verifyToken($request);

        try {
            Parameter::destroy($parameterId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter'], 500);
        }
    }

    /**
     * Returns the list of Parameters with their own Options.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameters(Request $request)
    {
        try {
            $parameters = Parameter::with(['options', 'type'])->get();

            return response()->json(['data' => $parameters], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Parameters with Options'], 500);
        }
    }

    /**
     * Returns the specified Parameter with its own Options.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameterOptions(Request $request, $parameterId)
    {
        try {
            $parameter = Parameter::with('options')->findOrFail($parameterId);

            if (!($parameter->translation($request->header('LANG-CODE')))) {
                if (!$parameter->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            foreach ($parameter->options as $option) {
                if (!($option->translation($request->header('LANG-CODE')))) {
                    if (!$option->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json($parameter, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Parameter with Options'], 500);
        }
    }

    /**
     * Add an Option to the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOption(Request $request, $parameterId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['addOption'], $request);

        try {
            $parameter = Parameter::findOrFail($parameterId);
            $parameter->options()->create(['label' => $request->json('label')]);

            return response()->json(Parameter::with('options')->findOrFail($parameterId), 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add Option to the Parameter']);
        }
    }

    /**
     * Add an array of Options to the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOptions(Request $request, $parameterId)
    {
        ONE::verifyToken($request);

        try {
            $parameter = Parameter::findOrFail($parameterId);

            foreach ($request->json('options') as $option) {
                $options[] = $parameter->options()->create(['label' => $option['label']]);
            }

            return response()->json(["data" => $options], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add Options to the Parameter']);
        }
    }

    /**
     * Edit the specified Option of the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @param $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editOption(Request $request, $parameterId, $optionId)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['editOption'], $request);

        try {
            $parameter = Parameter::findOrFail($parameterId);

            try {
                $option = $parameter->options()->findOrFail($optionId);
                $option->label = $request->json('label');
                $option->save();

                return response()->json(Parameter::with('options')->findOrFail($parameterId), 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Option not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Option'], 500);
        }
    }

    /**
     * Remove the specified Option of the specified Parameter.
     *
     * @param Request $request
     * @param $parameterId
     * @param $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOption(Request $request, $parameterId, $optionId)
    {
        ONE::verifyToken($request);

        try {
            $parameter = Parameter::findOrFail($parameterId);

            try {
                $parameter->options()->findOrFail($optionId)->delete();

                return response()->json('OK', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Option not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to remove Option'], 500);
        }
    }

    //STATIC FUNCTIONS FOR CB -> UPDATE ADVANCED

    /**
     * @param $data
     * @param $cbKey
     * @return bool
     */
    public static function storeParametersAdvance($data, $cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            foreach ($data as $parameter) {
                $parameter = (object)$parameter;

                $parameterLastPosition = $cb->parameters()
                    ->orderBy('position', 'desc')
                    ->where('parameter_type_id', '=', $parameter->parameter_type_id)
                    ->first();

                $lastPosition = !empty($parameterLastPosition) ? ($parameterLastPosition->position + 1) : 0;

                $cbParameter = $cb->parameters()->create(
                    [
                        'parameter_type_id' => $parameter->parameter_type_id,
                        'code' => $parameter->code,
                        'parameter_code' => $parameter->parameter_code,
                        'mandatory' => $parameter->mandatory,
                        'visible' => $parameter->visible,
                        'visible_in_list' => $parameter->visible_in_list,
                        'value' => $parameter->value,
                        'currency' => empty($parameter->currency) ? null : $parameter->currency,
                        'position' => $lastPosition,
                        'use_filter' => $parameter->use_filter
                    ]
                );

                foreach ($parameter->translations as $translation) {
                    if (isset($translation['language_code']) && isset($translation['parameter'])) {
                        $parameterTranslation = $cbParameter->parameterTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'parameter' => $translation['parameter'],
                                'description' => empty($translation['description']) ? null : $translation['description']
                            ]
                        );
                    }
                }

                if (!empty($parameter->options)) {
                    foreach ($parameter->options as $option) {
                        $parameterOption = $cbParameter->options()->create([]);
                        if(!empty($option['translations'])) {
                            foreach ($option['translations'] as $translation) {
                                if (isset($translation['language_code']) && isset($translation['label'])) {
                                    $parameterOptionTranslation = $parameterOption->parameterOptionTranslations()->create(
                                        [
                                            'language_code' => $translation['language_code'],
                                            'label' => $translation['label']
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }

//
                if (!empty($parameter->fields)) {
                    foreach ($parameter->fields as $parameterField) {
                        $field = $cbParameter->parameterFields()->create(
                            [
                                'value' => $parameterField['value'],
                                'code'  => $parameterField['code']
                            ]
                        );
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $data
     * @param $cbKey
     * @return bool
     */
    public static function updateParametersAdvance($data, $cbKey)
    {
        $item = new ParametersController();

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $parametersOld = $cb->parameters()->pluck('id');
            $parametersNew = [];

            foreach ($data as $parameter) {

                ONE::verifyKeysArray($item->required['update'], $parameter);
                $parameter = (object) $parameter;

                $parameterLastPosition = $cb->parameters()
                    ->orderBy('position', 'desc')
                    ->first();

                $lastPosition = !empty($parameterLastPosition) ? $parameterLastPosition->position + 1 : 0;

                $translationsOld = [];
                $translationsNew = [];

                $parameterId = $parameter->id;
                if ($parameterId){
                    $cbParameter = Parameter::findOrFail($parameterId);

                    $translationsOld = $cbParameter->parameterTranslations()->pluck('id')->toArray();
                }

                if(empty($cbParameter)){
                    $cbParameter = $cb->parameters()->create(
                        [
                            'parameter_type_id' => $parameter->parameter_type_id,
                            'code' => $parameter->code,
                            'parameter_code' => $parameter->parameter_code,
                            'mandatory' => $parameter->mandatory,
                            'visible' => $parameter->visible,
                            'visible_in_list' => $parameter->visible_in_list,
                            'value' => empty($parameter->value) ? null : $parameter->value,
                            'currency' => empty($parameter->currency) ? null : $parameter->currency,
                            'position' => empty($parameter->position) ? $lastPosition : $parameter->position,
                            'use_filter' => $parameter->use_filter,
                        ]
                    );
                }
                else{
                    $cbParameter->update([
                        'mandatory' => $parameter->mandatory,
                        'visible' => $parameter->visible,
                        'visible_in_list' => $parameter->visible_in_list,
                        'value' => empty($parameter->value) ? null : $parameter->value,
                        'currency' => empty($parameter->currency) ? null : $parameter->currency,
                        'position' => empty($parameter->position) ? $lastPosition : $parameter->position,
                        'use_filter' => $parameter->use_filter,
                    ]);
                }
                $parametersNew[] = $cbParameter->id;

                foreach ($parameter->translations as $translation) {
                    $parameterTranslation = '';
                    if (isset($translation['language_code']) && isset($translation['parameter'])) {
                        $parameterTranslation = $cbParameter->parameterTranslations()->whereLanguageCode($translation['language_code'])->first();
                        if (empty($parameterTranslation)) {
                            $parameterTranslation = $cbParameter->parameterTranslations()->create(
                                [
                                    'language_code' => $translation['language_code'],
                                    'parameter' => $translation['parameter'],
                                    'description' => empty($translation['description']) ? null : $translation['description']
                                ]
                            );
                        } else {
                            $parameterTranslation->parameter = $translation['parameter'];
                            $parameterTranslation->description = $translation['description'];
                            $parameterTranslation->save();
                        }
                    }
                    $translationsNew[] = $parameterTranslation->id;
                }

                $deleteTranslations = array_diff($translationsOld, $translationsNew);

                if (!empty($deleteTranslations)) {
                    $cbParameter->parameterTranslations()->whereIn('id', [$deleteTranslations])->delete();
                }

                if (!empty($parameter->options)) {
                    $optionsOld = $cbParameter->options()->pluck('id');
                    $optionsNew = [];

                    foreach ($parameter->options as $option) {

                        if (empty($option['option_id'])) {

                            $parameterOption = $cbParameter->options()->create([]);
                            $optionsNew[] = $parameterOption->id;

                            foreach ($option['translations'] as $translation) {
                                if (isset($translation['language_code']) && isset($translation['label'])) {
                                    $parameterOptionTranslation = $parameterOption->parameterOptionTranslations()->create(
                                        [
                                            'language_code' => $translation['language_code'],
                                            'label' => $translation['label']
                                        ]
                                    );
                                }
                            }
                        } else {
                            $optionsNew[] = $option['option_id'];
                            $optionUpdate = $cbParameter->options()->whereId($option['option_id'])->first();

                            $optionTranslationsOld = $optionUpdate->parameterOptionTranslations()->pluck('id');
                            $optionTranslationsNew = [];

                            foreach ($option['translations'] as $translation) {
                                $parameterOptionTranslation = '';
                                if (isset($translation['language_code']) && isset($translation['label'])) {
                                    $parameterOptionTranslation = $optionUpdate->parameterOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                                    if (empty($parameterOptionTranslation)) {
                                        $parameterOptionTranslation = $optionUpdate->parameterOptionTranslations()->create(
                                            [
                                                'language_code' => $translation['language_code'],
                                                'label' => $translation['label']
                                            ]
                                        );
                                    } else {
                                        $parameterOptionTranslation->label = $translation['label'];
                                        $parameterOptionTranslation->save();
                                    }
                                }
                                $optionTranslationsNew[] = $parameterOptionTranslation->id;
                            }
                            $deleteTranslations = $optionTranslationsOld->diff($optionTranslationsNew);

                            if (!empty($deleteTranslations->toArray())) {
                                $optionUpdate->parameterOptionTranslations()->whereIn('id', [$deleteTranslations])->delete();
                            }
                        }
                    }
                    $deleteOptions = array_diff($optionsOld->toArray(), $optionsNew);
                    ParameterOption::destroy($deleteOptions);
                    $item->syncParameterTopic($cbParameter->id, $deleteOptions);
                }
            }
            $deleteParameters = $parametersOld->diff($parametersNew);
            Parameter::destroy($deleteParameters->toArray());

            return true;
        }  catch (Exception $e) {
            return false;
        }
    }
}
