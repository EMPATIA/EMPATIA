@extends('backend.layouts.master')

@section('header')
    {{ __('backend.statistics.overall-stats.header') }}
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
@endsection

@section('content')
    <livewire:statistics :type="$type"/>
@endsection

@push('scripts')
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
@endpush
