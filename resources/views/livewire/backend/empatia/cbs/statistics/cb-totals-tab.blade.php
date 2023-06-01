<div>
    {{--   GRAPHS & TABLES   --}}
    <div class="my-4">
        {{--@dump($summayChart)--}}
        <div class="card-body tabs-container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('backend.statistics.summary.summary-chart.label') }}</h5>
                            <div class="tab-content">
                                <div class="{{--card--}} my-4">
                                    <div class="card-body tabs-container float-end">
                                        @if(empty($showVotesChart))
                                            <div
                                                class="d-flex aligns-items-center justify-content-center p-4"
                                                id="params-votes">{{ __('backend.statistics.no-info.label') }}
                                            </div>
                                        @endif
                                        <div
                                            class="chart-votes {{!empty($showVotesChart) ? 'chart-votes-show' : 'chart-votes-hide' }}">
                                            <canvas wire:ignore id="summaryChart" height="240"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('backend::statistics.cb-types.' . $cb_type . '.topic.parameters') }}</h5>
                            <div class="card-body pb-0">
                                <div class="row row-cols-4">
                                    <div class="col">
                                        <x-form-label
                                            :label="__('backend.statistics.users-parameters.parameters.label')"
                                            class="bold"></x-form-label>
                                        <x-form-select name="activeParameter"
                                                       :placeholder="__('backend.statistics.users-parameters.parameters.placeholder')"
                                                       :options="$optionsParam ?? []"
                                                       icon="chevron-down"
                                                       wire:model="summaryParam"
                                        >
                                        </x-form-select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body tabs-container">
                                <div class="tab-content">
                                    <div class="{{--card--}} my-4">
                                        <div class="card-body tabs-container">
                                            @php
                                                $dataset = [];
                                                    if(!empty($topicParamsChart) && !empty($summaryParam)){
                                                      $dataset = getField($topicParamsChart['topicParamsChart'], 'datasets', [])[0];
                                                      $dataset = array_filter(getField($dataset, 'data', []));
                                                    }else{
                                                        $dataset = 'none';
                                                    }
                                            @endphp
                                            @if($showTopicParamsChart === false)
                                                <div
                                                    class="d-flex aligns-items-center justify-content-center p-4"
                                                    id="params-topic">{{ !empty($dataset) ? __('backend.statistics.no-parameters.label'): __('backend.statistics.no-info.label') }}
                                                </div>
                                            @endif
                                            <div
                                                class="chart-cb-topic {{!empty($showTopicParamsChart) ? 'chart-cb-topic-show' : 'chart-cb-topic-hide' }}">
                                                <canvas wire:ignore id="topicParamsChart" height="240"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{--            <div class="card-body tabs-container">--}}
{{--                <div class="tab-content">--}}
{{--                    <h5>{{ __('backend.statistics.cb.balance-positive-negative.label') }}</h5>--}}
{{--                    <div class="--}}{{--card--}}{{-- my-4">--}}
{{--                        <div class="card-body tabs-container">--}}
{{--                            @if(empty($showTopicVotesChart))--}}
{{--                                <div--}}
{{--                                    class="d-flex aligns-items-center justify-content-center p-4"--}}
{{--                                    id="params-topic-votes">{{ __('backend.statistics.no-info.label') }}--}}
{{--                                </div>--}}
{{--                            @endif--}}
{{--                            <div--}}
{{--                                class="chart-topic-votes {{!empty($showTopicVotesChart) ? 'chart-topic-votes-show' : 'chart-topic-votes-hide' }}">--}}
{{--                                <canvas wire:ignore id="votesBalancePositiveNegative" width="700" height="140"></canvas>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <h5>{{ __('backend::statistics.cb-types.' . $cb_type . '.topic.chart') }}</h5>
            <div class="card-body tabs-container">
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
                @if(empty($showTopicChart))
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
                        id="params-topic">{{ !empty($datasetTopic) ?  __('backend.statistics.topics-no-parameters.label') : __('backend.statistics.no-info.label') }}
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
