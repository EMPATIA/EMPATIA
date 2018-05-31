<?php

namespace App\Http\Controllers;

use App\Entity;
use App\EntityGroup;
use App\EntityPermission;
use App\EntityUser;
use App\Module;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


/**
 * @SWG\Tag(
 *   name="Entity Permission",
 *   description="Everything about Entity Permissions",
 * )
 *
 *  @SWG\Definition(
 *      definition="entityPermissionErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="entityPermissionArrayCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"module_key", "module_type_key","permission_show", "permission_create", "permission_update", "permission_delete"},
 *           @SWG\Property(property="module_key", format="string", type="string"),
 *           @SWG\Property(property="module_type_key", format="string", type="string"),
 *           @SWG\Property(property="permission_show", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_create", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_update", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_delete", format="boolean", type="boolean")
 *        )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="entityPermissionCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_permissions"},
 *           @SWG\Property(property="entity_group_key", format="string", type="string"),
 *           @SWG\Property(property="user_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="entity_permissions",
 *              type="array",
 *              allOf={
 *                  @SWG\Schema(ref="#/definitions/entityPermissionArrayCreate")
 *              }
 *           )
 *        )
 *   }
 * )
 *
 *
 *  @SWG\Definition(
 *   definition="entityPermissionReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="entity_permission_key", format="string", type="string"),
 *           @SWG\Property(property="entity_group_id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_user_id", format="integer", type="integer"),
 *           @SWG\Property(property="module_id", format="integer", type="integer"),
 *           @SWG\Property(property="module_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="permission_show", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_create", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_update", format="boolean", type="boolean"),
 *           @SWG\Property(property="permission_delete", format="boolean", type="boolean"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="updated_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="entityPermissionCreateReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="data",
 *              type="array",
 *              allOf={
 *                  @SWG\Schema(ref="#/definitions/entityPermissionReply")
 *              }
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="entityPermissionListReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *          @SWG\Property(
 *              property="permissions",
 *              type="array",
 *              allOf={
 *                  @SWG\Schema(ref="#/definitions/entityPermissionReply")
 *              }
 *           )
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="entityPermissionDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */


