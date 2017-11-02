<?php

namespace App\Http\Controllers;

use App\CoConstruction;
use App\Entity;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class CoConstructionsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Co-Construction",
 *   description="Everything about Co-Construction",
 * )
 *
 *  @SWG\Definition(
 *     definition="replyDeleteCoConstruction",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 *
 *  @SWG\Definition(
 *      definition="coConstructionErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="coConstructionCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="coConstructionUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="coConstructionReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="co_construction_key", format="string", type="string"),
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class CoConstructionsController extends Controller
{
    protected $keysRequired = [
        'cb_key'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                $coConstructions = $entity->coConstructions()->get();
            }
            else{
                $coConstructions = CoConstruction::all();
            }

            return response()->json(['data' => $coConstructions], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Co-Constructions'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/coConstruction/{co_construction_key}",
     *  summary="Show a Co-Construction",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Co-Construction"},
     *
     * @SWG\Parameter(
     *      name="co_construction_key",
     *      in="path",
     *      description="Co-Construction Key",
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
     *      description="Show the Co-Construction data",
     *      @SWG\Schema(ref="#/definitions/discussionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/discussionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Co-Construction not Found",
     *      @SWG\Schema(ref="#/definitions/discussionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Co-Construction",
     *      @SWG\Schema(ref="#/definitions/discussionErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param $coConstructionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($coConstructionKey)
    {
        try{
            $coConstruction = CoConstruction::whereCoConstructionKey($coConstructionKey)->firstOrFail();
            return response()->json($coConstruction, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Co-Construction not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Co-Construction'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/coConstruction",
     *  summary="Create a Co-Construction",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Co-Construction"},
     *
     *  @SWG\Parameter(
     *      name="coConstruction",
     *      in="body",
     *      description="PCo-Construction Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/coConstructionCreate")
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
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created Co-Construction",
     *      @SWG\Schema(ref="#/definitions/coConstructionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not found",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Co-Construction",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *  )
     * )
     *
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
            do {
                $rand = str_random(32);

                if (!($exists = CoConstruction::whereCoConstructionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $coConstruction = $entity->coConstructions()->create(
                [
                    'co_construction_key'  =>  $key,
                    'cb_key'        =>  $request->json('cb_key')
                ]
            );
            return response()->json($coConstruction, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Co-Construction'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/coConstruction/{co_construction_key}",
     *  summary="Update a Co-Construction",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Co-Construction"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Co-Construction Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/coConstructionUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="co_construction_key",
     *      in="path",
     *      description="Co-Construction Key",
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
     *      description="The updated Co-Construction",
     *      @SWG\Schema(ref="#/definitions/coConstructionReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Co-Construction not Found",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Co-Construction",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $coConstructionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $coConstructionKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $coConstruction = CoConstruction::whereCoConstructionKey($coConstructionKey)->firstOrFail();

            $coConstruction->cb_key = $request->json('cb_key');
            $coConstruction->save();

            return response()->json($coConstruction, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Co-Construction not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Co-Construction'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/coConstruction/{co_construction_key}",
     *  summary="Delete a Co-Construction",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Co-Construction"},
     *
     * @SWG\Parameter(
     *      name="co_construction_key",
     *      in="path",
     *      description="Co-Construction Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteCoConstruction")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Co-Construction not Found",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Co-Construction",
     *      @SWG\Schema(ref="#/definitions/coConstructionErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $coConstructionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $coConstructionKey)
    {
        ONE::verifyToken($request);
        try{
            $coConstruction = CoConstruction::whereCoConstructionKey($coConstructionKey)->firstOrFail();
            $coConstruction->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Co-Construction not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Co-Construction'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
