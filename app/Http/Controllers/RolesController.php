<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Role;
use Illuminate\Http\Request;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class RolesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Role",
 *   description="Everything about Roles",
 * )
 *
 *  @SWG\Definition(
 *      definition="roleErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="role",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_id", "role_key", "name", "code", "description"},
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="role_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string")
 *       )
 *   }
 * )
 *
 */

class RolesController extends Controller
{
    protected $keysRequired = [
        'name',
        'code',
        'description'
    ];

    /**
     * @param \App\Http\Controllers\Request|Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $entity = ONE::getEntity($request);

            if($entity){
                $roles = Role::whereEntityId($entity->id)->get();
                return response()->json(['data' => $roles], 200);
            }
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Roles'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/role/{role_key}",
     *  summary="Show a Role",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Role"},
     *
     * @SWG\Parameter(
     *      name="role_key",
     *      in="path",
     *      description="Role Key",
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
     *      response="200",
     *      description="Show the Role data",
     *      @SWG\Schema(ref="#/definitions/role")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Role not Found",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Role",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param $roleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($roleKey)
    {
        try {
            $role = Role::with('permissions')->whereRoleKey($roleKey)->firstOrFail();
            return response()->json($role, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Role'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/role",
     *  summary="Creation of a Role",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Role"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Role data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/role")
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
     *      description="the newly created role",
     *      @SWG\Schema(ref="#/definitions/role")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Role",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param \App\Http\Controllers\Request|Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            do {
                $rand = str_random(32);

                if (!($exists = Role::whereRoleKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $role = $entity->roles()->create(
                [
                    'role_key' => $key,
                    'name' => $request->json('name'),
                    'code' => $request->json('code'),
                    'description' => $request->json('description'),
                ]
            );
            return response()->json($role, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Role'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/role/{role_key}",
     *  summary="Update a Role",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Role"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Role Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/role")
     *  ),
     *
     * @SWG\Parameter(
     *      name="role_key",
     *      in="path",
     *      description="Role Key",
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
     *      description="The updated Role",
     *      @SWG\Schema(ref="#/definitions/role")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Role not Found",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Role",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $roleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $roleKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $role = Role::whereRoleKey($roleKey)->firstOrFail();

            $role->name         = $request->json('name');
            $role->code         = $request->json('code');
            $role->description  = $request->json('description');
            $role->save();

            return response()->json($role, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Role'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteRole",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/role/{role_key}",
     *  summary="Delete a Role",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Role"},
     *
     * @SWG\Parameter(
     *      name="role_key",
     *      in="path",
     *      description="Role Key",
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
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteRole")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Role not Found",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Role",
     *      @SWG\Schema(ref="#/definitions/roleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $roleKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $roleKey)
    {
        ONE::verifyToken($request);

        try{
            $role = Role::whereRoleKey($roleKey)->firstOrFail();
            $role->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Role'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
