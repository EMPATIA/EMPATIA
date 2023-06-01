@php
    $viewPort = $viewPort ?? 'Desktop'; //This is used because of F.E topic show (There are 2 carousels (Desktop e Mobile), and they can't have the same ID's)
@endphp

<div id="{{"carouselGalleryIndicators$viewPort"}}" class="carousel slide" data-bs-ride="true">
    <div class="carousel-indicators">
        @foreach($gallery ?? [] as $galleryImage)
            <button type="button" data-bs-target="{{"#carouselGalleryIndicators$viewPort"}}"
                    data-bs-slide-to="{{ $loop->index }}" @if($loop->first) class="active"
                    @endif aria-current="true"
                    aria-label="{{ "Slide " . $loop->index }}"></button>
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach($gallery ?? [] as $galleryImage)
            <div class="carousel-item @if($loop->first) active @endif">
                <img class="d-block w-100"
                     src="{{ \App\Http\Controllers\Backend\FilesController::getFileUrlByName($galleryImage) ?? $defaultImage }}"
                     style="object-fit: cover; height:{{ !empty($galleryHeight) ? $galleryHeight : '40vh' }}"
                >
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button"
            data-bs-target="{{"#carouselGalleryIndicators$viewPort"}}" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button"
            data-bs-target="{{"#carouselGalleryIndicators$viewPort"}}" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>