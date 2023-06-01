@php
    $bannerImageContent = \App\Models\Backend\CMS\Content::where('code', 'banner-images')->pluck('sections')->toArray();
    $bannerImage = getField(findObjectByProperty('type', 'images', first_element($bannerImageContent) ?? []), 'value', []);
    $bannerImage = !empty($isCarousel) ? $bannerImage : [first_element($bannerImage)];
@endphp

<div id="carouselBannerIndicators" class="carousel slide" data-bs-ride="carousel">
    @if(!empty($isCarousel))
        <div class="carousel-indicators">
            @foreach($bannerImage ?? [] as $key => $image)
                <button type="button" data-bs-target="#carouselBannerIndicators" data-bs-slide-to="{{$key}}"
                        class="@if($loop->first) active @endif" aria-current="true" aria-label="Slide {{$key}}"></button>
            @endforeach
        </div>
    @endif
    <div class="carousel-inner">
        @foreach($bannerImage ?? [] as $key => $image)
            <div class="carousel-item @if($loop->first) active @endif">
                <img
                    src="{{ \App\Http\Controllers\Backend\FilesController::getImageUrlByName(getField($image, 'id', ''),null, null, 'webp') }}"
                    class="w-100 object-fit-cover" style="height: {{$bannerHeight}};">
            </div>
        @endforeach
    </div>
    @if( empty($isCarousel) && !empty($title ?? getField($content ?? null, 'title.'.getLang(), '')) )
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-6 position-relative bg-light-gray d-flex justify-content-center justify-content-md-end align-items-center"
                     style="margin-top: calc((2rem)*-1);">
                    <h5 class="text-uppercase text-primary mb-0">{{ getField($content, 'code') === 'news' ?  __("frontend.$projectPath.banner.$content->type.title") : $title ?? getField($content ?? null, 'title.'.getLang(), '') }}</h5>
                </div>
            </div>
        </div>
    @endif
</div>
