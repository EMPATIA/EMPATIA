<?php

namespace App\Http\Controllers;

use App\CbType;
use App\Entity;
use App\EntityCb;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class EntityCbsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Entity Pad",
 *   description="Everything about Entity PADs",
 * )
 *
 *  @SWG\Definition(
 *     definition="entityPadDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 *
 *  @SWG\Definition(
 *      definition="entityPadErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="entityPadCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cb_key", "code"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="entityPadReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="cb_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class EntityCbsController extends Controller
{
    protected $keysRequired = [
        'cb_key',
        'code'
    ];

    public function index(Request $request)
    {
        try{
            // Getting Entity from request header
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            
            if( !empty( $request->input('code') ) ){
                // Getting with a specif code
                $cbType = CbType::whereCode($request->input('code'))->firstOrFail();
                $entityCbs = $cbType->entityCbs()->with('cbType')->whereEntityId($entity->id)->get()->keyBy('cb_key');
            } else {
                // Getting all
                $entityCbs = EntityCb::with('cbType')->whereEntityId($entity->id)->get()->keyBy('cb_key');
            }

            return response()->json(['data' => $entityCbs], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbType not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Entity Pads'], 500);
        }
    }

    /**
     *
     * @SWG\Post(
     *  path="/entityCb",
     *  summary="Create an Entity Pad",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Pad"},
     *
     *  @SWG\Parameter(
     *      name="entityPad",
     *      in="body",
     *      description="Entity Pad Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityPadCreate")
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
     *      description="the newly created Entity Pad",
     *      @SWG\Schema(ref="#/definitions/entityPadReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Model not found",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Entity Pad",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
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
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $cbType = CbType::whereCode($request->json('code'))->firstOrFail();

            $entityCb = $cbType->entityCbs()->create(
                [
                    'cb_key'    => $request->json('cb_key'),
                    'entity_id' => $entity->id,
                ]
            );
            return response()->json($entityCb, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Entity Pad'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/entityCb/{entity_pad_key}",
     *  summary="Delete a Entity Pad",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Pad"},
     *
     * @SWG\Parameter(
     *      name="entity_pad_key",
     *      in="path",
     *      description="Entity Pad Key",
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
     *      @SWG\Schema(ref="#/definitions/entityPadDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity Pad not Found",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Entity Pad",
     *      @SWG\Schema(ref="#/definitions/entityPadErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        ONE::verifyToken($request);
        try{
            $entityCb = EntityCb::whereCbKey($key)->firstOrFail();
            EntityCb::destroy($entityCb->id);

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Pad not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Entity Pad'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
