<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\Page;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class PagesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Page",
 *   description="Everything about Pages",
 * )
 *
 *  @SWG\Definition(
 *      definition="pageErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="pageCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"page_key", "type"},
 *           @SWG\Property(property="page_key", format="string", type="string"),
 *           @SWG\Property(property="type", format="string", type="string")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="pageUpdate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"type", "entity_id"},
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="type", format="string", type="string")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="pageReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="page_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="type", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="pageDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class PagesController extends Controller
{
    protected $keysRequired = [
        'page_key',
        'type'
    ];

    /**
     * Request the list of Pages
     * Returns the list of all Pages
     * @param Request $request
     * @internal param $
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $pages = Page::whereEntityId($entity->id)->get();
            } else {
                $pages = Page::all();
            }

            return response()->json(['data' => $pages], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Pages'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/page/{page_key}",
     *  summary="Show a Page",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Page"},
     *
     *  @SWG\Parameter(
     *      name="page_key",
     *      in="path",
     *      description="Page Key",
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
     *      description="Show the Page data",
     *      @SWG\Schema(ref="#/definitions/pageReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Page not Found",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Page",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of an Page
     * Returns the attributes of the Page
     * @param $pageKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $id
     * @internal param $
     */
    public function show($pageKey)
    {
        try{
            $page = Page::wherePageKey($pageKey)->firstOrFail();
            return response()->json($page, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Page not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Page'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/page",
     *  summary="Create a Page",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Page"},
     *
     *  @SWG\Parameter(
     *      name="page",
     *      in="body",
     *      description="Page Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/pageCreate")
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
     *      description="the newly created Page",
     *      @SWG\Schema(ref="#/definitions/pageReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not found",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Page",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Page in the database
     * Return the Attributes of the Page created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $page = Page::create(
                [
                    'page_key'  => $request->json('page_key'),
                    'type'      => $request->json('type'),
                    'entity_id' => $entity->id
                ]
            );
            return response()->json($page, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Page'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/page/{page_key}",
     *  summary="Update an Page",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Page"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Page Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/pageUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="page_key",
     *      in="path",
     *      description="Page Key",
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
     *      description="The updated Page",
     *      @SWG\Schema(ref="#/definitions/pageReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Page not Found",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Page",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Page
     * Return the Attributes of the Page Updated
     * @param Request $request
     * @param $pageKey
     * @return mixed
     */
    public function update(Request $request, $pageKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $page = Page::wherePageKey($pageKey)->firstOrFail();

            $page->type         = $request->json('type');
            $page->entity_id    = $request->json('entity_id');
            $page->save();

            return response()->json($page, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Page not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Page'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/page/{page_key}",
     *  summary="Delete a Page",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Page"},
     *
     * @SWG\Parameter(
     *      name="page_key",
     *      in="path",
     *      description="Page Key",
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
     *      @SWG\Schema(ref="#/definitions/pageDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Page not Found",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Page",
     *      @SWG\Schema(ref="#/definitions/pageErrorDefault")
     *  )
     * )
     */

    /**
     * Delete existing Page
     * @param Request $request
     * @param $pageKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $pageKey)
    {
        ONE::verifyToken($request);
        try{
            $page = Page::wherePageKey($pageKey)->firstOrFail();
            $page->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Page not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Page'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByType(Request $request, $type)
    {
        try{
            $value = $request->input('value');      // if set n_items to show
            $getAll = $request->input('get_all');   // if != null shows all content keys for given type

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $pages = Page::whereType($type)->whereEntityId($entity->id)->get();

            if (!empty($pages))
                $pagesKeysArray = collect($pages)->pluck('page_key')->toArray();
            else
                $pagesKeysArray  = [];

            if (is_null($getAll)){

                $response = ONE::post([
                    'component' => 'cm',
                    'api' => 'content',
                    'method' => 'activeContentsByKey',
                    'params' => [
                        'content_keys' => $pagesKeysArray,
                        'n_items' => $value,
                        'type' => !empty($type) ? $type : null
                    ]
                ]);

                if ($response->statusCode() == 200) {
                    $pages = $response->json()->data;
                }
            }else{
                $pages = $pagesKeysArray;
            }

            return response()->json(['data' => $pages], 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Pages'], 500);
        }
    }
}
