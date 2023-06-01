<div class="modal-content">
    <x-backend.modal-header>
        {{ __('backend.generic.versions') }}
    </x-backend.modal-header>

    <x-backend.modal-body>
        @php
            $versions = $content->versions ?? [];

            if($versions != []) {
                $versions = array_reverse((array)$versions);
            }
        @endphp

        @foreach($versions ?? [] as $version)
            <div class="row mb-2 @if($version->version == $content->version) bg-light @endif">
                <div class="col-1">{{ $version->version }}</div>
                <div class="col-7">{{ App\Helpers\HContent::getVersionString($content, $version->version, false) }}</div>
                <div class="col-4 text-right">
                    @if($version->version == $content->version)
                        <span class="text-warning mr-2 white-tooltip" data-bs-toggle="tooltip" title="{{ __('backend.cms.contents.show.versions.active') }}"><i class="far fa-file-alt"></i></span>
                    @endif
                    @if($version->version == ($content->publishedVersion ?? $content->version))
                        <span class="text-success mr-2 white-tooltip" data-bs-toggle="tooltip" title="{{ __('backend.cms.contents.show.versions.published') }}"><i class="far fa-check-circle"></i></span>
                    @endif

                    <x-backend.btn
                        class="btn-primary btn-sm"
                        data-dismiss="modal"
                        wire:click="$emitTo('livewire.backend.c-m-s.content.content-manager','changeToVersion','{{ $version->version }}')"
                    >
                        <i class="far fa-play-circle"></i>
                    </x-backend.btn>
                </div>
            </div>
        @endforeach
    </x-backend.modal-body>

    <x-backend.modal-footer close="true" />
</div>
