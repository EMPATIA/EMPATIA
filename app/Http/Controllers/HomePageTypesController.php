<?php

namespace App\Http\Controllers;

use App\Entity;
use App\HomePageType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class HomePageTypesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Home Page Type",
 *   description="Everything about Home Page Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="homePageTypeErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="homePageTypeCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code", "type_code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="type_code", format="string", type="string"),
 *           @SWG\Property(property="parent_key", format="string", type="string")
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="homePageTypeShowReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="home_page_type_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="parent_id", format="integer", type="integer"),
 *           @SWG\Property(property="type_code", format="string", type="string"),
 *           @SWG\Property(
 *              property="parent",
 *              type="object",
 *              allOf={
 *                  @SWG\Schema(ref="#/definitions/homePageTypeReply")
 *              }
 *           ),
 *           @SWG\Property(
 *              property="childs",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/homePageTypeReply")
 *           ),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="homePageTypeReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="home_page_type_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="parent_id", format="integer", type="integer"),
 *           @SWG\Property(property="type_code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="homePageTypeDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class HomePageTypesController extends Controller
{
    protected $keysRequired = [
        'name',
        'code'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $homePageTypes = HomePageType::whereEntityId($entity->id)->get();
            } else {
                $homePageTypes = HomePageType::all();
            }

            return response()->json(['data' => $homePageTypes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function groupsList(Request $request)
    {

        try{

            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $homePageTypes = HomePageType::whereEntityId($entity->id)->get();
            } else {
                $homePageTypes = HomePageType::all();
            }
            $homePageTypes = HomePageType::whereNull('parent_id')->get();
            //$homePageTypes = HomePageType::where('id',21)->get();

            return response()->json(['data' => $homePageTypes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function groupTypesList(Request $request)
    {

        try{
            $parent = HomePageType::whereHomePageTypeKey($request->json('home_page_type_key'))->firstOrFail();
            $homePageTypes = HomePageType::where('parent_id',$parent->id)->get();
            return response()->json(['data' => $homePageTypes], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Get(
     *  path="/homePageType/{home_page_type_key}",
     *  summary="Show a Home Page Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Type"},
     *
     *  @SWG\Parameter(
     *      name="home_page_type_key",
     *      in="path",
     *      description="Home Page Type Key",
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
     *      description="Show the Home Page Type data",
     *      @SWG\Schema(ref="#/definitions/homePageTypeShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Home Page Type not Found",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param $homePageTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($homePageTypeKey)
    {
        try{
            $homePageType = HomePageType::whereHomePageTypeKey($homePageTypeKey)->firstOrFail();

            if (!is_null($homePageType->parent_id)){
                $parentHomePageType = HomePageType::findOrFail($homePageType->parent_id);
                $homePageType['parent'] = $parentHomePageType;
            }

            $homePageType['childs'] = HomePageType::whereParentId($homePageType->id)->get();
            return response()->json($homePageType, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Post(
     *  path="/homePageType",
     *  summary="Create a Home Page Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Type"},
     *
     *  @SWG\Parameter(
     *      name="HomePageType",
     *      in="body",
     *      description="Home Page Type Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/homePageTypeCreate")
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
     *      description="the newly created Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Home Page Type not found",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
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

                if (!($exists = HomePageType::whereHomePageTypeKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $parentKey = empty($request->json('parent_key')) ? null : $request->json('parent_key');
            if (!is_null($parentKey)){
                $parentHomePageType = HomePageType::whereHomePageTypeKey($parentKey)->firstOrFail();
            }

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $homePageType = $entity->homePageTypes()->create(
                [
                    'home_page_type_key'    => $key,
                    'name'                  => $request->json('name'),
                    'code'                  => $request->json('code'),
                    'parent_id'             => isset($parentHomePageType->id)?$parentHomePageType->id:null,
                    'type_code'             => $request->json('type_code'),
                ]
            );
            return response()->json($homePageType, 201);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Home Page Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Put(
     *  path="/homePageType/{home_page_type_key}",
     *  summary="Update a Home Page Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Type"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Home Page Type Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/homePageTypeCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="home_page_type_key",
     *      in="path",
     *      description="Home Page Type Key",
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
     *      description="The updated Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Home Page Type not Found",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $homePageTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $homePageTypeKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $homePageType = HomePageType::whereHomePageTypeKey($homePageTypeKey)->firstOrFail();

            $parentKey = empty($request->json('parent_key')) ? null : $request->json('parent_key');
            if (!is_null($parentKey)){
                $parentHomePageType = HomePageType::whereHomePageTypeKey($parentKey)->firstOrFail();
                $homePageType->parent_id = $parentHomePageType->id;
            }

            $homePageType->name         = $request->json('name');
            $homePageType->code         = $request->json('code');
            $homePageType->type_code    = $request->json('type_code');
            $homePageType->save();

            return response()->json($homePageType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Home Page Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/homePageType/{home_page_type_key}",
     *  summary="Delete a Home Page Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Home Page Type"},
     *
     * @SWG\Parameter(
     *      name="home_page_type_key",
     *      in="path",
     *      description="Home Page Type Key",
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
     *      @SWG\Schema(ref="#/definitions/homePageTypeDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Home Page Type not Found",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Home Page Type",
     *      @SWG\Schema(ref="#/definitions/homePageTypeErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $homePageTypeKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $homePageTypeKey)
    {
        ONE::verifyToken($request);
        try{
            $homePageType = HomePageType::whereHomePageTypeKey($homePageTypeKey)->firstOrFail();
            $homePageType->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Home Page Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Retrieve a list of all the Home Page Types with no Home Page Type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function parentsList(Request $request)
    {
        try{
            if(empty($request->json('home_page_type_key'))){
                $parentList = HomePageType::whereParentId(null)->get();
            } else {
                $parentList = HomePageType::whereParentId(null)->where('home_page_type_key','!=',$request->json('home_page_type_key'))->get();
            }

            return response()->json(['data' => $parentList], 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Home Page Types not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Home Page Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}