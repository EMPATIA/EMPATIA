<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuTranslation;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Class MenusController
 * @package App\Http\Controllers
 */
class MenusController extends Controller
{

    protected $keysRequired = [
        'access_id',
        'parent_id'
    ];

    /**
     * @SWG\Tag(
     *   name="Menus Method",
     *   description="Everything about Menus Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="menusErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *
     *  @SWG\Definition(
     *   definition="menusShowReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="menu_key", format="string", type="string"),
     *           @SWG\Property(property="access_id", format="integer", type="integer"),
     *           @SWG\Property(property="parent_id", format="integer", type="integer"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="page_id", format="integer", type="integer"),
     *           @SWG\Property(property="type", format="string", type="string"),
     *           @SWG\Property(property="value", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="link", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string"),
     *           @SWG\Property(property="menu_types", type="array", @SWG\Items(ref="#/definitions/menuTypesReply"))
     *       )
     *   }
     * )
     *
     *
     *  @SWG\Definition(
     *   definition="menuCreateUpdate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"access_id", "parent_id"},
     *      @SWG\Property(property="parent_id", format="integer", type="integer"),
     *      @SWG\Property(property="access_id", format="integer", type="integer"),
     *      @SWG\Property(property="type_id", format="integer", type="integer"),
     *      @SWG\Property(property="type", format="string", type="string"),
     *      @SWG\Property(property="value", format="string", type="string"),
     *      @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/menuTranslations"))
     *       )
     *   }
     * )
     *
     *
     *   @SWG\Definition(
     *   definition="menuDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *
     *  @SWG\Definition(
     *   definition="menusCreateUpdateReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="menu_key", format="string", type="string"),
     *           @SWG\Property(property="access_id", format="integer", type="integer"),
     *           @SWG\Property(property="parent_id", format="integer", type="integer"),
     *           @SWG\Property(property="position", format="integer", type="integer"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="type", format="string", type="string"),
     *           @SWG\Property(property="value", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string"),
     *       )
     *   }
     * )
     *
     *
     *   @SWG\Definition(
     *   definition="menuTranslations",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"language_code", "title"},
     *           @SWG\Property(property="language_code", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="link", format="string", type="string")
     *       )
     *   }
     * )
     *
     *
     *
     */


