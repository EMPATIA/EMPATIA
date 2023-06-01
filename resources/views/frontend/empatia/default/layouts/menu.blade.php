@php
    $menus = App\Http\Controllers\Frontend\CMS\FrontendController::getMenu($content ?? null);
@endphp

<header class="position-fixed">
    <nav class="navbar navbar-expand-md px-0 py-2">
        <div class="container d-flex">
            <a class="navbar-brand" href="/"><img src="{{ asset('/assets/img/las-logo.png') }}" class="logo"></a>
            <button class="navbar-toggler ms-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    @foreach($menus as $menuItem)
                        <li class="nav-item @if(!empty($menuItem['children'])) dropdown @endif {{ (($content->slug->{getLang()} ?? '') == $menuItem['link']) ? 'active' : '' }}">
                            <a id="menu-id-{{$menuItem['id']}}" class="nav-link pull-left" @if(empty($menuItem['children'])) href="{{$menuItem['link']}}" @else role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @endif>{{ $menuItem['title'] }}
                            @if(!empty($menuItem['children']))
                                <i class="fas fa-chevron-down"></i>
                            @endif
                            </a>
                            @if(!empty($menuItem['children']))
                                <div class="dropdown-menu" aria-labelledby="menu-id-{{$menuItem['id']}}">
                                    @foreach($menuItem['children'] as $child)
                                        <a class="dropdown-item" href="{{ $child['link'] }}">{{ $child['title'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <ul class="navbar-nav ms-auto my-3 my-lg-0 gap-1">
                    <li class="nav-item border-bottom-0 @if(!empty(Auth::user())) dropdown user-info @endif">
                        <a class="nav-link pb-0 pt-0 px-3"
                           @if(!empty(Auth::user())) href="#" role="button" data-bs-toggle="dropdown"
                           aria-expanded="false" @else aria-current="page"
                           href="/login" @endif >
                            <button class="btn btn btn-sm btn-primary px-3">
                                @if(empty(Auth::user()))
                                    {{__("frontend.$projectPath.menu.login.button") }}
                                @else
                                    @php
                                        $firstName = getField(collect(\App\Helpers\HKeycloak::getUsers())->where('id', Auth::user()->uuid)->first(), 'firstName');
                                        $lastName = explode(" ", getField(collect(\App\Helpers\HKeycloak::getUsers())->where('id', Auth::user()->uuid)->first(), 'lastName'));
                                        $name=  $firstName.' '.end($lastName);
                                    @endphp
                                    <i class="fa-solid fa-user me-1"></i> {{ $name }}
                                @endif
                            </button>
                        </a>
                        <ul class="dropdown-menu">
                            @if( !empty(Auth::user()) && Auth::user()->hasAnyRole(['laravel-admin','laravel-bo-user']))
                                <li><a class="dropdown-item"
                                       href="{{route('private')}}">{{ __("frontend.$projectPath.menu.backoffice.button") }}</a>
                                </li>
                            @endif
                            <li><a class="dropdown-item"
                                   href="/logout">{{ __("frontend.$projectPath.menu.logout.button") }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            @if(!empty($altLocalizedUrls))
                <li class="dropdown bg-white list-unstyled">
                    <a href="#" class="dropdown-toggle text-dark" data-toggle="dropdown">{{ $currentLanguage->name }} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        @foreach ($altLocalizedUrls as $alt)
                            <li><a href="{{ $alt['url'] }}" hreflang="{{ $alt['locale'] }}">{{ $alt['name'] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endif
        </div>
    </nav>
</header>
