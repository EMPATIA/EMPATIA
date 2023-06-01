<div class="position-relative">
    @if( !empty($cb) )
        @php
                $projectAssetsPath  = \App\Helpers\HFrontend::getProjectPath();
                $defaultImage = asset("/build/assets/frontend/$projectAssetsPath/images/img-default-topic.jpg");
        @endphp

        @if( $cb->activePhaseCode() == 'topic_collection' )
            <div class="text-center my-5">
                <a href="{{ route('page', [ \App\Helpers\Empatia\Cbs\HCb::getCbTypeSlug( getField($cb, 'type') ) , trim(getField($cb, 'slug.'.getLang(), ''), '/') . '/create' ]) }}"
                   class="btn btn-primary d-inline-block"
                ><i class="fas fa-plus me-2"></i>{{ __("frontend.$projectPath.cbs.$cb->type.btn.create") }}</a>
            </div>
        @endif

        @if( $cb->isActionActive('topic', 'index') ) 
            {{--  FILTERS  --}}
            {{--TODO: Filters and Add Translations--}}
            @include($filtersView)

            {{--  TOPICS LIST  --}}
            <div class="position-relative" style="min-height: 10rem">
                <div {{--wire:loading.remove--}}>
                    @include($topicsView, ['topics' => $topics, 'cb' => $cb, 'defaultImage' => $defaultImage])
                </div>
            </div>

            <div class="loading-overlay rounded-1" wire:ignore wire:loading>
                <div class="d-flex justify-content-center h-100" style="padding-top: 6rem">
                    <div class="spinner-border text-primary-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
