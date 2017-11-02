<?php

namespace App\Http\Controllers;

use App\ConfigurationPermissionType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;




class ConfigurationPermissionTypesController extends Controller
{
  public function index(Request $request)
  {
      try {
          $configurationPermissionTypes = ConfigurationPermissionType::all();

          foreach ($configurationPermissionTypes as $configurationPermissionType) {
              if (!($configurationPermissionType->translation($request->header('LANG-CODE')))) {
                  if (!$configurationPermissionType->translation($request->header('LANG-CODE-DEFAULT')))
                      return response()->json(['error' => 'No translation found'], 404);
              }
          }
          return response()->json(['data' => $configurationPermissionTypes], 200);
      } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Configuration Permission Type list'], 500);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
  }


  public function show(Request $request, $id)
  {
    try {
        $configurationPermissionType = ConfigurationPermissionType::findOrFail($id);

        if (!($configurationPermissionType->translation($request->header('LANG-CODE')))) {
            if (!$configurationPermissionType->translation($request->header('LANG-CODE-DEFAULT')))
                return response()->json(['error' => 'No translation found'], 404);
        }

        return response()->json($configurationPermissionType, 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Configuration Permission Type not Found'], 404);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
  }
}
