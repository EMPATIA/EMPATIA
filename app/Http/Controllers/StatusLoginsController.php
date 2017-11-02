<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\StatusLogin;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class StatusLoginsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Status Login",
 *   description="Everything about Status Logins",
 * )
 *
 *  @SWG\Definition(
 *      definition="statusLoginErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="statusLoginCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code"},
 *           @SWG\Property(property="code", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="statusLoginReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class StatusLoginsController extends Controller
{
    protected $keysRequired = [
        'code'
    ];

    public function index(Request $request)
    {
        try{
            $statusLogin = StatusLogin::all();

            return response()->json(['data' => $statusLogin], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Status Login'], 500);
        }
    }

    /**
     * @SWG\Post(
     *  path="/statusLogin",
     *  summary="Create a Status Login",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Status Login"},
     *
     *  @SWG\Parameter(
     *      name="StatusLogin",
     *      in="body",
     *      description="Status Login Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/statusLoginCreate")
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
     *      description="the newly created Status Login",
     *      @SWG\Schema(ref="#/definitions/statusLoginReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/statusLoginErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Status Login not found",
     *      @SWG\Schema(ref="#/definitions/statusLoginErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Status Login",
     *      @SWG\Schema(ref="#/definitions/statusLoginErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $statusLogin = StatusLogin::create(
                [
                    'code'  => $request->json('page_key')
                ]
            );
            return response()->json($statusLogin, 201);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Status Login'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
