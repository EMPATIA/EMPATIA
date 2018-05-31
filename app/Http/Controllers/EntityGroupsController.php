<?php

namespace App\Http\Controllers;
use App\PermGroup;
use App\Entity;
use App\EntityGroup;
use App\OrchUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Mockery\CountValidator\Exception;
use ONE;
use App\GroupType;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class EntityGroupsController
 * @package App\Http\Controllers
 */

/**
 *  @SWG\Definition(
 *      definition="entityGroupErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 * @SWG\Tag(
 *   name="Entity Group",
 *   description="Everything about Entity Groups",
 * )
 *
 *  @SWG\Definition(
 *   definition="entityGroupCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"group_type_key", "designation", "name"},
 *           @SWG\Property(property="group_type_key", format="string", type="string"),
 *           @SWG\Property(property="entity_group_key", format="string", type="string"),
 *           @SWG\Property(property="designation", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="entityGroupUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"group_type_key", "designation", "name"},
 *           @SWG\Property(property="group_type_key", format="string", type="string"),
 *           @SWG\Property(property="entity_group_key", format="string", type="string"),
 *           @SWG\Property(property="designation", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="position", format="integer", type="integer")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="entityGroupReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_group_key", format="string", type="string"),
 *           @SWG\Property(property="group_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="parent_group_id", format="integer", type="integer"),
 *           @SWG\Property(property="designation", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="position", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="entityGroupDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class EntityGroupsController extends Controller
{

    protected $keysRequired = [
        'group_type_key',
        'designation',
        'name'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{

            try {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $entityGroups = EntityGroup::with('groupType', 'entityGroup')->whereEntityId($entity->id)->get();

            return response()->json(['data' => $entityGroups], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the EntityGroups list'], 500);
        }

    }

    /**
     *
     * @SWG\Get(
     *  path="/entityGroup/{entity_group_key}",
     *  summary="Show an Entity Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Group"},
     *
     * @SWG\Parameter(
     *      name="entity_group_key",
     *      in="path",
     *      description="Entity Group Key",
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
     *      response="200",
     *      description="Show the Entity Group data",
     *      @SWG\Schema(ref="#/definitions/entityGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Group Type not Found",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Group Type",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Given a $entityGroupKey Returns an entity group
     *
     * @param Request $request
     * @param $entityGroupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $entityGroupKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $entityGroup = EntityGroup::with('groupType', 'entityGroup')->whereEntityGroupKey($entityGroupKey)->firstOrFail();

            return response()->json($entityGroup, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'GroupType not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the GroupType'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/entityGroup",
     *  summary="Create an Entity Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Group"},
     *
     *  @SWG\Parameter(
     *      name="entityGroup",
     *      in="body",
     *      description="Entity Group Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityGroupCreate")
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
     *      description="the newly created Entity Group",
     *      @SWG\Schema(ref="#/definitions/entityGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Model not found",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Entity Group",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
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
        try {
            ONE::verifyToken($request);
            ONE::verifyKeysRequest($this->keysRequired, $request);


            //get entity

            try {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            try {
                $groupType = GroupType::whereGroupTypeKey($request->json('group_type_key'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'GroupType not Found'], 404);
            }


            $parentGroup = EntityGroup::whereEntityGroupKey($request->json('entity_group_key'))->first();

            //increment position if has parent
            $position = is_null($parentGroup) ? 0: $parentGroup->position + 1;

            $key = '';
            do {
                $rand = str_random(32);

                if (!($exists = EntityGroup::whereEntityGroupKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);


            //create new entity group

            $entityGroup = $entity->entityGroups()->create(
                [
                    'entity_group_key' => $key,
                    'group_type_id' => $groupType->id,
                    'parent_group_id' => is_null($parentGroup) ? null:$parentGroup->id,
                    'designation' => $request->json('designation'),
                    'name' => $request->json('name'),
                    'position' => $position
                ]
            );

            //create permissions if $parentGroup != null
            if($parentGroup != null){
                $parentGroupPermissions = PermGroup::where('entity_group_id','=',$parentGroup->id)
                                                    ->where('entity_id','=',$entity->id)
                                                    ->where('cb_id','=',0)
                                                    ->select('code')
                                                    ->get();

                if(!empty($parentGroupPermissions->first())){
                    foreach( $parentGroupPermissions as $permission ){
                        PermGroup::create([
                            'code' => $permission->code,
                            'entity_group_id' => $entityGroup->id,
                            'entity_id' => $entity->id,
                            'cb_id' => 0,
                        ]);
                    }
                }
            }

            return response()->json($entityGroup, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store Entity Group'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/entityGroup/{entity_group_key}",
     *  summary="Update an Entity Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Group"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Entity Group Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityGroupUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="entity_group_key",
     *      in="path",
     *      description="Entity Group Key",
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
     *      response=200,
     *      description="The updated Entity Group",
     *      @SWG\Schema(ref="#/definitions/entityGroupReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Co-Construction not Found",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Co-Construction",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $entityGroupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $entityGroupKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        if (OrchUser::verifyRole($userKey, "admin")){
            try {

                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();

                try {
                    $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'EntityGroup not Found'], 404);
                }

                //Verifies if Entity group belongs to entity
                if($entity->id == $entityGroup->entity_id){

                    $parentGroup = EntityGroup::whereEntityGroupKey($request->json('entity_group_key'))->first();


                    try {
                        $groupType = GroupType::whereGroupTypeKey($request->json('group_type_key'))->firstOrFail();
                    }catch (ModelNotFoundException $e) {
                        return response()->json(['error' => 'GroupType not Found'], 404);
                    }

                    $entityGroup->group_type_id            = $groupType->id;
                    $entityGroup->parent_group_id          = is_null($parentGroup) ? null:$parentGroup->id;
                    $entityGroup->designation              = $request->json('designation');
                    $entityGroup->name                     = $request->json('name');
                    $entityGroup->position                 = is_null($request->json('position')) ? 0:$request->json('position');

                    $entityGroup->save();

                    return response()->json($entityGroup, 200);
                }

            }catch (ModelNotFoundException $e) {

                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to update GroupType"], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * @SWG\Delete(
     *  path="/entityGroup/{entity_group_key}",
     *  summary="Delete a Entity Group",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Group"},
     *
     * @SWG\Parameter(
     *      name="entity_group_key",
     *      in="path",
     *      description="Entity Group Key",
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
     *      @SWG\Schema(ref="#/definitions/entityGroupDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity Group not Found | Entity not Found",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Entity Group",
     *      @SWG\Schema(ref="#/definitions/entityGroupErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $entityGroupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $entityGroupKey)
    {
        ONE::verifyToken($request);

        try{

            $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();

            try {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            //Get collection of groups belonging to an entity
            $entityGroups = EntityGroup::with('groupType', 'entityGroup')->whereEntityId($entity->id)->get();

            //array with ids to delete
            $idsToDelete = $this->getItemsToDelete($entityGroup->id, $entityGroups);

            //delete
            EntityGroup::destroy($idsToDelete);

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Group not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Entity Group'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }


    /**
     *
     * Saves new parent id and positions for dragged/moved Entity Groups
     *
     * @param Request $request
     * @param $entityGroupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, $entityGroupKey)
    {

        ONE::verifyToken($request);
        try {

            $positions = $request->json('positions');

            //changes parent id for dragged/moved groups
            if(!is_null($request->json('parent_group_key'))){
                //saves new parent id
                $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                $parent = EntityGroup::whereEntityGroupKey($request->json('parent_group_key'))->first();
                $entityGroup->parent_group_id = $parent->id;
                $entityGroup->save();
            }else{
                //parent id = null = root
                $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                $entityGroup->parent_group_id = $request->json('parent_group_key');
                $entityGroup->save();
            }

            //Saves newly ordered positions
            foreach($positions as $position => $entityGroupKey){

                $tmp = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                $tmp->position = $position;
                $tmp->save();
            }
            return response()->json($entityGroup, 200);
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to reorder Groups'], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Groups not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *
     * Lists all EntityGroups who has a given GroupType, and Entity
     *
     * @param Request $request
     * @param $groupTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByType(Request $request, $groupTypeKey)
    {
        try{

            $groupType = GroupType::whereGroupTypeKey($groupTypeKey)->first();

            try {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $entityGroups = EntityGroup::with('groupType', 'entityGroup')->whereEntityId($entity->id)->whereGroupTypeId($groupType->id)->get();

            return response()->json(['data' => $entityGroups], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the EntityGroups list'], 500);
        }

    }


    /**
     *
     * Receives an array of groups and a group to delete (id)
     *
     * Returns an array of ids to delete (received group + children)
     *
     * @param $id
     * @param $ar
     * @param null $pid
     * @param array $op
     * @param bool $check
     * @return array
     */
    private function getItemsToDelete($id, $ar, $pid = null, &$op = [], $check = false) {

        foreach( $ar as $item ) {
            if( $item->parent_group_id == $pid ) {

                //prevents inclusion of self Group and its children
                $item->id == $id ? $check = true : false;

                if($check)
                    $op [] =  $item->id;    //saves current item and its position (identation level to be sent to the view and used on select options)

                $this->getItemsToDelete($id, $ar, $item->id, $op, $check);

                if($check)
                    break;

            }
        }

        return $op;
    }

    /**
     * Receives an user key, and entity_group key and saves new user/group relationship
     *
     * @param Request $request
     * @param $entityGroupKey
     * @param $selectedUserKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addEntityGroupUsers (Request $request, $entityGroupKey, $selectedUserKey){


        $userKey = ONE::verifyToken($request);

        if (OrchUser::verifyRole($userKey, "admin")){
            try {

                //get entity
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();

                //get entity group
                try {
                    $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'EntityGroup not Found'], 404);
                }

                //get user
                try {
                    $user = OrchUser::whereUserKey($selectedUserKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'User not Found'], 404);
                }

                //Verifies if Entity group belongs to entity
                if($entity->id == $entityGroup->entity_id){

                    //adds new user to group
                    $response = $entityGroup->users()->attach($user);

                    return response()->json(['message' => 'User Add Ok'], 200);
                }

            }catch (ModelNotFoundException $e) {

                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to update GroupType"], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
     * Receives an user key, and entity_group key and removes given user from group
     *
     * @param Request $request
     * @param $entityGroupKey
     * @param $selectedUserKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeEntityGroupUsers (Request $request, $entityGroupKey, $selectedUserKey){


        $userKey = ONE::verifyToken($request);

        if (OrchUser::verifyRole($userKey, "admin")){
            try {
                //get entity
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();

                //get entity group
                try {
                    $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'EntityGroup not Found'], 404);
                }
                //get user
                try {
                    $user = OrchUser::whereUserKey($selectedUserKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'User not Found'], 404);
                }

                //Verifies if Entity group belongs to entity
                if($entity->id == $entityGroup->entity_id){

                    //remove user from group
                    $response = $entityGroup->users()->detach($user);

                    return response()->json(['message' => 'User Removed Ok'], 200);
                }

            }catch (ModelNotFoundException $e) {

                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to update GroupType"], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }


    /**
     *
     * Lists all entity group users
     *
     * @param Request $request
     * @param $entityGroupKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsers(Request $request, $entityGroupKey){

        $userKey = ONE::verifyToken($request);

            try{

                //GET ALL USERS FROM ENTITY
                $allUsers = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first()->users()->get();

                //get entity
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

                //get entity group
                $entityGroup = EntityGroup::whereEntityGroupKey($entityGroupKey)->firstOrFail();

                //verifies if entity group belongs to given entity
                if ($entityGroup->entity_id == $entity->id){

                    $entityGroupUsers = $entityGroup->users()->get();
                    return response()->json($entityGroupUsers);
                }
                return response()->json(["data" => $allUsers], 200);

            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to list Users"], 500);
            }


        return response()->json(['error' => 'Unauthorized'], 401);

    }


    static public function listUserGroups($userId, $entityId){
        $groups = Entity::find($entityId)->entityGroups()->get();
        $groupUser=[];
        foreach ($groups as $group){
            if($group->users()->whereUserId($userId)->exists())
                $groupUser[] = $group;
        }

        return $groupUser;

    }

}
