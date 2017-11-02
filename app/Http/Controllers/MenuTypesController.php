<?php

namespace App\Http\Controllers;

use App\MenuType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\One\One;
use Exception;

/**
 * Class MenuTypesController
 * @package App\Http\Controllers
 */
class MenuTypesController extends Controller
{

    /**
     * @SWG\Tag(
     *   name="Menu Types Method",
     *   description="Everything about Menu Types Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="menuTypesMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="menuTypesReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="type", format="string", type="string"),
     *           @SWG\Property(property="module", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     */
 
    /**
     * Requests a list of menu types.
     * Returns the list of menu types.
     * 
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $menuTypes = MenuType::all();

            return response()->json(["data" => $menuTypes], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the menu types list'], 500);
        }           

        return response()->json(['error' => 'Unauthorized' ], 401);        
    }

    /**
     *
     * @SWG\Get(
     *  path="/menutype/{menu_type_id}",
     *  summary="Show a Menu Type Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Menu Type Method"},
     *
     * @SWG\Parameter(
     *      name="menu_type_id",
     *      in="path",
     *      description="Menu Type Method Id",
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
     *      description="Show the Menu Type data",
     *      @SWG\Schema(ref="#/definitions/menuTypesReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/menuTypesMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Menu type not Found",
     *      @SWG\Schema(ref="#/definitions/menuTypesMethodErrorDefault")
     *  )
     * )
     *
     */
    
    /**
     * Requests the details of a specific menu type.
     * Returns the details of a specific menu type.
     * 
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $menuType = MenuType::findOrFail($id);
            
            return response()->json($menuType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu type not Found'], 404);
        }          
        
        return response()->json(['error' => 'Unauthorized' ], 401);                
    }       
}