<x-backend.card container="col-12">
    <x-backend.card-header>
        {{__('backend.empatia.topics.header')}}
        {{--                <x-slot:right>--}}
        {{--                    <x-backend.btn-create :href="route('cbs.create')" />--}}
        {{--                </x-slot:right>--}}
    </x-backend.card-header>
    <x-backend.card-body>
        <livewire:topics-table
            loadingIndicator="true"
            sortField="id"
            perPage="10"
            cbId="{{ $model->id }}"
            cbType="{{ $model->type ?? '' }}"
        />
    </x-backend.card-body>
</x-backend.card>
@push('scripts')
    <script type="text/javascript" src="{{ URL::asset('backend/js/indexes.js') }}"></script>
@endpush
