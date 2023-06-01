<div class="modal fade" id="translationsImport" tabindex="-1"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="translationsImport" aria-hidden="true" 
    wire:ignore.self>
    
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            
        <div class="modal-header bg-light p-2">
                <h5 class="modal-title">{{__('backend.cms.translations.import-translations.header')}}</h5>
                <button type="button" class="btn-close btn-close-white m-0" data-bs-dismiss="modal" aria-label="Close" wire:loading.attr="disabled" wire:target="import" wire:click="closingModal()"></button>
        </div>

        <x-backend.modal-body>
                @if( !empty($errorMessage) )
                        <div class="alert alert-danger text-center py-2 mb-3" role="alert">
                                {!! $errorMessage !!}
                        </div>
                @endif

                @if( !empty($warningMessage) )
                        <div class="alert alert-warning text-center py-2 mb-3" role="alert">
                                {!! $warningMessage !!}
                        </div>
                @endif

                @if( !empty($successMessage) )
                        <div class="alert alert-success text-center py-2 mb-3" role="alert">
                                <i class="fas fa-check-circle text-success me-2"></i>{!! $successMessage !!}
                        </div>
                @endif
                    <div class="container">
                    @livewire('file-input', [
                       'name' => 'translations-import',
                       'type' => 'files',
                       'action' => 'edit',
                       'mimes' => 'xlsx',
                       'emits' => [             //Tells the component where to emit when files are uploaded
                           [
                           'type' => 'to',
                           'component' => 'translations-import',
                           'listener' => 'filesUpdated'
                           ]
                       ],
                       'class' => [
                            'container' => ' '
                       ]
                    ])
                    </div>
                    <div class="loading-overlay rounded-1" wire:loading.block wire:target="import">
                        <div class="d-flex align-items-center justify-content-center flex-wrap py-3 h-100">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="sr-only">{{ __('backend.cms.translations.import-translations.loading') }}</span>
                            </div>
                            @if( !empty($loadingMessage) )
                                <div class="text-center w-100">
                                    <p class="mt-2">{{ $loadingMessage }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
        </x-backend.modal-body>

            <div class="modal-footer">
                <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        wire:click="closingModal()"
                        wire:loading.attr="disabled"
                        wire:target="import"
                >{{ __('backend.generic.close') }}</button>
                <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="import(true)"
                        wire:loading.attr="disabled"
                        wire:target="import"
                >{{ __('backend.generic.import') }}</button>
            </div>
        </div>
    </div>
</div>