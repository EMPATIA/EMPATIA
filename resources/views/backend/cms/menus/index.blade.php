@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card container="col-12">
            <x-backend.card-header>
                {{ $title }}
                <x-slot:right>
                    <x-backend.btn-create :href="route('cms.menus.create')"/>
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body class="container-fluid">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mt-2 mb-4">
                    <div class="col-auto">
                        <x-form-select icon="chevron-down" action="edit" name="menuTypeFilter"
                                       id="menuTypeFilter"
                                       class="menuFilter"
                                       :options="$menuTypes"
                                       :label="__('backend.cms.menus.filters.menu-type.filter')"/>
                    </div>
                    <div class="col-auto">
                        <x-form-select icon="chevron-down" action="edit" name="menuTypeFilter"
                                       id="menuDeleteFilter"
                                       class="menuFilter"
                                       :options="$deletedTypes"
                                       :label="__('backend.cms.menus.filters.menu-type.filter.delete')"/>

                    </div>
                </div>
                <div class="row justify-content-between">
                    @livewire('menu-list')
                    @livewire('menu-form')
                </div>

            </x-backend.card-body>

            <div class="loading-overlay rounded-1" wire:loading >
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

        </x-backend.card>
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>    {{--Index common scripts--}}
@endpush


@push('scripts')
    <script>
        function loadSortable () {
            // Select all menus/submenus
            let menus = [].slice.call(document.querySelectorAll('.menu-sort-container'));

            // Loop through each nested sortable element
            for (let i = 0; i < menus.length; i++) {
                new Sortable(menus[i], {
                    group: 'nested',
                    animation: 600,
                    ghostClass: 'drag_drop_class',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,

                    onEnd: function (evt) {
                        let id = $(evt.item).data('id');
                        let newIndex = evt.newIndex;
                        let parentIdNew = $(evt.to).parent().data('id');
                        console.log(id, newIndex, parentIdNew)
                        Livewire.emit('menusMoved', id, newIndex, parentIdNew);
                    }
                });
            }
        }
        $(function () {
            reloadFunctions.add('menusIndex', () => {
                loadSortable();

                $(".menuFilter").change(function () {
                    Livewire.emit('filterMenus', $("#menuTypeFilter").val(), $("#menuDeleteFilter").val());
                });
            })
        });
    </script>
@endpush