class EntityPermissionController extends Controller
{
    protected $keysRequired = [

    ];


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @SWG\Get(
     *  path="/entityPermissions/list",
     *  summary="List of all Permissions of and Group or User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Permission"},
     *
     * @SWG\Parameter(
     *      name="userKey",
     *      in="query",
     *      description="Type of the permissions required (user/group)",
     *      required=false,
     *      type="string"
     *  ),
     * @SWG\Parameter(
     *      name="entityGroupKey",
     *      in="query",
     *      description="Entity Group Key",
     *      required=false,
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
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="List the Entity Permission data",
     *      @SWG\Schema(ref="#/definitions/entityPermissionListReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Permission not Found",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Permission",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  )
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function index(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)){
                throw new Exception("Entity not found", 30);
            }
            if ( !empty($request->entityGroupKey) && empty($request->userKey) ){

                $entityGroup = EntityGroup::whereEntityGroupKey($request->entityGroupKey)->whereEntityId($entity->id)->firstOrFail();
                $entityGroupId = $entityGroup->id;
                $userId = 0;

            }elseif ( empty($request->entityGroupKey) && !empty($request->userKey) ){
                $entityGroupId = 0;
                $user = $entity->users()->whereUserKey($request->userKey)->whereRole('manager')->first();
                if (empty($user)){
                    throw new Exception("User is not a Manager in this Entity", 30);
                }
                $userId = $user->id;

            }else{
                throw new Exception("Request malformed", 30);
            }

            $entityPermissions = EntityPermission::whereUserId($userId)
                ->whereEntityGroupId($entityGroupId)
                ->whereEntityId($entity->id)
                ->with(['moduleType', 'module'])
                ->get();


            return response()->json(['data' => $entityPermissions], 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Group not Found'], 404);
        }catch (Exception $e) {
            if($e->getCode() == 30){
                return response()->json(['error' => $e->getMessage()], 404);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * @SWG\Get(
     *  path="/entityPermissions/{entity_permission_key}",
     *  summary="Show a Entity Permission",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Permission"},
     *
     *  @SWG\Parameter(
     *      name="entity_permission_key",
     *      in="path",
     *      description="Entity Permission Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Module Token",
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
     *      description="Show the Entity Permission data",
     *      @SWG\Schema(ref="#/definitions/entityPermissionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity Permission not Found or Entity key not valid",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Permission",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  )
     * )
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */


    public function show(Request $request, $key)
    {
        try {
            $entityKey = $request->header('X-ENTITY-KEY', null);
            if (empty($entityKey)){
                throw new Exception("No Entity key", 30);
            }
            $entity = Entity::whereEntityKey($entityKey)->first();
            if (empty($entity)){
                throw new Exception("Entity not found", 30);
            }

            $data = $entity->entityPermissions()->whereEntityPermissionKey($key)->firstOrFail();

            return response()->json( $data,200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Permission not Found'], 404);
        } catch (Exception $e) {
            if($e->getCode() == 30){
                return response()->json(['error' => $e->getMessage()], 404);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/entityPermissions",
     *  summary="Manages the Entity Permission stores and updates it",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Permission"},
     *
     *  @SWG\Parameter(
     *      name="entityPermission",
     *      in="body",
     *      description="Entity Permission Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityPermissionCreate")
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
     *      description="The newly created Entity Permission",
     *      @SWG\Schema(ref="#/definitions/entityPermissionCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Role not found",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Permission",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * Need Security to avoid double entry
     */
    public function store(Request $request)
    {
        if(!env("DEMO_MODE",false)){
            $user_key= ONE::verifyToken($request);
        }
        //ONE::verifyKeysRequest($this->keysRequired['store'], $request);

        try {

            if(env("DEMO_MODE",false)){
                $user_key = $request->user_key;
                $entityKey = $request->entity_key;
                $entity = Entity::whereEntityKey($entityKey)->first();
            }
            else{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            }

            
            if (empty($entity)){
                throw new Exception("Entity not found", 30);
            }

            if ( !empty($request->json('entity_group_key')) && empty($request->json('user_key')) ){

                $entityGroup = EntityGroup::whereEntityGroupKey($request->json('entity_group_key'))->whereEntityId($entity->id)->firstOrFail();
                $entityGroupId = $entityGroup->id;
                $userId = 0;

            }elseif ( empty($request->json('entity_group_key')) && !empty($request->json('user_key')) ){
                $entityGroupId = 0;
                $user = $entity->users()->whereUserKey($request->json('user_key'))->whereRole('manager')->first();
                if (empty($user)){
                    throw new Exception("User is not a Manager in this Entity", 30);
                }
                $userId = $user->id;

            }else{
                throw new Exception("Json malformed", 30);
            }

            $permissions = $request->json('entity_permissions');

            $entityPermissions = [];

            foreach ($permissions as $permission){

                $module = Module::whereModuleKey($permission['module_key'])->first();

                if (empty($module)){
                    throw new Exception("Module not found", 30);
                }
                $moduleType = $module->moduleTypes()->whereModuleTypeKey($permission['module_type_key'])->first();

                if (!empty($moduleType)) {

                    $entityPermissionExists = EntityPermission::whereModuleId($module->id)->whereModuleTypeId($moduleType->id)->whereUserId($userId)->whereEntityGroupId($entityGroupId)->whereEntityId($entity->id)->first();

                    if (!empty($entityPermissionExists)) {

                        $entityPermissionExists->permission_show = isset($permission['permission_show']) ? $permission['permission_show'] : false;
                        $entityPermissionExists->permission_create = isset($permission['permission_create']) ? $permission['permission_create'] : false;
                        $entityPermissionExists->permission_update = isset($permission['permission_update']) ? $permission['permission_update'] : false;
                        $entityPermissionExists->permission_delete = isset($permission['permission_delete']) ? $permission['permission_delete'] : false;
                        $entityPermissionExists->updated_by = $user_key;

                        $entityPermissionExists->save();
                    } else {
                        do {
                            $rand = str_random(32);
                            if (!($exists = EntityPermission::whereEntityPermissionKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $permission = EntityPermission::create(
                            [
                                'entity_permission_key' => $key,
                                'entity_group_id' => $entityGroupId,
                                'entity_id' => $entity->id,
                                'user_id' => $userId,
                                'module_id' => $module->id,
                                'module_type_id' => $moduleType->id,
                                'permission_show' => isset($permission['permission_show']) ? $permission['permission_show'] : false,
                                'permission_create' => isset($permission['permission_create']) ? $permission['permission_create'] : false,
                                'permission_update' => isset($permission['permission_update']) ? $permission['permission_update'] : false,
                                'permission_delete' => isset($permission['permission_delete']) ? $permission['permission_delete'] : false,
                                'created_by' => $user_key,
                                'updated_by' => $user_key
                            ]
                        );
                    }


                    $entityPermissions[] = $permission;
                }
            }

            return response()->json(['data' => $entityPermissions], 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Group or User not found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * @SWG\Delete(
     *  path="/entityPermissions",
     *  summary="Delete a Entity Permissions of an Entity Group or User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Permission"},
     *
     * @SWG\Parameter(
     *      name="userKey",
     *      in="query",
     *      description="User key to delete permissions",
     *      required=false,
     *      type="string"
     *  ),
     * @SWG\Parameter(
     *      name="entityGroup",
     *      in="query",
     *      description="Entity Group Key to delete",
     *      required=false,
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
     * @SWG\Parameter(
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
     *      @SWG\Schema(ref="#/definitions/entityPermissionDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Module not Found",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Module",
     *      @SWG\Schema(ref="#/definitions/entityPermissionErrorDefault")
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
    public function destroy(Request $request)
    {
        ONE::verifyToken($request);
        try{

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)){
                throw new Exception("Entity not found", 30);
            }

            if ( !empty($request->entityGroupKey) && empty($request->userKey) ){

                $entityGroupId = EntityGroup::whereEntityGroupKey($request->entityGroupKey)->whereEntityId($entity->id)->firstOrFail();
                $userId = 0;

            }elseif ( empty($request->entityGroupKey) && !empty($request->userKey) ){
                $entityGroupId = 0;
                $user = $entity->users()->whereUserKey($request->userKey)->whereRole('manager')->first();
                if (empty($user)){
                    throw new Exception("User is not a Manager in this Entity", 30);
                }
                $userId = $user->id;

            }else{
                throw new Exception("Request malformed", 30);
            }

            EntityPermission::whereUserId($userId)->whereEntityGroupId($entityGroupId)->delete();


            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Permission not Found'], 404);
        }catch (Exception $e) {
            if ($e->getCode() == 30){
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    static public function getPermissions($userId, $entityId){
        $userGroups = EntityGroupsController::listUserGroups($userId,$entityId);
        $allPermissions = [];
        /*Get all group permissions*/
        foreach ($userGroups as $userGroup){
            $allPermissions['group'][] = $userGroup->entityPermissions()->get();

        }
        $allPermissions['user'] = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->get();

        $modulesList = Module::with(['moduleTypes'])->get();
        $data = [];
        if(!empty($allPermissions['group'])){
            /*Calculate low permission*/
            if(!empty($allPermissions['user'])){
                foreach ($modulesList as  $moduleList){
                    foreach ($moduleList->moduleTypes as  $moduleType){
                        if(OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->exists()){
                            $userPermissionCreate = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_create;
                            $userPermissionShow = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_show;
                            $userPermissionUpdate = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_update;
                            $userPermissionDelete = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_delete;
                        }else{
                            $userPermissionCreate = false;
                            $userPermissionShow = false;
                            $userPermissionUpdate = false;
                            $userPermissionDelete = false;
                        }

                        /*Verify reply of permission*/
                        $permissionCreate = false;
                        $permissionShow = false;
                        $permissionUpdate = false;
                        $permissionDelete = false;
                        foreach ($allPermissions["group"] as $groupPermissions) {
                            if(!$groupPermissions->where('entity_id',$entityId)->where('module_id',$moduleList->id)->where('module_type_id',$moduleType->id)->isEmpty()){
                                if ($groupPermissions->where('entity_id',$entityId)->where('module_id',$moduleList->id)->where('module_type_id',$moduleType->id)->first()->permission_create)
                                    $permissionCreate = true;

                                if ($groupPermissions->where('entity_id',$entityId)->where('module_id',$moduleList->id)->where('module_type_id',$moduleType->id)->first()->permission_show)
                                    $permissionShow = true;

                                if ($groupPermissions->where('entity_id',$entityId)->where('module_id',$moduleList->id)->where('module_type_id',$moduleType->id)->first()->permission_update)
                                    $permissionUpdate = true;

                                if ($groupPermissions->where('entity_id',$entityId)->where('module_id',$moduleList->id)->where('module_type_id',$moduleType->id)->first()->permission_delete)
                                    $permissionDelete = true;
                            }
                        }

                        $data[$moduleList->code][$moduleType->code]['permission_show'] = ($permissionShow or $userPermissionShow);
                        $data[$moduleList->code][$moduleType->code]['permission_create'] = ($permissionCreate or $userPermissionCreate);
                        $data[$moduleList->code][$moduleType->code]['permission_update'] = ($permissionUpdate or $userPermissionUpdate);
                        $data[$moduleList->code][$moduleType->code]['permission_delete'] = ($permissionDelete or $userPermissionDelete);
                    }
                }
            }
        }else{
            if(!empty($allPermissions['user'])) {
            foreach ($modulesList as $moduleList) {
                foreach ($moduleList->moduleTypes as $moduleType) {
                    if (OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->exists()) {
                        $userPermissionCreate = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_create;
                        $userPermissionShow = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_show;
                        $userPermissionUpdate = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_update;
                        $userPermissionDelete = OrchUser::whereId($userId)->first()->entityPermissions()->whereEntityId($entityId)->whereModuleId($moduleList->id)->whereModuleTypeId($moduleType->id)->first()->permission_delete;
                    } else {
                        $userPermissionCreate = false;
                        $userPermissionShow = false;
                        $userPermissionUpdate = false;
                        $userPermissionDelete = false;
                    }
                    $data[$moduleList->code][$moduleType->code]['permission_show'] = $userPermissionShow;
                    $data[$moduleList->code][$moduleType->code]['permission_create'] = $userPermissionCreate;
                    $data[$moduleList->code][$moduleType->code]['permission_update'] = $userPermissionUpdate;
                    $data[$moduleList->code][$moduleType->code]['permission_delete'] = $userPermissionDelete;
                }
            }
            }
        }

        return $data;
    }


}
