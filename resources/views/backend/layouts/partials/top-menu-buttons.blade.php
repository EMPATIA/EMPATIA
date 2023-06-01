<div class="dropdown me-2">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-globe me-1" aria-hidden="true"></i>
        {{ strtoupper(getField($languages, 'currentLang', '' ) ) }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
        @if(count($languages) > 1 && isset($languages["submenu"]))
            @foreach($languages["submenu"] ?? [] as $language)
                <li><a class="dropdown-item" href="{{ getField($language, 'url', '') }}" hreflang="{{ getField($language, 'locale', '') }}">
                        {{ getField($language, 'name', '') }}
                    </a></li>
            @endforeach
        @endif
    </ul>
</div>

<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user me-1" aria-hidden="true"></i>
        {{ Auth::user()->name }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
        <li><a class="dropdown-item" href="{{'/' . getLang() . '/profile'}}">
                <i class="fa-solid fa-id-card me-1"></i>
                {{ __('backend.top-menu.profile.button') }}
            </a></li>
        <li><a class="dropdown-item" href="/logout">
                <i class="fa-solid fa-power-off text-danger me-1"></i>
                {{ __('backend.top-menu.logout.button') }}
            </a></li>
    </ul>
</div>