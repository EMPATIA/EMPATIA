<div class="container-fluid bg-dark-gray">
    <div class="container py-4">
        @php
            $filterParameters = $cb->getParameters([
                'flags.use_as_filter' => true,
            ]);
        @endphp

        <div class="row">
            <div class="col-12 col-md-7 col-lg-9">
                @foreach($filterParameters ?? [] as $parameter)
                    @php
                        $parameterTitle = data_lang_get($parameter, 'title', null, true);
                    @endphp

                    <div class="mb-4">
                        <h6 class="text-uppercase fw-semibold small">{{ $parameterTitle }}</h6>
                        <div class="btn-group" role="group" aria-label="{{ $parameterTitle }}">
                            <div class="row g-2">
                                @foreach($parameter->options ?? [] as $option)
                                    @php
                                        $optionLabel    = data_lang_get($option, 'label', null, true);
                                        $optionCode     = $option->code ?? '__empty__';
                                        $optionValue    = data_get($option, 'value', data_get($option, 'code'));
                                        $optionId       = "filters.". ($parameter->code ?? '__empty__') .".$optionCode";
                                    @endphp
                                    <div class="col-auto">
                                        <input
                                            type="{{ $parameter->isFilterMultiple() ? 'checkbox' : 'radio' }}" class="btn-check"
                                            value="{{ $optionValue }}"
                                            id="{{$optionId}}"
                                            wire:model="filters.{{ $parameter->code ?? '__empty__' }}" autocomplete="off"
                                            {{ $parameter->isFilterMultiple() ? 'multiple' : '' }}
                                        >
                                        <label class="btn btn-filter{{ data_get($filters, $parameter->code) == $optionValue ? '-selected' : '' }} btn-sm" for="{{$optionId}}">{{ $optionLabel }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                {{--  WINNING  --}}
                {{-- TODO: temp hardcoded solution to filter by winning topics --}}

                @if( !$cb->isOngoing() )
                    <div class="mb-4">
                        <h6 class="text-uppercase fw-semibold small">{{ __("frontend.$projectPath.cbs.{$cb->type}.topics.state.label") }}</h6>
                        <div class="btn-group" role="group" aria-label="{{ __("frontend.$projectPath.cbs.{$cb->type}.topics.state.label") }}">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <input
                                        type="checkbox" class="btn-check"
                                        id="topicState"
                                        value="approved"
                                        wire:model="topicState" autocomplete="off"
                                    >
                                    <label class="btn btn-filter{{ $topicState == 'approved' ? '-selected' : '' }} btn-sm"
                                           for="topicState"
                                    ><i class="fa fa-trophy me-2"></i>{{ __("frontend.$projectPath.cbs.{$cb->type}.topics.filter.winner.label") }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{--  SORT  --}}
                {{-- TODO: review this; temp code; only sorts by state column, alphabetically --}}

            </div>
            <div class="col-12 col-md-5 col-lg-3">
                <div class="">
                    <h6 class="small {{ count($filterParameters ?? []) ?: 'd-none' }}">
                        &nbsp;
                        <span class="d-none">{{ __("frontend.$projectPath.cbs.$cb->type.cb.search.label") }}</span>
                    </h6>
                    <style>
                        .form-group.search {
                            --bs-btn-font-size: 1rem;
                            --bs-btn-padding-y: .375rem;
                            --bs-btn-padding-x: .75rem;
                            --bs-border-width: 1px;
                            position: relative;
                        }
                        .form-group.search .icon{
                            display: block;
                            position: absolute;
                            right: var(--bs-btn-font-size);
                            top: var(--bs-border-width);
                            height: inherit;
                            width: var(--bs-btn-font-size);
                            padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
                            font-size: var(--bs-btn-font-size);
                            line-height: inherit;
                            pointer-events: none;
                            color: inherit;
                            opacity: .5;
                        }
                        .form-group.search .icon::before{
                            font-size: inherit;
                            line-height: inherit;
                        }
                        .form-group.search input {
                            padding-right: calc(var(--bs-btn-padding-x) * 2 + var(--bs-btn-font-size));
                            background-color: transparent;
                        }
                        .form-group.search input::placeholder {
                            color: inherit;
                            opacity: .5;
                        }
                    </style>
                    <div class="form-group search mb-3">
                        <span class="fa fa-search icon btn-sm"></span>
                        <input type="text" class="form-control form-control-sm text-white"
                               placeholder="{{ __("frontend.$projectPath.cbs.$cb->type.cb.search.placeholder") }}"
                               name="searchTopic" wire:model="search"
                        >
                    </div>
                    <div class="text-end">
                        <button class="btn btn-link text-light-gray btn-sm p-0" wire:click="resetFilters">
                            {{ __("frontend.$projectPath.cbs.$cb->type.cb.clear-filters.btn") }}
                            <i class="fa-solid fa-filter-circle-xmark ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
