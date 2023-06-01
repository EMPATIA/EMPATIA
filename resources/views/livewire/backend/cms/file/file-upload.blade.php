<div class="row">
    <div class="col-12 file-upload">
        <div id="{{ $uploadId }}" wire:submit.prevent="saveFiles" class="m-0 p-0">

            @if( getField($misc, 'dropzone') == true )
                <div class="p-2 border border-secondary rounded w-100 text-center text-secondary file-upload-container position-relative"
                        style="font-size: 0.9rem; border-style: dashed !important; min-height: 2rem">
                    <input class="d-none" type="file" wire:model="files" @if( empty($maxFiles) || $maxFiles != 1 ) multiple @endif>

                    <div class="file-upload-progress d-flex align-items-center p-2 @if($uploadUploading && ! $uploadValidated && ! $uploadError && ! $uploadSuccess) block-show @else block-hide @endif "
                            style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                        <div class="progress w-100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                 style="width: {{ $uploadProgress }}%;" aria-valuemin="0"
                                 aria-valuemax="100">{{ $uploadProgress }}%
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-content-center bg-info text-white @if(! $uploadUploading && $uploadValidated && ! $uploadError && ! $uploadSuccess) block-show @else block-hide @endif "
                            style="">
                        <div class="px-2"
                             style="margin: auto">{{ getField($misc, 'translations.saving', __('files.dropzone.saving.label')) }}</div>
                    </div>
                    
                    <div class="d-flex align-content-center bg-success text-white @if(! $uploadUploading && ! $uploadValidated && $uploadSuccess && ! $uploadError) block-show @else block-hide @endif "
                            style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                        <div class="px-2"
                             style="margin: auto">{{ getField($misc, 'translations.success', __('files.dropzone.success.label')) }}</div>
                    </div>
                    
                    <div class="d-flex align-content-center bg-danger text-white @if(! $uploadUploading && ! $uploadValidated && ! $uploadSuccess && $uploadError) block-show @else block-hide @endif "
                            style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                        <div class="d-inline-block text-truncate px-2"
                             style="margin: auto">@error('files.*') {!! $message !!} @enderror</div>
                    </div>
                    
                    <div class="file-upload-drag-drop d-flex align-content-center @if(! $uploadUploading && ! $uploadValidated && ! $uploadSuccess && ! $uploadError) block-show @else block-hide @endif "
                            role="button"
                            style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                        <div class="px-2"
                             style="margin: auto">{{ getField($misc, 'translations.dragndrop', __('files.dropzone.dragndrop.label')) }}
                            <i class="fas fa-upload"></i></div>
                    </div>
                </div>

            @else
                <div class="row">
                    <div class="col-12">
                        <div class="m-0 small">
                            <span class="@if(! $uploadUploading && ! $uploadValidated && ! $uploadSuccess && ($uploadError || $filesValidationError)) d-inline-block @else d-none @endif alert alert-danger border-0 py-2 px-3 mb-2 "
                                    role="alert">
                                @error('files.*') {!! $message !!} @enderror
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 {{ getField($misc, 'button-container.class', '') }}">
                        <label class="btn btn-outline-secondary btn-sm" wire:loading.class="disabled">
                            <span wire:loading.remove><i class="fa fa-upload me-2"></i>{{ getField($misc, 'translations.choose-file', __('files.input.choose-file.label')) }}</span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true">
                                </span>{{ getField($misc, 'translations.loading', __('files.input.loading.label')) }}
                            </span>
                            <input class="d-none" type="file" @if( empty($maxFiles) || $maxFiles != 1 ) multiple @endif>
                        </label>
                        {{--                        <span wire:loading.remove--}}
                        {{--                            class="@if(! $uploadUploading && ! $uploadValidated && $uploadSuccess && ! $uploadError) d-inline-block @else d-none @endif align-middle"--}}
                        {{--                        ><i class="fas fa-check-circle text-success px-2"></i></span>--}}
                    </div>
                </div>
            @endif
        </div>
    </div>

    @include('livewire.backend.cms.file.file-upload-script')
</div>
