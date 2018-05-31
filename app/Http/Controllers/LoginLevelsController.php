<?php

namespace App\Http\Controllers;

use App\Entity;
use App\LevelParameter;
use App\LoginLevel;
use App\LoginLevelParameter;
use App\One\One;
use App\ParameterUserType;
use App\Site;
use App\OrchUser;
use App\UserLoginLevel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class LoginLevelsController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Login Level",
 *   description="Everything about Login Levels",
 * )
 *
 *  @SWG\Definition(
 *      definition="loginLevelErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="loginLevel",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="login_level_key", format="string", type="string"),
 *           @SWG\Property(property="manual_verification", format="boolean", type="boolean"),
 *           @SWG\Property(property="sms_verification", format="boolean", type="boolean"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="updated_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string")

 *       )
 *   }
 * )
 *
 *
 *
 *
 *  @SWG\Definition(
 *   definition="loginLevelParameter",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="login_level_key", format="string", type="string"),
 *           @SWG\Property(property="manual_verification", format="boolean", type="boolean"),
 *           @SWG\Property(property="sms_verification", format="boolean", type="boolean"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="updated_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(
 *              property="parameter_user_types",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/parameterUserTypeReply")
 *           )
 *
 *       )
 *   }
 * )
 *
 *
 *
 *
 * @SWG\Definition(
 *     definition="usersManualResponse",
 *     @SWG\Property(
 *     property="data",
 *     type="object",
 *     allOf={
 *       @SWG\Schema(
 *          type="array",
 *          @SWG\Items(
 *           @SWG\Property(property="user_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="login_levels",
 *              type="array",
 *              @SWG\Items(
 *                  @SWG\Property(property="name", format="string", type="string"),
 *                  @SWG\Property(property="key", format="string", type="string"),
 *              )
 *           )
 *          )
 *        ),
 *
 *      })
 * )
 *
 *
 *
 *
 */

class LoginLevelsController extends Controller
{
    protected $required = [
        'store' => ['name'],
        'update' => ['name']
    ];

