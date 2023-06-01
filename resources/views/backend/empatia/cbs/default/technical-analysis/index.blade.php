@extends('backend.layouts.master')

@section('title')
    {{ $title ?? '-' }}
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card container="col-12">
            <x-backend.card-header>
                {{ $title ?? '-' }}
                <x-slot:right>
                    <x-backend.btn-back :href="route('cbs.show', ['type' => $cb->type ?? 'all', 'id' => $cb->id])" />
                    <x-backend.btn-create :href="route('cbs.technical-analysis-questions.create', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id])" />
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                <livewire:technical-analysis-table :cb="$cb"/>
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>
@endpush
