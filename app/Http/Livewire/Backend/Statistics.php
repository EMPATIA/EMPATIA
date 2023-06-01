<?php

namespace App\Http\Livewire\Backend;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use App\Models\Empatia\Cbs\Vote;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Statistics extends Component
{
    public $activeParameter;
    public $startDateUser;
    public $endDateUser;
    public $users;
    public $usersList;
    public $param_configs;
    public $param_options = [];
    public $parameterChart = [];
    public $showChartUser = false;
    public $type;

    //Cbs
    public $startDateCbsTypes;
    public $endDateCbsTypes;
    public $cbs;
    public $cb_type;
    public $cbTypes;
    public $activeCbType;
    public $showChartCbType = false;
    public $cbTypeChart = [];
    public $overallStatistics = [];

    //Specific CB
    public $cb;
    public $activeCb;
    public $cbsOptions = [];
    public $cbChart = [];
    public $showCbChart = false;

    //Topics
    public $allTopics;
    public $activeTopic;
    public $topic;
    public $topicOptions = [];
    public $topicChart = [];
    public $showTopicChart = false;

    //Daily
    public $pane;
    public $activePane;
    public $dailyChart = [];
    public $topicPerDay;
    public $showVotesPerDayChart = false;
    public $showTopicVotesPerDayChart = false;
    public array $panes = [
        'totals',
        'per-day'
    ];

    //Summary
    public $summaryStatistics = [];
    public $summaryChart = [];
    public $optionsParam = [];
    public $summaryParam;
    public $topicParamsChart = [];
    public $showTopicParamsChart = false;
    public $showTopicBalanceChart = false;
    public $showVotesChart = false;
    public $showTopicVotesChart = false;

    //ALL
    public $votes;
    public $topics;
    public $param;
    public $cbId = null;

    public function mount()
    {
        $this->votes = Vote::all();

        if ($this->type == 'users') { //Inicialização dos dados quando é estatisticas do tipo users, ou seja (estatisticas dos users)
            $this->param_configs = HBackend::getConfigurationByCode('user_parameters');
            foreach ($this->param_configs ?? [] as $key => $param) {
                if (getField($param, 'statistics.enabled')) {
                    $this->param_options = data_set($this->param_options, $key, $param->title->{getLang()});
                }
            }
            $this->usersList = User::orderBy('created_at')->get();
        } elseif ($this->type == 'cbs') { //Inicialização dos dados quando é estatisticas do tipo cbs, ou seja (estatisticas dos cbs)
            $this->cbTypes = HCb::getCbTypes();
            $this->getStatistics();
//            $this->getCbTypeStatistics();
        } elseif (!empty($this->cbId) || $this->type == 'summary') { //Inicialização dos dados quando é estatisticas do tipo summary, ou seja (Gerais) ou então um cb especifico
            $cbParameters = getField(HBackend::getConfigurationByCode('cb_settings'), 'types.'.$this->cb_type.'.parameters', []);
            $this->optionsParam = findObjectsByProperty('statistics.enabled', true, collect($cbParameters));
            $this->optionsParam = collect($this->optionsParam)->pluck('title.' . getLang(), 'code')->toArray();
            $this->optionsParam = collect($cbParameters)->where('statistics.enabled', '===', true)->pluck('title.' . getLang(), 'code')->toArray();
            $this->getStatistics();
            $this->getStatisticsCbs();
            $this->getPositiveNegativeVotesChart();
            $this->getVotesBalancePositiveNegativeChart();
            $this->getTopicsParams();
            $this->getTopicVotesCharts();
        }

        //Inicializar as datas
        $this->endDateCbsTypes = Carbon::now()->isoFormat('Y-MM-DD');
        $this->startDateCbsTypes = Carbon::now()->subMonth(getenv('MONTHS'))->isoFormat('Y-MM-DD');

        $this->endDateUser = Carbon::now()->isoFormat('Y-MM-DD');
        $this->startDateUser = Carbon::now()->subMonth(getenv('MONTHS'))->isoFormat('Y-MM-DD');

        $this->activePane = 'totals';
    }

    public function render()
    {
        if($this->type == 'cbs'){
            $view = 'livewire.backend.empatia.cbs.statistics.cbs-statistics';
        }elseif($this->type === 'summary'){
            $view = 'livewire.backend.empatia.statistics.summary-statistics';
        } elseif (!empty($this->cbId)) {
            $view = 'livewire.backend.empatia.cbs.statistics.cb-statistics';
        } else {
            $view = 'livewire.backend.statistics.' . $this->type . '-statistics';
        }
        return view($view);
    }

    //Estatisticas dos Users
    public function updatingActiveParameter($value) //Obtem o parametro do select
    {
        $this->param = $value;
        if (!empty($value)) {
            $this->showChartUser = true;
        } else {
            $this->showChartUser = false;
        }
        $this->getDailyChart();
        $this->getUsersParametersCharts();
    }

    public function updatedStartDateUser($value) //Obtem a data inicial do input do tipo date
    {
        if (!empty($this->param)) {
            $this->updateDatesUser();
        }
    }

    public function updatedEndDateUser($value) //Obtem a data final do input do tipo date
    {
        if (!empty($this->param)) {
            $this->updateDatesUser();
        }
    }

