<?php

namespace App\Http\Controllers;

use App\Entity;
use App\GeographicArea;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

/**
 * Class GeographicAreasController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Geographic Area",
 *   description="Everything about Geographic Area",
 * )
 *
 *  @SWG\Definition(
 *      definition="geographicAreaErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="geographicArea",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"geo_key", "entity_id", "name", "created_by"},
 *           @SWG\Property(property="geo_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="password", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string")
 *       )
 *   }
 * )
 *
 */

class GeographicAreasController extends Controller
{
    protected $keysRequired = [
        'name'
    ];

    /**
     * Request the lis of all Geographic Areas
     * Returns the list of all Geographic Areas
     * @param Request $request
     * @return list of all
     * @internal param $
     */
    public function index(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $geoAreas = GeographicArea::whereEntityId($entity->id)->get();
            return response()->json(['data' => $geoAreas], 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Geographic Areas'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/geoarea/{geographic_area_key}",
     *  summary="Show a Geographic Area",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Geographic Area"},
     *
     * @SWG\Parameter(
     *      name="geographic_area_key",
     *      in="path",
     *      description="Geographic Area Key",
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
     *      response="200",
     *      description="Show the Geographic Area data",
     *      @SWG\Schema(ref="#/definitions/geographicArea")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Geographic Area not Found",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Geographic Area",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Geographic Area in the database
     * Return the Attributes of the Geographic Area created
     * @param $key
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show($key)
    {
        try{
            $geoArea = GeographicArea::whereGeoKey($key)->firstOrFail();
            return response()->json($geoArea, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Geographic Area not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Geographic Area'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/geoarea",
     *  summary="Creation of a Geographic Area",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Geographic Area"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="GeographicArea data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/geographicArea")
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
     *      description="the newly created geographicarea",
     *      @SWG\Schema(ref="#/definitions/geographicArea")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new GeographicArea",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Geographic Area in the database
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            do {
                $rand = str_random(32);

                if (!($exists = GeographicArea::whereGeoKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);
            $geoArea = $entity->geoAreaEntity()->create(
                [
                    'geo_key' =>$key,
                    'name' => $request->json('name'),
                    'created_by' => $userKey
                ]
            );
            return response()->json($geoArea, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Geographic Area'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/geoarea/{geographic_area_key}",
     *  summary="Update a Geographic Area",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Geographic Area"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Geographic Area Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/geographicArea")
     *  ),
     *
     * @SWG\Parameter(
     *      name="geographic_area_key",
     *      in="path",
     *      description="Geographic Area Key",
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
     *      description="The updated Geographic Area",
     *      @SWG\Schema(ref="#/definitions/geographicArea")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="GeographicArea not Found",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update GeographicArea",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Geographic Area
     * Return the Attributes of the Geographic Area Updated
     * @param Request $request
     * @param $key
     * @return mixed
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $geoArea = GeographicArea::whereGeoKey($key)->firstOrFail();

            $geoArea->name = $request->json('name');
            $geoArea->save();

            return response()->json($geoArea, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Geographic Area not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Geographic Area'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteGeographicArea",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/geoarea/{geographic_area_key}",
     *  summary="Delete a Geographic Area",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Geographic Area"},
     *
     * @SWG\Parameter(
     *      name="geographic_area_key",
     *      in="path",
     *      description="Geographic Area Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteGeographicArea")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="GeographicArea not Found",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete GeographicArea",
     *      @SWG\Schema(ref="#/definitions/geographicAreaErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Delete existing Geographic Area
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $geoArea = GeographicArea::whereGeoKey($key)->firstOrFail();
            GeographicArea::destroy($geoArea->id);

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Geographic Area not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Geographic Area'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
