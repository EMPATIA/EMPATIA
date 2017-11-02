<?php

namespace App\Http\Controllers;

use App\Layout;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class LayoutsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Layout",
 *   description="Everything about Layouts",
 * )
 *
 *  @SWG\Definition(
 *      definition="layoutErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="layoutCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "reference"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="reference", format="string", type="string"),
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="layoutReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="layout_key", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="reference", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="layoutDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class LayoutsController extends Controller
{
    protected $keysRequired = [
        'name',
        'reference'
    ];

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            $layouts = Layout::all();
            return response()->json(['data' => $layouts], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Layouts'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Get(
     *  path="/layout/{layout_key}",
     *  summary="Show a Layout",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Layout"},
     *
     *  @SWG\Parameter(
     *      name="layout_key",
     *      in="path",
     *      description="Layout key",
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
     *      description="Show the Layout Type data",
     *      @SWG\Schema(ref="#/definitions/layoutReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Layout not Found",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Layout",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  )
     * )
     */

    /**
     * @param $layoutKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($layoutKey)
    {
        try{
            $layout = Layout::whereLayoutKey($layoutKey)->firstOrFail();
            return response()->json($layout, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Layout not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Layout'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/layout",
     *  summary="Create a Layout",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Layout"},
     *
     *  @SWG\Parameter(
     *      name="Layout",
     *      in="body",
     *      description="Layout Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/layoutCreate")
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
     *      description="the newly created Layout",
     *      @SWG\Schema(ref="#/definitions/layoutReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Layout not found",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Layout",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
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
            do {
                $rand = str_random(32);
                $key = "";

                if (!($exists = Layout::whereLayoutKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $layout = Layout::create(
                [
                    'layout_key'  =>  $key,
                    'name'        =>  $request->json('name'),
                    'reference'   =>  $request->json('reference')
                ]
            );
            return response()->json($layout, 201);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Layout'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/layout/{layout_key}",
     *  summary="Update a Layout",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Layout"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Layout Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/layoutCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="layout_key",
     *      in="path",
     *      description="Layout Key",
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
     *      description="The updated Layout",
     *      @SWG\Schema(ref="#/definitions/layoutReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Layout not Found",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Layout",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $layoutKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $layoutKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $layout = Layout::whereLayoutKey($layoutKey)->firstOrFail();

            $layout->name       = $request->json('name');
            $layout->reference  = $request->json('reference');
            $layout->save();

            return response()->json($layout, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Layout not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Layout'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/layout/{layout_key}",
     *  summary="Delete a Layout",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Layout"},
     *
     * @SWG\Parameter(
     *      name="layout_key",
     *      in="path",
     *      description="Layout Key",
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
     *      @SWG\Schema(ref="#/definitions/layoutDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Layout not Found",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Layout",
     *      @SWG\Schema(ref="#/definitions/layoutErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $layoutKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $layoutKey)
    {
        ONE::verifyToken($request);
        try{
            $layout = Layout::whereLayoutKey($layoutKey)->firstOrFail();
            $layout->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Layout not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Layout'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
