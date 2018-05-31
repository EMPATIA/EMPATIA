<?php

namespace App\Http\Controllers;
use App\Entity;
use App\EntityGroup;
use App\User;
use App\OrchUser;
use App\Perm;
use App\PermUser;
use App\PermGroup;
use App\EntityGroupUser;
use Illuminate\Http\Request;
use App\Http\Requests;
use Exception;
use App\ComModules\EMPATIA;
use App\Cb;


/**
 * Class LogsController
 * @package App\Http\Controllers
 */
class PermsController extends Controller
{

    protected $keysRequired = [
        'code',
        'cb'
    ];

    /**
     * Requests a array of permissions and a array of permissions
     * Returns a array of permissions and a array of permissions.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            //get all permissions
            $menusPermissions = Perm::where('cb','=',0)->get();

            //get user name
            $userName = User::where('user_key','=',$request->user_key)->select('name')->get()->first();

            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();

            //get all user permissions
            $userPerm = self::getUserPermissions($request->user_key,$entityId->id);

            return response()->json(['data'=>['menusPermissions' => $menusPermissions,'userPermissions' => $userPerm, 'userName' => $userName->name ]], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the menus permissions list'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    public function update(Request $request,$userId)
    {
        try {
            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();

            if($request->permission){

                $permUser = PermUser::onlyTrashed()
                                    ->where('code','=', $request->code)
                                    ->where('user_id','=', $userId)
                                    ->where('entity_id','=', $entityId->id)
                                    ->where('cb_id','=', 0)
                                    ->first();

                if(isset($permUser->code)){
                     PermUser::onlyTrashed()
                                ->where('code','=', $request->code)
                                ->where('user_id','=', $userId)
                                ->where('entity_id','=', $entityId->id)
                                ->where('cb_id','=', 0)
                                ->restore();
                }
                else{
                    PermUser::create([
                        'code' => $request->code,
                        'user_id' => $userId,
                        'entity_id' => $entityId->id,
                        'cb_id' => 0,
                    ]);
                }
            }
            else{
                $permUser = PermUser::where('code','=', $request->code)
                                    ->where('user_id','=', $userId)
                                    ->where('entity_id','=', $entityId->id)
                                    ->where('cb_id','=', 0)
                                    ->first();

                if(!is_null($permUser))
                     PermUser::where('code','=', $request->code)
                        ->where('user_id','=', $userId)
                        ->where('entity_id','=', $entityId->id)
                        ->where('cb_id','=', 0)
                        ->delete();
            }

        }catch (Exception $e){
            return response()->json(['error' => 'Failed to update user permission'], 500);
        }
    }

    /**
     * Requests a array of entity groups permissions and a array of userPermissions
     * Returns a array of groupsPermissions and a array of userPermissions.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups(Request $request)
    {
        try {

            $groupsPermissions = Perm::where('cb','=',0)->get();

            $entityGroupName = EntityGroup::where('entity_group_key','=',$request->entityGroupKey)
                                            ->select('name')
                                            ->get()
                                            ->first();

            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();

            //get all entity groups permissions
            $groupPerm = self::getGroupsPermissions($request->entityGroupKey,null,$entityId->id);

            return response()->json(['data'=>['groupsPermissions' => $groupsPermissions,'groupPermissions' => $groupPerm , 'groupName' => $entityGroupName->name ]], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the groups permissions list'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    public function updateGroupPermission(Request $request)
    {
        try {
            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();

            if($request->permission){

                $permGroup = PermGroup::onlyTrashed()
                                        ->where('code','=', $request->code)
                                        ->where('entity_group_id','=', $request->entityGroupId)
                                        ->where('entity_id','=', $entityId->id)
                                        ->where('cb_id','=', 0)
                                        ->first();

                if(isset($permGroup->code)){
                    PermGroup::onlyTrashed()
                            ->where('code','=', $request->code)
                            ->where('entity_group_id','=', $request->entityGroupId)
                            ->where('entity_id','=', $entityId->id)
                            ->where('cb_id','=', 0)
                            ->restore();
                }
                else{
                    PermGroup::create([
                        'code' => $request->code,
                        'entity_group_id' => $request->entityGroupId,
                        'entity_id' => $entityId->id,
                        'cb_id' => 0,
                    ]);
                }
            }
            else{
                $permGroup = PermGroup::where('code','=', $request->code)
                                        ->where('entity_group_id','=', $request->entityGroupId)
                                        ->where('entity_id','=', $entityId->id)
                                        ->where('cb_id','=', 0)
                                        ->first();

                if(!is_null($permGroup))
                    PermGroup::where('code','=', $request->code)
                                ->where('entity_group_id','=', $request->entityGroupId)
                                ->where('entity_id','=', $entityId->id)
                                ->where('cb_id','=', 0)
                                ->delete();

            }

        }catch (Exception $e){
            return response()->json(['error' => 'Failed to update group permission'], 500);
        }
    }
    /**
     * Requests user
     * Returns a array of all userPermissions.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUserPermission(Request $request)
    {
        try {
            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();
            //get all user permissions
            $userPerm = self::getUserPermissions($request->user['user_key'],$entityId->id);

            $user = OrchUser::where('user_key','=',$request->user['user_key'])->get()->first();

            //get this user entities groups
            $userEntityGroupKey = $user->entityGroups()->select('entity_group_key')->get();

            //get all entity groups permissions
            $userGroupPerm = self::getGroupsPermissions(null,$userEntityGroupKey,$entityId->id);

            $allPermissions = array_unique(array_merge( $userPerm['code'],$userGroupPerm));

            return response()->json($allPermissions, 200);
        }catch (Exception $e){
            return response()->json(['error' => 'Failed to get user permission'], 500);
        }
    }

    public function getUserPermissions($userKey,$entityId)
    {
        $userPermissions = PermUser::join('users','users.id','perm_users.user_id')
                                    ->where('users.user_key','=',$userKey)
                                    ->where('entity_id','=',$entityId)
                                    ->where('cb_id','=',0)
                                    ->select('user_id as id','code')
                                    ->get();

        if(!empty($userPermissions->first())){
            $userPerm['id']=$userPermissions[0]->id;
            $userPerm['code']=[];
            foreach($userPermissions as $permission){
                array_push($userPerm['code'],$permission->code);
            }
        }
        else{
            //get user id
            $user = User::where('user_key','=',$userKey)
                ->select('id')
                ->get()
                ->first();

            $userPerm['id'] = $user->id;
            $userPerm['code']=[];
        }
        return $userPerm;
    }

    public function getGroupsPermissions($entityGroupKey = null,$collection = null,$entityId)
    {
        if(!is_null($entityGroupKey)){

            $entityGroupPermissions = PermGroup::join('entity_groups','entity_groups.id','perm_groups.entity_group_id')
                                                ->where('entity_groups.entity_group_key','=',$entityGroupKey)
                                                ->where('perm_groups.entity_id','=',$entityId)
                                                ->where('perm_groups.cb_id','=',0)
                                                ->select('perm_groups.entity_group_id as id','code')
                                                ->get();

            if(!empty($entityGroupPermissions->first())){
                $groupPerm['id']=$entityGroupPermissions[0]->id;
                $groupPerm['code']=[];
                foreach($entityGroupPermissions as $permission){
                    array_push($groupPerm['code'],$permission->code);
                }
            }
            else{
                //get entityGroup id
                $entityGroup = EntityGroup::where('entity_group_key','=',$entityGroupKey)
                                            ->select('id')
                                            ->get()
                                            ->first();

                $groupPerm['id'] = $entityGroup->id;
                $groupPerm['code']=[];
            }
            return $groupPerm;
        }
        else{
            $groupPerm=[];
            foreach($collection as $entityGroupKey){
                $entityGroupPermissions = PermGroup::join('entity_groups','entity_groups.id','perm_groups.entity_group_id')
                                                    ->where('entity_groups.entity_group_key','=',$entityGroupKey->entity_group_key)
                                                    ->where('perm_groups.entity_id','=',$entityId)
                                                    ->where('perm_groups.cb_id','=',0)
                                                    ->select('perm_groups.entity_group_id as id','code')
                                                    ->get();

                if(!empty($entityGroupPermissions->first())){
                    foreach($entityGroupPermissions as $permission){
                        array_push($groupPerm,$permission->code);
                    }
                }
            }
            return $groupPerm;
        }
    }


    /**
     * Requests a array of permissions and a array of cbPermissions
     * Returns a array of permissions and a array of cbPermissions.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbPermissions(Request $request)
    {
        try {
            //get all cb permissions
            $permissions = Perm::where('cb','=',1)->get();

            $entityId = Entity::where('entity_key','=',$request->entityKey)->select('id')->get()->first();
            $cbId = Cb::where('cb_key','=',$request->cbKey)->select('id')->get()->first();

            //if is a user - get user cb permissions
            if($request->userKey != null){
                $userPerm = self::getUserCbPermissions($cbId->id, $request->userKey, $entityId->id);

                //get user name
                $userName = User::where('user_key','=',$request->userKey)->select('name')->get()->first();

                return response()->json(['data'=>['permissions' => $permissions, 'userPermissions' => $userPerm, 'groupPermissions' => null, 'name' => $userName->name ]], 200);

            }
            //if is a group - get group cb permissions
            else{
                $groupPerm = self::getGroupCBPermissions($cbId->id, $request->groupKey, $entityId->id);

                //get group name
                $groupName = EntityGroup::where('entity_group_key','=',$request->groupKey)->select('name')->get()->first();

                return response()->json(['data'=>['permissions' => $permissions, 'userPermissions' => null, 'groupPermissions' => $groupPerm, 'name' => $groupName->name]], 200);
            }

        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the cbs permissions list'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }


    public function getUserCbPermissions($cbId,$userKey,$entityId)
    {
        $userPermissions = PermUser::join('users','users.id','perm_users.user_id')
                                    ->where('users.user_key','=',$userKey)
                                    ->where('entity_id','=',$entityId)
                                    ->where('cb_id','=',$cbId)
                                    ->select('user_id as id','code')
                                    ->get();

        if(!empty($userPermissions->first())){
            $userPerm['id']=$userPermissions[0]->id;
            $userPerm['code']=[];
            foreach($userPermissions as $permission){
                array_push($userPerm['code'],$permission->code);
            }
        }
        else{
            //get user id
            $user = User::where('user_key','=',$userKey)
                ->select('id')
                ->get()
                ->first();

            $userPerm['id'] = $user->id;
            $userPerm['code']=[];
        }
        return $userPerm;
    }

    public function getUserCBsPermissions(Request $request)
    {
        try{
            $cbId = Cb::where('cb_key','=',$request->cbKey)->select('id')->get()->first()->id;
            $entityId = Entity::where('entity_key','=',$request->entityKey)->select('id')->get()->first()->id;
            $userKey = $request->userKey;

            $userPermissions = PermUser::join('users','users.id','perm_users.user_id')
                                        ->where('users.user_key','=',$userKey)
                                        ->where('entity_id','=',$entityId)
                                        ->where('cb_id','=',$cbId)
                                        ->select('user_id as id','code')
                                        ->get();
            $userPerm=[];
            if(!empty($userPermissions->first())){

                foreach($userPermissions as $permission){
                    array_push($userPerm,$permission->code);
                }
            }

            //get user cbs from this user entities groups
            $user = OrchUser::where('user_key','=',$userKey)->get()->first();

            //get this user entities groups
            $userEntityGroupId = $user->entityGroups()->select('id')->get();

            if(!empty($userEntityGroupId->first())){
                foreach($userEntityGroupId as $userEntityGroup){

                    $groupCbs = PermGroup::join('cbs','cbs.id','perm_groups.cb_id')
                        ->where('entity_group_id','=',$userEntityGroup->id)
                        ->where('entity_id','=',$entityId)
                        ->where('cb_id','=',$cbId)
                        ->select('code')
                        ->get();

                    if(!empty($groupCbs->first())){
                        foreach($groupCbs as $cb){
                            array_push($userPerm,$cb->code);
                        }
                    }
                }
            }
            $userPerms = array_unique($userPerm);

            return $userPerms;
        }
        catch(Exception $e) {
        return response()->json(['error' => 'Failed to retrieve the user cbs permissions'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    public function getGroupCBPermissions($cbId,$entityGroupKey,$entityId)
    {
        $entityGroupPermissions = PermGroup::join('entity_groups','entity_groups.id','perm_groups.entity_group_id')
                                            ->where('entity_groups.entity_group_key','=',$entityGroupKey)
                                            ->where('perm_groups.entity_id','=',$entityId)
                                            ->where('perm_groups.cb_id','=',$cbId)
                                            ->select('perm_groups.entity_group_id as id','code')
                                            ->get();

        if(!empty($entityGroupPermissions->first())){
            $groupPerm['id']=$entityGroupPermissions[0]->id;
            $groupPerm['code']=[];
            foreach($entityGroupPermissions as $permission){
                array_push($groupPerm['code'],$permission->code);
            }
        }
        else{
            //get entityGroup id
            $entityGroup = EntityGroup::where('entity_group_key','=',$entityGroupKey)
                ->select('id')
                ->get()
                ->first();

            $groupPerm['id'] = $entityGroup->id;
            $groupPerm['code']=[];
        }
        return $groupPerm;
    }

    public function updateCbPermissions(Request $request)
    {
        try {

            $entityId = Entity::where('entity_key','=',$request->entity_Key)->select('id')->get()->first();
            $cbId = Cb::where('cb_key','=',$request->cbKey)->select('id')->get()->first();

            if($request->permission){
                if(!empty($request->groupId)){
                    $permGroup = PermGroup::onlyTrashed()
                        ->where('code','=', $request->code)
                        ->where('entity_group_id','=', $request->groupId)
                        ->where('entity_id','=', $entityId->id)
                        ->where('cb_id','=', $cbId->id)
                        ->first();

                    if(isset($permGroup->code)){
                        PermGroup::onlyTrashed()
                            ->where('code','=', $request->code)
                            ->where('entity_group_id','=', $request->groupId)
                            ->where('entity_id','=', $entityId->id)
                            ->where('cb_id','=', $cbId->id)
                            ->restore();
                    }
                    else {
                        PermGroup::create([
                            'code' => $request->code,
                            'entity_group_id' => $request->groupId,
                            'entity_id' => $entityId->id,
                            'cb_id' => $cbId->id,
                        ]);
                    }
                }
                else{
                    $permUser = PermUser::onlyTrashed()
                        ->where('code','=', $request->code)
                        ->where('user_id','=', $request->userId)
                        ->where('entity_id','=', $entityId->id)
                        ->where('cb_id','=',  $cbId->id)
                        ->first();

                    if(isset($permUser->code)){
                        PermUser::onlyTrashed()
                            ->where('code','=', $request->code)
                            ->where('user_id','=', $request->userId)
                            ->where('entity_id','=', $entityId->id)
                            ->where('cb_id','=',  $cbId->id)
                            ->restore();
                    }
                    else{
                        PermUser::create([
                            'code' => $request->code,
                            'user_id' => $request->userId,
                            'entity_id' => $entityId->id,
                            'cb_id' =>  $cbId->id,
                        ]);
                    }
                }
            }
            else{
                if(!empty($request->groupId)){
                    $permGroup = PermGroup::where('code','=', $request->code)
                        ->where('entity_group_id','=', $request->groupId)
                        ->where('entity_id','=', $entityId->id)
                        ->where('cb_id','=', $cbId->id)
                        ->first();

                    if(!is_null($permGroup))
                        PermGroup::where('code','=', $request->code)
                            ->where('entity_group_id','=', $request->groupId)
                            ->where('entity_id','=', $entityId->id)
                            ->where('cb_id','=', $cbId->id)
                            ->delete();
                }
                else{
                    $permUser = PermUser::where('code','=', $request->code)
                        ->where('user_id','=', $request->userId)
                        ->where('entity_id','=', $entityId->id)
                        ->where('cb_id','=', $cbId->id)
                        ->first();

                    if(!is_null($permUser))
                        PermUser::where('code','=', $request->code)
                            ->where('user_id','=', $request->userId)
                            ->where('entity_id','=', $entityId->id)
                            ->where('cb_id','=', $cbId->id)
                            ->delete();
                }

            }

        }catch (Exception $e){
            return response()->json(['error' => 'Failed to update cb permission'], 500);
        }
    }


    public function getUserCBs(Request $request)
    {
        try{
            $entityId = Entity::where('entity_key','=',$request->entityKey)->select('id')->get()->first();
            $userId = User::where('user_key','=',$request->user['user_key'])->select('id')->get()->first();
            $getCbs=[];

            //get  user cbs from perm_users table
            $cbs = PermUser::join('cbs','cbs.id','perm_users.cb_id')
                            ->where('user_id','=',$userId->id)
                            ->where('entity_id','=',$entityId->id)
                            ->where('cb_id','!=',0)
                            ->select('cb_key')
                            ->get();

            if(!empty($cbs->first())){
                foreach($cbs as $cb){
                    array_push($getCbs,$cb->cb_key);
                }
            }

            //get user cbs from this user entities groups
            $user = OrchUser::where('user_key','=',$request->user['user_key'])->get()->first();

            //get this user entities groups
            $userEntityGroupId = $user->entityGroups()->select('id')->get();

            if(!empty($userEntityGroupId->first())){
                foreach($userEntityGroupId as $userEntityGroup){

                    $groupCbs = PermGroup::join('cbs','cbs.id','perm_groups.cb_id')
                                            ->where('entity_group_id','=',$userEntityGroup->id)
                                            ->where('entity_id','=',$entityId->id)
                                            ->where('cb_id','!=',0)
                                            ->select('cb_key')
                                            ->get();

                    if(!empty($groupCbs->first())){
                        foreach($groupCbs as $cb){
                            array_push($getCbs,$cb->cb_key);
                        }
                    }
                }
            }

            $userCbs = array_unique($getCbs);

            return $userCbs;
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the user cbs'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }
}

