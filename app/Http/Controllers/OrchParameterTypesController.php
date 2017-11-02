<?php

namespace App\Http\Controllers;

use App\OrchParameterType;
use App\ParameterTypeVoteConfig;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

/**
 * Class ParameterTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Parameter Type",
 *   description="Everything about Parameter Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="parameterTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="parameterTypeCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="user_parameter", format="boolean", type="boolean"),
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="parameterTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="user_parameter", format="boolean", type="boolean"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="parameterTypeDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class OrchParameterTypesController extends Controller
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
            $types = OrchParameterType::all();

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Parameter Types'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/parameterTypes/{parameter_type_id}",
     *  summary="Show a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter Type"},
     *
     *  @SWG\Parameter(
     *      name="parameter_type_id",
     *      in="path",
     *      description="Parameter Type Id",
     *      required=true,
     *      type="integer"
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
     *      @SWG\Schema(ref="#/definitions/parameterTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
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
            $type = OrchParameterType::findOrFail($typeId);

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'ParameterType not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Parameter Type']);
        }
    }

    /**
     * @SWG\Post(
     *  path="/parameterTypes",
     *  summary="Create a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter Type"},
     *
     *  @SWG\Parameter(
     *      name="ParameterType",
     *      in="body",
     *      description="Parameter Type Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterTypeCreate")
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
     *      description="the newly createdParameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeReply")
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
     *      description="Parameter Type not found",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
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
            $type = OrchParameterType::create([
                'name'              => $request->json('name'),
                'code'              => $request->json('code'),
                'user_parameter'    => $request->json('user_parameter'),
            ]);

            return response()->json($type, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter Type'], 500);
        }
    }

    /**
     * @SWG\Put(
     *  path="/parameterTypes/{parameter_type_id}",
     *  summary="Update a Parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter Type"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Parameter Type Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/parameterTypeCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="parameter_type_id",
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
     *      description="The updated Parameter Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeReply")
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
            $type = OrchParameterType::findOrFail($typeId);
            $type->name             = $request->json('name');
            $type->code             = $request->json('code');
            $type->user_parameter   = $request->json('user_parameter');
            $type->save();

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Parameter Type'], 500);
        }
    }

    /**
     * @SWG\Delete(
     *  path="/parameterTypes/{parameter_type_id}",
     *  summary="Delete a parameter Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Parameter Type"},
     *
     * @SWG\Parameter(
     *      name="parameter_type_id",
     *      in="path",
     *      description="parameter Type Id",
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
     *      @SWG\Schema(ref="#/definitions/parameterTypeDeleteReply")
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
     *      description="Failed to delete mMdule Type",
     *      @SWG\Schema(ref="#/definitions/parameterTypeErrorDefault")
     *  )
     * )
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
            OrchParameterType::destroy($typeId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter Type'], 500);
        }
    }

    public function getVoteConfigParameterTypes(Request $request, $voteConfigKey)
    {
        ONE::verifyToken($request);

        try {
            $types = ParameterTypeVoteConfig::whereGeneralConfigTypeKey($voteConfigKey)->get();

            return response()->json(['data' => $types],200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Parameter Types'], 500);
        }
    }
}
