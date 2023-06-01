{{--

*** FILES ***

Files items list.

{
    type : files,
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
    - App\Helpers\HFrontend::getSectionItemDownload($item);
    - App\Helpers\HFrontend::getSectionItemField($item, $field, [$lang]);

--}}

@php
    use App\Http\Controllers\Backend\FilesController;
        $innerClass = \App\Helpers\HFrontend::getSectionOptions($section, 'inner-class');
        $fileClass = \App\Helpers\HFrontend::getSectionOptions($section, 'file-class');
@endphp

<div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
    <div class="container">
        <div class="row py-4 justify-content-center">
            <div class=" {{ !empty($innerClass) ? $innerClass : 'col' }}">
                @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                    @php
                        $f = FilesController::getFileUrlByName(getField($item, "id"));
                        if($f instanceof Exception)
                            $size = '-';
                        else
                            $size = bytesToHuman(FilesController::getFileSizeByName(getField($item, "id")));
                    @endphp
                    <div class="row py-1">
                        <div class=" {{ !empty($fileClass) ? $fileClass : '' }}">
                            <span class="float-start"><a
                                    href="{{ FilesController::getFileUrlByName(getField($item, 'id')) }}"
                                    download="{{ getField($item, 'filename') }}"><i class="fa fa-download me-2"></i>{{ App\Helpers\HFrontend::getSectionItemField($item, 'name') }}
                            </a></span>
                            <span class="float-end">{{  $size }}</span>
                        </div>
                    </div>
                @empty
                    {{ __('frontend:section.list.empty') }}
                @endforelse
            </div>
        </div>
    </div>
</div>
