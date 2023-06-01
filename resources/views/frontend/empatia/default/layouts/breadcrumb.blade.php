@php
    $menus = \Modules\Frontend\Http\Controllers\FrontendController::getMenu($content ?? null);
    $menusArray = [];
    $menusArray = App\Helpers\HFrontend::findActiveMenu($menus, []);
    $breadcrumbs = [];
    $breadcrumbs = App\Helpers\HFrontend::buildBreadcrumbs($menusArray ?? [], $breadcrumbs);
    $title = '';
    foreach($content->sections ?? [] as $key => $section){
        if($key == 0 && $section->type == 'heading'){
            $title = App\Helpers\HFrontend::getSectionValue($section);
        }
    }
    $lang = getLang();
@endphp
<section class="breadcrums">
    <div class="container">
        <a href="{{ asset("/") }}">{{__('frontend::hero.home')}}</a>
        @if($content->type == 'news')
            <i class="fas fa-chevron-right"></i>
            <a href="/news">{{__('frontend::hero.news')}}</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/news/{{ $content->slug->$lang }}">{{ $title }}</a>
        @else
            @forelse($breadcrumbs as $breadcrumb)
                <i class="fas fa-chevron-right"></i>
                <a @if(!empty($breadcrumb['link'])) href="{{ asset($breadcrumb['link']) }}" @endif>{{ $breadcrumb['title'] }}</a>
            @empty
                @if(!empty($title))
                    <i class="fas fa-chevron-right"></i>
                    <a href="/{{ $content->slug->$lang }}">{{ $title }}</a>
                @endif
            @endforelse
        @endif
    </div>
</section>
