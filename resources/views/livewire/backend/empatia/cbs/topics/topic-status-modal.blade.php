<x-backend.modal id="topicStatusModal" tabindex="-1"{{-- wire:ignore--}}>
    <x-backend.modal-header>
        {{ __('backend.empatia.topic.topic-status-modal.title') }}
    </x-backend.modal-header>
    <x-backend.modal-body>
        <div class="position-relative">
            <div>
                <x-form-select wire:model.defer="stateSelected" name="stateSelected"
                               :options="$states"
                               :label="__('backend.empatia.topic.topic-status-modal.select.label')"
{{--                               :placeholder="__('backend.empatia.topic.topic-status-modal.select.placeholder')"--}}
                />
            </div>
        </div>
    </x-backend.modal-body>
    <x-backend.modal-footer close="true">
        <button
            class="btn btn-primary"
            wire:click="saveState"
            data-bs-dismiss="modal"
        >
            {{ __('backend.empatia.topic.topic-status-modal.save.button')  }}
        </button>
    </x-backend.modal-footer>
</x-backend.modal>

