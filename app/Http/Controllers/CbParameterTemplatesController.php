<?php

namespace App\Http\Controllers;

use App\CbParameterTemplate;
use App\Entity;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class CbParameterTemplatesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="PAD Parameter Template",
 *   description="Everything about Pad Parameter Templates",
 * )
 *
 *  @SWG\Definition(
 *      definition="padParameterTemplateErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="padParameterTemplateCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"parameter_template_key"},
 *           @SWG\Property(property="parameter_template_key", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="padParameterTemplateReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="parameter_template_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class CbParameterTemplatesController extends Controller
{

    protected $required = ['parameter_template_key'];

    /**
     * Request a Parameter Templates list.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $cbParameterTemplates = $entity->parametersTemplates()->get();
            
            return response()->json(['data' => $cbParameterTemplates], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve CB Parameter Templates list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/cbParameterTemplate",
     *  summary="Create a PAD Parameter Template",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PAD Parameter Template"},
     *
     *  @SWG\Parameter(
     *      name="padParameterTemplate",
     *      in="body",
     *      description="PAD Parameter Template Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateCreate")
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
     *      description="Entity Key",
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
     *      description="the newly created PAD Parameter Template",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not found",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Parameter template",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Stores a new Parameter template returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required, $request);       

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $parameterTemplate = $entity->parametersTemplates()->create(
                [
                    'parameter_template_key' => $request->json('parameter_template_key')
                ]
            );

            return response()->json($parameterTemplate, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Updates the specified Parameter returning it afterwards.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
/*    public function update(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required, $request);

        try {
            $parameterTemplate = CbParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->findOrFail();
            $parameterTemplate->save();

            return response()->json($parameterTemplate, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update the Parameter template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }*/

    /**
     *  @SWG\Definition(
     *     definition="replyDeletePadParameterTemplate",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/cbParameterTemplate/{parameter_template_key}",
     *  summary="Delete a PAD Parameter Template",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"PAD Parameter Template"},
     *
     * @SWG\Parameter(
     *      name="parameter_template_key",
     *      in="path",
     *      description="PAD Parameter Template Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeletePadParameterTemplate")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Parameter template not Found",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Parameter template",
     *      @SWG\Schema(ref="#/definitions/padParameterTemplateErrorDefault")
     *  )
     * )
     */

    /**
     * Deletes the specified Parameter.
     *
     * @param Request $request
     * @param $parameterTemplateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $parameterTemplateKey)
    {
        ONE::verifyToken($request);

        try {
            $parameterTemplate = CbParameterTemplate::whereParameterTemplateKey($parameterTemplateKey)->firstOrFail();;
            $parameterTemplate->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter template not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
