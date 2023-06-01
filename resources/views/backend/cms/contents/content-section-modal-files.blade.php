@php
    use App\Http\Controllers\Backend\FilesController;
@endphp

<div class="row h-100">
    <div class="col-8 files-list overflow-auto h-100" id="section_files_list">
        @forelse($files as $key => $file)
                @if($type == 'files')
                    @php
                        $f = FilesController::getFileById(getField($file, "id"));

                        if($f instanceof Exception)
                            $size = '-';
                        else
                            $size = bytesToHuman(getField($f, "size"));
                    @endphp
                    <div class="row py-2 @if($filePosition !== null && $key == $filePosition) be-bg-light @endif">
                        <div class="col-8 text-truncate">{{ getField($file, "filename").' ('.$size.')' }}</div>
                        <div class="col-4 text-right">
                            <button class="btn btn-secondary btn-sm drag_handle" style="font-size: .7rem;"><i class="fas fa-expand-arrows-alt"></i></button>
                            <button class="btn btn-danger btn-sm" style="font-size: .7rem;" wire:click="fileDelete('{{ $key }}')"><i class="far fa-trash-alt"></i></button>
                            <button class="btn btn-success btn-sm" style="font-size: .7rem;" wire:click="fileDownload('{{ $key }}')"><i class="fas fa-download"></i></button>
                            <button class="btn btn-primary btn-sm" style="font-size: .7rem;" wire:click="fileSelected('{{ $key }}')"><i class="fas fa-cog"></i></button>
                        </div>
                    </div>
                @elseif($type == 'images')
                    <div class="" style="display: inline-block">
                        @php
                            $url = "";
                            if(!empty(getField($file, "id"))) {
                                $url = FilesController::getImageUrlByName(getField($file, "id"), 200, 200, 'webp');
                            }
                        @endphp
                        <div class="position-relative border mr-2 mb-2 @if($filePosition !== null && $key == $filePosition) border-primary @else border-secondary @endif" style="border-width: 2px !important; border-radius: 10px; overflow: hidden;">
                            <div class="position-absolute top-0 text-center w-100 p-1" style="background-color: rgba(0,0,0,.7)">
                                <button class="btn btn-secondary btn-sm drag_handle" style="font-size: .7rem;"><i class="fas fa-expand-arrows-alt"></i></button>
                                <button class="btn btn-danger btn-sm" style="font-size: .7rem;" wire:click="fileDelete('{{ $key }}')"><i class="far fa-trash-alt"></i></button>
                                <button class="btn btn-success btn-sm" style="font-size: .7rem;" wire:click="fileDownload('{{ $key }}')"><i class="fas fa-download"></i></button>
                                <button class="btn btn-primary btn-sm" style="font-size: .7rem;" wire:click="fileSelected('{{ $key }}')"><i class="fas fa-cog"></i></button>
                            </div>
                            <img class="" style="width: 150px; height: 150px; object-fit: cover;" src="{{ $url }}" />
                            <div class="position-absolute bottom-0 w-100 text-white text-truncate text-center p-1" style="font-size: .8rem; background-color: @if($filePosition !== null && $key == $filePosition) rgba(0,123,255,.7) @else rgba(0,0,0,.7) @endif">{{ getField($file, "filename", "-") }}</div>
                        </div>
                    </div>
                @endif
        @empty
            {{ __('backend.cms.contents.show.modal.files.no-files.'.$type) }}
        @endforelse
    </div>

    <div class="col-4 h-100">
        <div class="col alert alert-secondary h-100 overflow-auto mb-0">
            @if($filePosition !== null)
                <div class="row">
                    <div class="col-12">
                        <div class=" form-group ">
                            <label>{{ __('backend.generic.code') }}</label>
                            <div class="input-group">
                                <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.code">
                            </div>
                            @error('files.{{ $filePosition }}.code')
                                <div class="error invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class=" form-group ">
                            <label>{{ __('backend.cms.contents.show.modal.files.filename.label') }}</label>
                            <div class="input-group">
                                <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.filename">
                            </div>
                            @error('files.{{ $filePosition }}.filename')
                                <div class="error invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @foreach(getLanguagesFrontend() as $language)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="language-{{ $language['locale'] }}" wire:model="files.{{ $filePosition }}.{{ $language['locale'] }}.enabled">
                                <label class="form-check-label font-weight-bold" for="language-{{ $language['locale'] }}">{{ $language['name']." ".__('backend.generic.enabled') }}</label>
                            </div>
                        </div>
                        <div class="col-12 @if(!data_get($files, $filePosition.".".$language['locale'].'.enabled', false)) d-none @endif">
                            <div class=" form-group">
                                <label>{{ __('backend.generic.name').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.{{ $language['locale'] }}.name">
                                </div>
                            </div>
                            <div class=" form-group ">
                                <label>{{ __('backend.generic.description').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.{{ $language['locale'] }}.desc">
                                </div>
                            </div>
                            <div class=" form-group ">
                                <label>{{ __('backend.generic.alt').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.{{ $language['locale'] }}.alt">
                                </div>
                            </div>
                            <div class=" form-group ">
                                <label>{{ __('backend.generic.link').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="files.{{ $filePosition }}.{{ $language['locale'] }}.link">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{ __('backend.cms.contents.show.modal.files.not-selected.label') }}
            @endif
        </div>
    </div>
</div>

<script>
    new Sortable(document.getElementById('section_files_list'), {
        handle: '.drag_handle',
        animation: 600,
        ghostClass: 'drag_drop_class',

        onEnd: function (evt) {
            @this.emitSelf('sectionFileMoved', evt.oldIndex, evt.newIndex)
        },
    });
</script>
