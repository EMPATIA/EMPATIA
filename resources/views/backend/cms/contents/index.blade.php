@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
@endsection

@section('content')
    <x-backend.card>
        <x-backend.card-header>
            {{ $title }}

            <x-slot:right>
                <x-backend.btn-create :href="route('cms.content.create', ['type' => $type])" />
            </x-slot:right>
        </x-backend.card-header>

        <x-backend.card-body>
            @livewire('contents-table', ['type' => $type])
        </x-backend.card-body>
    </x-backend.card>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>    {{--Index common scripts--}}
@endpush
