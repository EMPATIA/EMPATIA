<div class="col-12">
    
    <div class="row row-cols-2 {{!$this->updated ? 'd-none' : 'my-3 my-md-0 mb-md-3'}}">
        <div class="col">
            <x-backend.btn :class="!$this->updated ? 'col-12 d-none btn-success content-save content-updated' : 'col-12 btn-success content-save content-updated'">
                <i class="fas fa-save me-2"></i> {{ __('backend.generic.save') }}
            </x-backend.btn>
        </div>
        <div class="col">
            <x-backend.btn :class="!$this->updated ? 'col-12 d-none btn-secondary content-cancel content-updated ' : 'col-12 btn-secondary content-cancel content-updated'">
                <i class="fas fa-undo me-2"></i> {{ __('backend.generic.cancel') }}
            </x-backend.btn>
        </div>
    </div>
    
    <div class="card p-2">
        <ul class="list-group list-group-flush section-right-buttons">
            @if($content->status != 'published')
                <x-backend.btn class="list-group-item list-group-item-action bg-light text-success shadow-sm my-1 border" wire:click="contentPublish()">
                    <i class="far fa-check-circle me-2"></i> {{ __('backend.cms.contents.show.actions.publish') }}
                </x-backend.btn>
            @else
                <x-backend.btn class="list-group-item list-group-item-action bg-light shadow-sm my-1 border" wire:click="contentUnpublish()">
                    <i class="fas fa-ban me-2"></i> {{ __('backend.cms.contents.show.actions.unpublish') }}
                </x-backend.btn>
            @endif
    
            <x-backend.btn class="list-group-item list-group-item-action bg-light shadow-sm my-1 border" wire:click="$emitTo('livewire.backend.c-m-s.content.content-modal-versions', 'loadModal')">
                <i class="fas fa-undo me-2"></i> {{ __('backend.generic.versions') }}
            </x-backend.btn>
    
            @if(empty($content->deleted_at))
                <x-backend.btn class="list-group-item list-group-item-action bg-light text-danger shadow-sm my-1 border" wire:click="contentDelete()">
                    <i class="far fa-trash-alt me-2"></i> {{ __('backend.generic.delete') }}
                </x-backend.btn>
            @else
                <x-backend.btn class="list-group-item list-group-item-action bg-light text-success shadow-sm my-1 border" wire:click="contentRestore()">
                    <i class="far fa-check-circle me-2"></i> {{ __('backend.generic.restore') }}
                </x-backend.btn>
            @endif
        </ul>
    </div>
</div>