<div class="cms-content-show mb-3">
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <x-backend.card>
                <x-backend.card-header>
                    {{ __('backend.cms.contents.show.sections.header') }}
        
                    <x-slot:right>
                        @if( count(getLanguagesFrontend()) > 1 )
                            @foreach(getLanguagesFrontend() as $language)
                                <span data-lang='{{ $language["locale"] }}' class='label-lang-href text-white badge fs-6 me-1 text-uppercase @if($lang == $language["locale"]) fw-bold bg-primary @else bg-secondary fw-normal @endif'>
                                    <span wire:click="changeLanguage('{{ $language['locale'] }}')" role="button" >{{ $language["locale"] }}</span>
                                </span>
                            @endforeach
                        @endif
                    </x-slot:right>
                </x-backend.card-header>
        
                <x-backend.card-body id="sections_list">
                    @forelse($content->sections as $key => $section)
                        @include('backend.cms.contents.content-section')
                    @empty
                        <span>{{ __('backend.cms.contents.show.sections.no-sections') }}</span>
                    @endforelse
                </x-backend.card-body>
        
                <x-backend.card-footer>
                    <x-backend.btn class='btn-primary' data-bs-toggle="modal" data-bs-target="#addSection"><i class="fas fa-plus"></i> {{ __('backend.cms.contents.show.sections.add-section') }}</x-backend.btn>
                    <div class="text-warning align-items-center justify-content-end d-flex mt-2 text-warning @if(!$this->updated) d-none @endif"><i class="fas fa-exclamation-triangle me-2"></i> {{ __('backend.cms.contents.show.warning-save') }}</div>
                </x-backend.card-footer>
            </x-backend.card>
        </div>

        <div class="col-lg-4 col-md-12 side-cards">
            <div class="row mb-3">
                {{-- Main actions --}}
                @include('backend.cms.contents.content-actions')
            </div>
            <div class="row mb-3">
                {{-- Content details --}}
                @include('backend.cms.contents.content-details')
            </div>
            <div class="row">
                {{-- Content details --}}
                @include('backend.cms.contents.content-seo')
            </div>
        </div>
    </div>

    {{-- Modal add section --}}
    @include('backend.cms.contents.content-add-section')

    {{-- Success and failt toast --}}
    @include('backend.cms.contents.content-toast')
</div>