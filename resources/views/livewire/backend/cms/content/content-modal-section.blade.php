<div class="modal-content">
    <x-backend.modal-header>
        {{ empty($sectionName) ? __('backend.cms.contents.show.sections.'.$type . '.title') : $sectionName }}
    </x-backend.modal-header>

    <x-backend.modal-body>
        @if($type == 'heading')
            @include('backend.cms.contents.content-section-modal-heading')
        @elseif($type == 'code')
            @include('backend.cms.contents.content-section-modal-code')
        @elseif($type == 'text')
            @include('backend.cms.contents.content-section-modal-text')
        @elseif($type == 'text-html')
            @include('backend.cms.contents.content-section-modal-text-html')
        @elseif($type == 'button')
            @include('backend.cms.contents.content-section-modal-button')
        @elseif($type == 'list')
            @include('backend.cms.contents.content-section-modal-list')
        @elseif($type == 'files' || $type == 'images')
            @include('backend.cms.contents.content-section-modal-files')
        @elseif($type == 'video')
            @include('backend.cms.contents.content-section-modal-video')
        @endif

    </x-backend.modal-body>

    <x-backend.modal-footer close="true">
        @if($type == 'images' || $type == 'files')
            <div class="col m-0 p-0">
                <div class="col-12 col-lg-6 m-0 p-0">
                    @livewire("file-upload", [ "type" => ($type == 'images') ? 'images' : 'files', "maxSize" => 128, "view" => "livewire.backend.cms.file.file-upload-cms"])
                </div>
            </div>
        @elseif($type == 'list')
            <div class="col m-0 p-0">
                <div class="col-12 col-lg-6 m-0 p-0">
                    <x-backend.btn class="btn-primary" wire:click="listAddItem()">
                        {{ __('backend.cms.contents.show.modal.list.add-button') }}
                    </x-backend.btn>
                </div>
            </div>
        @endif
    </x-backend.modal-footer>
</div>
