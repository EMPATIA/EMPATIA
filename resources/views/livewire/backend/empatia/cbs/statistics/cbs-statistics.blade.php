<div>
    <style>
        .stats-card:hover {
            transform: translateY(-5px);
        }

        .circle-icon {
            background: #c7e2ff;
            padding: 20px;
            border-radius: 50%;
        }

    </style>
    {{--   LOADING   --}}
    <div class="loading-overlay" wire:loading style="z-index:15; position: fixed;">
        <div class="d-flex align-items-center justify-content-center h-100">
            <div wire:loading.block class="spinner-border" role="status">
            </div>
        </div>
    </div>

    {{--   GRAPHS & TABLES   --}}
    <div class="card-body tabs-container">
        <div class="row mb-4 d-flex justify-content-center justify-content-md-around mt-3">
            @foreach($overallStatistics ?? [] as $stat)
                <div class="card col-12 col-sm-5 col-lg-3 mx-2 my-2 text-center shadow stats-card">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <div class="card-body p-3">
                                <p class="m-0 p-0" {{--style="font-size: large"--}}>{{ getField($stat, 'label') }}</p>
                                <h4 class="m-0 p-0 fw-bold"
                                    style="color: var(--be-primary)">{{getField($stat, 'value')}}</h4>
                            </div>
                        </div>
                        <div class="col-auto d-flex align-items-center justify-content-center">
                            {!! data_get($stat, 'icon', '') !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card my-4">
        {{--   CITIES SELECT   --}}
        <div class="card-body pb-0">
            <div class="row row-cols-4">
                <div class="col">
                    <x-form-label :label="__('backend.statistics.cbs.types.label')"
                                  class="bold"></x-form-label>
                    <x-form-select name="activeCbType"
                                   :placeholder="__('backend.statistics.cbs.types.placeholder')"
                                   :options="$cbTypes ?? []"
                                   icon="chevron-down"
                                   wire:model="activeCbType"
                    >
                    </x-form-select>
                </div>
                <div class="col">
                    <x-form-input
                        :bind="['startDateCbsTypes' => $startDateCbsTypes]"
                        name="startDateCbsTypes"
                        type="date"
                        wire:model.debounce.500ms="startDateCbsTypes"
                        action="edit"
                        :label="__('backend.generic.start-date')"
                    />
                </div>
                <div class="col">
                    <x-form-input
                        :bind="['endDateCbsTypes' => $endDateCbsTypes]"
                        name="endDateCbsTypes"
                        type="date"
                        wire:model.debounce.500ms="endDateCbsTypes"
                        action="edit"
                        :label="__('backend.generic.end-date')"
                    />
                </div>
            </div>
        </div>
        <div class="card-body tabs-container">
            <div class="col-12">
                <div class="my-3">
                    @if(empty($showChartCbType))
                        <div
                            class="d-flex aligns-items-center justify-content-center p-4"
                            id="params">{{$cbs->count() != 0 && empty($activeCbType) ? __('backend.statistics.cb-type.no-parameters.label') : __('backend.statistics.no-info.label') }}
                        </div>
                    @endif
                    <div class="chart {{!empty($showChartCbType) ? 'chat-cb-type-show' : 'chat-cb-type-hide' }}">
                        <canvas wire:ignore id="overallStatisticsChart" height="400"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @if(!empty($showChartCbType))
                                <div class="row row-cols-4 mt-5 mb-5">
                                    <div class="col-auto">
                                        <x-form-label :label="__('backend.statistics.cb.label')"
                                                      class="bold"></x-form-label>
                                        <x-form-select name="activeCb"
                                                       :placeholder="__('backend.statistics.cb.placeholder')"
                                                       :options="$cbsOptions ?? []"
                                                       icon="chevron-down"
                                                       wire:model="activeCb"
                                                       id="cbs"
                                        >
                                        </x-form-select>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($showChartCbType) && empty($showCbChart))
                                @php
                                    $dataset = [];
                                        if(!empty($cbChart)){
                                          $dataset = getField($cbChart['cbStatisticsChart'], 'datasets', [])[0];
                                          $dataset = array_filter(getField($dataset, 'data', []));
                                        }
                                @endphp
                                <div
                                    class="d-flex aligns-items-center justify-content-center p-4"
                                    id="params">{{ !empty($dataset) ? __('backend.statistics.cbs-no-parameters.label') : __('backend.statistics.no-info.label') }}
                                </div>
                            @endif
                            <div class="chart-cb {{!empty($showCbChart) ? 'chat-cb-show' : 'chat-cb-hide' }}">
                                <canvas wire:ignore id="cbStatisticsChart" height="400"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(!empty($showCbChart))
                                <div class="row row-cols-4 mt-5 mb-5">
                                    <div class="col-auto">
                                        <x-form-label :label="__('backend::statistics.cb-types.' . $cb_type . '.topic.scales')"
                                                      class="bold"></x-form-label>
                                        <x-form-select name="activeTopic"
                                                       :placeholder="__('backend::statistics.cb-types.' . $cb_type . '.topic.scales')"
                                                       :options="$topicOptions ?? []"
                                                       icon="chevron-down"
                                                       id="topics"
                                                       wire:model="activeTopic"
                                        >
                                        </x-form-select>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($showCbChart) && empty($showTopicChart))
                                    @php
                                        $datasetTopic = [];
                                            if(!empty($topicChart)){
                                              $datasetTopic = getField($topicChart['topicStatisticsChart'], 'datasets', [])[0];
                                              $datasetTopic = array_filter(getField($datasetTopic, 'data', []));
                                            }else{
                                                $datasetTopic = 'none';
                                            }
                                    @endphp
                                <div
                                    class="d-flex aligns-items-center justify-content-center p-4"
                                    id="params">{{ !empty($datasetTopic) ?  __('backend.statistics.topics-no-parameters.label') : __('backend.statistics.no-info.label') }}
                                </div>
                            @endif
                            <div
                                class="chart-topic {{!empty($showTopicChart) ? 'chat-topic-show' : 'chat-topic-hide' }}">
                                <canvas wire:ignore id="topicStatisticsChart" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
