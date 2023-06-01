@php
    $banner = App\Helpers\HFrontend::getContentByCode('pages-content');

    $w = 1024;
	$h = 768;
	$f = 'webp';

	if(App\Helpers\HFrontend::getSectionOptions($section) == 'no-resize') {
		$w = null;
		$h = null;
		$f = null;
	}
@endphp

@if($slug == 'home')
{{--<section class="hero"></section>--}}
<section class="hero hero-home d-flex flex-column flex-grow">
    <div class="flex-grow h-100">
        <div class="row h-100 hero-container d-flex align-items-center justify-content-center">
            <div class="hero-background h-100 w-100">
                @foreach($banner->sections ?? [] as $key => $section)
                    @if(empty($section->type))
                        @continue
                    @endif
                    @if($section->type == 'images')
                        {{--@include('frontend.cms.partials.images')--}}
                        @if(App\Helpers\HFrontend::getSectionCode($section) == 'banner')
                            @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                                <img class="hero-logo" alt="{{ App\Helpers\HFrontend::getSectionItemField($item, 'alt') }}"
                                        title="{{ App\Helpers\HFrontend::getSectionItemField($item, 'name') }}"
                                        src="{{ App\Helpers\HFrontend::getSectionItemImage($item, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                                >
                            @empty
                                {{ __('frontend:section.list.empty') }}
                            @endforelse
                        @endif
                    @endif
                @endforeach

                <div class="container-fluid" style="position:absolute; bottom:0;">
                    <div class="container">
                        @foreach($content->sections ?? [] as $key => $section)
                            @if(empty($section->type))
                                @continue
                            @endif

                            @if($section->type == 'heading')
                                @if(App\Helpers\HFrontend::getSectionCode($section) == 'title')
                                    @include('frontend.cms.partials.heading')
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
    <section class="hero hero-pages">
        <div class="flex-grow h-100">
            <div class="hero-background h-100 w-100">
                @foreach($banner->sections ?? [] as $key => $section)
                    @if(empty($section->type))
                        @continue
                    @endif

                    @if($section->type == 'images')
                        {{--@include('frontend.cms.partials.images')--}}
                        @if(App\Helpers\HFrontend::getSectionCode($section) == 'banner-pages')
                            @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                                {{-- <div class="col-6 col-sm-4 col-md-3"> --}}
                                    {{-- <div class="banner"> --}}
                                        <img class="hero-logo" alt="{{ App\Helpers\HFrontend::getSectionItemField($item, 'alt') }}"
                                                title="{{ App\Helpers\HFrontend::getSectionItemField($item, 'name') }}"
                                                src="{{ App\Helpers\HFrontend::getSectionItemImage($item, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                                        >
                                    {{-- </div> --}}
                                {{-- </div> --}}
                            @empty
                                {{ __('frontend:section.list.empty') }}
                            @endforelse
                        @endif
                    @endif
                @endforeach

                <div class="container-fluid" style="position:absolute; top:30%;">
                    <div class="container">
                        @foreach($content->sections ?? [] as $key => $section)
                            @if(empty($section->type))
                                @continue
                            @endif

                            @if($section->type == 'heading'  && $content->type != 'modules')
                                @if(App\Helpers\HFrontend::getSectionCode($section) == 'title')
                                    @include('frontend.cms.partials.heading')
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{--@include('frontend::layouts.breadcrumb')--}}
@endif
