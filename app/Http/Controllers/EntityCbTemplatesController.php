<?php

namespace App\Http\Controllers;

use App\CbType;
use App\Entity;
use App\EntityCbTemplate;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class EntityCbTemplateController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Entity Template Pad",
 *   description="Everything about Entity Template PADs",
 * )
 *
 *  @SWG\Definition(
 *     definition="entityTemplatePadDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 *
 *  @SWG\Definition(
 *      definition="entityTemplatePadErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="entityTemplatePadCreate",
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
 *   definition="entityTemplatePadReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="cb_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class EntityCbTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            // Getting Entity from request header
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            if( !empty( $request->input('code') ) ){
                // Getting with a specif code
                $cbType = CbType::whereCode($request->input('code'))->firstOrFail();
                $entityCbs = $cbType->entityCbTemplates()->with('cbType')->whereEntityId($entity->id)->get()->keyBy('cb_key');

            } else {
                // Getting all
                $entityCbs = EntityCbTemplate::with('cbType')->whereEntityId($entity->id)->get()->keyBy('cb_key');

            }

            return response()->json(['data' => $entityCbs], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbType not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Entity Pads'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     *
     * @SWG\Post(
     *  path="/entityCbTemplate",
     *  summary="Create an Entity Template Pad",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Template Pad"},
     *
     *  @SWG\Parameter(
     *      name="entityTemplatePad",
     *      in="body",
     *      description="Entity Template Pad Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityTemplatePadCreate")
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
     *      description="the newly created Entity Template Pad",
     *      @SWG\Schema(ref="#/definitions/entityTemplatePadReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityTemplatePadErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Model not found",
     *      @SWG\Schema(ref="#/definitions/entityTemplatePadErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Entity Template Pad",
     *      @SWG\Schema(ref="#/definitions/entityTemplatePadErrorDefault")
     *  )
     * )
     *
     */

    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $cbType = CbType::whereCode($request->json('code'))->firstOrFail();

            $entityCb = $cbType->entityCbTemplates()->create(
                [
                    'cb_key'    => $request->json('cb_key'),
                    'entity_id' => $entity->id,
                    'name' => $request->json('name')
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
