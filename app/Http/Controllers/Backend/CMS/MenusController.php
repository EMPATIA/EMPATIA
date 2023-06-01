<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Helpers\HCache;
use App\Helpers\HForm;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Helpers\HBackend;
use App\Models\Backend\CMS\Menu;
use App\Models\Backend\CMS\MenuType;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Str;

class MenusController extends Component
{
    private $prefix = "backend.cms.menus.";

    public $validateRules = [];
    public $validateMessages = [];

    private $guarded = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    function __construct($id = null)
    {
        parent::__construct($id);

        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'code' => [
                'rules' => ['required', 'string', 'max:50'],
            ],
            'title' => [
                'rules' => ['required', 'max:255'],
                'locale' => true,
            ],
            'parent_id' => [
                'rules' => ['required'],
            ],
            'menu_type' => [
                'rules' => ['required', 'string'],
            ],
            'options' => [
                'rules' => ['nullable'],
                'locale' => true,
            ],
            'link' => [
                'rules' => ['nullable'],
                'locale' => true,
            ],
        ], $this->prefix.'form.error');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view($this->prefix.'index', [
            'menuTypes' => self::getMenuTypes(true),
            'deletedTypes' =>  HBackend::getMenuTypeOptionsDelete(),
            'title' => __($this->prefix.'index.title'),
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // Used in component "menu-form"
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $menuTypeOptions = MenusController::getMenuTypes(true);
        $menuParentOptions = MenusController::getMenuParents(true);

        return view($this->prefix. 'menu', [
            'menu' => [],
            'action' => HForm::$CREATE,
            'title' => __($this->prefix.'create.title'),
            'menuTypeOptions' => $menuTypeOptions,
            'menuParentOptions' => $menuParentOptions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate($this->validateRules, $this->validateMessages);

        try {
            if($menu = Menu::create([
                'code' => Str::slug($request->input('code')),
                'title' => HBackend::setInput($request, 'title'),
                'menu_type' => $request->input('menu_type'),
                'parent_id' => $request->input('parent_id'),
                'link' => HBackend::setInput($request, 'link'),
                'options' => HBackend::setInput($request, 'options', true),
            ])) {
                HCache::flushMenus($menu->menu_type ?? null);
            }
            flash()->addSuccess(__('backend.generic.store.ok'));
            return redirect()->action([self::class, 'index']);

        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
        }
        flash()->addError(__('backend.generic.store.error'));
        return redirect()->back()->withInput();
    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        // Used in component "menu-form"
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate($this->validateRules, $this->validateMessages);

        try {
            $menu = Menu::findOrFail($id);
            if($menu->update([
                'code' => Str::slug($request->input('code')),
                'title' => HBackend::setInput($request, 'title'),
                'menu_type' => $request->input('menu_type'),
                'parent_id' => $request->input('parent_id'),
                'link' => HBackend::setInput($request, 'link'),
                'options' => HBackend::setInput($request, 'options', true),
            ])) {
                HCache::flushMenus($menu->menu_type ?? null);
            }
            //Doesn't redirect because it's used inside a component
            flash()->addSuccess(__('backend.generic.update.ok'));
        } catch (QueryException | Exception  | \Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.update.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id); //puts deleted menu with no parent
            $menu->parent_id = 0;
            $menu->save();

            Menu::where('parent_id', '=', $id) //puts the childs of a deleted parent their own menu
            ->update(['parent_id' => 0]);



            if($menu->delete()) {
                HCache::flushMenus($menu->menu_type ?? null);
            }

            //Doesn't redirect because it's used inside a component
            flash()->addSuccess(__('backend.generic.destroy.ok'));

        } catch (QueryException | Exception  | \Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.destroy.error'));
            return redirect()->back();
        }
    }


    /**
     * Restore the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {

        try {
            DB::beginTransaction();
            if (Menu::withTrashed()->findOrFail($id)->restore()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.restore.ok'));
                return response()->json(['success' => 'success'], 200);
            }

        } catch (QueryException | Exception | \Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.restore.error'));
            return redirect()->back()->withInput();
        }
        flash()->addError(__('backend.generic.restore.error'));
        return redirect()->back();
    }



    /**
     * Get menu blade depending on the type of menu
     *
     * @param string $menuType
     * @param int $parentId
     * @return array
     */
    public static function getMenu($menuType = 'private', $parentId = 0) : array {
        try{
            $user = auth()?->user();
            if( empty($user) ){
                throw new \Exception('Empty user');
            }

            $allowedRoles = ['admin', 'laravel-admin'];

            $menus = MenusController::getMenuChildren($menuType, $parentId);

            $arr = [];
            foreach($menus ?? [] as $menu) {
                if ( !$user->hasAnyRole(array_merge($allowedRoles, $menu->roles ?? []) ) ){
                    continue;
                }

                $menu->children = self::getMenu($menuType, $menu->id);
                $arr[] = $menu;
            }

            return $arr;
        } catch(Exception | \Throwable $e) {
            logError("Error getting menu: ".$e->getMessage());
            return [];
        }
    }



    /**
     * Get menu blade depending on the type of menu
     *
     * @param string $menuType
     * @param int $parentId
     * @return array
     */
    public static function getMenuTypesDelete($menuType = 'private',$delete = '0', $parentId = 0) : array {

        try{
            if(empty($delete)) {
                $delete = 0;
            }
            $menus = MenusController::getMenuChildrenDelete($menuType,$delete ,$parentId);

            $arr = [];
            foreach($menus ?? [] as $menu) {
                $menu->children = self::getMenu($menuType, $menu->id);
                $arr[] = $menu;
            }

            return $arr;
        } catch(Exception | \Throwable $e) {
            logError("Error getting menu: ".$e->getMessage());
            return [];
        }
    }


    /**
     * Get menu children to create hierarchical menu structure
     *
     * @param $menuType
     * @param int $parentId
     * @return Collection
     */
    public static function getMenuChildren($menuType, $parentId = 0): ?Collection
    {
        try{
            return Menu::where('menu_type', $menuType)
                ->whereParentId($parentId)
                ->orderBy('position')
                ->get();
        } catch(Exception | \Throwable $e) {
            logError("Error getting menu from database: ".$e->getMessage());
            return null;
        }
    }


    /**
     * Get menu children to create hierarchical menu structure with deleted
     *
     * @param $menuType
     * @param $deleted
     * @param int $parentId
     * @return Collection
     */
    public static function getMenuChildrenDelete($menuType,$deleted, $parentId = 0): ?Collection
    {

        try{

            if ($deleted=='0'){
                return Menu::where('menu_type', $menuType)
                    ->whereNull('deleted_at')
                    ->whereParentId($parentId)
                    ->orderBy('position')
                    ->get();
            }else{
                return Menu::where('menu_type', $menuType)
                    ->onlyTrashed()->get();
            }

        } catch(Exception | \Throwable $e) {
            logError("Error getting menu from database: ".$e->getMessage());
            return null;
        }
    }



    public static function reorderMenus($menuType, $parentId = 0, $newIndex = null, $parentIdNew = null, $id = null){
        try{

            $menus = Menu::where('menu_type', $menuType)
                ->where('id','<>',$id)
                ->whereParentId($parentId)
                ->orderBy('position')
                ->get();

            foreach($menus as $key => $menu) {

                $position = $key;
                if($menu->parent_id == $parentIdNew){
                    $position = $key >= $newIndex ? $key+1 : $position;
                }
                $menu->position = $position;
                $menu->save();
                self::reorderMenus($menuType, $menu->id, $newIndex, $parentIdNew, $id);
            }
        }catch(\Exception $e){

        }
    }

    public static function updateMenuPosition($id, $newIndex, $parentIdNew, $menuType){
        try{
            //REORDER ALL MENUS BEFORE START
            self::reorderMenus($menuType,0, $newIndex, $parentIdNew, $id);

            $menu = Menu::whereId($id)->firstOrFail();
            $menu->parent_id = $parentIdNew ?? 0;
            $menu->position = $newIndex ?? 0;
            if($menu->save()){
                HCache::flushMenus($menu->menu_type ?? null);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
        }
    }

    // Get menu types options available
    public static function getMenuTypes($convertToArray = false){
        try {
            $menuTypes = HBackend::getConfigurationByCode('menu_types');
            if(!$convertToArray){
                return $menuTypes;
            }
            else{
                $menuTypesArray = [];
                foreach ($menuTypes ?? [] as $menuType){
                    $menuTypesArray[$menuType->code] = getFieldLang($menuType, 'name');
                }
                return $menuTypesArray;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line ' . $e->getTraceAsString());
            return [];
        }
    }

    // Get menu parents options available
    public static function getMenuParents($convertToArray = false){
        try {
            $menuParents = Menu::where('parent_id', 0)->get();

            if(!$convertToArray){
                return $menuParents;
            }
            else{
                $menuParentsArray = [];
                foreach ($menuParents ?? [] as $menuParent){
                    $menuParentsArray[$menuParent->id] = getFieldLang($menuParent, 'title');
                }
                $menuParentsArray[0] = __('backend.cms.menus.form.menu-parent.no-parent'); //Add no parent option

                return $menuParentsArray;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line ' . $e->getTraceAsString());
            return [];
        }
    }

}
