@php
    $menus = [];
    foreach(\App\Http\Controllers\Backend\CMS\MenusController::getMenu() as $menu) {
        $menus = array_merge($menus, \App\Helpers\HBackend::createMenuOption($menu));
    }
    $languages = \App\Helpers\HBackend::getBackendMenuLanguages()
@endphp


<nav class="col-md-3 col-lg-2 d-md-block side-menu collapse overflow-auto shadow-lg bg-white" id="side-menu">
    <div class="position-sticky">
        <div class="col-12 d-flex d-md-none align-items-center justify-content-around px-2 py-3">
            @include('backend.layouts.partials.top-menu-buttons')
        </div>
        <ul class="nav flex-column">
            @foreach($menus ?? [] as $key => $menu)
                <li class="mb-2 p-1">
                    <a class="nav-link btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed px-2 w-100 menu-header"
                       @if(!empty(getField($menu, 'submenu', []))) data-bs-toggle="collapse"
                       data-bs-target="#collapse-{{ $key }}" @endif
                       aria-expanded="false"
                       href="{{ getField($menu, 'url', '#') }}">

                        <i class="pe-2 {{ empty(getField($menu, 'icon')) ? 'fa-solid fa-circle' : getField($menu, 'icon') }}"></i>
                        <span class="fs-6 d-inline">{{ getField($menu,'text','') }}</span>

                        @if(!empty(getField($menu, 'submenu', [])))
                            <i class="fa-solid fa-chevron-right ms-auto"></i>
                        @endif
                    </a>

                    @foreach( getField($menu, 'submenu', []) as $subMenu)
                        <div class="collapse p-0" id="collapse-{{ $key }}">
                            <ul class="list-unstyled fw-normal py-1">
                                <li class="{{ getField($subMenu, 'classes', 'ps-2' )}}">
                                    <a href="{{ '/' . getLang() . getField($subMenu, 'url', '#') }}"
                                       class="nav-link text-decoration-none fs-6 menu-children rounded">
                                        <i class="p-2 {{ empty(getField($subMenu, 'icon')) ? 'fa-solid fa-circle' : getField($subMenu, 'icon') }}"></i>
                                        {{ getField($subMenu, 'text', '') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </li>
            @endforeach
        </ul>
    </div>
</nav>
