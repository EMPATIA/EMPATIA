<?php

namespace App\Http\Controllers;

use App\AccessType;
use App\Entity;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AccessTypesController extends Controller
{
    /**
     * Returns the list of access types
     * Authentication required -> Admin or Manager
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            if (OrchUser::verifyRole($userKey, 'admin') || OrchUser::verifyRole($userKey, 'manager', $entity->id)) {
                $accessTypes = AccessType::whereIn('id', [1,2])->get();
                return response()->json(['data' => $accessTypes], 200);
            }

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Access Types list'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);

    }
}
