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
            <h5>{{ __('backend.statistics.users-parameters.daily.label') }}</h5>
            <div class="my-3">
                <div class="chart-per-day {{($activePane === 'per-day' && !empty($showChartUser)) ? 'chat-per-day-show' : 'chat-per-day-hide' }}">
                    <canvas wire:ignore id="registrationsPerDayChart" height="400"></canvas>
                </div>
                @if(empty($showChartUser))
                    <div
                        class="d-flex aligns-items-center justify-content-center p-4"
                        id="params">{{__('backend.statistics.no-parameters.label')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
