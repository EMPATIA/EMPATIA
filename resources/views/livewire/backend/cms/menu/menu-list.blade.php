<x-backend.card container="col-12 col-md-6 col-lg-7">
    <x-backend.card-header>
        {{ __('backend.cms.menus.list.title') }}
    </x-backend.card-header>

    <x-backend.card-body class="p-3 bg-light">
        <div class="menu-list menu-sort-container">
            @if(!empty($menus))
                @foreach($menus as $menu)
                    <div class="menu-group shadow rounded-bottom-3 pb-3 mb-3" id="item_{{$menu->id}}"
                         data-id="{{$menu->id}}">
                        <div class="menu-item bg-light shadow border-bottom rounded-top-3 py-2 px-1" role="button"
                             wire:click="$emitTo('menu-form', 'showMenu', {{ $menu->id }})">
                            @if($deletionStatusFilter!='1')
                                <button class="btn drag_handle"><i class="fas fa-expand-arrows-alt"></i></button>
                            @else
                                <a
                                    class="btn-fontawesome-icon btn btn-sm btn-light text-primary ms-1 restore-entry"
                                    title="' . __('backend::generic.restore') . '"
                                    data-id="{{$menu->id}}"
                                    data-component="menu-form">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                            @if(!empty($menuIcon = getField($menu, 'options.' .getLang() . '.icon')))
                                <i class="{{$menuIcon}}"></i>
                            @endif
                            <a role="button" class="fw-bold"
                               wire:click="$emitTo('menu-list', 'showMenu', {{ $menu->id }})">{{ getFieldLang($menu, 'title', '-') }}</a>
                        </div>

                        <div class="menu-list list-group-flush menu-sort-container">
                            @if( !empty($menu->children) )
                                @foreach($menu->children as $subMenu)
                                    <div class="list-group-item list-subitem py-1 px-3" id="item_{{$subMenu->id}}"
                                         data-id="{{$subMenu->id}}" role="button"
                                         wire:click="$emitTo('menu-form', 'showMenu', {{ $subMenu->id }})">

                                        <button class="btn drag_handle"><i class="fas fa-expand-arrows-alt"></i>
                                        </button>
                                        @if(!empty($subMenuIcon = getField($subMenu, 'options.' .getLang() . '.icon')))
                                            <i class="{{ $subMenuIcon }}"></i>
                                        @endif
                                        <a type="button"
                                           wire:click="$emitTo('menu-form', 'showMenu', {{ $subMenu->id }})"
                                        >{{ getFieldLang($subMenu, 'title', '-') }}</a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <h5 class="mb-0">{{ __('backend.cms.menus.list.empty') }}</h5>
            @endif
        </div>
    </x-backend.card-body>
</x-backend.card>


