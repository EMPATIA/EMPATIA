<?php

namespace App\Http\Controllers;

use App\Module;
use App\Entity;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ModulesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Module",
 *   description="Everything about Modules",
 * )
 *
 *  @SWG\Definition(
 *      definition="moduleErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="moduleCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="moduleReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="module_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="token", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="moduleDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class ModulesController extends Controller
{
    protected $keysRequired = [
        'name',
        'code'
    ];

    /**
     * Request list of all Modules
     * Returns the list of all Modules
     * @return list of all
     */

    public function index()
    {
        try{
            $modules = Module::with('moduleTypes')->get();
            
            return response()->json(['data' => $modules], 200);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Modules'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/module/{module_key}",
     *  summary="Show a Module",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module"},
     *
     * @SWG\Parameter(
     *      name="module_key",
     *      in="path",
     *      description="Module Key",
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
     *      description="Show the Module data",
     *      @SWG\Schema(ref="#/definitions/moduleReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module not Found",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Module",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of one Module
     * Returns the attributes of the Module
     * @param Request $request
     * @param $moduleKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show(Request $request, $moduleKey)
    {
        try{
            $module = Module::whereModuleKey($moduleKey)->firstOrFail();
            return response()->json($module, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Module'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/module",
     *  summary="Create a Module",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module"},
     *
     *  @SWG\Parameter(
     *      name="module",
     *      in="body",
     *      description="Module Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/moduleCreate")
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
     *      description="the newly created Module",
     *      @SWG\Schema(ref="#/definitions/moduleReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Module",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
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
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $key = '';
            $token = '';
            do {
                $rand = str_random(32);
                if (!($exists = Module::whereModuleKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            do {
                $rand = str_random(32);
                if (!($exists = Module::whereToken($rand)->exists())) {
                    $token = $rand;
                }
            } while ($exists);

                $module = Module::create([
                    'module_key'    => $key,
                    'token'         => $token,
                    'name'          => $request->json('name'),
                    'code'          => $request->json('code'),
                ]);

            return response()->json($module, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store Module'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/module/{module_key}",
     *  summary="Update an Module",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Module Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/moduleCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="module_key",
     *      in="path",
     *      description="Module Key",
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
     *      description="The updated Module",
     *      @SWG\Schema(ref="#/definitions/moduleReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Module not Found",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Module",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $moduleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $moduleKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $module = Module::whereModuleKey($moduleKey)->firstOrFail();

            $module->name   = $request->json('name');
            $module->code   = $request->json('code');
            $module->save();

            return response()->json($module, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Module'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/module/{module_key}",
     *  summary="Delete a Module",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Module"},
     *
     * @SWG\Parameter(
     *      name="module_key",
     *      in="path",
     *      description="Module Key",
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
     *      @SWG\Schema(ref="#/definitions/moduleDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module not Found",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Module",
     *      @SWG\Schema(ref="#/definitions/moduleErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $moduleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $moduleKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            Module::whereModuleKey($moduleKey)->firstOrFail()->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Module not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Module'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Sync new and existing Modules in Entity
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
/*    public function registerModule(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $modules= $request->json('modules');
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $entity->modules()->sync($modules);

            return response()->json($entity->modules()->get(), 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Modules in Category'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }*/

    /**
     * Verify if the token exists
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request)
    {
        if ($request->header('X-MODULE-TOKEN')) {
            if (Module::where('token', '=', $request->header('X-MODULE-TOKEN'))->exists()) {

                return response()->json('true', 200);
            } else
                return response()->json('false', 401);
        }
        return response()->json('false', 401);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function moduleWithTypes()
    {
        try{
            $modules = Module::with('moduleTypes')->get();
            return response()->json(['data' => $modules], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Modules'], 500);
        }
    }
}
