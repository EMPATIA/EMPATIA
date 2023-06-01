<x-backend.card container="col-12">
    <ul class="list-group list-group-flush bg-white">
        @if( Auth::user()->hasAnyRole(['admin','laravel-admin']) )
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
               href="{{ route('cbs.backend.topics.edit', ['type' => $type ?? 'all', 'cbId' => $cb->id,'id' => $modelId]) }}">
                <i class="fas fa-pencil-alt fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.edit') }}</span>
            </a>

            {{-- TODO: not implemented --}}
            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                <i class="fas fa-trash-alt fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.delete') }}</span>
                <span class="badge bg-secondary rounded-pill">disabled</span>
            </button>

            {{-- TODO: not implemented --}}
            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                <i class="fas fa-undo fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.versions') }}</span>
                <span class="badge bg-secondary rounded-pill">disabled</span>
            </button>
        @endif
    </ul>
</x-backend.card>

