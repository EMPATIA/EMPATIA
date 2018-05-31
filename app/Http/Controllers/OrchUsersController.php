<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Notify;
use App\Entity;
use App\EntityDomainName;
use App\EntityGroup;
use App\EntityUser;
use App\EntityVatNumber;
use App\GeographicArea;
use App\Jobs\AutoUpdateUserLoginLevels;
use App\LevelParameter;
use App\OrchParameterType;
use App\ParameterUserType;
use App\Role;
use App\Site;
use App\OrchUser;
use App\User;
use App\UserLoginLevel;
use App\LoginLevel;
use App\LoginLevelParameter;
use App\Cb;
use App\One\One;
use App\UserParameter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Collection;
use App\UserAnonymization;
use App\UserAnonymizationRequest;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="User",
 *   description="Everything about Users",
 * )
 *
 *  @SWG\Definition(
 *      definition="userErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="user",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"user_key", "admin", "geographic_area_id"},
 *           @SWG\Property(property="user_key", format="string", type="string"),
 *           @SWG\Property(property="admin", format="boolean", type="boolean"),
 *           @SWG\Property(property="geographic_area_id", format="string", type="string")
 *       )
 *   }
 * )
 *
 */

class OrchUsersController extends Controller
{
    protected $keysRequired = [
        'user_key'
    ];

    /**
     * List of Roles
     * @var array
     */
    protected $roles = [
        "ADMIN" => "admin",
        "MANAGER" => "manager",
        "USER" => "user"
    ];

