<?php

namespace App\Http\Controllers;

use App\Cb;
use App\One\One;


use App\ParameterTemplate;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class ParameterTemplatesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="ParameterTemplate",
 *   description="Everything about Parameter Templates",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterTemplateErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterTemplateResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_template_key", "code", "mandatory", "value", "currency", "position", "use_filter", "visible_in_list", "visible"},
 *           @SWG\Property(property="parameter_template_key", format="string", type="string"),
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
 *   definition="parameterTemplate",
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
class ParameterTemplatesController extends Controller
{
    protected $required = [
        'store' => ['parameter_type_id', 'parameter', 'description', 'mandatory'],
        'update' => ['parameter_type_id', 'parameter', 'description', 'mandatory'],
        'addOption' => ['label'],
        'editOption' => ['label']
    ];

    /**
     * Returns the list of ParameterTemplates.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $parameterTemplates = ParameterTemplate::with('type')->get();
            return response()->json(['data' => $parameterTemplates], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the list of Parameter Templates']);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/parameterTemplates/{parameter_id}",
     *  summary="Show a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterTemplate"},
     *
     * @SWG\Parameter(
     *      name="parameter_id",
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
     *      @SWG\Schema(ref="#/definitions/parameterTemplateResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Parameter",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Returns the details of the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $parameterTemplateKey)
    {

        try {
            $parameterTemplate = ParameterTemplate::with('templateOptions')->whereParameterTemplateKey($parameterTemplateKey)->firstOrFail();

            return response()->json($parameterTemplate, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameter Template'], 500);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/parameterTemplates",
     *  summary="Creation of a Parameter Template",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterTemplate"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Template data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterTemplate")
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
     *      @SWG\Schema(ref="#/definitions/parameterTemplateResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Parameter Template",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Stores a new ParameterTemplate returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {

            do {
                $rand = str_random(32);
                if (!($exists = Cb::whereCbKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $parameterTemplate = ParameterTemplate::create(
                [
                    'parameter_template_key' => $key,
                    'parameter_type_id' => $request->json('parameter_type_id'),
                    'parameter' => $request->json('parameter'),
                    'description' => $request->json('description'),
                    'code' => $request->json('code'),
                    'mandatory' => $request->json('mandatory'),
                    'visible' => $request->json('visible'),
                    'visible_in_list' => $request->json('visible_in_list'),
                    'value' => $request->json('value'),
                    'currency' => $request->json('currency'),
                    'position' => 0
                ]
            );

            if (!empty($request->json('options'))) {
                foreach ($request->json('options') as $option) {
                    $parameterTemplate->templateOptions()->create(['label' => $option['label']]);
                }
            }

            $response = ParameterTemplate::with('templateOptions')->findOrFail($parameterTemplate->id);

            return response()->json($response, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter Template'], 500);
        }
    }

    /**
     *
     * @SWG\Put(
     *  path="/parameterTemplate/{parameterId}",
     *  summary="Update a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterTemplate"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Template Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterTemplate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="parameterId",
     *      in="path",
     *      description="Parameter Template Key",
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
     *      description="The updated Parameter Template",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Parameter Template not Found",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Parameter Template",
     *      @SWG\Schema(ref="#/definitions/parameterTemplateErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates the specified ParameterTemplate returning it afterwards.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $parameterTemplate = ParameterTemplate::with('templateOptions')->whereParameterTemplateKey($parameterTemplateKey)->firstOrFail();

            $parameterTemplate->parameter_type_id = $request->json('parameter_type_id');
            $parameterTemplate->parameter = $request->json('parameter');
            $parameterTemplate->description = $request->json('description');
            $parameterTemplate->code = $request->json('code');
            $parameterTemplate->mandatory = $request->json('mandatory');
            $parameterTemplate->visible = $request->json('visible');
            $parameterTemplate->visible_in_list = $request->json('visible_in_list');
            $parameterTemplate->value = $request->json('value');
            $parameterTemplate->currency = $request->json('currency');
            $parameterTemplate->position = $request->json('position');

            $parameterTemplate->save();

            if (!empty($request->json('options'))) {

                $optionsOld = [];
                $optionsNew = [];

                foreach ($parameterTemplate->templateOptions as $option) {
                    $optionsOld[] = $option['id'];
                }

                foreach ($request->json('options') as $option) {
                    if (empty($option['id'])) {
                        $parameterTemplate->templateOptions()->create(['label' => $option['label']]);
                    } else {

                        $optionsNew[] = $option['id'];
                        $optionUpdate = $parameterTemplate->templateOptions()->whereId($option['id'])->first();
                        $optionUpdate->label = $option['label'];
                        $optionUpdate->save();
                    }
                }

                $deleteOptions = array_diff($optionsOld, $optionsNew);

                foreach ($deleteOptions as $deleteId) {
                    $option = $parameterTemplate->templateOptions()->whereId($deleteId)->first();
                    $option->delete();
                }

            }

            $response = ParameterTemplate::with('templateOptions')->findOrFail($parameterTemplate->id);

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update the Parameter Template'], 500);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteParameterTemplate",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/parameterTemplate/{parameterId}",
     *  summary="Delete a Parameter",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ParameterTemplate"},
     *
     * @SWG\Parameter(
     *      name="parameterId",
     *      in="path",
     *      description="Parameter Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteParameterTemplate")
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
     * Deletes the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);

        try {
            $parameterTemplate = ParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firstOrFail();
            $parameterTemplate->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter Template'], 500);
        }
    }

    /**
     * Returns the list of ParameterTemplates with their own Options.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameters(Request $request)
    {

        try {
            $parameterTemplates = ParameterTemplate::whereIn('parameter_template_key', $request->json('parameter_template_keys'))->with(['templateOptions', 'type'])->get();

            return response()->json(['data' => $parameterTemplates], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Parameter Templates with Options'], 500);
        }
    }

    /**
     * Returns the specified ParameterTemplate with its own Options.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function parameterOptions(Request $request, $parameterTemplateKey)
    {
        try {
            $parameterTemplate = ParameterTemplate::with('templateOptions')->whereParameterTemplateKey($parameterTemplateKey);

            return response()->json($parameterTemplate, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Parameter Template with Options'], 500);
        }
    }

    /**
     * Add an Option to the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOption(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['addOption'], $request);

        try {
            $parameterTemplate = ParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firstOrFail();
            $parameterTemplate->options()->create(['label' => $request->json('label')]);

            return response()->json(ParameterTemplate::with('templateOptions')->whereParameterTemplateKey($parameterTemplateKey)->firstOrFail(), 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add Option to the Parameter Template']);
        }
    }

    /**
     * Add an array of Options to the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOptions(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);

        try {
            $parameterTemplate = ParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firtOrFail();

            foreach ($request->json('options') as $option) {
                $options[] = $parameterTemplate->options()->create(['label' => $option['label']]);
            }

            return response()->json(["data" => $options], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add Options to the Parameter Template']);
        }
    }

    /**
     * Edit the specified Option of the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @param $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function editOption(Request $request, $parameterTemplateKey, $optionId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['editOption'], $request);

        try {
            $parameterTemplate = ParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firtOrFail();

            try {
                $option = $parameterTemplate->options()->findOrFail($optionId);
                $option->label = $request->json('label');
                $option->save();

                return response()->json(ParameterTemplate::with('templateOptions')->whereParameterTemplateKey($parameterTemplateKey)->firtOrFail(), 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Option not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Option'], 500);
        }
    }

    /**
     * Remove the specified Option of the specified ParameterTemplate.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @param $optionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOption(Request $request, $parameterTemplateKey, $optionId)
    {
        ONE::verifyToken($request);

        try {
            $parameterTemplate = ParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firtOrFail();

            try {
                $parameterTemplate->options()->findOrFail($optionId)->delete();

                return response()->json('OK', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Option not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to remove Option'], 500);
        }
    }

}
