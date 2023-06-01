<?php
namespace App\Http\Livewire\Backend\CMS\Menu;

use App\Helpers\HBackend;
use App\Http\Controllers\Backend\CMS\MenusController;
use App\Models\Backend\CMS\Menu;
use Livewire\Component;
use App\Traits\LivewireForm;

class MenuForm extends Component
{
    use LivewireForm;

    private $prefix = "backend.cms.menus.";
    CONST DEFAULT_MENU_TYPE = 'private';

    // Menu properties
    public $menu;
    public $code;
    public $title;
    public $link;
    public $menu_type;
    public $options;
    public $parent_id;

    public $menuTypeOptions;
    public $menuParentOptions;
    public $action = 'show';
    public $selectorLanguage;

    //Trait properties (Deal with livewire form)
    public $controllerClass = MenusController::class;

    protected $listeners = ['showMenu','editMenu','updateMenu', 'changeLang', 'menuTypeChanged','restore'];

    public function mount() {
        $this->menuTypeOptions = MenusController::getMenuTypes(true);
        $this->menuParentOptions = MenusController::getMenuParents(true);
        $this->selectorLanguage = getLang();
    }

    public function render()
    {
        return view('livewire.backend.cms.menu.menu-form');
    }

    public function showMenu($id = null) {
        $this->action = 'show';
        $this->menu = Menu::whereId($id)->first();

        if($this->menu!=null){
            $this->menu = Menu::whereId($id)->first();
            $this->code = $this->menu->code;
            $this->title = (array)$this->menu->title;
            $this->link = (array)$this->menu->link;
            $this->menu_type = $this->menu->menu_type;
            $this->parent_id = $this->menu->parent_id;

            foreach (getLanguagesFrontend() ?? [] as $lang){
                $this->options[getField($lang,'locale')] = json_encode(getField($this->menu, 'options.' . getField($lang,'locale')));
            }
        }
    }

    public function editMenu() {
        $this->action = 'edit';
    }

    public function deleteMenu() {
        $this->controller('destroy', $this->menu->id);
        $this->emitTo('menu-list', 'reload');
    }

    public function restore($id)
    {
        $this->controller('restore',$id);
        $this->emitTo('menu-list','reload');
    }


    public function updateMenu() {
        $this->makeRequest('update', $this->menu->id);
        $this->emitTo('menu-list', 'reload');
        self::showMenu($this->menu->id);
    }

    // Event triggered when the input language selector changes
    public function changeLang($lang = null) {
        $this->selectorLanguage = $lang;
    }

    // Event triggered when the menu type filter changes
    public function menuTypeChanged() {
        $this->selectorLanguage = getLang();
        $this->menu = null;
    }

}
