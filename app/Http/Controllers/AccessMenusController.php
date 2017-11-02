<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Site;
use App\AccessMenu;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Class AccessMenusController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Access Menu",
 *   description="Everything about Access Menus",
 * )
 *
 *  @SWG\Definition(
 *      definition="accessMenuErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="accessMenuCreateReply",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_id", "site_id", "name", "description", "active", "created_by", "updated_by"},
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="site_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="active", format="boolean", type="boolean"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="updated_by", format="string", type="string"),
 *           @SWG\Property(property="site_key", format="string", type="string")
 *       )
 *   }
 * )
 *
 *
 *  @SWG\Definition(
 *   definition="accessMenuShowReply",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_id", "site_id", "name", "description", "active", "created_by", "updated_by"},
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="site_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="active", format="boolean", type="boolean"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="updated_by", format="string", type="string"),
 *           @SWG\Property(
 *              property="site",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/site")
 *           ),
 *       )
 *   }
 * )
 */

class AccessMenusController extends Controller
{
    protected $keysRequired = [
        'name',
        'site_key',
        'active'
    ];

    /**
     * AccessMenusController constructor.
     */
    public function __construct(Request $request)
    {

    }

    /**
     * Request list of all Access Menus
     * Returns the list of all Access Menus
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $accessMenus = AccessMenu::with('accessType','site')->where('entity_id', '=', $entity->id)->get();
            return response()->json(['data' => $accessMenus ], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Access Menus list'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/accessmenu/{access_menu_id}",
     *  summary="Show a AccessMenu",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Access Menu"},
     *
     * @SWG\Parameter(
     *      name="access_menu_id",
     *      in="path",
     *      description="Access Menu id",
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
     *      description="Show the Access Menu data",
     *      @SWG\Schema(ref="#/definitions/accessMenuShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="AccessMenu not Found",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve AccessMenu",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of one Access Menus
     * Returns the attributes of the Access Menus
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $id)
    {
        try {
            $accessMenu = AccessMenu::with('site')->findOrFail($id);
            return response()->json($accessMenu, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Access Menu not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Access Menu'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
     * @SWG\Post(
     *  path="/accessmenu",
     *  summary="Create an Access Menu",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Access Menu"},
     *
     *  @SWG\Parameter(
     *      name="access_menu",
     *      in="body",
     *      description="Access Menu data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/accessMenuCreateReply")
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
     *      description="the newly created Access Menu",
     *      @SWG\Schema(ref="#/definitions/accessMenuCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Access Menu not Found",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Access enu",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Access Menu in the database
     * Return the Attributes of the Access Menu created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $site = Site::where('key',$request->json('site_key'))->firstOrFail();
            if ($request->json('active') == 1) {
                $oldAccessMenu = $site->accessMenus()->whereActive('1')->whereSiteId($site->id)->first();
                
                if(!empty($oldAccessMenu)){
                    $oldAccessMenu->active = 0;
                    $oldAccessMenu->save();
                }
            }
            
            $accessMenu = $site->accessMenus()->create(
                [
                    'entity_id'     => $entity->id,
                    'site_id'       => $site->id,
                    'name'          => $request->json('name'),
                    'description'   => $request->json('description') ?? '',
                    'active'        => $request->json('active'),
                    'created_by'    => $userKey
                ]
            );

            return response()->json($accessMenu, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Object not Found'], 404);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Access Menu'], 500);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Access Menu'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/accessmenu/{access_menu_id}",
     *  summary="Update an Access Menu",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Access Menu"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Access Menu Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/accessMenuCreateReply")
     *  ),
     *
     * @SWG\Parameter(
     *      name="access_menu_id",
     *      in="path",
     *      description="Access Menu Id",
     *      required=true,
     *      type="integer"
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
     *      description="The updated Access Menu",
     *      @SWG\Schema(ref="#/definitions/accessMenuCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Access Menu not Found",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Access Menu",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Access Menu
     * Return the Attributes of the Access Menu Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $site = Site::where('key',$request->json('site_key'))->firstOrFail();
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            if ($request->json('active') == 1) {
                $oldAccessMenu = $site->accessMenus()->whereActive('1')->whereSiteId($site->id)->first();

                if(!empty($oldAccessMenu)){
                    $oldAccessMenu->active = 0;
                    $oldAccessMenu->save();
                }
            }

            $accessMenu = AccessMenu::findOrFail($id);

            $accessMenu->entity_id      = $entity->id;            
            $accessMenu->site_id        = $site->id;
            $accessMenu->name           = $request->json('name');
            $accessMenu->description    = $request->json('description') ?? '';
            $accessMenu->active         = $request->json('active');
            $accessMenu->updated_by     = $userKey;
            $accessMenu->save();

            return response()->json($accessMenu, 200);
        }catch (QueryException $e) {
            return response()->json(['error' => $e], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Access Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteAccessMenu",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/accessmenu/{access_menu_id}",
     *  summary="Delete an Access Menu",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Access Menu"},
     *
     * @SWG\Parameter(
     *      name="access_menu_id",
     *      in="path",
     *      description="Access Menu Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteAccessMenu")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Access Menu not Found",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Access Menu",
     *      @SWG\Schema(ref="#/definitions/accessMenuErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Delete existing Access Menu
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        try{
            AccessMenu::destroy($id);
            return response()->json('Ok', 200);
        } catch (QueryException $e) {

            return response()->json(['error' => 'Failed to delete Access Menu'], 500);

        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Access Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * Activate existing Access Menu
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $accessMenu = AccessMenu::findOrFail($id);
            $site = $accessMenu->site()->first();
            $oldAccessMenu = $site->accessMenus()->whereActive('1')->whereSiteId($site->id)->first();

            if(!empty($oldAccessMenu)){
                $oldAccessMenu->active = 0;
                $oldAccessMenu->save();
            }
            
            $accessMenu->active = 1;
            $accessMenu->save();

            return response()->json('Ok', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to activate Access Menu'], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Access Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Request list of the Access Menu active
     * Returns the list of all Access Menus
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function menuConstructor(Request $request)
    {
        try{
            $site = Site::where('key',$request->header('X-SITE-KEY'))->first();
            $accessMenu = AccessMenu::whereActive(1)->whereSiteId($site->id)->first();

            return response()->json($accessMenu, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Access Menu information'], 500);
        }
    }

}