    /**
     * Requests a list of menus by Access Id.
     * Returns the list of menus by Access Id.
     *
     * @param $request
     * @param $accessId
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Request $request, $accessId)
    {
        ONE::verifyLogin($request);

        try {
            $menus = Menu::where("access_id","=",$accessId)
                    ->orderBy("position")
                    ->get();

            foreach ($menus as $menu) {
                if (!($menu->translation($request->header('LANG-CODE')))) {
                    if (!$menu->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            return response()->json(["data" => $menus], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the menus list'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    public function edit(Request $request, $menu_key)
    {
        try {

            $menu = Menu::whereMenuKey($menu_key)->firstOrFail();
            $parentMenu = Menu::find($menu->parent_id);

            is_null($parentMenu) ? $menu['parent_key'] = "" : $menu['parent_key'] = $parentMenu->menu_key;

            $menu->translations();

            return response()->json($menu, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/menu/{menu_key}",
     *  summary="Show a Menu Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Menu Method"},
     *
     * @SWG\Parameter(
     *      name="menu_key",
     *      in="path",
     *      description="Menu Method Key",
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
     *      name="LANG-CODE-DEFAULT",
     *      in="header",
     *      description="Default Laguage Code",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Menu data",
     *      @SWG\Schema(ref="#/definitions/menusShowReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Menu not Found | No translation found",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Requests the details of a specific menu.
     * Returns the details of a specific menu.
     *
     * @param Request $request
     * @param $menu_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function show(Request $request, $menu_key)
    {
        try {
            $menu = Menu::with('menuTypes')->whereMenuKey($menu_key)->firstOrFail();

            if (!($menu->translation($request->header('LANG-CODE')))) {
                if (!$menu->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($menu, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }


    /**
     *
     * @SWG\Post(
     *  path="/menu",
     *  summary="Create a Menu Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Menu Method"},
     *
     *  @SWG\Parameter(
     *      name="menu",
     *      in="body",
     *      description="Menu Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/menuCreateUpdate")
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
     *      description="the newly created Menu Method",
     *      @SWG\Schema(ref="#/definitions/menusCreateUpdateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="400",
     *      description="Failed to store new Menu translations",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Menu",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Store a newly created menu in storage.
     * Returns the details of the newly created menu.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        $menuLastPosition = Menu::whereParentId($request->json('parent_id'))->orderBy('position', 'desc')->first();
        $lastPosition = !empty($menuLastPosition) ?  $menuLastPosition->position + 1 : 0;

        try {
            do {
                $rand = str_random(32);
                if (!($exists = Menu::whereMenuKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $menu = Menu::create([
                'menu_key' => $key,
                'parent_id' => $request->json('parent_id'),
                'access_id' => $request->json('access_id'),
                'position'  => $lastPosition,
                'type_id'   => $request->json('type_id'),
                'type'      => $request->json('type'),
                'value'     => $request->json('value')
            ]);
            if ((!empty($request->json('translations'))) && (is_array($request->json('translations'))) ){
                foreach ($request->json('translations') as $translation){
                     $menu->menuTranslations()->create([
                        'language_code' => $translation['language_code'],
                        'title'       => $translation['title'],
                        'link'        => !empty($translation['link']) ? '/'.ltrim($translation['link'], '/') : "",
                        'updated_by' => $userKey,
                        'created_by' => $userKey
                    ]);
                }
            }
            else{
                Menu::destroy($menu->id);
                return response()->json(['error' => 'Failed to store new Menu translations'], 400);
            }
            return response()->json($menu, 201);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Menu'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/menu/{menu_key}",
     *  summary="Update a Menu Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Menu Method"},
     *
     *  @SWG\Parameter(
     *      name="menu",
     *      in="body",
     *      description="Menu Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/menuCreateUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="menu_key",
     *      in="path",
     *      description="Menu Key",
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
     *      description="The updated Menu",
     *      @SWG\Schema(ref="#/definitions/menusCreateUpdateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="400",
     *      description="Failed to update Menu",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Menu not Found",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update the menu in storage.
     * Returns the details of the updated menu.
     *
     * @param Request $request
     * @param $menu_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */

    public function update(Request $request, $menu_key)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $menu = Menu::whereMenuKey($menu_key)->firstOrFail();

            $menu->parent_id = $request->json('parent_id');
            $menu->access_id = $request->json('access_id');
            $menu->type = $request->json('type');
            $menu->type_id   = $request->json('type_id');
            $menu->value = $request->json('value');
            $menu->save();

