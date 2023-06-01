<x-backend.card container="col-12">
    <ul class="list-group list-group-flush bg-white">
        @if( Auth::user()->hasAnyRole(['admin','laravel-admin']) )
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
               href="{{ route('cbs.edit', ['type' => $type ?? 'all', 'id' => $model->id]) }}">
                <i class="fas fa-pencil-alt fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.edit') }}</span>
            </a>

            {{-- TODO: disabled --}}
            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                <i class="fas fa-trash-alt fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.delete') }}</span>
                <span class="badge bg-secondary rounded-pill">disabled</span>
            </button>

            {{-- TODO: disabled --}}
            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                <i class="fas fa-undo fa-fw me-3"></i>
                <span class="me-auto">{{ __('backend.generic.versions') }}</span>
                <span class="badge bg-secondary rounded-pill">disabled</span>
            </button>

            @if(  Auth::user()->hasAnyRole(['admin','laravel-admin'])  )
                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="fas fa-chart-line fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.analytics.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="fas fa-bell fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.notifications.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="fas fa-question fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.parameters.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="fa-solid fa-timeline fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.phases.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                   href="{{ route('cbs.operation-schedules.index', ['type' => $type, 'cbId' => $model->id]) }}">
                    <i class="fas fa-clock fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.operation-schedules.button') }}</span>
                </a>

                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="far fa-calendar-alt fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.vote-events.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                   href="{{ route('cbs.technical-analysis-questions.index', ['type' => $type ?? 'all', 'cbId' => $model->id]) }}">
                    <i class="fas fa-question fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.ta-questions.button') }}</span>
                </a>

                {{-- TODO: disabled --}}
                <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                    <i class="fas fa-sliders-h fa-fw me-3"></i>
                    <span class="me-auto">{{ __('backend.cbs.configurations.button') }}</span>
                    <span class="badge bg-secondary rounded-pill">disabled</span>
                </button>

            @endif
        @endif
    </ul>
</x-backend.card>

