<?php

namespace App\Http\Controllers;

use App\Entity;
use App\LevelParameter;
use App\One\One;
use App\ParameterUserType;
use App\Site;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

/**
 * Class LevelParametersController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Level Parameter",
 *   description="Everything about Level Parameters",
 * )
 *
 *  @SWG\Definition(
 *      definition="levelErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="Level Parameter",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"id", "level_parameter_key", "mandatory", "manual_verification", "name", "position"},
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="level_parameter_key", format="string", type="string"),
 *           @SWG\Property(property="mandatory", format="boolean", type="boolean"),
 *           @SWG\Property(property="manual_verification", format="boolean", type="boolean"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="position", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 */

class LevelParametersController extends Controller
{
    protected $required = [
        'store' => [],
        'update' => []
    ];

    /**
     *
     *
     * @SWG\Definition(
     *     definition="replyLevelList",
     *     required={"data"},
     *     @SWG\Property(
     *     property="data",
     *     type="object",
     *     allOf={
     *       @SWG\Schema(
     *     type="array",
     *      @SWG\Items(ref="#/definitions/Level Parameter")
     * ),
     *
     *   })
     * )
     *
     * @SWG\Get(
     *  path="/level/list",
     *  summary="List all levels of a site",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Level Parameter"},
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
     *      name="X-SITE-KEY",
     *      in="header",
     *      description="Site Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *     @SWG\Response(
     *      response="200",
     *      description="List of all Site Login Levels",
     *      @SWG\Schema(ref="#/definitions/replyLevelList")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Levels",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Returns all the Levels of a Site
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if(!empty($request->site_key)){
                $site = Site::where('key',$request->site_key)->firstOrFail();
            }else{
                $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            }
            $levelParameters = $site->levelParameters()->with('parameterUserTypes')->get();

            return response()->json(['data' => $levelParameters], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Levels'], 500);
        }
    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyShowLevel",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Level Parameter")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/level/{level_parameter_key}",
     *  summary="List a Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Level Parameter"},
     *
     * @SWG\Parameter(
     *      name="level_parameter_key",
     *      in="path",
     *      description="Level Parameter Key",
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
     *      name="X-SITE-KEY",
     *      in="header",
     *      description="Site Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *     @SWG\Response(
     *      response="200",
     *      description="Show the Login Level",
     *      @SWG\Schema(ref="#/definitions/replyShowLevel")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Levels",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Returns the specified Level of a Site
     *
     * @param Request $request
     * @param $levelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $levelParameterKey)
    {
        try {
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $levelParameter = $site->levelParameters()->whereLevelParameterKey($levelParameterKey)->firstOrFail();

            return response()->json($levelParameter, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Level'], 500);
        }
    }

    /**
     *
     * @SWG\Definition(
     *     definition="createLevel",
     *
     *     required={"name","mandatory","manual_verification"},
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="mandatory",
     *     type="boolean",
     *     format="boolean"
     * ),
     *     @SWG\Property(
     *     property="manual_verification",
     *     type="boolean",
     *     format="boolean"
     * )
     * )
     *
     * @SWG\Definition(
     *    definition="replyCreateLevel",
     *    @SWG\Property(
     *      property="level",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Level Parameter")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/level",
     *  summary="Creation of a Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Level Parameter"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Login Level data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createLevel")
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
     *  @SWG\Parameter(
     *      name="X-SITE-KEY",
     *      in="header",
     *      description="Site Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created level",
     *      @SWG\Schema(ref="#/definitions/replyCreateLevel")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Site not Found",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Level",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
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
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")) {

                $site = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))
                    ->firstOrFail()
                    ->sites()
                    ->where('key', $request->get("siteKey"))
                    ->firstOrFail();

                $position = $site->levelParameters()->count()+1;

                do {
                    $rand = str_random(32);
                    $key = "";
                    if (!($exists = LevelParameter::whereLevelParameterKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $levelParameter = $site->levelParameters()->create(
                    [
                        'level_parameter_key' => $key,
                        'mandatory' => $request->json('mandatory') ?? 0,
                        'show_in_registration' => $request->json('show_in_registration') ?? 0,
                        'sms_verification' => $request->json('sms_verification') ?? 0,
                        'manual_verification' => $request->json('manual_verification') ?? 0,
                        'name' => $request->json('name'),
                        'position' => $position
                    ]
                );

                return response()->json($levelParameter, 201);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Level'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *     definition="updateLevel",
     *
     *     required={"name","mandatory","manual_verification"},
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="mandatory",
     *     type="boolean",
     *     format="boolean"
     * ),
     *     @SWG\Property(
     *     property="manual_verification",
     *     type="boolean",
     *     format="boolean"
     * )
     * )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedLevel",
     *    @SWG\Property(
     *      property="level",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Level Parameter")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/level/{level_parameter_key}",
     *  summary="Update a Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Level Parameter"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Login Level Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateLevel")
     *  ),
     *
     * @SWG\Parameter(
     *      name="level_parameter_key",
     *      in="path",
     *      description="Level Parameter Key",
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
     *      @SWG\Schema(ref="#/definitions/replyUpdatedLevel")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Level Parameter not Found",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Level Parameter",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Updates and stores the specified Login Level for a Site
     *
     * @param Request $request
     * @param $levelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $levelParameterKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")) {

                $levelParameter = LevelParameter::whereLevelParameterKey($levelParameterKey)->firstOrFail();

                $levelParameter->mandatory              = $request->json('mandatory');
                $levelParameter->show_in_registration   = $request->json('show_in_registration');
                $levelParameter->sms_verification       = $request->json('sms_verification');
                $levelParameter->manual_verification    = $request->json('manual_verification');
                $levelParameter->name                   = $request->json('name');

                $levelParameter->save();

                return response()->json($levelParameter, 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Level Parameter not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Level Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteLevel",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/level/{level_parameter_key}",
     *  summary="Delete a Level",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Level Parameter"},
     *
     * @SWG\Parameter(
     *      name="level_parameter_key",
     *      in="path",
     *      description="Level Parameter Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteLevel")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="'Failed to delete Level Parameter",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Level Parameter not Found'",
     *      @SWG\Schema(ref="#/definitions/levelErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Deletes the specified Login Level
     *
     * @param Request $request
     * @param $levelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $levelParameterKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")) {
                $levelParameter = LevelParameter::whereLevelParameterKey($levelParameterKey)->firstOrFail();
                $levelParameter->users()->detach();
                $levelParameter->delete();

                return response()->json('Ok', 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Level Parameter not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Level Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Returns the User Parameters that belongs to the specified Login Level
     *
     * @param Request $request
     * @param $levelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginLevelParameters(Request $request, $levelParameterKey)
    {
        try {
            $levelParameter = LevelParameter::whereLevelParameterKey($levelParameterKey)->firstOrFail();

            $parameterUserTypes = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))
                ->firstOrFail()
                ->parameterUserTypes()
                ->get();

            foreach ($parameterUserTypes as $parameterUserType) {

                if (!($parameterUserType->translation($request->header('LANG-CODE')))) {
                    if (!$parameterUserType->translation($request->header('LANG-CODE-DEFAULT'))) {
                        if (!$parameterUserType->translation('en'))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }

                if ($parameterUserType->level_parameter_id == $levelParameter->id){
                    $parameterUserType['level_parameter_id'] = $parameterUserType['selected'] = true;
                } else {
                    $parameterUserType['level_parameter_id'] = $parameterUserType['selected'] = false;
                }
                unset($parameterUserType['level_parameter_id']);
            }

            return response()->json(['data' => $parameterUserTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Level Parameters'], 500);
        }
    }

    /**
     * Updates the User Parameters that belongs to the specified Login Level
     *
     * @param Request $request
     * @param $levelParameterKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLoginLevelParameters(Request $request, $levelParameterKey)
    {
        try {
            $levelParameter = LevelParameter::whereLevelParameterKey($levelParameterKey)->firstOrFail();
            $parameterUserTypeKey = ParameterUserType::whereParameterUserTypeKey($request->json('parameter_user_type_key'))->firstOrFail();

            if ($test = $levelParameter->parameterUserTypes()->pluck('parameter_user_type_key')->contains($parameterUserTypeKey->parameter_user_type_key)){
                $parameterUserTypeKey->update([
                    'level_parameter_id' => null
                ]);
            } else {
                $parameterUserTypeKey->update([
                    'level_parameter_id' => $levelParameter->id
                ]);
            }
            return response()->json('Ok', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Level'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLoginLevelPositions(Request $request)
    {
        try {
            $site = Site::where('key',$request->header('X-SITE-KEY'))->firstOrFail();
            $levelParameterKeys = $request->json('login_levels');

            foreach ($levelParameterKeys as $key => $value){
                $levelParameter = LevelParameter::whereLevelParameterKey($value)
                    ->whereSiteId($site->id)
                    ->first();

                $levelParameter->position = $key+1;
                $levelParameter->save();
            }
            return response()->json('Ok', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Update Level Parameters Order'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsersToModerate(Request $request){
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $sites = $entity->sites()->get();

            $users = [];
            foreach ($sites as $site){
                $levelModeration = $site->levelParameters()->whereManualVerification(1)->first();
                if(!empty($levelModeration)){
                    $usersToModerate = $entity->users()->whereRole('user')->get();
                    foreach ($usersToModerate as $userToModerate){
                        $userModerateLevel = $userToModerate->levelParameters()->whereSiteId($site->id)->first();
                        if(!empty($userModerateLevel)){
                            $level = $userModerateLevel->position;
                        }
                        else{
                            $level = 0;
                        }
                        if(($levelModeration->position-1) == $level){
                            $users[$userToModerate->user_key] = ['site_key' => $site->key ];
                        }
                    }
                }
            }
            return response()->json(['data' => $users], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteUsersToModerate(Request $request)
    {
        try {
            $siteKey = $request->site_key ?? null;
            $site = Site::where('key',$siteKey)->firstOrFail();

            $moderationLevel = $site->levelParameters()->whereManualVerification(1)->first();
            $users = Collection::make();

            if ($moderationLevel){
                $neededUserLevel = $site->levelParameters()->wherePosition($moderationLevel->position - 1)->first();
                $users = $neededUserLevel->users()->pluck('user_key');
            }

            return response()->json(['data' => $users], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
