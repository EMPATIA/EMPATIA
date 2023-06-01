@extends('backend.layouts.master')

@section('title')
    {{ $title ?? '-' }}
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card>
            <x-backend.card-header>
                {{__("backend.empatia.cbs.$type.header")}}
                <x-slot:right>
                    <x-backend.btn-create :href="route('cbs.create', [$type])" />
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                <livewire:cbs-table
                    loadingIndicator="true"
                    sortField="id"
                    perPage="10"
                    type="{{$type}}"
                />
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>
@endpush

{{--@extends('backend::layouts.master')--}}

{{--@section('header')--}}
{{--    {{ __('cbs::cbs.index.header') }}--}}
{{--@endsection--}}

{{--@section('header-actions')--}}
{{--    <x-backend::form-button class="btn btn-sm btn-primary"--}}
{{--                            :href="route('cbs.type.create', [$type])">{{ __('backend::generic.create') }}</x-backend::form-button>--}}
{{--@endsection--}}

{{--@section('content')--}}
{{--    <div class="row">--}}
{{--        <div class="card col-12">--}}
{{--            <div class="card-header action-header d-flex justify-content-between">--}}
{{--                <div class="font-weight-bold">--}}
{{--                    {{ __('cbs::cbs.index.header-' . $type) }}--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="card-body bo-table">--}}
{{--                <div class="row">--}}
{{--                    @include('backend::layouts.partials.index-filters', ['componentName' => 'cbs-table'])--}}

{{--                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">--}}
{{--                        <x-form-select--}}
{{--                            id="cbTypeFilter"--}}
{{--                            name="cbTypeFilter"--}}
{{--                            icon="chevron-down"--}}
{{--                            :options="$cbType ?? []"--}}
{{--                            :label="__('backend::generic.type')"--}}
{{--                            :placeholder="__('backend::generic.type')"--}}
{{--                        />--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--                <livewire:cbs-table--}}
{{--                    loadingIndicator="true"--}}
{{--                    sortField="id"--}}
{{--                    perPage="10"--}}
{{--                    type="{{$type}}"--}}
{{--                />--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}

{{--@push('scripts')--}}
{{--    <script type="text/javascript" src="{{ URL::asset('backend/js/indexes.js') }}"></script>--}}
{{--@endpush--}}

{{--@section('scripts')--}}
{{--    <script>--}}
{{--        $(function () {--}}
{{--            $('select#cbTypeFilter').on('change', function () {--}}
{{--                let cbtype = $(this).val();--}}
{{--                Livewire.emitTo('cbs-table', 'filterCbType', cbtype);--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}
