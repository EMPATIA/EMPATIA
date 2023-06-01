@extends('backend.layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <x-backend.body>
        <x-backend.card>
            <x-backend.card-header>
                {{ $title }}
                <x-slot:right>
                    <x-backend.btn-back
                        :href="route('cbs.show', ['type' => $type, 'id' => $cbId])"
                    />
                    <x-backend.btn-create :href="route('cbs.operation-schedules.create', [$type, $cbId])" />
                </x-slot:right>
            </x-backend.card-header>
            <x-backend.card-body>
                <livewire:operation-schedules-table :type="$type" :cbId="$cbId"/>
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection
@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>
@endpush