    public function updateDatesUser()//Filtra as queries/collections tendo em conta as datas inseridas
    {
        $this->usersList = User::all();
        if (!empty($this->startDateUser)) {
            $this->usersList = $this->usersList->where('created_at', '>=', $this->startDateUser);
        }

        if (!empty($this->endDateUser)) {
            $this->usersList = $this->usersList->where('created_at', '<=', $this->endDateUser);
        }else{
            $now = Carbon::now()->isoFormat('Y-MM-DD');
            $this->usersList = $this->usersList->where('created_at', '<=', $now);
        }
        $this->getDailyChart();
        $this->getUsersParametersCharts();
    }

    public function getUsersParametersCharts() //Gera o gráfico das estatisticas dos users
    {
        $labels = [];
        $dataSet = [];
        foreach ($this->usersList ?? [] as $user) {
            if (!empty(getField($user->parameters, $this->param)) && !in_array(getField($user->parameters, $this->param), $labels)) {
                array_push($labels, getField($user->parameters, $this->param));
            }
        }
        sort($labels);

        $param = $this->param;
        //Obter as traduções para as labels do chart
            foreach ($labels ?? [] as $code => $label) {
                if (!is_numeric($label)) {
                    $config = findObjectsByProperty('code', $label, getField(HBackend::getConfigurationByCode('user_parameters'), $this->param . '.options', []))[0];
                    data_set($labels, $code, getField($config, 'label.' . getLang(), ''));
                    $type = getField($config, 'statistics.chart_type', 'bar');
                } else {
                    data_set($labels, $code, $label);
                }
            }

        //Criação dos datasets
        foreach ($labels ?? [] as $code => $option) {
            $data = array_count_values($data = $this->usersList->pluck('parameters.' . $param)->filter()->toArray());
            sort($data);
            $dataSet[] = [
                'label' => [$option],
                'data' => [$option => $data[$code]],
                'backgroundColor' => HBackend::getRandomHsl(),
                'borderColor' => HBackend::getRandomHsl(),
            ];

        }

        //Criação do chart
        $this->parameterChart['overallStatisticsChart'] = [
            'labels' => array_values($labels),
            'type' => !empty($type) ? $type : 'bar',
            'datasets' => $dataSet,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
                'scales' => [
                    'x' => HBackend::getScaleOptions('x', __('backend::statistics.users-parameters.' . $this->param . '.scales'), ['stacked' => true]),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.users-parameters.users-number.scales'), ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['stepSize' => 1]])
                ]
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->parameterChart]);
    }

    //Cbs
    public function updatingActiveCbType($value) //Obtem o tipo de cb do select
    {
        $this->cb_type = $value;
        if (!empty($value)) {
            $this->showChartCbType = true;
        } else {
            $this->showChartCbType = false;
        }
        //Inicialização das flags
        $this->showCbChart = false; //Flag para saber se se deve mostrar o gráfico
        $this->showTopicChart = false; //Flag para saber se se deve mostrar o gráfico
        $this->topicOptions = []; //Array para o select do gráfico dos tópicos
        $this->cbsOptions = []; //Array para o select do gráfico dos cbs
        $this->cb = null; //Limpar a colelction
        $this->topic = null; //Limpar a colelction
        $this->activeCb = '';
        $this->activeTopic = '';
        $this->getStatistics();
        $this->getCbs();

//        $this->getCbTypeStatistics();
        $this->getCbTypeCharts();
    }

    public function updatingActiveCb($value) //Obtem o cb do select
    {
        $this->cb = $value;
        if (!empty($value)) {
            $this->showCbChart = true;
        } else {
            $this->showCbChart = false;
        }
        $this->showTopicChart = false;
        $this->topic = null;
        $this->activeTopic = '';
        $this->getStatistics();
        $this->getTopicsParams();
        $this->getCbCharts();

    }

    public function updatingActiveTopic($value) //Obtem o tópico do select
    {
        $this->topic = $value;
        if (!empty($value)) {
            $this->showTopicChart = true;
        } else {
            $this->showTopicChart = false;
        }
        $this->getStatistics();
        $this->getTopicVotesCharts();
    }

    public function updatedStartDateCbsTypes($value)
    {
        $this->topicOptions = [];
        $this->cbsOptions = [];
        $this->showCbChart = false;
        $this->showTopicChart = false;
        $this->cb = null;
        $this->topic = null;
        $this->activeCb = '';
        $this->activeTopic = '';
        $this->getStatistics();
        $this->getCbs();
        $this->getCbTypeCharts();
//        $this->getCbTypeStatistics();
        $this->getCbTypeCharts();

    }

    public function updatedEndDateCbsTypes($value)
    {
        $this->topicOptions = [];
        $this->cbsOptions = [];
        $this->showCbChart = false;
        $this->showTopicChart = false;
        $this->cb = null;
        $this->topic = null;
        $this->activeCb = '';
        $this->activeTopic = '';
        $this->getStatistics();
        $this->getCbs();
        $this->getCbTypeCharts();
//        $this->getCbTypeStatistics();
        $this->getCbTypeCharts();
    }

    public function getStatisticsCbs() //Filtrar as collections
    {
        $this->cbs = Cb::all();
        if (!empty($this->cb_type)) {
            $this->cbs = $this->cbs->where('type', $this->cb_type);
            if (!empty($this->cb)) {
                $this->cbs = $this->cbs->where('id', $this->cb);
            }
            //Obtem os ids dos votos cujo pertencentes aos cbs filtrados
            $votes_id = [];
            foreach ($this->votes ?? [] as $votes) {
                foreach ($this->cbs as $cbs) {
                    if (getField($votes, 'cb_id') == $cbs->id) {
                        array_push($votes_id, $votes->id);
                        break;
                    }
                }
            }
            //Obtem os ids dos topicos cujo pertencentes aos cbs filtrados
            $topics_id = [];
            foreach ($this->topics ?? [] as $topic) {
                foreach ($this->cbs ?? [] as $cbs) {
                    if (getField($topic, 'cb_id') == $cbs->id) {
                        array_push($topics_id, $topic->id);
                        break;
                    }
                }
            }
            if (empty($topics_id)) {
                $this->topics = [];
            } else {
                $this->topics = $this->topics->whereIn('id', $topics_id);
            }
            if (empty($votes_id)) {
                $this->votes = [];
            } else {
                $this->votes = $this->votes->whereIn('id', $votes_id);
            }
        }
        $this->updateCbTypesDates();
    }

