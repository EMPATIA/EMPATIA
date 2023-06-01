@php
    use \App\Helpers\HForm;
@endphp

<div class="form-file-list position-relative" wire:loading.class="loading-overlay-container" style="transition: display 250ms, width 150ms, height 150ms;">
    <style>
        .loading-overlay-container{
            min-width: 5rem;
            min-height: 5rem;
        }
        .form-file-list .file-item{
            background-color: rgba(0 0 0 / .04);
            transition: background-color 150ms;
        }
        .form-file-list .file-item:hover{
            background-color: rgba(0 0 0 / .1);
        }
        .form-file-list .file-item .btn-close{
            background-color: #fff;
            font-size: .5rem;
            padding: 0.35rem;
            border-radius: 50%;
            transition: background-color 150ms;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-align: end;
            transition: background-color 150ms;
        }

        .image-overlay:hover {
            background-color: rgba(0 0 0 / .2);
        }

        .image-overlay .btn-close{
            background-color: rgba(255 255 255 / .2);
            font-size: .5rem;
            padding: 0.35rem;
            border-radius: 50%;
            transition: background-color 150ms;
        }

        .image-overlay:hover .btn-close{
            background-color: rgba(255 255 255 / .4);
            opacity: .6;
        }

        .image-overlay .btn-close:hover{
            opacity: .7;
        }
    </style>
    @php
        $count = count($files ?? []);
        if( $count > 1 ) {
            $containerClass .= ' my-2';
        } else {
            $containerClass .= ' mb-2';
        }
    @endphp
    <div class="files-container row gx-0 gap-2 {{ $containerClass }}"
         style="{{ $containerStyle }}">
        @if($type == 'files')
            @if( count($files ?? []) < 1 )
                <div class="col-12 text-muted fst-italic">
                    {{ __('backend.form.files.no-files') }}
                </div>
            @else
                @foreach($files as $file)
                    <div class="file-item col-12 position-relative rounded-1 small py-1 px-2 align-middle d-inline-block w-100">
                        <div class="row align-items-center">
                            <div class="col">
                                <i class="fas fa-{{ \App\Http\Controllers\Backend\FilesController::iconClassFromFileName(getField($file, 'filename', '-')) }} me-2"></i>{{ getField($file, 'filename', '-') }}
                            </div>
                            <div class="col-auto">
                                @if( HForm::isShow() )
                                    <a href="{{route('download.file', ['name' => getField($file, 'id')])}}"
                                       class="stretched-link"
                                    >
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                @else
                                    <button
                                        wire:click="removeFile('{{getField($file, 'id', '')}}')"
                                        type="button" class="btn-close btn-close-white float-end"
                                        aria-label="Close"
                                        onclick="this.blur();"
                                    ></button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @elseif($type == 'images')
            @if( count($files ?? []) < 1 )
                @if( $single ?? null )
                    <div class="col-12 d-flex align-items-center rounded-1" style="width: 100%; height: 150px; background-color: #dfdfdf; color: #FFF">
                        <span class="mx-auto">
                            <i class="fas fa-image" style="font-size: 3rem"></i>
                        </span>
                    </div>
                @else
                    <div class="col-12 text-muted fst-italic">
                        {{ __('backend.form.files.no-files') }}
                    </div>
                @endif
            @else
                @if( $single ?? null )
                    <div class="col-auto position-relative" style="display: inline-block">
                        <img class="rounded-1" style="width: 100%; height: 180px; object-fit: cover;" src="{{ getField($files, '0.url', '') }}" />
                        <div class="image-overlay rounded-1 p-2">
                            @if( HForm::isShow() )
                                <a href="{{route('download.file', ['name' => getField($files, '0.id', '')])}}"
                                   class="action-btn lh-1 stretched-link"
                                >
                                    <i class="fa-solid fa-download lh-1"></i>
                                </a>
                            @else
                                <button
                                    wire:click="removeFile('{{getField($files, '0.id', '')}}')"
                                    type="button" class="btn-close btn-close-white"
                                    aria-label="Close"
                                    onclick="this.blur();"
                                ></button>
                            @endif
                        </div>
                    </div>
                @else
                    @foreach($files as $image)
                        <div class="col-auto position-relative">
                            <img class="rounded-1" style="width: 150px; height: 150px; object-fit: cover;"
                                 src="{{ getField($image, 'url', '') }}" data-file-id="{{getField($image, 'id', '')}}"/>
                            <div class="image-overlay rounded-1 p-2">
                                @if( HForm::isShow() )
                                    <a href="{{route('download.file', ['name' => getField($image, 'id')])}}"
                                       class="action-btn lh-1 stretched-link"
                                    >
                                        <i class="fa-solid fa-download lh-1"></i>
                                    </a>
                                @else
                                    <button
                                        wire:click="removeFile('{{getField($image, 'id', '')}}')"
                                        type="button" class="btn-close btn-close-white"
                                        aria-label="Close"
                                        onclick="this.blur();"
                                    ></button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif
        @endif
    </div>

    <div class="loading-overlay rounded-1" wire:ignore wire:loading >
        <div class="d-flex align-items-center justify-content-center h-100">
            <div class="spinner-border text-secondary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
