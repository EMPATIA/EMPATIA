<?php

namespace App\Http\Controllers;

use App\KioskType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\One\One;
use Exception;

/**
 * Class KioskTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Kiosk Type",
 *   description="Everything about Kiosk Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="kioskTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="kioskTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

/**
 * Class KioskTypesController
 * @package App\Http\Controllers
 */
class KioskTypesController extends Controller
{
 
    /**
     * Requests a list of kiosk types.
     * Returns the list of kiosk types.
     * 
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        
        try {
            $kioskTypes = KioskType::all();

            return response()->json(["data" => $kioskTypes], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the kiosk types list'], 500);
        }           

        return response()->json(['error' => 'Unauthorized' ], 401);        
    }

    /**
     * @SWG\Get(
     *  path="/kiosktype/{kiosk_type_id}",
     *  summary="Show a Kiosk Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Kiosk Type"},
     *
     *  @SWG\Parameter(
     *      name="kiosk_type_id",
     *      in="path",
     *      description="Kiosk Type Id",
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
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Kiosk Type data",
     *      @SWG\Schema(ref="#/definitions/kioskTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/kioskTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Kiosk Type not Found",
     *      @SWG\Schema(ref="#/definitions/kioskTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Kiosk Type",
     *      @SWG\Schema(ref="#/definitions/kioskTypeErrorDefault")
     *  )
     * )
     */

    /**
     * Requests the details of a specific kiosk type.
     * Returns the details of a specific kiosk type.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @internal param $request
     */
    public function show($id)
    {
        try {
            $kioskType = KioskType::findOrFail($id);
            
            return response()->json($kioskType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Kiosk Type not Found.'], 404);  
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Kiosk Type.'], 500);
        }        
        
        return response()->json(['error' => 'Unauthorized' ], 401);                
    }       
}