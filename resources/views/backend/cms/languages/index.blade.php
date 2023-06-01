@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card>
            <x-backend.card-header>
                {{$title}}
                <x-slot:right>
                    <x-backend.btn-create :href="route('cms.languages.create')"/>
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                <livewire:languages-table/>
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>    {{--Index common scripts--}}
@endpush
