@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card container="pt-3">

            <x-backend.card-header>
                {{ $title }}
                <x-slot:right>
                    <x-backend.btn-export class="export-translations"/>
                    <x-backend.btn-import class="import-translations" data-bs-toggle="modal"
                                          href="#translationsImport"/>
                    <x-backend.btn-create :href="route('cms.translations.create')" />
                    <livewire:translations-import/>
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                <livewire:translations-table/>
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>   {{--Index common scripts--}}

    <script type="module">
        $('.export-translations').unbind('click').click(function () {
            Livewire.emitTo('translations-table', 'exportTranslations');
        })
    </script>
@endpush
