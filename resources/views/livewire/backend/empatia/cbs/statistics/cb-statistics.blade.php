<div>
    <style>
        .card-dark {
            background-color: var(--be-primary);
            color: white;
            box-shadow: none;
        }

        .card-dark hr {
            border-color: rgba(255 255 255 / .2);
        }

        .loading-overlay .spinner-border {
            color: var(--be-primary);
        }

        .nav-tabs .nav-link.active {
            background-color: #FBFBFB;
            border-bottom-color: #FBFBFB;
        }

        .tabs-container {
            background-color: #FBFBFB !important;
        }

        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
            color: #495057;
            border-color: #dee2e6 #dee2e6 #fff !important;
        }
    </style>

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
        <div class="card-body pb-0">
            {{--   CITY TABS   --}}
            <ul class="nav nav-tabs mx-n4 px-4 w-100" id="statsBreakdownTab" role="tablist">
                @foreach($panes ?? [] as $pane)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activePane == $pane ? 'active' : '' }}" id="{{$pane}}-tab" role="tab"
                           aria-controls="{{$pane}}-tab"
                           aria-selected="false"
                           wire:click="changePane('{{$pane}}')"
                        >{{ __("backend.statistics.cb.tab.$pane") }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body tabs-container">
            <div class="tab-content">
                @foreach($panes ?? [] as $pane)
                    <div class="tab-pane {{ $activePane == $pane ? 'active' : '' }}"
                         id="{{$pane}}-pane"
                         role="tabpanel" aria-labelledby="{{$pane}}-tab"
                    >
                        @includeIf("livewire.backend.empatia.cbs.statistics.cb-$pane-tab")
                    </div>
                @endforeach
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

            update: function (id, data) {
                if (typeof id != 'string' || typeof data != 'object') {
                    this.logDebug('update: invalid arguments');
                    return false;
                }

                if (typeof cityAnalyticsCharts[id] != 'undefined') {
                    cityAnalyticsCharts[id].data = data;
                    if (typeof data.labels != 'undefined') {
                        cityAnalyticsCharts[id].chart.data.labels = data.labels;
                    }
                    cityAnalyticsCharts[id].chart.data.datasets = data.datasets;
                    cityAnalyticsCharts[id].chart.options = data.options;

                    cityAnalyticsCharts[id].chart.update();
                    return true;
                }

                return false;
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

        function chart() {
            var value = $('.form-control').val();
            var value_cbs = $('#cbs').val();
            var value_topics = $('#topics').val();
            if (value == '' || $('.chat-cb-type-hide')[0]) {
                $(".chart").hide();
            } else {
                $(".chart").show();
            }
            if (value_cbs == '' || value == '' || $('.chat-cb-hide')[0]) {
                $(".chart-cb").hide();
            } else {
                $(".chart-cb").show();
            }
            if (value_topics == '' || value == '' || value_cbs == '' || $('.chat-topic-hide')[0]) {
                $(".chart-topic").hide();
            } else {
                $(".chart-topic").show();
            }

            if ($('.chat-totals-hide')[0]) {
                $(".chart-totals").hide();
            } else {
                $(".chart-totals").show();
            }

            if ($('.chat-per-day-hide')[0]) {
                $(".chart-per-day").hide();
            } else {
                $(".chart-per-day").show();
            }

            if ($('.chart-summary-topic-show')[0]) {
                $(".chart-summary-topic").hide();
            } else {
                $(".chart-summary-topic").show();
            }
            if ($('.chart-summary-votes-hide')[0]) {
                $(".chart-summary-votes").hide();
            } else {
                $(".chart-summary-votes").show();
            }
            if ($('.chart-summary-topic-hide')[0]) {
                $(".chart-summary-topic").hide();
            } else {
                $(".chart-summary-topic").show();
            }
            if ($('.chat-topic-balance-hide')[0]) {
                $(".chart-topic-balance").hide();
            } else {
                $(".chart-topic-balance").show();
            }
        }
    </script>
</div>
