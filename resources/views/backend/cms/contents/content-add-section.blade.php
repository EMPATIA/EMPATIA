<x-backend.modal id="addSection" class="modal-lg">

    <x-backend.modal-header>
        {{ __('backend.cms.contents.show.sections.add-section-modal.title') }}
    </x-backend.modal-header>

    <x-backend.modal-body>
        <div class="">
            <h5 class="text-primary fw-bold"> {{ __('backend.cms.contents.show.sections.modal.basic-content') }} </h5>
            <div class="row row-cols-5">

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('heading')" data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-heading.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.heading') }}
                    </button>
                </div>

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('text')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-text.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.text') }}
                    </button>
                </div>

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('text-html')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-html.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.text-html') }}
                    </button>
                </div>

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('button')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-button.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.button') }}
                    </button>
                </div>

{{--                <div class="col">--}}
{{--                    <button class="btn modal-section-button" wire:click="addSection('card')"--}}
{{--                            data-bs-dismiss="modal">--}}
{{--                        <img src="{{asset('/build/assets/backend/icons/content-section-card.svg')}}">--}}
{{--                        {{ __('backend.cms.contents.show.sections.modal.add.card') }}--}}
{{--                    </button>--}}
{{--                </div>--}}
            </div>
        </div>


        <div class="my-4">
            <h5 class="text-primary fw-bold"> {{ __('backend.cms.contents.show.sections.modal.media-content') }} </h5>
            <div class="row row-cols-5">
                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('files')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-files.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.files') }}
                    </button>
                </div>

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('images')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-images.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.images') }}
                    </button>
                </div>

                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('video')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-video.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.video') }}
                    </button>
                </div>
            </div>
        </div>


        <div class="">
            <h5 class="text-primary fw-bold"> {{ __('backend.cms.contents.show.sections.modal.advanced-content') }} </h5>
            <div class="row row-cols-5">
                <div class="col">
                    <button class="btn modal-section-button" wire:click="addSection('code')"
                            data-bs-dismiss="modal">
                        <img src="{{asset('/build/assets/backend/icons/content-section-code.svg')}}">
                        {{ __('backend.cms.contents.show.sections.modal.add.code') }}
                    </button>
                </div>

{{--                <div class="col">--}}
{{--                    <button class="btn modal-section-button" wire:click="addSection('list')"--}}
{{--                            data-bs-dismiss="modal">--}}
{{--                        <img src="{{asset('/build/assets/backend/icons/content-section-list.svg')}}">--}}
{{--                        {{ __('backend.cms.contents.show.sections.modal.add.list') }}--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--                <div class="col">--}}
{{--                    <button class="btn modal-section-button" wire:click="addSection('map')"--}}
{{--                            data-bs-dismiss="modal">--}}
{{--                        <img src="{{asset('/build/assets/backend/icons/content-section-map.svg')}}">--}}
{{--                        {{ __('backend.cms.contents.show.sections.modal.add.map') }}--}}
{{--                    </button>--}}
{{--                </div>--}}
            </div>
        </div>
    </x-backend.modal-body>
    <x-backend.modal-footer close="true" />
</x-backend.modal>
