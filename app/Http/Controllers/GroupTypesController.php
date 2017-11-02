<?php

namespace App\Http\Controllers;

use App\Entity;
use App\GroupType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\OrchUser;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class GroupTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Group Type",
 *   description="Everything about Group Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="groupTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="groupTypeCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code","name"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string")
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="groupTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="groupType_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="groupTypeDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class GroupTypesController extends Controller
{
    protected $keysRequired = [
        'code',
        'name'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $groupTypes = GroupType::all();
            return response()->json(['data' => $groupTypes], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the GroupTypes list'], 500);
        }
    }

    /**
     * @SWG\Get(
     *  path="/groupType/{group_type_key}",
     *  summary="Show a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Group Type"},
     *
     *  @SWG\Parameter(
     *      name="group_type_key",
     *      in="path",
     *      description="Group Type Key",
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
     *      description="Show the Group Type data",
     *      @SWG\Schema(ref="#/definitions/groupTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Group Type not Found",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $groupTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $groupTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")){
            try {
                $groupType = GroupType::whereGroupTypeKey($groupTypeKey)->firstOrFail();
                return response()->json($groupType, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch(Exception $e){
                return response()->json(['error' => 'Failed to retrieve the GroupType'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * @SWG\Post(
     *  path="/groupType",
     *  summary="Create a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Group Type"},
     *
     *  @SWG\Parameter(
     *      name="GroupType",
     *      in="body",
     *      description="Group Type Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/groupTypeCreate")
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
     *      response=201,
     *      description="the newly created Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Group Type not found",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")){
            try{

                // group_type_key generation
                $key = '';
                do {
                    $rand = str_random(32);

                    if (!($exists = GroupType::whereGroupTypeKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                //Group Type creation
                $groupType = GroupType::create(
                    [
                        'group_type_key' => $key,
                        'code' => $request->json('code'),
                        'name' => $request->json('name')
                    ]
                );

                return response()->json($groupType, 201);
            } catch(Exception $e){
                return response()->json(['error' => 'Failed to store GroupType'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/groupType/{group_type_key}",
     *  summary="Update a Group Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Group Type"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Group Type Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/groupTypeCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="group_type_key",
     *      in="path",
     *      description="Group Type Key",
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
     *      description="The updated Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Group Type not Found",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $groupTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $groupTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "admin")){

            try {
                $groupType = GroupType::whereGroupTypeKey($groupTypeKey)->firstOrFail();
                $groupType->code = $request->json('code');
                $groupType->name = $request->json('name');
                $groupType->save();

                return response()->json($groupType, 200);

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
     *  path="/groupType/{group_type_key}",
     *  summary="Delete a Group Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Group Type"},
     *
     * @SWG\Parameter(
     *      name="group_type_key",
     *      in="path",
     *      description="Group Type Key",
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
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/groupTypeDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Group Type not Found",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Group Type",
     *      @SWG\Schema(ref="#/definitions/groupTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $groupTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $groupTypeKey)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "admin")){
            try{

                $groupType = GroupType::whereGroupTypeKey($groupTypeKey)->firstOrFail();

                $groupType->delete();

                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'GroupType not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete GroupType'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);


    }
}
