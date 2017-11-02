<?php

namespace App\Http\Controllers;


use App\ConfigurationPermission;
use App\ConfigurationPermissionType;
use App\Cb;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Collection;
use Exception;



class ConfigurationPermissionsController extends Controller
{
  public function index(Request $request)
  {
      try {
          $configurationPermissions = ConfigurationPermission::all();

          foreach ($configurationPermissions as $configurationPermission) {
              if (!($configurationPermission->translation($request->header('LANG-CODE')))) {
                  if (!$configurationPermission->translation($request->header('LANG-CODE-DEFAULT')))
                      return response()->json(['error' => 'No translation found'], 404);
              }
          }
          return response()->json(['data' => $configurationPermissions], 200);
      } catch (Exception $e) {

          return response()->json(['error' => 'Failed to retrieve the Configuration Permissions list'], 500);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
  }


  public function show(Request $request, $id)
  {
      try {
          $configurationPermission = ConfigurationPermission::findOrFail($id);

          if (!($configurationPermission->translation($request->header('LANG-CODE')))) {
              if (!$configurationPermission->translation($request->header('LANG-CODE-DEFAULT')))
                  return response()->json(['error' => 'No translation found'], 404);
          }

          return response()->json($configurationPermission, 200);
      } catch (ModelNotFoundException $e) {
          return response()->json(['error' => 'Configuration Permission not Found'], 404);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
  }

  public function configurationPermissions(Request $request, $cbKey){
    try {
        $cb = Cb::whereCbKey($cbKey)->firstOrFail();
        $cbConfigPermission=$cb->cb_ConfigurationsPermission()->get();

        $data['cbConfigPermission'] = $cbConfigPermission;
        $data['cbId'] = $cb->id;


        return response()->json($data, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Configurations not Found'], 404);
    } catch (Exception $e) {
        return response()->json($e->getMessage());
        return response()->json(['error' => 'Failed to retrieve the configurations'], 500);
    }
      return response()->json(['error' => 'Unauthorized'], 401);
  }


  public function insertConfigurationPermission(Request $request){

    try {
        $userKey = ONE::verifyToken($request);
        $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();
        if($request->levels!=null){
        foreach ($request->levels as $key => $value) {
          $cb->cb_ConfigurationsPermission()->attach([$key => ['value' => json_encode($value), 'created_by'=>$userKey]]);
        }
      }
    return response()->json('ok', 200);
  }
    catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Insert Configuration Permission not Found'], 404);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to retrieve the insert configuration permission'], 500);
    }
      return response()->json(['error' => 'Unauthorized'], 401);
}


public function updateConfigurationPermission(Request $request){

  try {
    $userKey = ONE::verifyToken($request);

    $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();

    $cbConfigPermission=$cb->cb_ConfigurationsPermission()->get();

        if($request->levels!=null){
            foreach ($request->levels as $key => $value) {
              $cb->cb_ConfigurationsPermission()->sync($key,['value' => json_encode($value), 'created_by'=>$userKey]);
              $cb->cb_ConfigurationsPermission()->updateExistingPivot($key,['value' => json_encode($value), 'created_by'=>$userKey]);
            }

            $array_configPermissions=[];

            foreach ($request->levels as $key => $value) {
              $configPermission=collect($cb->cb_ConfigurationsPermission()->where('config_permission_id','=',$key)->get()->toArray());
              $array_configPermissions[$key]=$configPermission;
              if(!($configPermission->isNotEmpty())){
                 $cb->cb_ConfigurationsPermission()->attach([$key => ['value' => json_encode($value), 'created_by'=>$userKey]]);
              }
            }
      }
      else{
         foreach ($cbConfigPermission as $key => $value) {
          $cb->cb_ConfigurationsPermission()->sync(null,['value' => null, 'created_by'=>null]);
        }

      }


    return response()->json('ok', 200);
}
  catch (ModelNotFoundException $e) {
      return response()->json(['error' => 'Update Configuration Permission not Found'], 404);
  } catch (Exception $e) {
      return response()->json(['error' => 'Failed to retrieve the update configuration permission'], 500);
  }
    return response()->json(['error' => 'Unauthorized'], 401);
  }
}
