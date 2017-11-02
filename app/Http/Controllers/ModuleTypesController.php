<?php

namespace App\Http\Controllers;

use App\Module;
use App\ModuleType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class moduleTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Module Type",
 *   description="Everything about module Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="moduleTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="moduleType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "name"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string")
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="moduleTypeUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *      )
 *   }
 * )
 *
 *
 *  @SWG\Definition(
 *   definition="moduleTypeCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"module_key", "module_types"},
 *           @SWG\Property(property="module_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="module_types",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/moduleType")
 *           )
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="moduleTypeShowReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="module_type_key", format="string", type="string"),
 *           @SWG\Property(property="module_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(
 *              property="module",
 *              type="object",
 *              allOf={
 *                  @SWG\Schema(ref="#/definitions/moduleReply")
 *              }
 *           ),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="moduleTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="module_type_key", format="string", type="string"),
 *           @SWG\Property(property="module_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="moduleTypeDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class ModuleTypesController extends Controller
{
    protected $keysRequired = [
        'module_key',
        'code'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $moduleTypes = Module::whereModuleKey($request->json('module_key'))
                ->first()
                ->moduleTypes()
                ->get();

            return response()->json(['data' => $moduleTypes], 200);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Module Types'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/moduleType/{module_type_key}",
     *  summary="Show a Module Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module Type"},
     *
     *  @SWG\Parameter(
     *      name="module_type_key",
     *      in="path",
     *      description="Module Type Key",
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
     *      description="Show the Module Type data",
     *      @SWG\Schema(ref="#/definitions/moduleTypeShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module Type not Found",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Module Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $moduleTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $moduleTypeKey)
    {
        try{
            $moduleType = ModuleType::with('module')->whereModuleTypeKey($moduleTypeKey)->firstOrFail();
            return response()->json($moduleType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module Type not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Module Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/moduleType",
     *  summary="Create a Module Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module Type"},
     *
     *  @SWG\Parameter(
     *      name="ModuleType",
     *      in="body",
     *      description="Module Type Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/moduleTypeCreate")
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
     *      description="the newly createdModule Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module Type not found",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Module Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
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
        ONE::verifyKeysRequest(['module_key', 'module_types'], $request);

        try{
            $module = Module::whereModuleKey($request->json('module_key'))->firstOrFail();

            foreach ($request->json('module_types') as $moduleType) {

                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = ModuleType::whereModuleTypeKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                if (isset($moduleType['code']) && isset($moduleType['name'])){
                    $module->moduleTypes()->create([
                        'module_type_key' => $key,
                        'code' => $moduleType['code'],
                        'name' => $moduleType['name']
                    ]);
                }
            }
            $moduleTypes = $module->with('moduleTypes')->findOrFail($module->id);

            return response()->json($moduleTypes, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store Module Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/moduleType/{module_type_key}",
     *  summary="Update a Module Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module Type"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Module Type Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/moduleTypeUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="module_type_key",
     *      in="path",
     *      description="Module Type Key",
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
     *      description="The updated Module Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Module Type not Found",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Module Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $moduleTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $moduleTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['name', 'code'], $request);

        try{
            $moduleType = ModuleType::whereModuleTypeKey($moduleTypeKey)->firstOrFail();

            $moduleType->name     = $request->json('name');
            $moduleType->code     = $request->json('code');
            $moduleType->save();

            return response()->json($moduleType, 200);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store Module Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/moduleType/{module_type_key}",
     *  summary="Delete a module Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module Type"},
     *
     * @SWG\Parameter(
     *      name="module_type_key",
     *      in="path",
     *      description="module Type Key",
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
     *      @SWG\Schema(ref="#/definitions/moduleTypeDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module Type not Found",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete mMdule Type",
     *      @SWG\Schema(ref="#/definitions/moduleTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $moduleTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $moduleTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            ModuleType::whereModuleTypeKey($moduleTypeKey)->firstOrFail()->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Module Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypes(Request $request)
    {
        try{
            $module = Module::whereModuleKey($request->json('module_key'))->firstOrFail();
            $moduleTypes = ModuleType::whereModuleId($module->id)->get();

            return response()->json(['data' => $moduleTypes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Module Types'], 500);
        }
    }
}