//    public function getCbTypeStatistics() //Obter estatisticas para os cards (Usado nas estatisticas gerais dos cbs /private/statistics/cbs)
//    {
//        try {
//            $this->overallStatistics['totalCbs']['label'] = __('backend::statistics.cbs.cbs.total.label');
//            $this->overallStatistics['totalCbs']['value'] = !empty($this->cbs) ? $this->cbs->count() : 0;
//            $this->overallStatistics['totalCbs']['icon'] = '<i class="fa-brands fa-wpforms fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//
//            $this->overallStatistics['totalVoters']['label'] = __('backend::statistics.cbs.votes.total-users.label');
//            $this->overallStatistics['totalVoters']['value'] = !empty($this->votes) ? $this->votes->groupBy('user_id')->count() : 0;
//            $this->overallStatistics['totalVoters']['icon'] = '<i class="fa-solid fa-users fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//
//
//            $this->overallStatistics['totalVotes']['label'] = __('backend::statistics.cbs.votes.total.label');
//            $this->overallStatistics['totalVotes']['value'] = !empty($this->votes) ? $this->votes->count() : 0;
//            $this->overallStatistics['totalVotes']['icon'] = '<i class="fa-solid fa-check-to-slot fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//
//            $this->overallStatistics['totalTopicsCreated']['label'] = __('backend::statistics.cbs.topics.total.label');
//            $this->overallStatistics['totalTopicsCreated']['value'] = !empty($this->topics) ? $this->topics->count() : 0;
//            $this->overallStatistics['totalTopicsCreated']['icon'] = '<i class="fa-solid fa-lightbulb fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//        } catch (QueryException|Exception|\Throwable $e) {
//            logError('getOverallStatistics: ' . json_encode($e->getMessage()));
//            return redirect()->back();
//        }
//    }

    public function getCbTypeCharts() //Obter o gráfico do cbtype (1º gráfico)
    {
        $cbsTypeDatasets = [];
        $labels = [];
        $cbTypeData = [];
        $topicTotal = [];
        $votesTotal = [];
        $usersTotal = [];
        $backgroundColor = [];

        if (empty($this->cbs->toArray())) {
            $this->showChartCbType = false;
        }



        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type === $this->cb_type && !in_array(getField($cb, 'title.' . getLang()), $labels)) {
                array_push($labels, getField($cb, 'title.' . getLang()));
            }
        }

        //Criação do dataset dos topicos
        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->topics)) {
                    array_push($cbTypeData, $this->topics->where('cb_id', $cb->id)->count());
                    array_push($backgroundColor, 'hsl(208, 55%, 54%)');
                }
            }
        }
        array_push($topicTotal, $cbTypeData);
        $cbsTypeDatasets[] = [
            'label' => [__('backend::statistics.cb-types.' . $this->cb_type . '.topic.scales')],
            'data' => $topicTotal[0],
            'backgroundColor' => $backgroundColor,
        ];

        //Criação do dataset dos votos
        $cbTypeData = [];
        $backgroundColor = [];

        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->votes)) {
                    array_push($cbTypeData, $this->votes->where('cb_id', $cb->id)->count());
                    array_push($backgroundColor, 'hsl(208, 86%, 82%)');

                }
            }
        }
        array_push($votesTotal, $cbTypeData);
        $cbsTypeDatasets[] = [
            'label' => [__('backend::statistics.cb-types.' . $this->cb_type . '.votes.scales')],
            'data' => $votesTotal[0],
            'backgroundColor' => $backgroundColor,
        ];

        //Criação do dataset das submissões
        $cbTypeData = [];
        $backgroundColor = [];

        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->votes)) {
                    array_push($cbTypeData, $this->votes->where('cb_id', $cb->id)->groupBy('user_id')->count());
                    array_push($backgroundColor, 'hsl(208, 64%, 86%)');
                }
            }
        }
        array_push($usersTotal, $cbTypeData);
        $cbsTypeDatasets[] = [
            'label' => [__('backend::statistics.cb-types.' . $this->cb_type . '.users.scales')],
            'data' => $usersTotal[0],
            'backgroundColor' => $backgroundColor,
        ];

        //Validar se é para mostrar o gráfico ou não
        foreach ($cbsTypeDatasets as $data) {
            if (empty(array_filter(getField($data, 'data', [])))) {
                $this->showChartCbType = false;
            } else {
                $this->showChartCbType = true;
                break;
            }
        }

        $this->cbTypeChart['overallStatisticsChart'] = [
            'type' => 'bar',
            'labels' => $labels,
            'datasets' => $cbsTypeDatasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
                'scales' => [
                    'x' => HBackend::getScaleOptions('x', __('backend::statistics.cb-types.' . $this->cb_type . '.scales'), ['stacked' => true]),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.cb-types.total-number.scales'), ['stacked' => true, 'beginAtZero' => true])
                ]
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->cbTypeChart]);
    }

    public function getCbCharts() //Obter o gráfico do cb (2º gráfico)
    {
        $cbsTypeDatasets = [];
        $labels = [];
        $cbTypeData = [];
        $topicTotal = [];
        $backgroundColor = [];

        //Criação do dataset dos topicos
        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->topics)) {
                    array_push($cbTypeData, $this->topics->where('cb_id', $cb->id)->count());
                    array_push($backgroundColor, HBackend::getRandomHsl('0.5'));
                } else {
                    array_push($cbTypeData, 0);
                }
            }
        }
        array_push($topicTotal, $cbTypeData);
        array_push($labels, [__('backend::statistics.cb-types.' . $this->cb_type . '.topic.scales')]);
        $cbsTypeDatasets[] = [
            'data' => $topicTotal[0] ?? [0],
            'backgroundColor' => $backgroundColor,
        ];

        //Criação do dataset dos votos
        $cbTypeData = [];
        $backgroundColor = [];

        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->votes)) {
                    array_push($cbTypeData, $this->votes->where('cb_id', $cb->id)->count());
                } else {
                    array_push($cbTypeData, 0);
                }
            }
        }
        data_set($cbsTypeDatasets[0], 'data', array_merge($cbsTypeDatasets[0]['data'], $cbTypeData ?? [0]));
        data_set($cbsTypeDatasets[0], 'backgroundColor', array_merge($cbsTypeDatasets[0]['backgroundColor'], [HBackend::getRandomHsl('0.5')]));
        array_push($labels, [__('backend::statistics.cb-types.' . $this->cb_type . '.votes.scales')]);

        //Criação do dataset das submissões
        $cbTypeData = [];
        $backgroundColor = [];

        foreach ($this->cbs ?? [] as $cb) {
            if ($cb->type == $this->cb_type) {
                if (!empty($this->votes)) {
                    array_push($cbTypeData, $this->votes->where('cb_id', $cb->id)->groupBy('user_id')->count());
                } else {
                    array_push($cbTypeData, 0);
                }
            }
        }
        data_set($cbsTypeDatasets[0], 'data', array_merge($cbsTypeDatasets[0]['data'], $cbTypeData) ?? [0]);
        data_set($cbsTypeDatasets[0], 'backgroundColor', array_merge($cbsTypeDatasets[0]['backgroundColor'], [HBackend::getRandomHsl('0.5')]));
        array_push($labels, [__('backend::statistics.cb-types.' . $this->cb_type . '.users.scales')]);

        //Validar se é para mostrar o gráfico ou não
        if (empty(array_filter(getField($cbsTypeDatasets[0], 'data', [])))) {
            $this->showCbChart = false;
            $this->showTopicChart = false;
        }

        if (empty($this->cb)) {
            data_set($cbsTypeDatasets[0], 'data', ['none']);
        }

        $this->cbChart['cbStatisticsChart'] = [
            'type' => 'polarArea',
            'labels' => $labels,
            'datasets' => $cbsTypeDatasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
            ]
        ];

        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->cbChart]);
    }

    public function getTopicVotesCharts() //Obter o gráfico do tópico (3º gráfico)
    {
        $cbsTypeDatasets = [];
        $labels = [__('backend::statistics.topics.submitted-votes.label'), __('backend::statistics.topics.unsubmitted-votes.label')];

        $cbTypeData = [];
        $background = [];

        if (!empty($this->votes) && !empty($this->votes->toArray())) {
            array_push($cbTypeData, $this->votes->where('topic_id', $this->topic)->where('submitted', 1)->count());
            array_push($background, HBackend::getRandomHsl());
            array_push($cbTypeData, $this->votes->where('topic_id', $this->topic)->where('submitted', 0)->count());
            array_push($background, HBackend::getRandomHsl());
        } else {
            $this->showTopicChart = false;
        }

        $cbsTypeDatasets[] = [
            'data' => $cbTypeData,
            'backgroundColor' => $background,
        ];

        if (empty(array_filter($cbTypeData ?? []))) {
            $this->showTopicChart = false;
        }

        if (empty($this->topic)) {
            data_set($cbsTypeDatasets[0], 'data', ['none']);
        }

        $this->topicChart['topicStatisticsChart'] = [
            'type' => 'doughnut',
            'labels' => $labels,
            'datasets' => $cbsTypeDatasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->topicChart]);
    }

    //Daily
    public function getDailyChart() //Obter o gráfico por datas dos user parameters
    {
        $labels = [];
        $registrationsPerDay = [];
        $period = $this->getPeriod();
        $dataSet = [];

        foreach ($period ?? [] as $date) {
            $labels[] = $date;
            $registrationsPerDay[$date] = 0;
        }

        $param = $this->param;
        $parameterOptions = $this->usersList->pluck('parameters.' . $param)->unique()->filter()->toArray();
        //Totais das opções do parametro agrupadas por datas e os seus respetivos data sets
        foreach ($parameterOptions as $code => $option) {
            $data = $this->usersList->map(function ($item) {
                return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
            })->groupBy(function ($item) use ($param) {
                return data_get($item, 'parameters.' . $param);
            }, 'created_at')->get($option)->groupBy('create_date')->map(function ($item) {
                return $item->count();
            })->toArray();

            if ($this->param != 'profession') {
                if (!is_numeric($option)) {
                    $option = getField(findObjectsByProperty('code', $option, getField(HBackend::getConfigurationByCode('user_parameters'), $this->param . '.options', []))[0], 'label.' . getLang(), '');
                }
            }
            $dataSet[] = [
                'label' => [$option],
                'data' => array_merge($registrationsPerDay, $data),
                'backgroundColor' => HBackend::getRandomHsl(),
                'borderColor' => HBackend::getRandomHsl(),
                'tension' => 0.5
            ];
        }
        $this->dailyChart['registrationsPerDayChart'] = [
            'labels' => $labels,
            'type' => 'line',
            'datasets' => $dataSet,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
                'scales' => [
                    'x' => HBackend::getScaleOptions('x', __('backend::statistics.users-parameters.' . $this->param . '.scales'), []),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.users-parameters.users-number.scales'), ['ticks' => ['stepSize' => 1]])
                ]
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->dailyChart]);

    }

    public function getTopicAndVotesPerDayChart() //Obter o gráfico por datas dos topics + votos (diferentes tipos)
    {
        $labels = [];
        $registrationsPerDay = [];
        $period = $this->getPeriod();
        $dataSet = [];

        foreach ($period ?? [] as $date) {
            $labels[] = $date;
            $registrationsPerDay[$date] = 0;
        }

        $votes = $this->votes->where('cb_id', '=', $this->cbId);

        //DataSet Total topics created by date
        $totalTopics = $this->topics->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.total-topics.label'),
            'data' => array_merge($registrationsPerDay, $totalTopics),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];


        //DataSet Positive Votes Submitted
        $positiveVotesSubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 1)->where('value', '=', 1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.positive-votes-submitted.label'),
            'data' => array_merge($registrationsPerDay, $positiveVotesSubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Positive Votes Unsubmitted
        $positiveVotesUnsubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 0)->where('value', '=', 1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.positive-votes-unsubmitted.label'),
            'data' => array_merge($registrationsPerDay, $positiveVotesUnsubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Negative Votes Submitted
        $negativeVotesSubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 1)->where('value', '=', -1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.negative-votes-submitted.label'),
            'data' => array_merge($registrationsPerDay, $negativeVotesSubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Negative Votes Unsubmitted
        $negativeVotesUnsubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 0)->where('value', '=', -1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.negative-votes-unsubmitted.label'),
            'data' => array_merge($registrationsPerDay, $negativeVotesUnsubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Validar se é para mostrar o gráfico ou não
        foreach ($dataSet as $data) {
            if (empty(array_filter(getField($data, 'data', [])))) {
                $this->showTopicVotesPerDayChart = false;
            } else {
                $this->showTopicVotesPerDayChart = true;
                break;
            }
        }

        $this->dailyChart['TopicAndVotesPerDayChart'] = [
            'labels' => $labels,
            'type' => 'line',
            'datasets' => $dataSet,
            'options' => ['plugins' => ['legend' => 'top'],
                'scales' => ['x' => HBackend::getScaleOptions('x', __('backend::statistics.cb.dates.scales'), []),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.users-parameters.votes-number.scales'), ['ticks' => ['stepSize' => 1]])]]];

        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->dailyChart]);
    }

    public function getVotesPerDayChart() //Obter o gráfico por datas dos votos (diferentes tipos)
    {
        $labels = [];
        $registrationsPerDay = [];
        $period = $this->getPeriod();
        $dataSet = [];

        foreach ($period ?? [] as $date) {
            $labels[] = $date;
            $registrationsPerDay[$date] = 0;
        }

        $votes = $this->votes->where('cb_id', '=', $this->cbId)->where('topic_id', '=', $this->topicPerDay);

        //DataSet Positive Votes Submitted
        $positiveVotesSubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 1)->where('value', '=', 1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.positive-votes-submitted.label'),
            'data' => array_merge($registrationsPerDay, $positiveVotesSubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Positive Votes Unsubmitted
        $positiveVotesUnsubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 0)->where('value', '=', 1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.positive-votes-unsubmitted.label'),
            'data' => array_merge($registrationsPerDay, $positiveVotesUnsubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Negative Votes Submitted
        $negativeVotesSubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 1)->where('value', '=', -1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.negative-votes-submitted.label'),
            'data' => array_merge($registrationsPerDay, $negativeVotesSubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Dataset Negative Votes Unsubmitted
        $negativeVotesUnsubmitted = $votes->map(function ($item) {
            return data_set($item, 'create_date', $item->created_at->format('Y-m-d'));
        })->groupBy('create_date')->map(function ($item) {
            return $item->where('submitted', '=', 0)->where('value', '=', -1)->count();
        })->toArray();

        $dataSet[] = [
            'label' => __('backend::statistics.cb.negative-votes-unsubmitted.label'),
            'data' => array_merge($registrationsPerDay, $negativeVotesUnsubmitted),
            'backgroundColor' => HBackend::getRandomHsl(),
            'borderColor' => HBackend::getRandomHsl(),
            'tension' => 0.5
        ];

        //Validar se é para mostrar o gráfico ou não
        foreach ($dataSet as $data) {
            if (empty(array_filter(getField($data, 'data', [])))) {
                $this->showVotesPerDayChart = false;
            } else {
                $this->showVotesPerDayChart = true;
                break;
            }
        }

        $this->dailyChart['VotesPerDayChart'] = [
            'labels' => $labels,
            'type' => 'line',
            'datasets' => $dataSet,
            'options' => ['plugins' => ['legend' => 'top'],
                'scales' => ['x' => HBackend::getScaleOptions('x', __('backend::statistics.cb.dates.scales'), []),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.users-parameters.votes-number.scales'), ['ticks' => ['stepSize' => 1]])]]];

        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->dailyChart]);

    }

    public function changePane($pane) //Atualizar o pane (Entre estatísticas totais e diárias)
    {
        $this->activePane = $pane;
        if (empty($this->cbId)) {
            $this->getDailyChart();
        } else {
            $this->getTopicAndVotesPerDayChart();
        }
    }

    public function updatingTopicPerDay($value)
    {
        $this->topicPerDay = $value;
        $this->getVotesPerDayChart();
    }

    //Summary

//    public function getSummaryStatistics() //Estatiticas Gerais (Summary e cb Especifico)
//    {
//        try {
//            $topics = Topic::all();
//            $overallStatistics = [];
//
//            if (empty($this->cbId)) {
//                $overallStatistics['cbs']['label'] = __('backend::statistics.summary.empaville-cbs.label');
//                $overallStatistics['cbs']['value'] = Cb::where('type', 'empaville')->count();
//                $overallStatistics['cbs']['icon'] = '<i class="fa-solid fa-users fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//                $overallStatistics['cbs']['divide'] = false;
//            } else {
//                $overallStatistics['submissions']['label'] = __('backend::statistics.cb.submissions.label');
//                $overallStatistics['submissions']['value'] = Vote::where('cb_id', $this->cbId)->pluck('user_id')->unique()->count();
//                $overallStatistics['submissions']['icon'] = '<i class="fa-solid fa-users fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//                $overallStatistics['submissions']['divide'] = false;
//            }
//
//            $overallStatistics['totalTopicsCreated']['label'] = __('backend::statistics.summary.total-topics.label');
//            $overallStatistics['totalTopicsCreated']['value'] = empty($this->cbId) ? $topics->count() : $topics->where('cb_id', $this->cbId)->count() ?? 0;
//            $overallStatistics['totalTopicsCreated']['icon'] = '<i class="fa-brands fa-wpforms fa-5x text-dark circle-icon" aria-hidden="true"></i>';
//            $overallStatistics['totalTopicsCreated']['divide'] = false;
//
//            if (!empty($this->votes)) {
//                $submittedPositiveVotes = empty($this->cbId) ? $this->votes->where('submitted', 1)->where('value', '>', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', 1)->where('value', '>', 0)->count();
//                $unsubmittedPositiveVotes = empty($this->cbId) ? $this->votes->where('submitted', 0)->where('value', '>', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', 0)->where('value', '>', 0)->count();
//                $submittedNegativeVotes = $this->votes->where('submitted', 1)->where('value', '<', 0)->count();
//                $unsubmittedNegativeVotes = $this->votes->where('submitted', 0)->where('value', '<', 0)->count();
//            }
//
//            $overallStatistics['totalPositiveVotes'] = [
//                'submittedPositiveVotes' => ['label' => __('backend::statistics.summary.submitted-positive-votes.label'),
//                    'value' => $submittedPositiveVotes ?? 0,
//                    'icon' => '<i class="fa-solid fa-user-plus fa-5x text-dark circle-icon" aria-hidden="true"></i>'],
//
//                'unsubmittedPositiveVotes' => ['label' => __('backend::statistics.summary.unsubmitted-positive-votes.label'),
//                    'value' => $unsubmittedPositiveVotes ?? 0,
//                    'icon' => '<i class="fa-solid fa-user-minus fa-5x text-dark circle-icon" aria-hidden="true"></i>']
//            ];
//            $overallStatistics['totalPositiveVotes']['divide'] = true;
//            $overallStatistics['totalPositiveVotes']['label'] = __('backend::statistics.summary.total-positive-votes.label');
//
//            $overallStatistics['totalNegativeVotes'] = [
//                'submittedNegativeVotes' => ['label' => __('backend::statistics.summary.submitted-negative-votes.label'),
//                    'value' => $submittedNegativeVotes ?? 0,
//                    'icon' => '<i class="fa-solid fa-user-plus fa-5x text-dark circle-icon" aria-hidden="true"></i>'],
//
//                'unsubmittedNegativeVotes' => ['label' => __('backend::statistics.summary.unsubmitted-negative-votes.label'),
//                    'value' => $unsubmittedNegativeVotes ?? 0,
//                    'icon' => '<i class="fa-solid fa-user-minus fa-5x text-dark circle-icon" aria-hidden="true"></i>']
//            ];
//            $overallStatistics['totalNegativeVotes']['divide'] = true;
//            $overallStatistics['totalNegativeVotes']['label'] = __('backend::statistics.summary.total-negative-votes.label');
//
//            $this->summaryStatistics = $overallStatistics;
//
//        } catch (QueryException|Exception|\Throwable $e) {
//            logError('getOverallStatistics: ' . json_encode($e->getMessage()));
//            return redirect()->back();
//        }
//    }

    public function getPositiveNegativeVotesChart() //Obter o gráfico dos votos Positovs e Negativos
    {
        $votesDatasets = [];

        $labels = [__('backend.statistics.summary.positive-votes-submitted.label'), __('backend.statistics.summary.positive-votes-not-submitted.label'),
            __('backend.statistics.summary.negative-votes-submitted.label'), __('backend.statistics.summary.negative-votes-not-submitted.label')];
        if (!empty($this->votes)) {
            $positiveVotes[] = empty($this->cbId) ? $this->votes->where('submitted', true)->where('value', '>', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', 1)->where('value', '>', 0)->count();
            $positiveVotes[] = empty($this->cbId) ? $this->votes->where('submitted', false)->where('value', '>', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', false)->where('value', '>', 0)->count();
            $negativeVotes[] = empty($this->cbId) ? $this->votes->where('submitted', true)->where('value', '<', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', true)->where('value', '<', 0)->count();
            $negativeVotes[] = empty($this->cbId) ? $this->votes->where('submitted', false)->where('value', '<', 0)->count() : $this->votes->where('cb_id', $this->cbId)->where('submitted', false)->where('value', '<', 0)->count();
            $dataset = array_merge($positiveVotes, $negativeVotes);
        }
        $votesDatasets[] = [
            'label' => __('backend::statistics.summary.votes-number.label'),
            'data' => isset($dataset) ? $dataset : [],
            'backgroundColor' => ["hsl(140, 100%, 35%)", "hsl(140, 100%, 48%)", "hsl(12, 68%, 41%)", "hsl(12, 100%, 45%)"]
        ];

        if (empty(array_filter(getField($votesDatasets[0], 'data', [])))) {
            $this->showVotesChart = false;
        } else {
            $this->showVotesChart = true;
        }

        $this->summaryChart['summaryChart'] = [
            'type' => 'pie',
            'labels' => $labels,
            'datasets' => $votesDatasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->summaryChart]);

    }

    public function updatingSummaryParam($value)
    {
        $this->summaryParam = $value;
        if (!empty($value)) {
            $this->showTopicParamsChart = true;
        } else {
            $this->showTopicParamsChart = false;
        }
        $this->getStatistics();
        $this->getTopicParametersChart();
    }

    public function getTopicParametersChart() //Obter o gráfico do parametro do tópico selecionado
    {
        $labels = [];
        $datasets = [];
        $optionsParam = findObjectsByProperty('code', $this->summaryParam, getField(HBackend::getConfigurationByCode('cb_settings'), 'types.'.$this->cb_type.'.parameters', []));
        if (!empty($optionsParam)) {
            $labels = HFrontend::optionsFromParameter(first_element((object)$optionsParam));
        }
        $param = $this->summaryParam;
        $dataDefault = array_fill_keys(array_keys($labels), 0);
        if (!empty($this->topics) && !empty($this->topics->toArray())) {
            if (!empty($this->cbId)) {
                $this->topics = $this->topics->where('cb_id', $this->cbId);
            }
            //Obter os datasets de para opção do parametro selecionado
            foreach (array_keys($labels) ?? [] as $code => $value) {
                $data = $this->topics->groupBy(function ($item) use ($param) {
                    return data_get($item, 'parameters.' . $param);
                })->map(function ($item) use ($param, $value) {
                    return $item->count();
                })->toArray();
                    if (!is_numeric($value)) {
                        $label = getField(HFrontend::optionsFromParameter(first_element((object)$optionsParam)), $value, '');
                    }
                $data = array_merge($dataDefault, $data);
                $datasets[] = [
                    'label' => [$label],
                    'data' => [$label => isset($data[$value]) ? $data[$value] : $dataDefault[$value]],
                    'backgroundColor' => [HBackend::getRandomHsl()]
                ];
            }
        }

        $config = first_element($optionsParam) ?? [];

        if (!empty($data) && !empty($this->summaryParam)) {

            foreach ($datasets as $data) {
                if (!empty(array_filter($data ?? []))) {
                    $this->showTopicParamsChart = true;
                    break;
                } else {
                    $this->showTopicParamsChart = false;
                }
            }
        }

        $this->topicParamsChart['topicParamsChart'] = [
            'labels' => array_values($labels),
            'type' => getField($config, 'statistics.chart_type', 'bar'),
            'datasets' => empty($datasets) ? $datasets = [['data' => []]] : $datasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
                'scales' => [
                    'x' => HBackend::getScaleOptions('x', __('backend::statistics.summary.' . $this->summaryParam . '.scales'), ['stacked' => true]),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.summary.topics-number.scales'), ['stacked' => true, 'beginAtZero' => true])
                ]
            ]
        ];
        $this->dispatchBrowserEvent('updateCharts', ['charts' => $this->topicParamsChart]);
    }

    //CB Specific
    public function getVotesBalancePositiveNegativeChart() //Obter o gráfico
    {
        $balanceTopics = [];
        $positiveTopics = [];
        $negativeTopics = [];
        $labels = [];
        $datasets = [];
        $topicsIds = [];

        if (!empty($this->cbId)) {
            $this->topics = $this->topics->where('cb_id', $this->cbId);
        }
        //Obter os dados para os datasets
        foreach ($this->topics ?? [] as $topic) {
            $positive = !empty($this->votes) ? $this->votes->where('topic_id', $topic->id)->where('value', 1)->count() : 0;
            $negative = !empty($this->votes) ? $this->votes->where('topic_id', $topic->id)->where('value', -1)->count() : 0;
            $balance = $positive + $negative;
            data_set($balanceTopics, $topic->id, $balance);
            data_set($positiveTopics, $topic->id, $positive);
            data_set($negativeTopics, $topic->id, $negative);
            array_push($labels, getField($topic, 'title.' . getLang(), ''));
            array_push($topicsIds, $topic->id);

        }
        //Datasets
        $datasets[] = [
            'label' => __('backend::statistics.summary.topics-top-balance.balance'),
            'data' => array_values($balanceTopics),
            'backgroundColor' => [HBackend::getRandomHsl()],
            'borderWidth' => 1,
            'borderColor' => HBackend::getRandomHsl(),
        ];
        $datasets[] = [
            'label' => __('backend::statistics.summary.topics-top-positive.balance'),
            'data' => array_values($positiveTopics),
            'backgroundColor' => [HBackend::getRandomHsl()],
            'borderWidth' => 1,
            'borderColor' => HBackend::getRandomHsl(),
        ];
        $datasets[] = [
            'label' => __('backend::statistics.summary.topics-top-negative.balance'),
            'data' => array_values($negativeTopics),
            'backgroundColor' => [HBackend::getRandomHsl()],
            'borderWidth' => 1,
            'borderColor' => HBackend::getRandomHsl(),
        ];

        foreach ($datasets as $dataset) {
            if (empty(array_filter(getField($dataset, 'data', [])))) {
                $this->showTopicVotesChart = false;
            } else {
                $this->showTopicVotesChart = true;
                break;
            }
        }

        $this->summaryChart['votesBalancePositiveNegative'] = [
            'labels' => $labels,
            'type' => 'bar',
            'datasets' => $datasets,
            'options' => [
                'plugins' => [
                    'legend' => 'top'
                ],
                'scales' => [
                    'x' => HBackend::getScaleOptions('x', __('backend::statistics.summary.topics-votes.scales'), [/*'stacked' => true, */ 'ticks' => ['stepSize' => 1]]),
                    'y' => HBackend::getScaleOptions('y', __('backend::statistics.summary.topics-number.scales'), [/*'stacked' => true,*/ 'beginAtZero' => true]),
                ]
            ]
        ];
    }

    //ALL
    public function getStatistics() //Obter os tópicos, votos e cbs antes de os filtrar
    {
        if (empty($this->allTopics))
            $this->allTopics = Topic::all();
        $this->topics = $this->allTopics;
        $this->votes = Vote::all();
        if ($this->type == 'cbs') {
            $this->getStatisticsCbs();
        }
    }

    public function updateCbTypesDates() //Atualizar os dados das estatisticas tendo em conta as datas associadas
    {
        if (!empty($this->startDateCbsTypes)) {
            if (!empty($this->cbs))
                $this->cbs = $this->cbs->where('created_at', '>=', Carbon::parse($this->startDateCbsTypes));
            if (!empty($this->votes))
                $this->votes = $this->votes->where('created_at', '>=', Carbon::parse($this->startDateCbsTypes));
            if (!empty($this->topics))
                $this->topics = $this->topics->where('created_at', '>=', Carbon::parse($this->startDateCbsTypes));
        }else{
            $now = Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss');
            if (!empty($this->cbs))
                $this->cbs = $this->cbs->where('created_at', '<=', Carbon::parse($now));
            if (!empty($this->votes))
                $this->votes = $this->votes->where('created_at', '<=', Carbon::parse($now));
            if (!empty($this->topics))
                $this->topics = $this->topics->where('created_at', '<=', Carbon::parse($now));
        }

        if (!empty($this->endDateCbsTypes)) {
            if (!empty($this->cbs))
                $this->cbs = $this->cbs->where('created_at', '<=', Carbon::parse($this->endDateCbsTypes)->addDay());
            if (!empty($this->votes))
                $this->votes = $this->votes->where('created_at', '<=', Carbon::parse($this->endDateCbsTypes)->addDay());
            if (!empty($this->topics))
                $this->topics = $this->topics->where('created_at', '<=', Carbon::parse($this->endDateCbsTypes)->addDay());
        }
    }

    public function getCbs() //Obter os cbs para o select
    {
        $this->cbsOptions = $this->cbs->pluck('title.' . getLang(), 'id')->toArray();
    }

    public function getTopicsParams() //Obter os tópicos para o select
    {
        if (!empty($this->topics)) {
            $this->topicOptions = $this->topics->pluck('title.' . getLang(), 'id')->toArray();
        }
    }

    public function getPeriod() //Obter as datas dentro de um espaço de tempo
    {
        $period = [];
        $endDate = Carbon::now()->isoFormat('DD-MM-Y');
        $startDate = Carbon::now()->subMonth(getenv('MONTHS'))->isoFormat('DD-MM-Y'); //Obter a start date

        // Use strtotime function to convert the time
        $Variable1 = strtotime($startDate);
        $Variable2 = strtotime($endDate);

        // Use for loop to store dates into array
        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        for ($currentDate = $Variable1; $currentDate <= $Variable2;
             $currentDate += (86400)) {
            $Store = date('Y-m-d', $currentDate);
            $period[] = $Store;
        }
        return $period;
    }
}