    /**
     *
     *
     * @SWG\Definition(
     *     definition="replyLoginLevelList",
     *     required={"data"},
     *     @SWG\Property(
     *     property="data",
     *     type="object",
     *     allOf={
     *       @SWG\Schema(
     *     type="array",
     *      @SWG\Items(ref="#/definitions/loginLevel")
     * ),
     *
     *   })
     * )
     *
     * @SWG\Get(
     *  path="/loginLevel/list",
     *  summary="List all Login Levels of a entity",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
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
     *     @SWG\Response(
     *      response="200",
     *      description="List of all Entity Login Levels",
     *      @SWG\Schema(ref="#/definitions/replyLoginLevelList")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Login Levels",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Returns all the entity Login Levels
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            if(!empty($request->entity_key)){
                $entity = Entity::whereEntityKey($request->entity_key)->firstOrFail();
            }else{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }
            $loginLevelParameters = $entity->loginLevels()->with('parameters.parameterUserType.parameterUserTypeTranslations')->get()->keyBy('login_level_key');
// ->with('parameterUserTypes')

            return response()->json(['data' => $loginLevelParameters], 200);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to retrieve the Login Levels'], 500);
        }
    }

    /**
     *
     *
     *
     * @SWG\Get(
     *  path="/loginLevel/{login_level_key}",
     *  summary="Show a Login Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *
     * @SWG\Parameter(
     *      name="login_level_key",
     *      in="path",
     *      description="Login Level Key",
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
     *     @SWG\Response(
     *      response="200",
     *      description="Show the Login Level",
     *      @SWG\Schema(ref="#/definitions/loginLevel")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Login Level",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Returns the specified Login Level
     *
     * @param Request $request
     * @param $loginLevelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $loginLevelParameterKey)
    {
        try {
            if(!empty($request->entity_key)){
                $entity = Entity::whereEntityKey($request->entity_key)->firstOrFail();
            }else{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }
            $loginLevelParameter = $entity->loginLevels()->whereLoginLevelKey($loginLevelParameterKey)->firstOrFail();
            // ->with('parameterUserTypes')

            /** Get all Login Level dependencies*/
            $loginLevelParameter->login_level_dependencies = $loginLevelParameter->loginLevelDependencies()->with('loginLevelDependency')->get()->pluck('loginLevelDependency')->keyBy('login_level_key');
            return response()->json($loginLevelParameter, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Login Level'], 500);
        }
    }

    /**
     *
     *  @SWG\Definition(
     *   definition="loginLevelCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"name"},
     *           @SWG\Property(property="name", format="string", type="string"),
     *           @SWG\Property(
     *              property="dependencies",
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(property="login_level_key", type="string", format="string")
     * )
     *           ),
     *       )
     *   }
     * )
     *
     *
     * @SWG\Post(
     *  path="/loginLevel",
     *  summary="Creation of a Login Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Login Level data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/loginLevelCreate")
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
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created login level",
     *      @SWG\Schema(ref="#/definitions/loginLevel")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Login Level",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Creates and stores a new Login Level for a Site
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            if (OrchUser::verifyRole($userKey, "admin") == false && !OrchUser::verifyRole($userKey, "manager") == false) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if(!empty($request->entity_key)){
                $entity = Entity::whereEntityKey($request->entity_key)->firstOrFail();
            }else{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }


            do {
                $rand = str_random(32);
                $key = "";
                if (!($exists = LoginLevel::whereLoginLevelKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $loginLevel = $entity->loginLevels()->create(
                [
                    'login_level_key' => $key,
                    'manual_verification' => $request->json('manual_verification') ?? 0,
                    'sms_verification' => $request->json('sms_verification') ?? 0,
                    'email_verification' => $request->json('email_verification') ?? 0,
                    'name' => $request->json('name'),
                    'created_by' => $userKey,
                    'updated_by' => $userKey
                ]
            );

            $dependencies = $request->json('dependencies');
            if(empty($dependencies)){
                return response()->json($loginLevel, 201);
            }

            $loginLevelDependencies = LoginLevel::whereIn('login_level_key', $dependencies)->get();

            foreach ($loginLevelDependencies as $loginLevelDependency){
                $newLoginLevelDependency = $loginLevel->loginLevelDependencies()->create(
                    [
                        'dependency_login_level_id' => $loginLevelDependency->id,
                        'created_by' => $userKey,
                        'updated_by' => $userKey
                    ]
                );
            }

            return response()->json($loginLevel, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *
     *
     * @SWG\Put(
     *  path="/loginLevel/{login_level_key}",
     *  summary="Update a Login Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Login Level Update",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/loginLevelCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="login_level_key",
     *      in="path",
     *      description="Login Level Key",
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
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated level",
     *      @SWG\Schema(ref="#/definitions/loginLevel")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Login Level not Found",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Login Level",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates and stores the specified Login Level for a Entity
     *
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $loginLevelKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {

            if (OrchUser::verifyRole($userKey, "admin") == false && !OrchUser::verifyRole($userKey, "manager") == false) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if(!empty($request->entity_key)){
                $entity = Entity::whereEntityKey($request->entity_key)->first();
            }else{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            }
            if(empty($entity)){
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            $loginLevel = $entity->loginLevels()->whereLoginLevelKey($loginLevelKey)->firstOrFail();

            $loginLevel->name              = $request->json('name');
            $loginLevel->sms_verification       = $request->json('sms_verification') ?? 0;
            $loginLevel->manual_verification    = $request->json('manual_verification') ?? 0;
            $loginLevel->email_verification    = $request->json('email_verification') ?? 0;
            $loginLevel->save();


            $dependencies = $request->json('dependencies');
            if(empty($dependencies)){
                $loginLevel->loginLevelDependencies()->delete();
                return response()->json($loginLevel, 200);
            }

            $oldDependenciesIds = $loginLevel->loginLevelDependencies()->pluck('dependency_login_level_id');

            $loginLevelDependencies = LoginLevel::whereIn('login_level_key', $dependencies)->get();
            $newDependenciesIds = $loginLevelDependencies->pluck('id');

            foreach ($loginLevelDependencies as $loginLevelDependency){

                if(!$loginLevel->loginLevelDependencies()->whereDependencyLoginLevelId($loginLevelDependency->id)->exists()){
                    $newLoginLevelDependency = $loginLevel->loginLevelDependencies()->create(
                        [
                            'dependency_login_level_id' => $loginLevelDependency->id,
                            'created_by' => $userKey,
                            'updated_by' => $userKey
                        ]
                    );
                }

            }
            $dependenciesToDelete = $oldDependenciesIds->diff($newDependenciesIds);
            $loginLevel->loginLevelDependencies()->whereIn('dependency_login_level_id', $dependenciesToDelete)->delete();

            return response()->json($loginLevel, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteLoginLevel",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/loginLevel/{login_level_key}",
     *  summary="Delete a Login Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *
     * @SWG\Parameter(
     *      name="login_level_key",
     *      in="path",
     *      description="Login Level Key",
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
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteLoginLevel")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="'Failed to delete Login Level",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Login Level not Found'",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Deletes the specified Login Level
     *
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $loginLevelKey)
    {

        $userKey = ONE::verifyToken($request);

        try {
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")) {
                $loginLevel = LoginLevel::whereLoginLevelKey($loginLevelKey)->firstOrFail();

                $loginLevel->loginLevelUsers()->delete();
                $loginLevel->loginLevelDependencies()->delete();
                $loginLevel->loginLevels()->delete();
                $loginLevel->delete();

                return response()->json('Ok', 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Login Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *
     *  @SWG\Definition(
     *     definition="replyUpdateLoginLevelParameters",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *
     *  @SWG\Definition(
     *   definition="loginLevelUpdateParameters",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"parameter_user_type_key"},
     *           @SWG\Property(property="parameter_user_type_key", format="string", type="string"),
     *       )
     *   }
     * )
     *
     *
     * @SWG\Post(
     *  path="/loginLevel/{login_level_key}/updateLoginLevelParameters",
     *  summary="Update Login Level Parameters",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Login Level Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/loginLevelUpdateParameters")
     *  ),
     *
     *
     * @SWG\Parameter(
     *      name="login_level_key",
     *      in="path",
     *      description="Login Level Key",
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
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyUpdateLoginLevelParameters")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Login Level not Found'",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="'Failed to update Login Level Parameters",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates the User Parameters that belongs to the specified Login Level
     *
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLoginLevelParameters(Request $request, $loginLevelKey)
    {
        try {
            $userKey = ONE::verifyLogin($request);
            $loginLevel= LoginLevel::whereLoginLevelKey($loginLevelKey)->firstOrFail();

            $parameterUserType = ParameterUserType::whereParameterUserTypeKey($request->json('parameter_user_type_key'))->firstOrFail();

            if($loginLevel->parameters()->whereParameterUserTypeId($parameterUserType->id)->exists()){
                $existingRelation = LoginLevelParameter::whereParameterUserTypeId($parameterUserType->id)->whereLoginLevelId($loginLevel->id)->first();
                $existingRelation->updated_by = $userKey;
                $existingRelation->save();
                $existingRelation->delete();
            }else{
                LoginLevelParameter::create([
                    'parameter_user_type_id' => $parameterUserType->id,
                    'login_level_id' => $loginLevel->id,
                    'created_by' => $userKey,
                    'updated_by' => $userKey
                ]);
            }

            /*
            /*if ($loginLevel->parameterUserTypes()->pluck('parameter_user_type_key')->contains($parameterUserType->parameter_user_type_key)){

                $parameterUserType->update([
                    'login_level_id' => null
                ]);
            } else {
                $parameterUserType->update([
                    'login_level_id' => $loginLevel->id
                ]);
            }*/

            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Login Level Parameter'], 500);
        }
    }


    /**
     *
     *
     *
     *
     *
     * @SWG\Get(
     *  path="/loginLevel/{login_level_key}/getLoginLevelParameters",
     *  summary="Update Login Level Parameters",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *
     *
     *
     * @SWG\Parameter(
     *      name="login_level_key",
     *      in="path",
     *      description="Login Level Key",
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
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/loginLevelParameter")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Login Level not Found'",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="'Failed to get Login Level Parameters",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Get Login Level Parameters
     *
     * @param Request $request
     * @param $loginLevelKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoginLevelParameters(Request $request, $loginLevelKey)
    {
        try {
            $loginLevel = LoginLevel::whereLoginLevelKey($loginLevelKey)->firstOrFail();

            try{
                if(!empty($request->entity_key)){
                    $entity = Entity::whereEntityKey($request->entity_key)->with('parameterUserTypes')->firstOrFail();
                }else{
                    $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->with('parameterUserTypes')->firstOrFail();
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            $parameterUserTypes = $entity->parameterUserTypes;

            foreach ($parameterUserTypes as $parameterUserType) {

                if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                    if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT'))) {
                        if (!$parameterUserType->translation('en'))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }



                if($loginLevel->parameters()->whereParameterUserTypeId($parameterUserType->id)->exists()){
                    $parameterUserType['selected'] = true;
                } else {
                    $parameterUserType['selected'] = false;
                }
                /* if ($parameterUserType->login_level_id == $loginLevel->id){
                     $parameterUserType['selected'] = true;
                 } else {
                     $parameterUserType['selected'] = false;
                 }*/
            }

            return response()->json(['data' => $parameterUserTypes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Login Level not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Login Level Parameters'], 500);
        }
    }


    /**
     *
     *
     *
     *
     *
     * @SWG\Get(
     *  path="/loginLevel/manualListUsers",
     *  summary="List users that need manual login level verification",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Login Level"},
     *
     *
     *
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
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/usersManualResponse")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Not Found'",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Operation Failed",
     *      @SWG\Schema(ref="#/definitions/loginLevelErrorDefault")
     *  )
     * )
     *
     */

    /** List users that need manual login level verification
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualListUsers(Request $request)
    {
        try {
            try{
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            $entityLoginLevels = $entity->loginLevels()->whereManualVerification(true)->with('loginLevelDependencies')->get();

            $usersLoginLevels = $entity->users()->with('userLoginLevels')->get()->pluck('userLoginLevels','user_key');
            $manualCheckUsersKeys = [];
            foreach ($entityLoginLevels as $entityLoginLevel){
                if($entityLoginLevel->loginLevelDependencies->isEmpty()){
                    foreach ($usersLoginLevels as $userKey => $loginLevels){
                        if(!$loginLevels->contains('login_level_id',$entityLoginLevel->id)){
                            $manualCheckUsersKeys[$userKey][] = ['name' => $entityLoginLevel->name,'key' => $entityLoginLevel->login_level_key];
                        }
                    }
                }else{

                    foreach ($usersLoginLevels as $userKey => $loginLevels){
                        if($loginLevels->contains('login_level_id',$entityLoginLevel->id)){
                            continue;
                        }
                        $loginLevelDiff = $entityLoginLevel->loginLevelDependencies->pluck('dependency_login_level_id')->diff($loginLevels->pluck('login_level_id'));
                        if($loginLevelDiff->isEmpty()){
                            $manualCheckUsersKeys[$userKey][] = ['name' => $entityLoginLevel->name,'key' => $entityLoginLevel->login_level_key];
                        }
                    }
                }
            }
            $manualCheckUsers = [];
            foreach ($manualCheckUsersKeys as $userKey => $levelKeys){
                $manualCheckUsers[$userKey] = ['user_key' => $userKey, 'login_levels' => $levelKeys];
            }
            return response()->json(['data' => $manualCheckUsers], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Users check manual Login Level'], 500);
        }
    }
}
