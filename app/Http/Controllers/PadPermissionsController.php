<?php

namespace App\Http\Controllers;

use App\Cb;
use App\PadPermission;
use App\ParameterOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\One\One;
use Exception;

class PadPermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();

            if(!empty($request->groupKey)){
                $cbPermissions = $cb->padPermissions()->whereGroupKey($request->groupKey)->with('parameterOptions')->get()->toArray();
            }

            if(!empty($request->userKey)){
                $cbPermissions = $cb->padPermissions()->whereUserKey($request->userKey)->with('parameterOptions')->get()->toArray();
            }

            return response()->json(['data' => $cbPermissions], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Permissions'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();
            $permissions = $request->permissions;

            $cbPermissions = $cb->padPermissions()->whereGroupKey($request->groupKey)->with('parameterOptions')->get()->toArray();
            if (!empty($cbPermissions)) {

                foreach ($cbPermissions as $cbPermission) {

                    foreach ($cbPermission['parameter_options'] as $option) {

                        if (!in_array($option['id'], $request->optionsId)) {
                            foreach ($request->permissions as $permissions) {
                                $padPermission = $cb->padPermissions()->create([
                                    'user_key'          => isset($request->userKey) ? $request->userKey : null,
                                    'group_key'         => isset($request->groupKey) ? $request->groupKey : null,
                                    'permission_show'   => isset($permissions['permission_show']) ? $permissions['permission_show'] : 0,
                                    'permission_create' => isset($permissions['permission_create']) ? $permissions['permission_create'] : 0,
                                    'permission_update' => isset($permissions['permission_update']) ? $permissions['permission_update'] : 0,
                                    'permission_delete' => isset($permissions['permission_delete']) ? $permissions['permission_delete'] : 0,
                                    'created_by'        => $userKey,
                                    'updated_by'        => $userKey,
                                ]);

                                $padPermission->parameterOptions()->sync([$permissions[$option['id']]]);
                            }
                        } else {

                            $permission = PadPermission::find($cbPermission['id']);

                            $permission['user_key'] = isset($request->userKey) ? $request->userKey : null;
                            $permission['group_key'] = isset($request->groupKey) ? $request->groupKey : null;
                            $permission['permission_show'] = isset($permissions[$option['id']]['permission_show']) ? $permissions[$option['id']]['permission_show'] : 0;
                            $permission['permission_create'] = isset($permissions[$option['id']]['permission_create']) ? $permissions[$option['id']]['permission_create'] : 0;
                            $permission['permission_update'] = isset($permissions[$option['id']]['permission_update']) ? $permissions[$option['id']]['permission_update'] : 0;
                            $permission['permission_delete'] = isset($permissions[$option['id']]['permission_delete']) ? $permissions[$option['id']]['permission_delete'] : 0;
                            $permission['updated_by'] = $userKey;


                            $permission->save();
                        }

                    }
                }

            } else {
                foreach ($request->permissions as $permissions) {

//                foreach($permissions['permission'] as $key => $permission){
                    $padPermission = $cb->padPermissions()->create([
                        'user_key'          => isset($request->userKey) ? $request->userKey : null,
                        'group_key'         => isset($request->groupKey) ? $request->groupKey : null,
                        'permission_show'   => isset($permissions['permission_show']) ? $permissions['permission_show'] : 0,
                        'permission_create' => isset($permissions['permission_create']) ? $permissions['permission_create'] : 0,
                        'permission_update' => isset($permissions['permission_update']) ? $permissions['permission_update'] : 0,
                        'permission_delete' => isset($permissions['permission_delete']) ? $permissions['permission_delete'] : 0,
                        'created_by'        => $userKey,
                        'updated_by'        => $userKey,
                    ]);

                    $padPermission->parameterOptions()->sync([$permissions['optionId']]);

                }
            }

            return response()->json("Ok", 201);
        }catch(Exception $e){
            return response()->json($e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getOptionsPermissions(Request $request){

        try{

            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();

            $permissions = $cb->padPermissions()->whereUserKey($request->userKey)->with('parameterOptions')->get()->toArray();

            $optionsPermissions = [];
            foreach($permissions as $permission){
                foreach($permission['parameter_options'] as $option){
                    $index = (integer) $option['pivot']['parameter_option_id'];
                    $optionsPermissions[$index] = [
                        'parameter_option_id' => $option['pivot']['parameter_option_id'],
                        'show' => $permission['permission_show'],
                        'create' => $permission['permission_create'],
                        'update' => $permission['permission_update'],
                        'delete' => $permission['permission_delete'],
                    ];
                }
            }

            return response()->json($optionsPermissions, 200);
        }catch(Exception $e){
            return response()->json($e->getMessage());
        }
    }

}