    /**
     * Request list of Users
     * Returns the list of all Users
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try {
            /*Verify if List is for a specific Role*/
            if (!empty($request->json('role'))) {
                /*List of all admins, has to be admin to get the list of admins*/
                if ($request->json('role') == $this->roles["ADMIN"] && OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                    $admins = OrchUser::whereAdmin(1)->get();
                    return response()->json(["data" => $admins], 200);
                } /*List of all managers*/
                elseif ($request->json('role') == $this->roles["MANAGER"]) {
                    if(!empty($request->entityKey)){
                        $entity = Entity::with(['users' => function ($query) {
                            $query->whereRole($this->roles["MANAGER"]);
                        }])->whereEntityKey($request->entityKey)->first();
                    }
                    else{
                        $entity = Entity::with(['users' => function ($query) {
                            $query->whereRole($this->roles["MANAGER"]);
                        }])->whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                    }
                    if (empty($entity)) {
                        $entity = Entity::with(['users' => function ($query) {
                            $query->whereRole($this->roles["MANAGER"]);
                        }])->whereEntityKey($request->json('entity_key'))->first();
                        if (empty($entity)) {
                            return response()->json(['error' => 'Entity not Found'], 404);
                        }
                    }
                    /*Verify if user is admin*/
                    if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                        $managers = $entity->users;
                        return response()->json(["data" => $managers], 200);
                    } /*Verify if user is manager and what entity belongs*/
                    elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id)) {

                        $managers = $entity->users;
                        return response()->json(["data" => $managers], 200);
                    }
                } /*List of all users*/
                elseif ($request->json('role') == $this->roles["USER"]) {

                    $entity = Entity::with(['users' => function ($query) {
                        $query->whereRole($this->roles["USER"]);
                    }])->whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                    if (empty($entity)) {
                        $entity = Entity::with(['users' => function ($query) {
                            $query->whereRole($this->roles["USER"]);
                        }])->whereEntityKey($request->json('entity_key'))->first();
                        if (empty($entity)) {
                            return response()->json(['error' => 'Entity not Found'], 404);
                        }
                    }
                    /*Verify if user is admin*/
                    if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                        $users = $entity->users;
                        return response()->json(["data" => $users], 200);
                    } /*Verify if user is manager/user and what entity belongs*/
                    elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id) || OrchUser::verifyRole($userKey, $this->roles["USER"], $entity->id)) {
                        $users = $entity->users;
                        return response()->json(["data" => $users], 200);
                    }
                }
            } else {
                if(is_null($request->header('X-ENTITY-KEY'))){
                    if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                        $allUsers = OrchUser::all();
                        return response()->json(["data" => $allUsers], 200);
                    }
                } else {
                    $allUsers = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first()->users()->get();
                    return response()->json(["data" => $allUsers], 200);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Users list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Request list of Users with a specific status.
     * Returns the list of all Users
     *
     * @param Request $request
     * @param $status
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function indexWithStatus(Request $request, $status)
    {
        $userKey = ONE::verifyToken($request);
        try {
            /*Verify if List is for a specific Role*/
            $entity = Entity::with(['users' => function ($query) use ($status) {
                $query->whereRole($this->roles["USER"])->whereStatus($status);
            }])->whereEntityKey($request->header('X-ENTITY-KEY'))->first();

            /*Verify if user is admin*/
            if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                $users = $entity->users;
                return response()->json(["data" => $users], 200);
            } /*Verify if user is manager/user and what entity belongs*/
            elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id) || OrchUser::verifyRole($userKey, $this->roles["USER"], $entity->id)) {
                $users = $entity->users;
                return response()->json(["data" => $users], 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/user/{user_key}",
     *  summary="Show a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     * @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
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
     *      description="Show the User data",
     *      @SWG\Schema(ref="#/definitions/user")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of one User
     * Returns the attributes of the User
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $userKeyRequest
     */
    public function show(Request $request, $userKey)
    {
        $userKeyAuth = ONE::verifyToken($request);

        try {
            $entity = ONE::getEntity($request);

            if (empty($entity)) {
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
                if($user->admin){
                    $user['moderated'] = true;
                }else{
                    $user['moderated'] = false;
                }
                return response()->json($user, 200);
            } else {
                $sites = $entity->sites()->get();
            }

            $user = OrchUser::with(['entities' => function ($query) use ($entity) {
                $query->whereEntityId($entity->id);
            }, 'roles' => function ($query) use ($entity) {
                $query->whereEntityId($entity->id);
            }])->whereUserKey($userKey)->firstOrFail();
            $user->getRole($entity->id);

            $user['moderated'] = true;

            foreach ($sites as $site){
                $levelModeration = $site->levelParameters()->whereManualVerification(1)->first();
                if(!empty($levelModeration)){
                    $userModerateLevel = $user->levelParameters()->first();
                    if(!empty($userModerateLevel)){
                        $level = $userModerateLevel->position;
                    }
                    else{
                        $level = 0;
                    }
                    if($levelModeration->position != $level){
                        $user['moderated'] = false;
                        $user['moderation_site_key'] = $site->key;
                    }
                }
            }

            if (OrchUser::verifyRole($userKeyAuth, $this->roles["ADMIN"])) {
                return response()->json($user, 200);
            }elseif(OrchUser::verifyRole($userKeyAuth, $this->roles["MANAGER"], $entity->id)){
                if(OrchUser::verifyRole($userKeyAuth, $this->roles["USER"], $entity->id) || OrchUser::verifyRole($userKeyAuth, $this->roles["MANAGER"], $entity->id)) {
                    return response()->json($user, 200);
                }
            }elseif (OrchUser::verifyRole($userKeyAuth, $this->roles["USER"], $entity->id)) {
                $user['status'] = $user->entities()->find($entity->id)->pivot->status;
                if ($userKeyAuth == $userKey) {
                    return response()->json($user, 200);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/user",
     *  summary="Creation of a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/user")
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
     *      description="the newly created user",
     *      @SWG\Schema(ref="#/definitions/user")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new User in the database
     * Return the Attributes of the User created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        if(env("DEMO_MODE",false)!=true){
            ONE::verifyKeysRequest($this->keysRequired, $request);
        }

        \Log::info(">>>> ERROR: 0a");

        try {
            $entity = ONE::getEntity($request);
            if ((empty($entity)) && ($request->json('role') != $this->roles["ADMIN"])) {
                return response()->json(['error' => 'Entity not found'], 404);
            }

            \Log::info(">>>> ERROR: 0");

    	    $emailTags["name"] = (User::whereUserKey($request->get("user_key"))->first()->name??"");

            \Log::info(">>>> ERROR: 0b");
            if ($request->has('geographic_area_key')) {
                $geographicArea = GeographicArea::whereGeoKey($request->json('geographic_area_key'))->first();
                $geographicAreaId = $geographicArea->id ?? null;
            } else
                $geographicAreaId = null;


            \Log::info(">>>> ERROR: 1");

            if (!empty($request->json('role'))) {

                \Log::info(">>>> ERROR: 2");

                if(env("DEMO_MODE",false)!=true){
                    $userKey = ONE::verifyToken($request);
                }
                else{
                    $userKey = $request->user_key;
                }

                if ($request->json('role') === $this->roles["ADMIN"] && OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                    $user = OrchUser::firstOrCreate(['user_key' => $request->json('user_key')]);
                    $user->admin = 1;
                    $user->geographic_area_id = $geographicAreaId;
                    $user->save();

                    //Sends Notification Emails to the selected groups of users
                    ONE::sendNotificationEmail($request,'new_user_registration',$emailTags);
                    return response()->json($user, 201);
                } elseif (($request->json('role') === $this->roles["MANAGER"] || $request->json('role') === $this->roles["USER"]) && OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id) || env("DEMO_MODE",false)!=false) {

                    \Log::info(">>>> ERROR: 2a");


                    if (OrchUser::whereUserKey($request->json("user_key"))->exists()) {
                        $user = OrchUser::whereUserKey($request->json("user_key"))->first();
                    } else {
                        $user = $entity->users()->firstOrCreate(['user_key' => $request->json('user_key')]);
                        $user->admin = 0;
                        $user->geographic_area_id = $geographicAreaId;
                        $user->save();
                    }

                    if ($user->entities()->whereEntityId($entity->id)->exists()){
                        $user->entities()->updateExistingPivot($entity->id, ['role' => $request->json('role')]);
                    } else
                        $user->entities()->attach($entity->id, ['role' => $request->json('role')]);

                    //Sends Notification Emails to the selected groups of users
                    ONE::sendNotificationEmail($request,'new_user_registration',$emailTags);
                    return response()->json($user, 201);

                } elseif (($request->json('role') === $this->roles["MANAGER"] || $request->json('role') === $this->roles["USER"]) && OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id)) {

                    \Log::info(">>>> ERROR: 2b");

                    if (OrchUser::whereUserKey($request->json("user_key"))->exists()){
                        $user = OrchUser::whereUserKey($request->json("user_key"))->first();

                    } else {
                        $user = $entity->users()->firstOrCreate(['user_key' => $request->json('user_key')]);
                        $user->admin = 0;
                        $user->geographic_area_id = $geographicAreaId;
                        $user->save();
                    }

                    if ($user->entities()->whereEntityId($entity->id)->exists()){
                        $user->entities()->updateExistingPivot($entity->id, ['role' => $request->json('role')]);
                    }
                    else
                        $user->entities()->attach($entity->id, ['role' => $request->json('role')]);

                    //Sends Notification Emails to the selected groups of users
                    ONE::sendNotificationEmail($request,'new_user_registration',$emailTags);

                    return response()->json($user, 201);
                }

            } else {

                \Log::info(">>>> ERROR: 3");


                if (!empty($request->header('X-ENTITY-KEY'))) {
                    $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                    $user = $entity->users()->firstOrCreate(['user_key' => $request->json('user_key')]);
                    $user->admin = 0;
                    $user->geographic_area_id = $geographicAreaId;
                    $user->save();

                    $user->entities()->updateExistingPivot($entity->id, ['role' => $this->roles["USER"], 'status' => 'registered']);

                    //Sends Notification Emails to the selected groups of users
                    ONE::sendNotificationEmail($request,'new_user_registration',$emailTags);
                    return response()->json($user, 201);
                }
            }

        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['error' => 'Trying to store a user that already exists','e'=>$e->getMessage()], 409);
            }
            return response()->json(['error' => 'Failed to store User'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store the User'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/user/{user_key}",
     *  summary="Update a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/user")
     *  ),
     *
     * @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
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
     *      description="The updated User",
     *      @SWG\Schema(ref="#/definitions/user")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing User
     * Return the Attributes of the User Updated
     * @param Request $request
     * @param $userKeyRequest
     * @return mixed
     */
    public function update(Request $request, $userKeyRequest)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $user = OrchUser::whereUserKey($userKeyRequest)->firstOrFail();
            $entity = ONE::getEntity($request);
            if ((empty($entity)) && ($request->json('role') != $this->roles["ADMIN"])) {
                return response()->json(['error' => 'Entity not found'], 404);
            }

            if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                if (!empty($request->json('role'))) {
                    if ($request->json('role') == $this->roles["ADMIN"]) {
                        if (OrchUser::verifyRole($userKeyRequest, $this->roles["MANAGER"], $entity->id ?? null) || OrchUser::verifyRole($userKeyRequest, $this->roles["USER"], $entity->id ?? null)) {
                            $user->entities()->detach($entity->id);
                        }
                        $user->admin = 1;
                        $user->save();

                        return response()->json($user, 200);
                    } elseif ($request->json('role') == $this->roles["MANAGER"] || $request->json('role') == $this->roles["USER"]) {

                        if (OrchUser::verifyRole($userKeyRequest, $this->roles["MANAGER"], $entity->id) || OrchUser::verifyRole($userKeyRequest, $this->roles["USER"], $entity->id)) {
                            $user->entities()->updateExistingPivot($entity->id, ['role' => $request->json('role')]);
                            $user->admin = 0;
                            $user->save();
                        } else {
                            $user->entities()->attach($entity->id, ['role' => $request->json('role')]);
                            $user->admin = 0;
                            $user->save();
                        }
                    }
                    return response()->json($user, 200);
                }
            } elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id)) {

                $userAuth = OrchUser::whereUserKey($userKey);

                if (!empty($request->json('role'))) {

                    if (OrchUser::verifyRole($userKeyRequest, $this->roles["MANAGER"], $entity->id) || OrchUser::verifyRole($userKeyRequest, $this->roles["USER"], $entity->id)) {
                        $user->entities()->updateExistingPivot($entity->id, ['role' => $request->json('role')]);
                    } else {
                        $user->entities()->attach($entity->id, ['role' => $request->json('role')]);
                    }

                    return response()->json($user, 200);
                }
            } elseif ($userKeyRequest == $userKey) {
                $geographicArea = GeographicArea::whereGeoKey($request->json('geographic_area_key'))->firstOrFail();

                $user->geographic_area_id = $geographicArea->id;
                $user->save();

            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Fail Updating User'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteUser",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/user/{user_key}",
     *  summary="Delete a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     * @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
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
     *  @SWG\Parameter(
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteUser")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Delete existing User
     * @param Request $request
     * @param $userKeyRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $userKeyRequest)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = Entity::with(['users' => function ($query) use ($userKeyRequest) {
                $query->whereUserKey($userKeyRequest);
            }])->whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)) {
                $entity = Entity::with(['users' => function ($query) use ($userKeyRequest) {
                    $query->whereUserKey($userKeyRequest);
                }])->whereEntityKey($request->json('entity_key'))->first();
                if (empty($entity) || empty($entity->users)) {
                    if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                        $user = OrchUser::whereUserkey($userKeyRequest)->firstOrFail();
                        $user->delete();
                        return response()->json('Ok', 200);
                    }
                    return response()->json(['error' => 'Entity not found'], 404);
                }
            }
            $user = $entity->users[0];

            if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                $this->deleteUserParameters($entity,$user);
                $user->entities()->detach($entity->id);
                $user->delete();
                return response()->json('Ok', 200);

            } elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id)) {
                $this->deleteUserParameters($entity,$user);
                $user->entities()->detach($entity->id);
                $user->delete();
                return response()->json('Ok', 200);

            } elseif (OrchUser::verifyRole($userKey, $this->roles["USER"], $entity->id)) {
                if ($userKey == $userKeyRequest) {
                    $user->entities()->detach($entity->id);
                    return response()->json('Ok', 200);
                }
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete User'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listRole(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = ONE::getEntity($request);
            if (empty($entity)) {
                return response()->json(['error' => 'Entity not found'], 404);
            }

            if (OrchUser::verifyRole($userKey, $this->roles["ADMIN"])) {
                return response()->json(["data" => [$this->roles["MANAGER"], $this->roles["USER"]]], 200);
            } elseif (OrchUser::verifyRole($userKey, $this->roles["MANAGER"], $entity->id)) {
                return response()->json(["data" => [$this->roles["MANAGER"], $this->roles["USER"]]], 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Roles'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /*    public function listEntityRoles(Request $request, $userKeyRequest)
        {
            $userKey = ONE::verifyToken($request);
            try {
                $entity = ONE::getEntity($request);
                if (empty($entity)) {
                    return response()->json(['error' => 'Entity not found'], 404);
                }

                $user = OrchUser::with(['roles' => function ($query) use ($entity) {
                    $query->whereEntityId($entity->id)->with('permissions');
                }])->whereUserKey($userKey)->first();
                $roles = $user->roles;

                return response()->json(['data' => $roles], 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve Entity Roles of user'], 500);
            }

            return response()->json(['error' => 'Unauthorized'], 401);
        }*/


    /**
     * @param Request $request
     * @param $userKeyRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEntityUserRole(Request $request, $userKeyRequest)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = ONE::getEntity($request);

            if (empty($entity)) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $user = OrchUser::with(['roles' => function ($query) use ($entity) {
                $query->whereEntityId($entity->id);
            }])->whereUserKey($userKeyRequest)->first();
            if (empty($user)) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            $newRole = Role::whereRoleKey($request->json('role_key'))->first();
            if (empty($newRole)) {
                return response()->json(['error' => 'Role not Found'], 404);
            }
            //TODO:one user has multiple roles
            $role = null;
            foreach ($user->roles as $userRole) {
                if ($userRole->entity_id == $newRole->entity_id) {
                    $role = $userRole;
                    break;
                }
            }
            if ($role != null) {
                $user->roles()->detach($role->id);
            }
            $user->roles()->attach($newRole->id);

            $response = $user->with(['roles' => function ($query) use ($entity) {
                $query->whereEntityId($entity->id);
            }])->whereUserKey($userKeyRequest)->first();
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Entity User Roles'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        if(env("DEMO_MODE",false)!=true){
            $userKey = ONE::verifyToken($request);
            ONE::verifyKeysRequest(['status'], $request);
        }

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $user = is_null($request->json('user_key')) ? $entity->users()->whereUserKey($userKey)->firstOrFail() : $entity->users()->whereUserKey($request->json('user_key'))->firstOrFail();
            $user->entities()->updateExistingPivot($entity->id, ['status' => $request->json('status')]);

            $id = $user->id;
            $user = $entity->users()->findOrFail($id);
            $user['status'] = $user->pivot->status;
            unset($user->pivot);

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to change status'], 500);
        }
    }

    /**
     * Updates the Level of the user
     *
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserLevel(Request $request, $userKey)
    {
        ONE::verifyToken($request);
        try {
            try{
                $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Site not Found'], 404);
            }

            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

//          Parameters filled by the user
            $userParameters = $request->json('user_parameters');

//          All the levels of specified the site with the necessary parameters
            $levelParameters = $site->levelParameters()->with('parameterUserTypes')->orderBy('position', 'asc')->get();

            $currentLevel = $user->levelParameters()->whereSiteId($site->id)->first();

            if (!is_null($currentLevel)){
                $currentLevel = 0;
            }
            $currentLevelObject = null;
//          Verifies the level of the user

            foreach ($levelParameters as $levelParameter){
                if ($levelParameter->position == $currentLevel+1){
                    if (!empty($levelParameter->parameterUserTypes->toArray())){
                        if(count(array_intersect($levelParameter->parameterUserTypes->where('mandatory',1)->pluck('parameter_user_type_key')->toArray(), $userParameters)) == count($levelParameter->parameterUserTypes->where('mandatory',1)->pluck('parameter_user_type_key')->toArray())){

                            $currentLevel = $levelParameter->position;
                            $currentLevelObject = $levelParameter;
                        } else {
                            break;
                        }
                    }
                }
            }

            if (!empty($currentLevelObject)){
                $user->levelParameters()->sync([$currentLevelObject->id]);
                return response()->json($user->levelParameters()->first(), 200);
            } else {
                return response()->json($user->levelParameters()->first(), 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update User Level'], 500);
        }
    }

    /**
     * Deletes the Level of the user
     *
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserLevel(Request $request, $userKey)
    {
        ONE::verifyToken($request);
        try {
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();

            $user->levelParameters()->whereSiteId($site->id)->detach();
            return response()->json(["result"=>true]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete User Level'], 500);
        }
    }

    /**
     * Returns the level of the user in the specified site
     *
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLevel(Request $request, $userKey)
    {
//        ONE::verifyToken($request);
        try {
            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }
            try{
                $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Site not Found'], 404);
            }
            $levelParameters = $site->levelParameters()->pluck('id');

            $userLevel = $user->levelParameters()->whereUserId($user->id)->whereIn('level_parameter_id', $levelParameters)->first();

            if (is_null($userLevel)){
                $confirmed = $request->json('user_confirmed');
                if (!is_null($confirmed) && $confirmed == '1'){
                    $level = $site->levelParameters()->orderBy('position', 'asc')->first();
                    if(!is_null($level)){
                        $userLevel = 0;
                    }
                }
            }

            return response()->json($userLevel, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Manually Updates the Level of the user to the level needing manual verification
     *
     * @param Request $request
     * @param $userKey
     * @param $siteKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualUpdateUserLevel(Request $request, $userKey, $siteKey)
    {
        ONE::verifyToken($request);
        try {
            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $site = Site::where('key',$siteKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Site not Found'], 404);
            }

            $manualVerificationLevel = $site->levelParameters()->whereManualVerification(true)->firstOrFail();
            $user->levelParameters()->sync([$manualVerificationLevel->id]);

            return response()->json($user->levelParameters()->first(), 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to manually Update User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function migrateUserToEntity(Request $request) {
        ONE::verifyKeysRequest($this->keysRequired, $request);

        if (!empty($request->header('X-ENTITY-KEY')) && $request->has("user_key")) {
            try {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $user = OrchUser::whereUserKey($request->input("user_key"))->firstOrFail();
                $user->entities()->attach($entity->id, ['role' => $this->roles["USER"], 'status' => 'complete']);

                return response()->json(["user"=>$user], 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Failed to migrate user to entity'], 500);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to migrate user to entity'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SmsUpdateUserLevel(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        $siteKey = $request->header('X-SITE-KEY');
        try {
            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $site = Site::where('key',$siteKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Site not Found'], 404);
            }

            $SmsVerificationLevel = $site->levelParameters()->whereSmsVerification(true)->firstOrFail();
            $user->levelParameters()->sync([$SmsVerificationLevel->id]);

            $this->AutomateManualUpdate($request, $SmsVerificationLevel);

            return response()->json($user->levelParameters()->first(), 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $currentLevel
     * @return \Illuminate\Http\JsonResponse
     */
    private function AutomateManualUpdate(Request $request, $currentLevel)
    {
        try {
            $userKey = ONE::verifyToken($request);

            $response = ONE::get([
                'headers'   => ['X-AUTH-TOKEN: '.$request->header('X-AUTH-TOKEN')],
                'component' => 'auth',
                'api'       => 'auth',
                'method'    => 'getUser'
            ]);

            if (isset($response->json()->user->confirmed) && $response->json()->user->confirmed == 1){

                $siteKey = $request->header('X-SITE-KEY');

                try {
                    $user = OrchUser::whereUserKey($userKey)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'User not Found'], 404);
                }

                try {
                    $site = Site::where('key',$siteKey)->firstOrFail();
                    $entity = $site->entity()->first();
                } catch (ModelNotFoundException $e) {
                    return response()->json(['error' => 'Site not Found'], 404);
                }

                $nextLevel = $site->levelParameters()->where('position', ($currentLevel->position +1))->first();

                if (!is_null($nextLevel) && ($nextLevel->manual_verification == 1)){

                    if (!is_null($entity)){

                        if ($entity->authMethodEntities()->whereCode('verification_vat')->exists()){

                            $parameterUserTypes = $entity->parameterUserTypes()->with('parameterType')->get();

                            $parameterUserTypeKey = null;
                            $parameterUserTypeEmailKeys = array();
                            foreach ($parameterUserTypes as $parameterUserType){
                                if(isset($parameterUserType->parameterType) && ($parameterUserType->parameterType->code == 'vat_number')){
                                    $parameterUserTypeKey =  $parameterUserType->parameter_user_type_key;
                                }
                                if(isset($parameterUserType->parameterType) && ($parameterUserType->parameterType->code == 'email')){
                                    $parameterUserTypeEmailKeys[$parameterUserType->parameter_user_type_key] = $parameterUserType->parameter_user_type_key;
                                }
                            }

                            if (!is_null($parameterUserTypeKey)){
                                if(!empty($response->json()->user->user_parameters->$parameterUserTypeKey)){
                                    $vatNumber = $response->json()->user->user_parameters->{$parameterUserTypeKey}[0]->value;

                                    if (EntityVatNumber::whereEntityId($entity->id)->whereVatNumber($vatNumber)->exists()){
                                        $manualVerificationLevel = $site->levelParameters()->whereManualVerification(true)->firstOrFail();
                                        $user->levelParameters()->sync([$manualVerificationLevel->id]);
                                    } else {
                                        if(!empty($response->json()->user->email)){
                                            $shouldValidatePrimaryEmail = !(Site::where("key",$request->header('X-SITE-KEY'))
                                                ->whereHas("configurationsValues",function ($q) {
                                                    $q->where("value","=","1")->whereHas("siteConf",function ($q) {
                                                        $q->where("code", "=", "boolean_primary_email_domain_validation");
                                                    });
                                                })->exists());

                                            if ($shouldValidatePrimaryEmail) {
                                                $domain = explode("@", $response->json()->user->email);

                                                if (!is_null($domain[1])) {
                                                    if (EntityDomainName::whereEntityId($entity->id)->whereDomainName($domain[1])->exists()) {
                                                        $manualVerificationLevel = $site->levelParameters()->whereManualVerification(true)->firstOrFail();
                                                        $user->levelParameters()->sync([$manualVerificationLevel->id]);
                                                    }
                                                }
                                            }
                                        }

                                        if (!empty($parameterUserTypeEmailKeys)) {
                                            foreach ($parameterUserTypeEmailKeys as $parameterUserTypeEmailKey) {
                                                if (!empty($response->json()->user->user_parameters->{ $parameterUserTypeEmailKey }) &&
                                                    !empty($response->json()->user->user_parameters->{ $parameterUserTypeEmailKey }[0]->value) &&
                                                    empty($response->json()->user->user_parameters->{ $parameterUserTypeEmailKey }[0]->confirmation_code)) {

                                                    $domain = explode("@", $response->json()->user->user_parameters->{ $parameterUserTypeEmailKey }[0]->value);

                                                    if(!is_null($domain[1])){
                                                        if (EntityDomainName::whereEntityId($entity->id)->whereDomainName($domain[1])->exists()){
                                                            $manualVerificationLevel = $site->levelParameters()->whereManualVerification(true)->firstOrFail();
                                                            $user->levelParameters()->sync([$manualVerificationLevel->id]);
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
                return response()->json($user->levelParameters()->first(), 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAndUpdateLevel(Request $request){

        try {
            $userKey = $request->json('user_key');
            $userLevel = $request->json('user_level');
            $userConfirmed = $request->json('user_confirmed');
            $siteKey = $request->header('X-SITE-KEY');
            $entityKey = $request->header('X-ENTITY-KEY');

            $user = OrchUser::whereUserKey($userKey)->firstOrFail();


            if ($userConfirmed == 1){
                $site = Site::where('key',$siteKey)->firstOrFail();

                if (!$site->levelParameters()->exists()) {
                    return response()->json('OK', 200);
                }

                /* Verifies if there is a secundary email and if it "fits" the domains whitelist */
                $entityEmailParameter = ParameterUserType::with("entity","parameterType")
                    ->whereHas("entity",function ($q) use ($entityKey) {
                        $q->where("entity_key","=",$entityKey);
                    })->whereHas("parameterType", function ($q) use ($entityKey) {
                        $q->where("code","=","email");
                    })->first();

                $shouldUpdateLevel = false;
                if (!empty($entityEmailParameter)) {
                    $authUser = User::
                    with("userParameters")
                        ->whereUserKey($user->user_key)
                        ->first();

                    $userSecundaryEmail = $authUser->userParameters->where("parameter_user_key","=",$entityEmailParameter->parameter_user_type_key)->first();

                    if (!empty($userSecundaryEmail)) {
                        $domain = explode("@", $userSecundaryEmail->value);

                        if(!is_null($domain[1])) {
                            $shouldUpdateLevel = EntityDomainName::
                            whereDomainName($domain[1])
                                ->whereHas("entity",function ($q) use ($entityKey) {
                                    $q->where("entity_key","=",$entityKey);
                                })
                                ->exists();
                        } else
                            $shouldUpdateLevel = true;
                    } else
                        $shouldUpdateLevel = true;
                } else
                    $shouldUpdateLevel = true;

                if ($shouldUpdateLevel) {
                    $SmsVerificationLevel = $site->levelParameters()->whereSmsVerification(true)->first();

                    if ($SmsVerificationLevel != null && $userLevel == $SmsVerificationLevel->position) {
                        $manualVerificationLevel = $site->levelParameters()->whereManualVerification(true)->firstOrFail();
                        $user->levelParameters()->sync([$manualVerificationLevel->id]);
                    }
                }
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->first();
                $this->autoUpdateUserLoginLevels($user,$entity,$userKey);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            return response()->json('OK', 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countEntityUsers(Request $request)
    {
        try {
            $allUsers = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first()->users()->get()->count();
            return response()->json(["data" => $allUsers], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Users'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $userKey
     * @param $levelPosition
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserLevel(Request $request, $userKey, $levelPosition = 0)
    {
        //TODO: verify if user is admin or manager
        ONE::verifyToken($request);

        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $newUserLevel = $site->levelParameters()->wherePosition($levelPosition)->first();

            if (is_null($newUserLevel)){
                $user->levelParameters()->detach();
            } else {
                $user->levelParameters()->sync([$newUserLevel->id]);
            }

            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Set the User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserEntities(Request $request, $userKey)
    {
        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $entities = $user->entities()->get();

            $response = $entities;

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function autoUpdateEntityUsersLoginLevels(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            $count = 0;
            if($entity){
                $entityUsers = $entity->users()->get();
                if($entityUsers){

                    dispatch(new AutoUpdateUserLoginLevels($entity,$entityUsers));

                    /*foreach ($entityUsers as $entityUser){
                        $user = OrchUser::whereUserKey($entityUser->user_key)->firstOrFail();
                        if($this->autoUpdateUserLoginLevels($user,$entity,'SYSTEM')){
                            $count++;
                        }
                    }
                    \Log::info("[LOGIN LEVELS] USERS UPDATED: ".$count);*/
                }
            }

            return response()->json('Ok', 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to automatically update entity users login levels'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }


    /**
     * @param $user
     * @param $entity
     * @param $responsibleKey
     * @return bool
     */
    public static function autoUpdateUserLoginLevels($user, $entity, $responsibleKey)
    {
        try {
            //get user with parameters
            $authUser = User::whereUserKey($user->user_key)->with('userParameters')->first();
            $orchUser = OrchUser::whereUserKey($user->user_key)->firstOrFail();
            $userParameters = $authUser->userParameters;
            $userHasEmailConfirmed = $authUser->confirmed;
            $userHasSmsConfirmed = $authUser->sms_token == null ? true : false;

            //get all entity login levels with parameter user types
            $entityLoginLevels = $entity->loginLevels()->with('parameters')->get();
            $userRelation = $entity->users()->where('user_id',$orchUser->id)->first();
            $userIsModerated = ($userRelation->pivot->status == 'authorized');
            $loginLevelsCompleted = [];
            //verify all the entity login levels
            //TODO:improve double foreach
            foreach ($entityLoginLevels as $loginLevel) {

                $parameterUserTypes = $loginLevel->parameters;
                if (!$parameterUserTypes->isEmpty()) {
                    // verify if user as complete all parameters form login level
                    foreach ($parameterUserTypes as $parameter) {

                        $parameterUserType = ParameterUserType::find($parameter->parameter_user_type_id);
                        if(!empty($parameterUserType)){
                            $parameterKey = $parameterUserType->parameter_user_type_key;

                            switch ($parameterKey) {
                                case 'name':
                                case 'surname':
                                case 'email':
                                    if (empty($authUser->{$parameter->parameter_user_type_key})) {
                                        //exit from switch and foreach of parameter user types
                                        break 2;
                                    }
                                    break;
                                default:
                                    $checkRelation = $userParameters->where('parameter_user_key', '=', $parameterKey)->first();
                                    if (empty($checkRelation) || empty($checkRelation->value)) {
                                        break 2;
                                    }
                                    break;
                            }
                        }
                        // verify if it's the last element and update the user login level
                        if ($parameter === $parameterUserTypes->last()) {

                            if ((empty($loginLevel->manual_verification) || $userIsModerated) && (empty($loginLevel->email_verification) || $userHasEmailConfirmed) && (empty($loginLevel->sms_verification) || $userHasSmsConfirmed)){
                                $loginLevelsCompleted[] = $loginLevel->id;
                            }

//                            if (empty($loginLevel->email_verification) && empty($loginLevel->sms_verification)) {
//                                $loginLevelsCompleted[] = $loginLevel->id;
//                            } else {
//                                if ($userHasEmailConfirmed) {
//                                    $loginLevelsCompleted[] = $loginLevel->id;
//                                }
//                            }
                        }
                    }
                } elseif ((empty($loginLevel->manual_verification) || $userIsModerated) && (empty($loginLevel->email_verification) || $userHasEmailConfirmed) && (empty($loginLevel->sms_verification) || $userHasSmsConfirmed)) {
                    $loginLevelsCompleted[] = $loginLevel->id;

//                    if (empty($loginLevel->email_verification)) {
//                        $loginLevelsCompleted[] = $loginLevel->id;
//                    } else {
//                        if ($userHasEmailConfirmed) {
//                            $loginLevelsCompleted[] = $loginLevel->id;
//                        }
//                    }
                }
            }

            $oldUserLoginLevels = UserLoginLevel::whereUserId($user->id)->whereManual('0')->get();

            //verify old login levels if they are complete
            foreach ($oldUserLoginLevels as $oldUserLoginLevel) {
                $loginLevelOld = $oldUserLoginLevel->loginLevel()->first();

                if (!in_array($oldUserLoginLevel->login_level_id, $loginLevelsCompleted) && empty($loginLevelOld->manual_verification) && empty($loginLevelOld->sms_verification)) {
                    $oldUserLoginLevel->updated_by = $responsibleKey;
                    $oldUserLoginLevel->save();
                    $oldUserLoginLevel->delete();
                    continue;
                }
                $dependencies = $loginLevelOld->loginLevelDependencies()->pluck('dependency_login_level_id');
                if ($dependencies->isEmpty()) {
                    continue;
                }
                //intersect the dependencies with login levels completed to verify if all the levels are completed
                $intersectionArray = $dependencies->intersect($loginLevelsCompleted);
                if (count($intersectionArray) != count($dependencies)) {
                    $oldUserLoginLevel->updated_by = $responsibleKey;
                    $oldUserLoginLevel->save();
                    $oldUserLoginLevel->delete();
                }
            }

            //verify new login levels dependencies of completed levels
            foreach ($entityLoginLevels as $loginLevel) {
                $userHasLevel = UserLoginLevel::whereLoginLevelId($loginLevel->id)->whereUserId($user->id)->exists();
                if (in_array($loginLevel->id, $loginLevelsCompleted) && empty($userHasLevel)) {
                    $dependencies = $loginLevel->loginLevelDependencies()->pluck('dependency_login_level_id');
                    if ($dependencies->isEmpty()) {

                        UserLoginLevel::create([
                            'user_id'        => $user->id,
                            'login_level_id' => $loginLevel->id,
                            'created_by'     => $responsibleKey,
                            'updated_by'     => $responsibleKey,
                        ]);
                    } else {
                        //intersect the dependencies with login levels completed to verify if all the levels are completed
                        $intersectionArray = $dependencies->intersect($loginLevelsCompleted);
                        if (count($intersectionArray) == count($dependencies)) {
                            UserLoginLevel::create([
                                'user_id'        => $user->id,
                                'login_level_id' => $loginLevel->id,
                                'created_by'     => $responsibleKey,
                                'updated_by'     => $responsibleKey,
                            ]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    /** Check And Update User Login Level
     * Return all User Login Levels
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoCheckLoginLevel(Request $request){
        try{
            $userKeyAuth = ONE::verifyToken($request);
            $userKey = $request->json('user_key');
            $entityKey = $request->header('X-ENTITY-KEY');

            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $this->autoUpdateUserLoginLevels($user,$entity,$userKeyAuth);

            $userLoginLevels = UserLoginLevel::whereUserId($user->id)->get();

            return response()->json(['data' => $userLoginLevels], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update User Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }




    /**
     * Manually Updates the Login Level of the user
     *
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualCheckLoginLevel(Request $request, $userKey)
    {
        $userKeyAuth = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['login_level_key'], $request);
        try {
            $entityKey = $request->header('X-ENTITY-KEY');
            $loginLevelKey = $request->json('login_level_key');
            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $manualVerificationLevel = $entity->loginLevels()->whereLoginLevelKey($loginLevelKey)->whereManualVerification(true)->firstOrFail();
            if(!$user->userLoginLevels()->whereLoginLevelId($manualVerificationLevel->id)->exists()){
                $user->userLoginLevels()->create(
                    [
                        'login_level_id' => $manualVerificationLevel->id,
                        'created_by' => $userKeyAuth,
                        'updated_by' => $userKeyAuth
                    ]
                );
            }
            $userLoginLevels = $user->userLoginLevels()->with('loginLevel')->get()->pluck('loginLevel');

            return response()->json(['data' => $userLoginLevels], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to manually Update User Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /** Check Login Level with SMS verification
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function smsCheckLoginLevel(Request $request,$userKey)
    {
        $userKeyAuth = ONE::verifyToken($request);
        try {
            $entityKey = $request->header('X-ENTITY-KEY');

            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }

            $smsVerificationLevel = $entity->loginLevels()->whereSmsVerification(true)->firstOrFail();
            if(!$user->userLoginLevels()->whereLoginLevelId($smsVerificationLevel->id)->exists()) {
                $user->userLoginLevels()->create(
                    [
                        'login_level_id' => $smsVerificationLevel->id,
                        'created_by' => $userKeyAuth,
                        'updated_by' => $userKeyAuth
                    ]
                );
            }

            $userLoginLevels = $user->userLoginLevels()->with('loginLevel')->get()->pluck('loginLevel');

            return response()->json(['data' => $userLoginLevels], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update User Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /** Get OrchUser Login Level
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginLevels(Request $request, $userKey){
        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $completeUserLoginLevels = UserLoginLevel::where('user_id','=', $user->id)->with('loginLevel')->get();
            $userLoginLevels = $completeUserLoginLevels->pluck('loginLevel')->keyBy('login_level_key');
            foreach ($completeUserLoginLevels as $userLoginLevel){
                $userLoginLevels[$userLoginLevel->loginLevel->login_level_key]->setAttribute('manual', $userLoginLevel->manual);
                $userLoginLevels[$userLoginLevel->loginLevel->login_level_key]->created_at = $userLoginLevel->created_at;
                $userLoginLevels[$userLoginLevel->loginLevel->login_level_key]->created_by = User::whereUserKey($userLoginLevel->created_by)->first()->name ?? 'SYSTEM';
            }
            return response()->json(['data' => $userLoginLevels], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Login Levels '.$e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLoginLevels(Request $request, $userKey){

        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $userLoginLevels=UserLoginLevel::where('user_id','=', $user->id)->with('loginLevel')->get()->pluck('loginLevel');

            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();
            $cbConfigPermission=$cb->cb_ConfigurationsPermission()->get();
            $levels='';

            if($cbConfigPermission !='[]'){
                foreach ($cbConfigPermission as $key => $value) {
                    if($value->code=='create_topic' && $request->codeConfigPermission=='create_topic'){

                        $levels=$value->pivot->value;

                    }elseif($value->code=='comment' && $request->codeConfigPermission=='comment'){

                        $levels=$value->pivot->value;
                    }
                }
                if($levels!=''){
                    $arrayKey=[];
                    foreach ($userLoginLevels as $userLoginLevel => $loginLevel) {
                        $arrayKey[]=$loginLevel->login_level_key;
                    }

                    $collect=explode(',',$levels);
                    $arrayLevels=[];
                    foreach ($collect as $key => $value) {
                        $space=preg_replace('/\["|"\]|"/', '', $value);
                        $arrayLevels[]=$space;
                    }

                    $diff=collect($arrayLevels)->diff(collect($arrayKey));
                    $arrayDiff=[];
                    if($diff->isNotEmpty()){

                        foreach ($diff as $diffValue) {
                            $loginLevel=LoginLevel::where('login_level_key','=',$diffValue)->first();

                            $loginLevel->manual_verification == 1 ? ($parameterUserTypes[] = 'manual_verification') : null;
                            $loginLevel->sms_verification == 1 ? ($parameterUserTypes[] = 'sms_verification') : null;
                            $loginLevel->email_verification == 1 ? ($parameterUserTypes[] = 'email_verification') : null;

                            $loginLevelParameters = $loginLevel->parameters()->get();

                            if (!$loginLevelParameters->isEmpty()) {

                                foreach ($loginLevelParameters as $loginLevelParameter) {
                                    $parameterUserType=ParameterUserType::find($loginLevelParameter->parameter_user_type_id);
                                    if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                                        if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT'))){
                                            $translation = $parameterUserType->parameterUserTypeTranslations()->first();
                                            $parameterUserType->translation($translation->language_code);
                                        }
                                    }
                                    $parameterUserTypes[] = $parameterUserType->name;
                                }
                            }
                        }
                        $diff['parameterUserTypes'] = $parameterUserTypes;
                        $arrayDiff[$request->codeConfigPermission]=$diff;
                    }else{
                        $diff['parameterUserTypes'] = null;
                        $arrayDiff[$request->codeConfigPermission]=null;
                    }
                }else{
                    $diff['parameterUserTypes'] = null;
                    $arrayDiff[$request->codeConfigPermission]=null;
                }
            }else{
                $diff['parameterUserTypes'] = null;
                $arrayDiff[$request->codeConfigPermission]=null;

            }

            return response()->json(['data' => $arrayDiff], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Login Levels'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAllManagers(Request $request)
    {
        try {
            /*Verify if List is for a specific Role*/
            if (!empty($request->json('role'))) {
                if ($request->json('role') == $this->roles["MANAGER"]) {

                    $entity = Entity::with(['users' => function ($query) {
                        $query->whereRole($this->roles["MANAGER"]);
                    }])->whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                    if (empty($entity)) {
                        $entity = Entity::with(['users' => function ($query) {
                            $query->whereRole($this->roles["MANAGER"]);
                        }])->whereEntityKey($request->json('entity_key'))->first();
                        if (empty($entity)) {
                            return response()->json(['error' => 'Entity not Found'], 404);
                        }
                    }
                    $managers = $entity->users;
                    return response()->json(["data" => $managers], 200);
                }
            }
            return response()->json(["data" => []], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Managers list'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserEmail(Request $request){
        try{
            $user = User::whereUserKey($request->userKey)->get();
            foreach ($user as $item){
                $email = $item->email;
            }
            return response()->json($email, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user email'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLoginLevelsVotes(Request $request, $userKey){

        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $userLoginLevels=UserLoginLevel::where('user_id','=', $user->id)->with('loginLevel')->get()->pluck('loginLevel');
            $arrayDiff=[];

            if($request->configPermission!= []){
                foreach ($request->configPermission as $keyVote => $valueVote) {

                    if($valueVote!= []){

                        $collect=explode(',',$valueVote);
                        $arrayLevels=[];

                        foreach ($collect as $value) {
                            $space=preg_replace('/\["|"\]|"/', '', $value);
                            $arrayLevels[]=$space;
                        }
                    }

                    $arrayKey=[];
                    foreach ($userLoginLevels as $loginLevel) {
                        $arrayKey[]=$loginLevel->login_level_key;
                    }

                    $diff=collect($arrayLevels)->diff(collect($arrayKey));
                    $parameterUserTypes=[];

                    if($diff !=[]){
                        foreach ($diff as $diffValue) {
                            $loginLevel=LoginLevel::where('login_level_key','=',$diffValue)->first();

                            $loginLevel->manual_verification == 1 ? ($parameterUserTypes[] = 'manual_verification') : null;
                            $loginLevel->sms_verification == 1 ? ($parameterUserTypes[] = 'sms_verification') : null;
                            $loginLevel->email_verification == 1 ? ($parameterUserTypes[] = 'email_verification') : null;

                            $loginLevelParameters = $loginLevel->parameters()->get();
                            if (!$loginLevelParameters->isEmpty()) {

                                foreach ($loginLevelParameters as $loginLevelParameter) {
                                    $parameterUserType=ParameterUserType::find($loginLevelParameter->parameter_user_type_id);

                                    if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                                        if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT'))){
                                            $translation = $parameterUserType->parameterUserTypeTranslations()->first();
                                            $parameterUserType->translation($translation->language_code);
                                        }
                                    }

                                    $parameterUserTypes[] = $parameterUserType->name;
                                }
                            }
                        }
                        $diff['parameterUserTypes'] = $parameterUserTypes;
                        $arrayDiff[$keyVote]=$diff;

                    }else{
                        $diff['parameterUserTypes'] = null;
                        $arrayDiff[$keyVote]=$diff;
                    }

                }
            }else{
                $arrayDiff[]=null;
            }

            return response()->json(['data' => $arrayDiff], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Login Levels Votes'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualGrantLoginLevel(Request $request)
    {
        try {
            $managementUserKey  = ONE::verifyToken($request);
            $userKey            = $request->json('user_key');
            $loginLevelKey     = $request->json('login_level_key');

            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $loginLevel = LoginLevel::whereLoginLevelKey($loginLevelKey)->first();

            UserLoginLevel::whereUserId($user->id)->updateOrCreate([
                'user_id'           => $user->id,
                'login_level_id'    => $loginLevel->id
            ],[
                'created_by'        => $managementUserKey,
                'updated_by'        => $managementUserKey,
                'manual'            => '1'
            ]);

            return response()->json(UserLoginLevel::whereUserId($user->id)->get(), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to Manually Grant Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualRemoveLoginLevel(Request $request)
    {
        try {
            $managementUserKey  = ONE::verifyToken($request);
            $userKey            = $request->json('user_key');
            $loginLevelKey     = $request->json('login_level_key');

            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $loginLevel = LoginLevel::whereLoginLevelKey($loginLevelKey)->first();

            $userLoginLevel = UserLoginLevel::whereUserId($user->id)->whereLoginLevelId($loginLevel->id)->first();

            if ($userLoginLevel){
                $userLoginLevel->delete();
            }

            return response()->json(UserLoginLevel::whereUserId($user->id)->get(), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to Manually Remove Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function updateUserLoginLevels(Request $request, $userKey)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            $userKey = ONE::verifyToken($request);
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();

            if($entity){
                if($this->autoUpdateUserLoginLevels($user,$entity,$userKey))
                    return response()->json('Ok', 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to automatically update entity users login levels'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatusLoginLevel(Request $request){
        try{
            $userKey = ONE::verifyToken($request);
            $user = OrchUser::with('entities')->whereUserKey($request->user_key)->firstOrFail();
            $userAuth = User::whereUserKey($request->user_key)->firstOrFail();
            $entity = $user->entities->where('entity_key', '=', $request->header('X-ENTITY-KEY'))->first();
            if($entity) {
                $entity->pivot->status = 'authorized';
                $entity->pivot->save();
                if($this->autoUpdateUserLoginLevels($user,$entity,$userKey)){
                    $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();

                    $emailTemplate = Notify::getEmailTemplate($request->header('X-SITE-KEY'), 'account_authorized');
                    $mails[] = $userAuth->email;
                    $tags = [
                        'name' => $userAuth->name,
                    ];
                    $response = Notify::sendEmailByTemplateKey($request, $site, $emailTemplate->email_template_key, $mails, $user->user_key, $tags);

                    return response()->json(['success' => $response], 200);
                }

            }
            return response()->json(['success' => false], 204);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to automatically update user login levels'], 500);
        }
    }


    public function storeUserAnonymizationRequest(Request $request) {
        try{
            $currentUserKey = ONE::verifyToken($request);
            $entity = One::getEntity($request);
            
            $userKeys = $request->get("userKeys");
            if (empty($userKeys))
                $userKeys = $entity->users()->whereRole("user")->pluck("user_key")->toArray();
            
            $pendingUserAnonymizationRequest = UserAnonymizationRequest::create([
                'created_by' => $currentUserKey,
                'entity_key' => $entity->entity_key,
                'user_keys' => json_encode($userKeys)
            ]);
            
            if(count($userKeys)==1) {
                try {
                    $this->processAnonymizationRequest($pendingUserAnonymizationRequest);
                } catch(Exception $e) {}
            }

            return response()->json(["success" => true]);
        } catch(Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()],500);
        }
    }

    static public function anonymizeUsers() {
        try {
            $pendingUserAnonymizationRequests = UserAnonymizationRequest::whereProcessStatus(0)->get();
            
            foreach ($pendingUserAnonymizationRequests as $pendingUserAnonymizationRequest) {
                $this->processAnonymizationRequest($pendingUserAnonymizationRequest);
            }
        } catch (Exception $e) {
            \Log::info("[USER-ANONYMIZATION] Error: ", $e);
            dd($e);
        }
    }
    private function processAnonymizationRequest(UserAnonymizationRequest $pendingUserAnonymizationRequest) {
        $pendingUserAnonymizationRequest->load([
            "entity.parameterUserTypes" => function($q) {
                $q->where("anonymizable","=",1);
            }]);

        $userKeys = json_decode($pendingUserAnonymizationRequest->user_keys);
        $anonymizableParameterKeys = $pendingUserAnonymizationRequest->entity->parameterUserTypes->pluck("parameter_user_type_key");
        $entityKey = $pendingUserAnonymizationRequest->entity->entity_key;

        /* Checks if it's not already being processed by another queue process */
        if (UserAnonymizationRequest::whereId($pendingUserAnonymizationRequest->id)->whereProcessStatus(0)->exists()) {
            $pendingUserAnonymizationRequest->process_status = 1;
            $pendingUserAnonymizationRequest->save();
            $startTime = microtime(true);

            $users = User::with([
                    "orchUser.entities",
                    "userParameters" => function($q) use ($anonymizableParameterKeys) {
                        $q->whereIn("parameter_user_key",$anonymizableParameterKeys);
                    }
                ])
                ->whereHas("orchUser.entities", function($query) use($entityKey) {
                    $query->where("entity_key","=",$entityKey);
                })
                ->whereDoesntHave("anonymization")
                ->whereIn("user_key",$userKeys)
                ->get();

            $logData = array(
                "time"      => 0,
                "userCount" => $users->count(),
                "success"   => 0,
                "failed"    => 0,
                "users"     => array()
            );
            
            foreach ($users as $user) {
                $resultCode = null;

                if ($user->orchUser->entities->count()==1) {
                    foreach ($user->userParameters as $userParameter) {
                        $userParameter->value = null;
                        $userParameter->save();
                    }
                    
                    $user->password = bcrypt(str_random(8));
                    $user->name = "Anonymized User";
                    $user->email = $user->user_key . "-" . $entityKey . "@anonymized.empatia-project.eu";
                    $user->public_email = 0;
                    $user->save();

                    $resultCode = 1;
                    $logData["success"]++;

                    $user->anonymization()->create([
                        'entity_key' => $entityKey,
                        'user_anonymization_request_id' => $pendingUserAnonymizationRequest->id
                    ]);
                } else {
                    $resultCode = -1;
                    $logData["failed"]++;
                }

                $logData["users"][$user->user_key] = $resultCode;
            }
            $logData["time"] = microtime(true)-$startTime;

            $pendingUserAnonymizationRequest->process_status = 2;
            $pendingUserAnonymizationRequest->log = serialize($logData);
            $pendingUserAnonymizationRequest->save();
        }
    }

    public function deleteUserParameters($entity,$user) {
        $userId = User::whereUserKey($user->user_key)->firstOrFail();
        $parameters = ParameterUserType::whereEntityId($entity->id)->get();
        
        foreach($parameters as $parameter){
            $userParameter = UserParameter::whereParameterUserKey($parameter->parameter_user_type_key)->whereUserId($userId->id)->firstOrFail();
            $userParameter->delete();
        }
        $userId->delete();
    }
}
