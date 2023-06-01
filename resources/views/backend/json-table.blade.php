@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('content')
    @php
        $jsonData = '{
             "topic-collection-phase": {
                 "title": {
                     "en": "Ideas Collection Phase"
                 },
                 "description": {
                     "en": "Phase to collect ideas"
                 },
                 "start_date": "2023-03-22 11:16:05",
                 "end_date": null,
                 "enabled": false
             },
             "vote-phase": {
                 "title": {
                     "en": "Vote Phase"
                 },
                 "description": {
                     "en": "Phase to vote"
                 },
                 "start_date": "2023-03-22 11:16:05",
                 "end_date": null,
                 "enabled": false
             }
         }';

         $data = json_decode($jsonData, true);
    @endphp
    <x-backend.body>
        <x-backend.card>
            <x-backend.card-header>
                Something
            </x-backend.card-header>
            <x-backend.card-body>
        @livewire('json-table', [
        'columns' => [
        [
        'key' => 'title',
        'label' => 'Title',
        'translatable' => false,
        'sortable' => true,
        'searchable' => true,
        'type' => 'boolean',
        ],
        [
        'label' => 'Description',
        ]
        ],
        'data' => $data
        ])
            </x-backend.card-body>
        </x-backend.card>
    </x-backend.body>
@endsection
