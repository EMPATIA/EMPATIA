<?php

namespace App\Http\Controllers;

use App\One\One;
use App\Permission;
use App\Role;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class PermissionsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Permission",
 *   description="Everything about Permissions",
 * )
 *
 *  @SWG\Definition(
 *      definition="permissionErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="permissionCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "module", "api", "create", "view", "update", "delete"},
 *           @SWG\Property(property="role_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="module", format="string", type="string"),
 *           @SWG\Property(property="api", format="string", type="string"),
 *           @SWG\Property(property="create", format="boolean", type="boolean"),
 *           @SWG\Property(property="view", format="boolean", type="boolean"),
 *           @SWG\Property(property="update", format="boolean", type="boolean"),
 *           @SWG\Property(property="delete", format="boolean", type="boolean")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="permissionReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="role_id", format="integer", type="integer"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="module", format="string", type="string"),
 *           @SWG\Property(property="api", format="string", type="string"),
 *           @SWG\Property(property="create", format="boolean", type="boolean"),
 *           @SWG\Property(property="view", format="boolean", type="boolean"),
 *           @SWG\Property(property="update", format="boolean", type="boolean"),
 *           @SWG\Property(property="delete", format="boolean", type="boolean"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="permissionDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class PermissionsController extends Controller
{
    protected $keysRequired = [
        'store' => ['code','module','api','create','view','update','delete'],
        'update' => ['code','module','api','create','view','update','delete'],
        'addPermission' => ['code','module','api']
    ];


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json(['data' => $permissions], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Permissions'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/permissions/{permission_id}",
     *  summary="Show a Permission",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Permission"},
     *
     *  @SWG\Parameter(
     *      name="permission_id",
     *      in="path",
     *      description="Permission Id",
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
     *      description="Show the Permission data",
     *      @SWG\Schema(ref="#/definitions/permissionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Permission not Found",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Permission",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  )
     * )
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $permission = Permission::whereId($id)->firstOrFail();
            return response()->json($permission, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Permission'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/permissions",
     *  summary="Create a Permission",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Permission"},
     *
     *  @SWG\Parameter(
     *      name="permission",
     *      in="body",
     *      description="Permission Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/permissionCreate")
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
     *      description="the newly created Permission",
     *      @SWG\Schema(ref="#/definitions/permissionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Role not found",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Permission",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired['store'], $request);

        try {
            $role = Role::whereRoleKey($request->json('role_key'))->firstOrFail();
            $permission = $role->permissions()->create(
                [
                    'code' => $request->json('code'),
                    'module' => $request->json('module'),
                    'api' => $request->json('api'),
                    'create' => $request->json('create'),
                    'view' => $request->json('view'),
                    'update' => $request->json('update'),
                    'delete' => $request->json('delete'),
                ]
            );
            return response()->json($permission, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Role not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Permission'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
     * @SWG\Put(
     *  path="/permissions/{permission_id}",
     *  summary="Update an Permission",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Permission"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Permission Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/permissionCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="permission_id",
     *      in="path",
     *      description="Permission Id",
     *      required=true,
     *      type="integer"
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
     *      description="The updated Permission",
     *      @SWG\Schema(ref="#/definitions/permissionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Permission not Found",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Permission",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired['update'], $request);

        try{
            $permission = Permission::findOrFail($id);

            $permission->code = $request->json('code');
            $permission->module = $request->json('module');
            $permission->api = $request->json('api');
            $permission->create = $request->json('create');
            $permission->view = $request->json('view');
            $permission->update = $request->json('update');
            $permission->delete = $request->json('delete');
            $permission->save();

            return response()->json($permission, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Permission'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/permissions/{permission_id}",
     *  summary="Delete a Permission",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Permission"},
     *
     * @SWG\Parameter(
     *      name="permission_id",
     *      in="path",
     *      description="Permission Id",
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
     *      @SWG\Schema(ref="#/definitions/permissionDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Permission not Found",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Permission",
     *      @SWG\Schema(ref="#/definitions/permissionErrorDefault")
     *  )
     * )
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);
        try{
            Permission::destroy($id);
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Permission'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Add Permission to the specified Role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired['addPermission'], $request);

        try {
            $role = Role::whereRoleKey($request->json('role_key'))->first();
            if(empty($role)){
                return response()->json(['error' => 'Role not found']);
            }
            $permission = Permission::whereRoleId($role->id)->whereCode($request->json('code'))->first();
            if(empty($permission)){
                $permission = $role->permissions()->create(
                    [
                        'code' => $request->json('code'),
                        'module' => $request->json('module'),
                        'api' => $request->json('api'),
                        'create' => ($request->json('create') != null)? $request->json('create'):0,
                        'view' => ($request->json('view') != null)? $request->json('view'):0,
                        'update' => ($request->json('update') != null)? $request->json('update'):0,
                        'delete' => ($request->json('delete') != null)? $request->json('delete'):0
                    ]
                );
                return response()->json($permission, 201);
            }
            else{
                $permission->create = ($request->json('create') != null)? $request->json('create'):$permission->create;
                $permission->view = ($request->json('view') != null)? $request->json('view'):$permission->view;
                $permission->update = ($request->json('update') != null)? $request->json('update'):$permission->update;
                $permission->delete = ($request->json('delete') != null)? $request->json('delete'):$permission->delete;
                $permission->save();
                return response()->json($permission, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not found']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add permission']);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
