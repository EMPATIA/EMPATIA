<div class="row">
    <div class="col-12 file-upload">
        <form id="{{ $uploadId }}" wire:submit.prevent="saveFiles" class="m-0 p-0">
            <div class="p-2 border border-secondary rounded w-100 text-center text-secondary file-upload-container position-relative" style="font-size: 0.9rem; border-style: dashed !important; min-height: 3rem">
                <input class="d-none" type="file" wire:model="files" multiple>

                <div class="file-upload-progress d-flex align-items-center p-2 @if($uploadUploading && ! $uploadValidated && ! $uploadError && ! $uploadSuccess) block-show @else block-hide @endif " style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                    <div class="progress w-100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $uploadProgress }}%;" aria-valuemin="0" aria-valuemax="100">{{ $uploadProgress }}%</div>
                    </div>
                </div>
                <div class="d-flex align-content-center bg-info text-white @if(! $uploadUploading && $uploadValidated && ! $uploadError && ! $uploadSuccess) block-show @else block-hide @endif " style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                    <div class="px-2" style="margin: auto">{{ __('files.dropzone.saving.label') }}</div>
                </div>
                <div class="d-flex align-content-center bg-success text-white @if(! $uploadUploading && ! $uploadValidated && $uploadSuccess && ! $uploadError) block-show @else block-hide @endif " style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                    <div class="px-2" style="margin: auto">{{ __('files.dropzone.success.label') }}</div>
                </div>
                <div class="d-flex align-content-center bg-danger text-white @if(! $uploadUploading && ! $uploadValidated && ! $uploadSuccess && $uploadError) block-show @else block-hide @endif " style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                    <div class="d-inline-block text-truncate px-2" style="margin: auto">@error('files.*') {{ $message }} @enderror</div>
                </div>
                <div class="file-upload-drag-drop d-flex align-content-center @if(! $uploadUploading && ! $uploadValidated && ! $uploadSuccess && ! $uploadError) block-show @else block-hide @endif " role="button"  style="position: absolute; top: 0; bottom: 0; left: 0; right: 0;transition: opacity 1s ease-in-out">
                    <div class="px-2" style="margin: auto">{{ __('files.dropzone.dragndrop.label') }} <i class="fas fa-upload"></i></div>
                </div>
            </div>
        </form>
    </div>

    <script>
        console.log("Starting FileUpload");

        let form = $('.file-upload #{{ $uploadId }}');
        let dropzone = $(form).find('.file-upload-drag-drop');
        let progress = $(form).find('.file-upload-progress');
        let progressbar = $(form).find('.file-upload-progress .progress-bar');
        let container = $(form).find('.file-upload-container');
        let inputField = $(form).find("input");

        bindDropzone(dropzone);

        $(inputField).on('livewire-upload-start', () => {
            uploadStart();
        }).on('livewire-upload-progress', (event) => {
            uploadProgress(event);
        }).on('livewire-upload-finish', () => {
            uploadSuccess();
        }).on('livewire-upload-error', () => {
            uploadError();
        });

        function bindDropzone(dropzone) {
            dropzone.click(function(evt) {
                $(inputField).trigger('click');
            });

            dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            })

            dropzone.on('dragover dragenter', function() {
                $(container).addClass('is-dragover border-primary text-primary');
                $(container).removeClass('is-dragover border-secondary text-secondary');
            })
            dropzone.on('dragleave dragend drop', function() {
                $(container).addClass('border-secondary text-secondary');
                $(container).removeClass('is-dragover border-primary text-primary');
            })

            dropzone.on('drop',function(e) {
                var files = e.originalEvent.dataTransfer.files;
                uploadStart(files.length);
                @this.uploadMultiple('files', files, uploadSuccess, uploadError, uploadProgress)
            });
        }

        function uploadStart() {
            // console.log("livewire-upload-start");
            $(dropzone).off();
            @this.uploadProgress = 0;
            @this.uploadUploading = true;
            @this.uploadSuccess = false;
        }

        function uploadSuccess() {
            let uploadSuccess = @this.uploadValidated;
            // console.log("livewire-upload-success: "+uploadSuccess);

            @this.uploadUploading = false;

            if(uploadSuccess)
                    @this.saveFiles()
        else
            @this.uploadError = true;

            bindDropzone(dropzone);

            setTimeout(() => {
                // console.log("Closing");
                @this.uploadUploading = false;
                @this.uploadSuccess = false;
                @this.uploadError = false;
                @this.uploadProgress = 0;
            }, 5000);
        }

        function uploadError() {
            // console.log("livewire-upload-error");
            @this.uploadUploading = false;
            @this.uploadSuccess = false;
            @this.uploadError = true;

            bindDropzone(dropzone);

            setTimeout(() => {
                // console.log("Closing");
                @this.uploadUploading = false;
                @this.uploadSuccess = false;
                @this.uploadError = false;
            }, 5000);
        }

        function uploadProgress(event) {
            // console.log("livewire-upload-progress: "+event.detail.progress);
            @this.uploadProgress = event.detail.progress;
        }
    </script>
</div>
