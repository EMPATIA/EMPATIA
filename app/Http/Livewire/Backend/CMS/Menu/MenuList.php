<?php
namespace App\Http\Livewire\Backend\CMS\Menu;

use App\Http\Controllers\Backend\CMS\MenusController;
use Livewire\Component;

class MenuList extends Component
{
    CONST DEFAULT_MENU_TYPE_FILTER = 'private';
    CONST DEFAULT_MENU_DELETION_STATUS_FILTER = '0';

    public $typeFilter;
    public $deletionStatusFilter;
    public $lang = null;
    public $menus = null;
    public $menu = null;

    public $errorMessage = null;

    protected $listeners = ['filterMenus','menusMoved', 'reload'];

    public function mount() {
        $this->typeFilter = self::DEFAULT_MENU_TYPE_FILTER;
        $this->deletionStatusFilter = self::DEFAULT_MENU_DELETION_STATUS_FILTER;
        $this->lang = getLang();
        $this->reload();
    }

    public function render()
    {
        return view('livewire.backend.cms.menu.menu-list');
    }

    public function reload() {
        $this->menus = MenusController::getMenuTypesDelete($this->typeFilter,$this->deletionStatusFilter,0, true);
    }

    public function filterMenus($typeFilter = 'private',$deletionStatusFilter = '0') {
        $this->typeFilter = $typeFilter;
        $this->deletionStatusFilter = $deletionStatusFilter;
        $this->emitTo('menu-form', "menuTypeChanged");
        $this->reload();
    }

    public function menusMoved($id, $newIndex, $parentIdNew) {
        try{
            MenusController::updateMenuPosition($id, $newIndex, $parentIdNew, $this->typeFilter);
            $this->reload();
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            $this->errorMessage = __('backend.cms.menus.re-order-menu.error');
        }
    }


}
