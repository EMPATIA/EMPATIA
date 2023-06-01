<div>
    {{--   LOADING   --}}
    <div class="loading-overlay" wire:loading style="z-index:15; position: fixed;">
        <div class="d-flex align-items-center justify-content-center h-100">
            <div wire:loading.block class="spinner-border" role="status">
                <span class="sr-only">{{ __('cms.menus.loading') }}</span>
            </div>
        </div>
    </div>
    {{--   GRAPHS & TABLES   --}}
    <div class="{{--card--}} my-4">
        <div class="card-body tabs-container">
            <h5>{{ __('backend.statistics.cb.topic-votes.daily.label') }}</h5>
            <div class="my-3">
                <div
                    class="chart-topic-votes-per-day {{($activePane === 'per-day' && !empty($showTopicVotesPerDayChart)) ? 'chart-topic-votes-per-day-show' : 'chart-topic-votes-per-day-hide' }}">
                    <canvas wire:ignore id="TopicAndVotesPerDayChart" height="400"></canvas>
                </div>
                @if(empty($showTopicVotesPerDayChart))
                    <div
                        class="d-flex aligns-items-center justify-content-center p-4"
                        id="params">{{__('backend.statistics.no-parameters.label')}}
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body tabs-container">
            <h5>{{ __('backend.statistics.cb.votes.daily.label') }}</h5>
            <div class="row row-cols-4 mt-5 mb-5">
                <div class="col-auto">
                    <x-form-label :label="__('backend.statistics.cb.topics.label')"
                                  class="bold"></x-form-label>
                    <x-form-select name="activeTopic"
                                   :placeholder="__('backend.statistics.cb.topics.placeholder')"
                                   :options="$topicOptions ?? []"
                                   icon="chevron-down"
                                   id="topics"
                                   wire:model="topicPerDay"
                    >
                    </x-form-select>
                </div>
            </div>
            <div class="my-3">
                <div
                    class="chart-votes-per-day {{($activePane === 'per-day' && !empty($showVotesPerDayChart)) ? 'chart-votes-per-day-show' : 'chart-votes-per-day-hide' }}">
                    <canvas wire:ignore id="VotesPerDayChart" height="400"></canvas>
                </div>
                @if(empty($showVotesPerDayChart))
                    <div
                        class="d-flex aligns-items-center justify-content-center p-4"
                        id="params">{{ empty($topicPerDay) ?  __('backend.statistics.topics-no-parameters.label') : __('backend.statistics.no-info.label') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
