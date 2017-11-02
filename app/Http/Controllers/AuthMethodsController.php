<?php

namespace App\Http\Controllers;

use App\AuthMethod;
use App\Entity;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class AuthMethodsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Authentication Method",
 *   description="Everything about Authentication Method",
 * )
 *
 *  @SWG\Definition(
 *      definition="authMethodErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="authMethod",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"auth_method_key", "name", "description", "code"},
 *           @SWG\Property(property="auth_method_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string")
 *       )
 *   }
 * )
 */

class AuthMethodsController extends Controller
{
    protected $keysRequired = [
        'name'
    ];

    /**
     * Request list of all AuthMethod
     * Returns the list of all Authentication methods
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        try{
            $authMethod = AuthMethod::all();
            return response()->json(['data' =>$authMethod], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Authentication Methods list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/authmethod/{auth_method_key}",
     *  summary="Show an Authentication Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Authentication Method"},
     *
     * @SWG\Parameter(
     *      name="auth_method_key",
     *      in="path",
     *      description="Authentication Method Id",
     *      required=true,
     *      type="integer"
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
     *      description="Show the Access Menu data",
     *      @SWG\Schema(ref="#/definitions/authMethod")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Authentication Method not Found",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Authentication Method",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of one Authentication Method
     * Returns the attributes of the Authentication Method
     * @param Request $request
     * @param $authMethodKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $authMethodKey)
    {
        $userKey = ONE::verifyToken($request);

        if (OrchUser::verifyRole($userKey, "admin")){
            try {
                $authMethod = AuthMethod::whereAuthMethodKey($authMethodKey)->firstOrFail();
                return response()->json($authMethod, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Authentication Method not Found'], 404);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/authmethod",
     *  summary="Create an Authentication Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Authentication Method"},
     *
     *  @SWG\Parameter(
     *      name="auth_method",
     *      in="body",
     *      description="Authentication Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/authMethod")
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
     *      description="the newly created Authentication Method",
     *      @SWG\Schema(ref="#/definitions/authMethod")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Authentication Method Method not Found",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Authentication Method",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Authentication Method in the database
     * Return the Attributes of the Authentication Method created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                do {
                    $rand = str_random(32);
                    $key = "";

                    if (!($exists = AuthMethod::whereAuthMethodKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $authMethod = AuthMethod::create(
                    [
                        'auth_method_key'   => $key,
                        'name'              => $request->json('name'),
                        'description'       => $request->json('description') ?? '',
                        'code'              => $request->json('code')
                    ]
                );
                return response()->json($authMethod, 201);
            }catch(Exception $e){
                return response()->json(['error' => 'Failed to store new Authentication Method'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/authmethod/{auth_method_key}",
     *  summary="Update an Authentication Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Authentication Method"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Authentication Method Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/authMethod")
     *  ),
     *
     * @SWG\Parameter(
     *      name="auth_method_key",
     *      in="path",
     *      description="Authentication Method Key",
     *      required=true,
     *      type="integer"
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
     *      description="The updated Authentication Method",
     *      @SWG\Schema(ref="#/definitions/authMethod")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Authentication Method not Found",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Authentication Method",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Authentication Method
     * Return the Attributes of the Authentication Method Updated
     * @param Request $request
     * @param $authMethodKey
     * @return mixed
     */
    public function update(Request $request, $authMethodKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $authMethod                 = AuthMethod::whereAuthMethodKey($authMethodKey)->firstOrFail();

                $authMethod->name           = $request->json('name');
                $authMethod->description    = $request->json('description') ?? '';
                $authMethod->code           = $request->json('code');

                $authMethod->save();

                return response()->json($authMethod, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Authentication Method not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to update Authentication Method'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteAuthMethod",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/authmethod/{auth_method_key}",
     *  summary="Delete an Authentication Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Authentication Method"},
     *
     * @SWG\Parameter(
     *      name="auth_method_key",
     *      in="path",
     *      description="Authentication Method Key",
     *      required=true,
     *      type="integer"
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteAuthMethod")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="AccessMenu not Found",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete AccessMenu",
     *      @SWG\Schema(ref="#/definitions/authMethodErrorDefault")
     *  )
     * )
     */

    /**
     * Delete existing Authentication Method
     * @param Request $request
     * @param $authMethodKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $authMethodKey)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $authMethod = AuthMethod::whereAuthMethodKey($authMethodKey)->firstOrFail();
                $authMethod->delete();

                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Authentication Method not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Authentication Method'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAvailableAuthMethods(Request $request)
    {
        try{
            
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            } else {
                $entity = Entity::whereEntityKey($request->input('entity_key'))->firstOrFail();
            }            

            $authMethods = AuthMethod::all();
            $availableAuthMethods = [];

            foreach ($authMethods as $authMethod){
                if(!$entity->authMethodEntities()->whereAuthMethodId($authMethod->id)->exists()){
                    $availableAuthMethods[] = $authMethod;
                }
            }

            return response()->json(['data' =>$availableAuthMethods], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Authentication Methods list'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listEntityAuthMethods(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            } else {
                $entity = Entity::whereEntityKey($request->input('entity_key'))->firstOrFail();
            }

            $authMethods = $entity->authMethodEntities()->get();
            return response()->json(['data' =>$authMethods], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Authentication Methods list'], 500);
        }
    }
}
