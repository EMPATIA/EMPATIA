@php
    use App\Helpers\HForm;
@endphp

@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('header')
    <x-backend.header>
        <x-backend.header-row :title="__('backend.generic.title')">{{ getFieldLang($content, 'title') }}</x-backend.header-row>
        <x-backend.header-row :title="__('backend.generic.slug')">
            {{ getFieldLang($content, 'slug') }} <a class="ms-2 small text-white" href="{{ url('/'.getFieldLang($content, 'slug')) }}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        </x-backend.header-row>
        <x-backend.header-row :title="__('backend.generic.status')">{{ getField($content, 'status') }}</x-backend.header-row>

        <x-slot:right>
            <x-backend.btn class="btn-sm btn-light" :href="url('/'.getFieldLang($content, 'slug'))" :title="__('backend.cms.contents.show.open')" target="_blank">
                <i class="fa-regular fa-file"></i> {{ __('backend.cms.contents.show.open') }}
            </x-backend.btn>

            <x-backend.btn class="btn-sm btn-light" :href="url('/'.getFieldLang($content, 'slug').'?preview')" :title="__('backend.cms.contents.show.preview')" target="_blank">
                <i class="fa-regular fa-file"></i> {{ __('backend.cms.contents.show.preview') }}
            </x-backend.btn>
            <x-backend.btn-back class="btn-sm btn-light" :href="route('cms.content.index', ['type' => $type])" />
        </x-slot:right>
    </x-backend.header>
@endsection

@section('content')
    @livewire('livewire.backend.c-m-s.content.content-manager', ['contentId' => $id])

    <x-backend.modal id="editDetails"  class="modal-lg">
        @livewire('livewire.backend.c-m-s.content.content-modal-details', ['contentId' => $id])
    </x-backend.modal>

    <x-backend.modal id="editSeo"  class="modal-lg">
        @livewire('livewire.backend.c-m-s.content.content-modal-seo', ['contentId' => $id])
    </x-backend.modal>

    <x-backend.modal id="contentVersions"  class="modal-lg">
        @livewire('livewire.backend.c-m-s.content.content-modal-versions', ['contentId' => $id])
    </x-backend.modal>

    <x-backend.modal id="editSection" class="modal-xl">
        @livewire('livewire.backend.c-m-s.content.content-modal-section', ['contentId' => $id])
    </x-backend.modal>

    <x-backend.modal id="editSectionConfig" class="modal-xl">
        @livewire('livewire.backend.c-m-s.content.content-modal-section-config', ['contentId' => $id])
    </x-backend.modal>
@endsection

@push('scripts')
    <script src="/build/tinymce/tinymce.min.js"></script>
    <script type="module">
        $().ready(function () {
            new Sortable(document.getElementById('sections_list'), {
                handle: '.drag_handle',
                animation: 600,
                ghostClass: 'drag_drop_class',

                onEnd: function (evt) {
                    Livewire.emit('sectionsMoved', evt.oldIndex, evt.newIndex);
                },
            });
            
            $(".content-save").click(function() {
                window.livewire.emit('updateContent');
            });

            $(".content-cancel").click(function() {
                location.reload();
            });

            bindModal();

            Livewire.on('toggleModalDetails', () => $('#editDetails').modal('toggle'));
            Livewire.on('toggleModalSeo', () => $('#editSeo').modal('toggle'));
            Livewire.on('toggleModalVersions', () => $('#contentVersions').modal('toggle'));
            Livewire.on('toggleModalSection', () => $('#editSection').modal('toggle'));
            Livewire.on('toggleModalSectionConfig', () => $('#editSectionConfig').modal('toggle'));
            Livewire.on('showEdited', () => $(".content-updated").removeClass("d-none"));
            Livewire.on('hideEdited', () => $(".content-updated").addClass("d-none"));
            Livewire.on('setContentPublished', () => {
                $(".content-published").removeClass("d-none");
                $(".content-published").addClass("d-flex");
                $(".content-unpublished").addClass("d-none");
                $(".content-unpublished").removeClass("d-flex");
                $(".content-deleted").addClass("d-none");
                $(".content-deleted").removeClass("d-flex");
            });
            Livewire.on('setContentUnpublished', () => {
                $(".content-published").addClass("d-none");
                $(".content-published").removeClass("d-flex");
                $(".content-unpublished").removeClass("d-none");
                $(".content-unpublished").addClass("d-flex");
                $(".content-deleted").addClass("d-none");
                $(".content-deleted").removeClass("d-flex");
            });
            Livewire.on('setContentDeleted', () => {
                $(".content-published").addClass("d-none");
                $(".content-published").removeClass("d-flex");
                $(".content-unpublished").addClass("d-none");
                $(".content-unpublished").removeClass("d-flex");
                $(".content-deleted").removeClass("d-none");
                $(".content-deleted").addClass("d-flex");
            });
        });

        function bindModal() {
            $(".modal-section").on('show.bs.modal', function (e) {
                $(this).find("textarea.livewire-html-editor").each(function() {
                    document.addEventListener('focusin', (e) => {
                        if (e.target.closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                            e.stopImmediatePropagation();
                        }
                    });
                    
                    tinymce.init({
                        selector: 'textarea.livewire-html-editor',
                        plugins: 'fullscreen image link media table code preview lists searchreplace visualblocks help',
                        toolbar1: 'undo redo styles bold italic alignleft aligncenter alignright alignjustify outdent indent bullist numlist',
                        toolbar2: 'fullscreen preview code link searchreplace visualblocks',
                        branding: false,
                        promotion: false,
                        width: '100%',
                        height: '50vh',
                    });
                });
            })

            $(".modal-section").on('hide.bs.modal', function (e) {
                let arr = {};

                $(this).find("textarea.livewire-html-editor").each(function() {
                    try {
                        arr[$(this).data('name')] = tinymce.get($(this).attr('id')).getContent();
                        $(this).tinymce().remove();
                        
                    } catch(err) {
                        // console.log("TinyMCE ERROR: ", err);
                    }
                });

                window.livewire.emit('updateSectionTextHtml', arr);
            });
        }
    </script>
@endpush