            if ((!empty($request->json('translations'))) && (is_array($request->json('translations'))) ){
                foreach ($request->json('translations') as $translation){
                    $menuTranslation = $menu->menuTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($menuTranslation)) {
                        $menu->menuTranslations()->create([
                            'language_code' => $translation['language_code'],
                            'title'       => $translation['title'],
                            'link'        => !empty($translation['link']) ? '/'.ltrim($translation['link'], '/') : "",
                            'updated_by' => $userKey,
                            'created_by' => $userKey
                        ]);
                    }else{

                        $menuTranslation->language_code = $translation['language_code'];
                        $menuTranslation->title = $translation['title'];
                        $menuTranslation->link = !is_null($translation['link'])? $translation['link'] : "";
                        $menuTranslation->updated_by = $userKey;

                        $menuTranslation->save();
                    }
                }
            }

            return response()->json($menu, 200);
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update Menu'], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Reorder the menu item in storage.
     * Returns the details of the updated menu.
     *
     * @param Request $request
     * @param $menu_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function reorder(Request $request, $menu_key)
    {
        ONE::verifyToken($request);
        try {
            $positions = $request->json('positions');

            if(!empty($request->json('parent_key'))){
                $menu = Menu::whereMenuKey($menu_key)->firstOrFail();
                $parentMenu = Menu::whereMenuKey($request->json('parent_key'))->first();
                $menu->parent_id = $parentMenu->id;
                $menu->save();
            }

            foreach($positions as $position => $menuKey){
                $menuTmp = Menu::whereMenuKey($menuKey)->firstOrFail();

                if (empty($request->json('parent_key')))
                    $menuTmp->parent_id = 0;

                $menuTmp->position = $position;
                $menuTmp->save();
            }

            return response()->json($menu ?? "",200);
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to reorder Menu'], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     *
     *
     * @SWG\Delete(
     *  path="/menu/{menu_key}",
     *  summary="Delete menu Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Menu Method"},
     *
     * @SWG\Parameter(
     *      name="menu_key",
     *      in="path",
     *      description="Menu Key",
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
     *      @SWG\Schema(ref="#/definitions/menuDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Menu not Found",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Menu",
     *      @SWG\Schema(ref="#/definitions/menusErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified menu from storage.
     *
     * @param Request $request
     * @param $menu_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $menu_key)
    {
        ONE::verifyToken($request);

        try {
            $menu = Menu::whereMenuKey($menu_key)->firstOrFail();
            Menu::destroy($menu->id);
            return response()->json('OK', 200);
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Menu'], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Request the list of menus.
     * Returns the list of menus.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        try {
            $menus = $this->getMainMenu();
            return response()->json($menus, 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the menu list'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Requests an hierarquical structure of the menus.
     * Returns an hierarquical structure of the menus.
     *
     * @return array
     */
    private function getMainMenu()
    {
        try {
            $menusList = Menu::orderBy('position')
                    ->get();
            $menus = $this->buildMainMenu($menusList, []);
            return $menus;
        }
        catch (QueryException $e) {
            return response()->json(['error' => 'Failed to retrieve the menu list'], 500);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * This function is used to help to build the menus in a recursive way.
     *
     * @param $menus
     * @param $menusArray
     * @param $level
     * @param $parentid
     * @return array
     */
    private function buildMainMenu($menus, $menusArray, $level = 0, $parentid = 0)
    {
        if ($level >= 3) {
            return $menusArray;
        }

        foreach ($menus as $menu) {
            $subMenu = [];
            if ($menu->parent_id == $parentid) {
                $subMenu[0] = $menu->toArray();
                $subMenu = $this->buildMainMenu($menus, $subMenu, $level + 1, $menu->id);
                $menusArray[$menu->id] = (count($subMenu) == 1) ? $menu->toArray() : $subMenu;
            }
        }

        return $menusArray;
    }

    /**
     * Requests the details of a specific menu.
     * Returns the details of a specific menu.
     *
     * @param Request $request
     * @param $menu_key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function sonsList(Request $request, $menu_key)
    {
        try {
            $menu = Menu::whereMenuKey($menu_key)->firstOrFail();

            $menus = Menu::whereParentId($menu->id)
                    ->orderBy('position')
                    ->get();

            if (!($menu->translation($request->header('LANG-CODE')))) {
                if (!$menu->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json(["data" => $menus], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Requests the details of a specific menu.
     * Returns the details of a specific menu.
     *
     * @param $request
     * @param $page_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(Request $request, $page_id)
    {
        try {
            $menu = Menu::where("page_id", "=", $page_id)
                ->first();

            if (!($menu->translation($request->header('LANG-CODE')))) {
                if (!$menu->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($menu, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Requests a list of menus by Access Id.
     * Returns the list of menus by Access Id.
     *
     * @param $request
     * @param $accessId
     * @return \Illuminate\Http\JsonResponse
     */
//    public function listByAccessId(Request $request,$accessId)
    public function listByAccessId(Request $request, $accessId)
    {

        try {
            if ( (!empty(ONE::verifyLogin($request))) && ($accessId != 1) ){
                $menus = Menu::where("access_id","=",$accessId)
                    ->orderBy("position")
                    ->get();

                foreach ($menus as $menu) {
                    $menu->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                }

                $menus = $this->buildMainMenu($menus, []);
            }
            else{
                $menus = Menu::where("access_id","=",$accessId)
                    ->orderBy("position")
                    ->get();

                foreach ($menus as $menu) {
                    $menu->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                }

                $menus = $this->buildMainMenu($menus, []);
            }

            return response()->json(["data" => $menus], 200);
        }

        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the menus list'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

}
