<div>
    <x-backend.card>
        <x-backend.card-header>
            {{ __('backend.empatia.cbs.topics.status.header') }}
            <x-slot:right>
                <x-backend.btn-create
                    data-bs-toggle="modal"
                    data-bs-target="#topicStatusModal"
                    id="topicStatus"
                />
            </x-slot:right>
        </x-backend.card-header>
        <x-backend.card-body class="overflow-auto" style="max-height:25vh; min-height: 10vh;">
            @foreach($history ?? [] as $key => $state)
                <div class="{{ $loop->last ? '' : 'border-bottom mb-4' }}">
                    @if(isset($state['title']))
                        <div class="col-12 mb-2">
                            <div class="form-show-container">
                                <div class="form-show-content">
                                    <b>{{$state['title']}}</b> <br> {{ $state['created_at'] }}
                                    | {{ $state['created_by'] }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </x-backend.card-body>
    </x-backend.card>
    @include('livewire.backend.empatia.cbs.topics.topic-status-modal')
</div>

