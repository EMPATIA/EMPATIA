<div>
    {{--   LOADING   --}}
    <div class="loading-overlay" wire:loading style="z-index:15; position: fixed;">
        <div class="d-flex align-items-center justify-content-center h-100">
            <div wire:loading.block class="spinner-border" role="status">
                {{--                <span class="sr-only">{{ __('cms::menus.loading') }}</span>--}}
            </div>
        </div>
    </div>

    <div class="row mb-4 justify-content-md-around">
        @foreach($summaryStatistics ?? [] as $key => $stat)
            @if(empty(getField($stat, 'divide')))
                <div
                    class="card col-12 col-sm-5 col-lg-3 mx-2 my-2 text-center shadow stats-card d-flex justify-content-center ">
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
            @else
                <div
                    class="card col-12 col-sm-5 col-lg-3 mx-2 my-2 text-center shadow stats-card d-flex justify-content-center">
                    <h5 class="fw-bold">{{getField($stat, 'label', '')}}</h5>
                    <div class="row mx-0 justify-content-center ">
                        @foreach($stat ?? [] as $key => $divide)
                            @if(is_array($divide))
                                <div class="col p-4 text-center">
                                    <h6>{{ getField($divide, 'label') }}</h6>
                                    <div>
                                        <p class="mb-0">{{getField($divide, 'value')}}</p>
                                    </div>
                                </div>
                            @else
                                @break
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{--   GRAPHS & TABLES   --}}
    <div class="card my-4">
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
                                                id="params">{{__('backend.statistics.no-info.label')}}
                                            </div>
                                        @endif
                                        <div
                                            class="chart-summary-votes {{!empty($showVotesChart) ? 'chart-summary-votes-show' : 'chart-summary-votes-hide' }}">
                                            <canvas wire:ignore id="summaryChart" width="700" height="240"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getRandomHsl() {
            var hue = 208;
            return "hsl(" + hue + ", " + rand(10, 90) + "%, " + rand(10, 90) + "%)";
        }

        cityAnalyticsCharts = {};
        chartTools = {

            debug: true,
            logDebug: function (...args) {
                if (this.debug) {
                    console.log('chartTools', ...args);
                }
            },

            add: function (id, data, type = 'bar', options = {}) {
                if (typeof id != 'string' || typeof data != 'object') {
                    this.logDebug('add: invalid arguments');
                    return false;
                }

                if (typeof cityAnalyticsCharts[id] != 'undefined') {
                    return this.update(id, data);
                }

                let canvas = document.getElementById(id);
                if (canvas == null || typeof canvas.getContext != 'function') {
                    this.logDebug('add: invalid canvas');
                    return false;
                }

                let chartOptions = {
                    plugins: {
                        legend: false
                    },
                    maintainAspectRatio: false
                };

                cityAnalyticsCharts[id] = {
                    canvas: canvas,
                    context: canvas.getContext('2d'),
                    data: data,
                    options: {...chartOptions, ...options}
                }
                $(window).resize(function () {
                    cityAnalyticsCharts[id].chart.resize();
                });

                return cityAnalyticsCharts[id].chart = new Chart(cityAnalyticsCharts[id].context, {
                    type: (typeof type == 'string' ? type : 'bar'),
                    data: {
                        labels: (typeof data.labels == 'object' ? data.labels : []),
                        datasets: (typeof data.datasets == 'object' ? data.datasets : [])
                    },
                    options: {...chartOptions, ...options}
                })
            },
            addMultiple: function (charts) {
                this.logDebug('updateMultiple', charts);
                Object.keys(charts).forEach(key => {
                    this.logDebug('updateMultiple iteration', charts[key]);

                    let type = typeof charts[key].type != 'undefined' ? charts[key].type : null;
                    let options = typeof charts[key].options != 'undefined' ? charts[key].options : null;
                    this.add(key, charts[key], type, options);
                });
                chart();

            }
        }

        window.addEventListener('updateCharts', event => {
            if (typeof event.detail != 'undefined' && typeof event.detail.charts != 'undefined') {
                chartTools.addMultiple(event.detail.charts);
            }
        });

        $(function () {
            chartTools.addMultiple(@json($summaryChart));
        })

    </script>
</div>
