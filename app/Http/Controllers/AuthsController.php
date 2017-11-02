<?php

namespace App\Http\Controllers;

use App\Entity;
use App\OrchUser;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AuthsController extends Controller
{

    /**
     * Return the informations of
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){

        $userKey = ONE::verifyToken($request);
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if(empty($entity)){
                if(OrchUser::verifyRole($userKey, "admin"))
                {
                    $user = OrchUser::where('user_key', '=',$userKey)->first();
                    return response()->json($user, 200);
                }
                return response()->json(["Error" => 'Entity invalid'], 400);
            }

            if(OrchUser::verifyRole($userKey, "admin"))
            {
                $user = OrchUser::where('user_key', '=',$userKey)->first();
                return response()->json($user, 200);
            }
            elseif(OrchUser::verifyRole($userKey, "manager",$entity->id) || OrchUser::verifyRole($userKey, "user",$entity->id))
            {

                $user = $entity->users()->with(['roles' => function ($query) use ($entity) {
                    $query->whereEntityId($entity->id)->with('permissions');}] )->whereUserKey($userKey)->first();
                $user->getRole($entity->id);

                $user['status'] = $user->pivot->status;
                unset($user->pivot);

                if (!empty($user) && $user->role == 'manager'){
                    $user['permissions'] = EntityPermissionController::getPermissions($user->id, $entity->id);
                }

                if (empty($user)){
                    return response()->json(["Error" => 'The user does not belong to the entity'], 200);
                }
                return response()->json($user, 200);
            } else
                return response()->json(["status"=>"needsEntityMigration"], 200);
        }catch(ModelNotFoundException $e) {
            return response()->json(["Error" => 'Entity invalid'], 400);
        }catch(Exception $e) {
            return response()->json(["Error" => $e->getMessage()], 500);
        }
        return response()->json(["error" => "Unauthorized"], 401);
    }

    /**
     * Check the role of one user
     * Return of the user
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRole(Request $request, $userKey){
        
        $user = OrchUser::where('user_key', '=',$userKey)->firstOrFail();

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();

            if(empty($entity)){
                $userKeyAuth = ONE::verifyToken($request);
                if(OrchUser::verifyRole($userKey, "admin")){
                    if(OrchUser::verifyRole($userKeyAuth, "admin")) {
                        return response()->json(["role" => 'admin'], 200);
                    }
                }
                return response()->json(['error' => "Checking the user role"], 500);
            }

            $userKeyAuth = ONE::verifyToken($request);
            if(OrchUser::verifyRole($userKey, "admin")){
                if(OrchUser::verifyRole($userKeyAuth, "admin")) {
                    return response()->json(["role" => 'admin'], 200);
                }
            }

            elseif(OrchUser::verifyRole($userKey, "manager",$entity->id) || OrchUser::verifyRole($userKey, "user",$entity->id)){

                $user = OrchUser::where('user_key', '=',$userKey)->firstOrFail();
                $user->getRole($entity->id);

                $userAuth = OrchUser::where('user_key', '=',$userKeyAuth)->firstOrFail();

                if(OrchUser::verifyRole($userKeyAuth, "admin")) {
                    return response()->json(["role" => $user->role], 200);
                }
                elseif(OrchUser::verifyRole($userKeyAuth, "manager",$entity->id)){
                        return response()->json(["role" => $user->role], 200);

                }
            }
        }catch(Exception $e) {
            return response()->json(['error' => "Checking the user role"], 500);
        }
        return response()->json(["error" => "Unauthorized"], 401);
    }
}
