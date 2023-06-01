{{--

*** IMAGES ***

Image items list. Use code or options to define slideshow or image list.

{
    type : images,
    code : <code>,
    class : <class>,
    value : {
        0 : {
            id : id,
            code : <code>,
            filename : <filename>,
            size : size,
            en : {
                enabled : true/false
                name : <name>,
                desc: <description>,
                alt: <alt>,
                link: <link>
            },
            (... languages ...)
        },
        (... list ...)
    },
    options : <options>
}

Helpers:
    - App\Helpers\HFrontend::getSectionCode($section);
    - App\Helpers\HFrontend::getSectionClass($section);
    - App\Helpers\HFrontend::getSectionOptions($section, [$field]);
    - App\Helpers\HFrontend::getSectionEnabledItems($section, [$lang]);
    - App\Helpers\HFrontend::getSectionItemImage($item, [$default], [$w], [$h], [$format], [$quality]);
    - App\Helpers\HFrontend::getSectionItemField($item, $field, [$lang]);

--}}
@php
    use App\Helpers\HFrontend;

	$w = 1024;
	$h = 768;
	$f = 'webp';

	if(HFrontend::getSectionOptions($section) == 'no-resize') {
		$w = null;
		$h = null;
		$f = null;
	}

    $enabledImages = HFrontend::getSectionEnabledItems($section);
    $numberOfImages = count((array)$enabledImages ?? []);

    $sectionOptions = json_decode(HFrontend::getSectionOptions($section));
    $sectionClass =  HFrontend::getSectionClass($section);
    $outerClass = HFrontend::getSectionOptions($section, 'outer-class');
    $innerClass = HFrontend::getSectionOptions($section, 'inner-class');
    $imageClass = HFrontend::getSectionOptions($section, 'image-class');
    $imageHeight = HFrontend::getSectionOptions($section, 'height');


    $singleImage = null;
    if($numberOfImages == 1)
        $singleImage = first_element($enabledImages);


@endphp

@if($numberOfImages > 0)
    @if(!empty($singleImage))
        <div class="container-fluid {{ $outerClass }}">
            <div class="container {{$sectionClass}}">
                <div class="row">
                    <div class="{{ !empty($innerClass) ? $innerClass : 'col-12' }}">
                        <img class="img-fluid {{ $imageClass ?? ' w-100' }}"
                             src="{{ HFrontend::getSectionItemImage($singleImage, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                             alt="{{ HFrontend::getSectionItemField($singleImage, 'alt') }}"
                             title="{{ HFrontend::getSectionItemField($singleImage, 'name') }}"
                             style="object-fit: cover;
                                    height:{{ !empty($imageHeight) ? $imageHeight : '40vh' }}
                             "
                        >
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid {{ $outerClass }}">
            <div class="container {{$sectionClass}}">
                <div class="row d-flex justify-content-center">
                    <div class="{{ !empty($innerClass) ? $innerClass : 'col-12' }}">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="true">
                            <div class="carousel-indicators">
                                @foreach($enabledImages as $item)
                                    <button type="button" data-bs-target="#carouselExampleIndicators"
                                            data-bs-slide-to="{{ $loop->index }}" @if($loop->first) class="active"
                                            @endif aria-current="true"
                                            aria-label="{{ "Slide " . $loop->index }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($enabledImages as $image)
                                    <div class="carousel-item @if($loop->first) active @endif">
                                        <img class="d-block w-100 {{ $imageClass ?? '' }}"
                                             src="{{ HFrontend::getSectionItemImage($image, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                                             alt="{{ HFrontend::getSectionItemField($image, 'alt') }}"
                                             title="{{ HFrontend::getSectionItemField($image, 'name') }}"
                                             style="object-fit: cover;
                                         height:{{ !empty($imageHeight) ? $imageHeight : '40vh' }}
                                         "
                                        >
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="{{ HFrontend::getSectionClass($section) }}">
        {{ __("frontend.$projectPath.section.images.empty") }}
    </div>
@endif
