<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use App\Models\User;
use App\Models\Empatia\Cbs\Cb;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CbPhase extends Component
{

    public $cbId;
    public $cb;

    //Flags propertys
    public $canStartTopicCreation = true;
    public $canStopTopicCreation = true;
    public $isTopicCreationClosed = false;

    public $isVotePhaseClosed = false;
    public $startedResultPhase= false;

    //CB State
    public $cbAction = null;

    //Topic phase
    public $topicStartDate;
    public $topicEndDate;
    public $topicAction = null;


    //Show topics phase
    public $topicShowStartDate;
    public $topicShowEndDate;
    public $topicShowAction = null;

    //Vote phase
    public $voteStartDate;
    public $voteEndDate;
    public $voteAction = null;

    //Results phase
    public $resultStartDate;
    public $resultEndDate;
    public $resultAction = null;

    //Modal
    public $topic_start_date; //Modal form start date
    public $topic_end_date; //Modal form end date
    public $voting_start_date; //Modal form start date
    public $voting_end_date; //Modal form end date
    public $results_start_date;
    public $results_end_date;

    //Warning propertys
    public $warning_message;


    private $prefix = "cbs::cbs.";

    protected $listeners = ['topicPhase', 'votePhase', 'editDates','topicShow', 'resultPhase', 'cbStates'];


    public function mount()
    {
        $this->cb = Cb::findOrFail($this->cbId);
        $this->reload();
    }

    public function reload()
    {
        $this->updateFlags();
        $this->getAction();
        $this->cbAction();
    }

    public function render()
    {
        $this->updateFlags();
        $this->getAction();
        return view('empatia::cbs.livewire.cbs.cb-phases');
    }

    public function updateFlags()
    {
        $now = Carbon::now();
        $data = getField($this->cb, 'data', []);
        if (getField($data, 'configurations')) {
            if ($topic = getField($data->configurations, 'topic')) {
                if(isset($topic->create)){
                    if ($this->canStartTopicCreation && Carbon::parse(getField($topic, 'create.end_date')) <= $now && getField($topic, 'create.end_date') != null) {
                        $this->isTopicCreationClosed = true;
                    }
                    if(getField($topic, 'create.end_date') == null){
                            $this->isTopicCreationClosed = false;
                            $this->canStartTopicCreation = true;
                    }
                }
                if(isset($topic->show)){
                    if (getField($topic, 'create.start_date') != null) {
                        $this->topicShowAction = 'start';
                    }
                }

                if(!empty(getField($topic, 'create.start_date')) != null && $this->isTopicCreationClosed){
                    $this->isTopicCreationClosed = true;
                    $this->canStopTopicCreation = true;
                    $this->canStartTopicCreation = true;
                }
            }
            if ($vote = getField($data->configurations, 'vote')) {
                if (!empty(getField($vote, 'vote.start_date')) && Carbon::parse(getField($vote, 'vote.start_date')) <= $now && $this->isTopicCreationClosed) {
                    $this->isTopicCreationClosed = true;
                    $this->canStopTopicCreation = false;
                    $this->canStartTopicCreation = false;
                }


                if ($this->isTopicCreationClosed && Carbon::parse(getField($vote, 'vote.end_date')) <= $now && getField($vote, 'vote.end_date') != null) {
                    $this->isVotePhaseClosed = true;
                }else{
                    $this->isVotePhaseClosed = false;
                }

//                if(empty(getField($vote, 'vote.start_date')) && $this->isTopicCreationClosed){
//                    $this->isTopicCreationClosed = false;
//                }
                if (!empty(getField($vote, 'results.start_date')) && Carbon::parse(getField($vote, 'results.start_date')) <= $now && $this->isVotePhaseClosed) {
                    $this->startedResultPhase = true;
                }else{
                    $this->startedResultPhase = false;
                }
            }
        }
    }

    //Know what the action is, to do the translation
    public function getAction()
    {
        $now = Carbon::now();
        $data = getField($this->cb, 'data', []);
        if (getField($data, 'configurations')) {
            if ($topic = getField($data->configurations, 'topic')) {
                if(isset($topic->create)){
                    if (getField($topic, 'create.start_date') != null && getField($topic, 'create.end_date') != null) {
                        if(Carbon::parse(getField($topic, 'create.start_date')) < $now  && Carbon::parse(getField($topic, 'create.end_date')) <= $now){
                            $this->topicAction = 'resume';
                        }elseif(Carbon::parse(getField($topic, 'create.start_date')) < $now  && Carbon::parse(getField($topic, 'create.end_date')) > $now){
                            $this->topicAction = 'stop';
                        }elseif(Carbon::parse(getField($topic, 'create.start_date')) > $now){
                            $this->topicAction = 'start';
                        }
                        $this->topicEndDate =  Carbon::parse(getField($topic->create, 'end_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->topicStartDate = Carbon::parse(getField($topic->create, 'start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->topic_start_date = Carbon::parse($this->topicStartDate)->format('Y-m-d\TH:i');
                        $this->topic_end_date = Carbon::parse($this->topicEndDate)->format('Y-m-d\TH:i');
                    }elseif(getField($topic, 'create.start_date') != null && getField($topic, 'create.end_date') == null){
                        if(Carbon::parse(getField($topic, 'create.start_date')) > $now){
                            $this->topicAction = 'start';
                        }else{
                            $this->topicAction = 'stop';
                        }
                        $this->topicStartDate = Carbon::parse(getField($topic, 'create.start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->topicEndDate = null;
                        $this->topic_start_date = Carbon::parse($this->topicStartDate)->format('Y-m-d\TH:i');
                        $this->topic_end_date = null;
                    }elseif(getField($topic, 'create.start_date') == null && getField($topic, 'create.end_date') == null){
                        $this->topicAction = 'start';
                        $this->topicStartDate  = null;
                        $this->topicEndDate = null;
                    }
                }
                if (isset($topic->show)) {
                    if (getField($topic, 'show.start_date') != null && getField($topic, 'show.end_date') != null) {
                        if (Carbon::parse(getField($topic, 'show.start_date')) < $now && Carbon::parse(getField($topic, 'show.end_date')) <= $now) {
                            $this->topicShowAction = 'resume';
                        } elseif (Carbon::parse(getField($topic, 'show.start_date')) < $now && Carbon::parse(getField($topic, 'show.end_date')) > $now) {
                            $this->topicShowAction = 'stop';
                        } elseif (Carbon::parse(getField($topic, 'show.start_date')) > $now) {
                            $this->topicShowAction = 'start';
                        }
                        $this->topicShowEndDate = Carbon::parse(getField($topic->show, 'end_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->topicShowStartDate = Carbon::parse(getField($topic->show, 'start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                    } elseif (getField($topic, 'show.start_date') != null && getField($topic, 'show.end_date') == null) {
                        if (Carbon::parse(getField($topic, 'show.start_date')) > $now) {
                            $this->topicShowAction = 'start';
                        } else {
                            $this->topicShowAction = 'stop';
                        }
                        $this->topicShowStartDate = Carbon::parse(getField($topic, 'show.start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->topicShowEndDate = null;
                    } elseif (getField($topic, 'show.start_date') == null && getField($topic, 'show.end_date') == null) {
                        $this->topicShowAction = 'start';
                        $this->topicShowStartDate = null;
                        $this->topicShowEndDate = null;
                    }
                }
            }
            if ($vote = getField($data->configurations, 'vote')) {
                if (isset($vote->vote)) {
                    if (getField($vote, 'vote.start_date') != null && getField($vote, 'vote.end_date') != null) {
                        if(Carbon::parse(getField($vote, 'vote.start_date')) < $now  && Carbon::parse(getField($vote, 'vote.end_date')) <= $now){
                            $this->voteAction = 'resume';
                        }elseif(Carbon::parse(getField($vote, 'vote.start_date')) < $now  && Carbon::parse(getField($vote, 'vote.end_date')) > $now){
                            $this->voteAction = 'stop';
                        }elseif(Carbon::parse(getField($vote, 'vote.start_date')) > $now){
                            $this->voteAction = 'start';
                        }
                        $this->voteEndDate =  Carbon::parse(getField($vote->vote, 'end_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->voteStartDate = Carbon::parse(getField($vote->vote, 'start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->voting_start_date = Carbon::parse($this->voteStartDate)->format('Y-m-d\TH:i');
                        $this->voting_end_date = Carbon::parse($this->voteEndDate)->format('Y-m-d\TH:i');
                    }elseif(getField($vote, 'vote.start_date') != null && getField($vote, 'vote.end_date') == null){
                        if(Carbon::parse(getField($vote, 'vote.start_date')) > $now){
                            $this->voteAction = 'start';
                        }else{
                            $this->voteAction = 'stop';
                        }
                        $this->voteStartDate = Carbon::parse(getField($vote, 'vote.start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->voteEndDate = null;
                        $this->voting_start_date = Carbon::parse($this->voteStartDate)->format('Y-m-d\TH:i');
                        $this->voting_end_date = null;
                    }elseif(getField($vote, 'vote.start_date') == null && getField($vote, 'vote.end_date') == null){
                        $this->voteAction = 'start';
                        $this->voteStartDate  = null;
                        $this->voteEndDate = null;
                    }
                }
                if (isset($vote->results)) {
                    if (getField($vote, 'results.start_date') != null && getField($vote, 'results.end_date') != null) {
                        if(Carbon::parse(getField($vote, 'results.start_date')) < $now  && Carbon::parse(getField($vote, 'results.end_date')) <= $now){
                            $this->resultAction = 'resume';
                        }elseif(Carbon::parse(getField($vote, 'results.start_date')) < $now  && Carbon::parse(getField($vote, 'results.end_date')) > $now){
                            $this->resultAction = 'stop';
                        }elseif(Carbon::parse(getField($vote, 'results.start_date')) > $now){
                            $this->resultAction = 'start';
                        }
                        $this->resultEndDate =  Carbon::parse(getField($vote, 'results.end_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->resultStartDate = Carbon::parse(getField($vote, 'results.start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->results_start_date = Carbon::parse($this->resultStartDate)->format('Y-m-d\TH:i');
                        $this->results_end_date = Carbon::parse($this->resultEndDate)->format('Y-m-d\TH:i');
                    }elseif(getField($vote, 'results.start_date') != null && getField($vote, 'results.end_date') == null){
                        if(Carbon::parse(getField($vote, 'results.start_date')) > $now){
                            $this->resultAction = 'start';
                        }else{
                            $this->resultAction = 'stop';
                        }
                        $this->resultStartDate = Carbon::parse(getField($vote, 'results.start_date'))->isoFormat('Y-MM-DD HH:mm:ss');
                        $this->resultEndDate = null;
                        $this->results_start_date = Carbon::parse($this->resultStartDate)->format('Y-m-d\TH:i');
                        $this->results_end_date = null;
                    }elseif(getField($vote, 'results.start_date') == null && getField($vote, 'results.end_date') == null){
                        $this->resultAction = 'start';
                        $this->resultStartDate  = null;
                        $this->resultEndDate = null;
                    }
                }
            }
        }
    }

    public
    function topicPhase(bool $state)
    {
        try {
            DB::beginTransaction();

            $data = getField($this->cb, 'data', []);
            if (getField($data, 'configurations')) {
                if ($topic = getField($data->configurations, 'topic')) {
                    if (isset($topic->create)) {
                        if ($state) {
                            $start_date = data_set($topic->create, 'start_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            if (!empty(getField($topic->create, 'end_date'))) {
                                $start_date = data_set($topic->create, 'end_date', null);
                            }
                            $data->configurations->topic->create = $start_date;

                        } else {
                            $end_date = data_set($topic->create, 'end_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            $data->configurations->topic->create = $end_date;
                        }
                        $this->topicStartDate = getField($topic->create, 'start_date');
                        $this->topicEndDate = getField($topic->create, 'end_date');
                    }
                }
            }
            $this->cb->data = $data;

            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
            }
            $this->reload();
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }

    public
    function votePhase(bool $state)
    {
        try {
            DB::beginTransaction();
            $data = getField($this->cb, 'data', []);
            if (getField($data, 'configurations')) {
                if ($vote = getField($data->configurations, 'vote')) {
                    if ($state) {
                        $start_date = data_set($vote->vote, 'start_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                        if (!empty(getField($vote, 'vote.end_date'))) {
                            $start_date = data_set($vote->vote, 'end_date', null);
                        }
                        $data->configurations->vote->vote = $start_date;
                    } else {
                        $end_date = data_set($vote->vote, 'end_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                        $data->configurations->vote->vote = $end_date;
                    }
                    $this->voteStartDate = getField($vote, 'vote.start_date');
                    $this->voteEndDate = getField($vote, 'vote.end_date');
                }
            }
            $this->cb->data = $data;

            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
            }
            $this->reload();
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }

    }

    public
    function editDates()
    {
        try {
            DB::beginTransaction();
            $canShowWaringModal = true;
            $data = getField($this->cb, 'data', []);
            if (getField($data, 'configurations')) {
                if ($this->canStartTopicCreation || Auth::user()->hasRole(['admin'])) {
                    if ($topic = getField($data->configurations, 'topic')) {
                        if (isset($topic->create)) {
                            if (!empty($this->topic_start_date) && !empty($this->topic_end_date)) {
                                $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->topic_start_date);
                                $this->topic_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                                $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->topic_end_date);
                                $this->topic_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                            }elseif(!empty($this->topic_start_date) && empty($this->topic_end_date)){
                                $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->topic_start_date);
                                $this->topic_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                                $this->topic_end_date = null;
                            }elseif(empty($this->topic_start_date) && !empty($this->topic_end_date)){
                                $this->topic_start_date = null;
                                $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->topic_end_date);
                                $this->topic_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                            }else{
                                $this->topic_start_date = null;
                                $this->topic_end_date = null;
                            }
                            if($this->topic_start_date > $this->topic_end_date && !empty($this->topic_end_date)){
                                $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.start_date');
                                $this->dispatchBrowserEvent('showWarning');
                            }

                            if($this->topic_end_date < $this->topic_start_date && !empty($this->topic_end_date)){
                                $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.end_date');
                                $this->dispatchBrowserEvent('showWarning');
                            }

                            if(!empty($topic->create->start_date) && $this->topic_start_date > $topic->create->start_date){
                                $canShowWaringModal = false;
                                $this->warning_message = __('cbs::cbs.cb.phase.warning.description');
                                $this->dispatchBrowserEvent('showWarning');
                            }

                            $topic_data = data_set($topic->create, 'start_date', $this->topic_start_date);
                            $topic_data = data_set($topic->create, 'end_date', $this->topic_end_date);
                            $data->configurations->topic->create = $topic_data;
                        }
                    }
                }
                if ($vote = getField($data->configurations, 'vote')) {
                    if (isset($vote->vote)) {
                        if (!empty($this->voting_start_date) && !empty($this->voting_end_date)) {
                            $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->voting_start_date);
                            $this->voting_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                            $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->voting_end_date);
                            $this->voting_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                        }elseif(!empty($this->voting_start_date) && empty($this->voting_end_date)){
                            $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->voting_start_date);
                            $this->voting_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                            $this->voting_end_date = null;
                        }elseif(empty($this->voting_start_date) && !empty($this->voting_end_date)){
                            $this->voting_start_date = null;
                            $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->voting_end_date);
                            $this->voting_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                        }else{
                            $this->voting_start_date = null;
                            $this->voting_end_date = null;
                        }
                        if($this->voting_start_date > $this->voting_end_date && !empty($this->voting_end_date)){
                            $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.start_date');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        if($this->voting_end_date < $this->voting_start_date && !empty($this->voting_end_date)){
                            $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.end_date');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        if(!empty($vote->vote->start_date) && $this->voting_start_date > $vote->vote->start_date){
                            $canShowWaringModal = false;
                            $this->warning_message = __('cbs::cbs.cb.phase.warning.description');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        $vote_data = data_set($vote->vote, 'start_date', $this->voting_start_date);
                        $vote_data = data_set($vote->vote, 'end_date', $this->voting_end_date);
                        $data->configurations->vote->vote = $vote_data;
                    }
                    if (isset($vote->results)) {
                        if (!empty($this->results_start_date) && !empty($this->results_end_date)) {
                            $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->results_start_date);
                            $this->results_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                            $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->results_end_date);
                            $this->results_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                        }elseif(!empty($this->results_start_date) && empty($this->results_end_date)){
                            $time = Carbon::createFromFormat('Y-m-d\TH:i', $this->results_start_date);
                            $this->results_start_date = $time->isoFormat('Y-MM-DD HH:mm:ss');
                            $this->results_end_date = null;
                        }elseif(empty($this->results_start_date) && !empty($this->results_end_date)){
                            $this->results_start_date = null;
                            $time_end = Carbon::createFromFormat('Y-m-d\TH:i', $this->results_end_date);
                            $this->results_end_date = $time_end->isoFormat('Y-MM-DD HH:mm:ss');
                        }else{
                            $this->results_start_date = null;
                            $this->results_end_date = null;
                        }
                        if($this->results_start_date > $this->results_end_date && !empty($this->results_end_date)){
                            $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.start_date');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        if($this->results_end_date < $this->results_start_date && !empty($this->results_end_date)){
                            $this->warning_message = __('cbs::cbs.cb.phase.topic.warning.end_date');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        if(!empty($vote->results->start_date) && $this->results_start_date > $vote->results->start_date){
                            $canShowWaringModal = false;
                            $this->warning_message = __('cbs::cbs.cb.phase.warning.description');
                            $this->dispatchBrowserEvent('showWarning');
                        }

                        $results_data = data_set($vote->results, 'start_date', $this->results_start_date);
                        $results_data = data_set($vote->results, 'end_date', $this->results_end_date);
                        $data->configurations->vote->results = $results_data;
                    }
                }
            }
            $this->cb->data = $data;
            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
                $this->reload();
            }

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }

    public
    function topicShow(bool $state)
    {
        try {
            DB::beginTransaction();

            $data = getField($this->cb, 'data', []);
            if (getField($data, 'configurations')) {
                if ($topic = getField($data->configurations, 'topic')) {
                    if (isset($topic->show)) {
                        if ($state) {
                            $start_date = data_set($topic->show, 'start_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            if (!empty(getField($topic->show, 'end_date'))) {
                                $start_date = data_set($topic->show, 'end_date', null);
                            }
                            $data->configurations->topic->show = $start_date;

                        } else {
                            $end_date = data_set($topic->show, 'end_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            $data->configurations->topic->show = $end_date;
                        }
                        $this->topicShowStartDate = getField($topic->show, 'start_date');
                        $this->topicShowEndDate = getField($topic->show, 'end_date');
                    }
                }
            }
            $this->cb->data = $data;

            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
            }
            $this->reload();
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }

    }

    public function resultPhase(bool $state){
        try {
            DB::beginTransaction();
            $data = getField($this->cb, 'data', []);
            if (getField($data, 'configurations')) {
                if ($vote = getField($data->configurations, 'vote')) {
                    if(isset($vote->results)){
                        if ($state) {
                            $start_date = data_set($vote->results, 'start_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            if (!empty(getField($vote->results, 'end_date'))) {
                                $start_date = data_set($vote->results, 'end_date', null);
                            }
                            $data->configurations->vote->results = $start_date;
                        } else {
                            $end_date = data_set($vote->results, 'end_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss'));
                            $data->configurations->vote->results = $end_date;
                        }
                        $this->resultStartDate = getField($vote->results, 'start_date');
                        $this->resultEndDate = getField($vote->results, 'end_date');
                    }
                }
            }
            $this->cb->data = $data;

            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
            }
            $this->reload();
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }

    private function cbAction()
    {
        $now = Carbon::now();
        if (isset($this->cb)) {
            if (getField($this->cb, 'start_date') != null && getField($this->cb, 'end_date') != null) {
                if (Carbon::parse(getField($this->cb, 'start_date')) < $now && Carbon::parse(getField($this->cb, 'end_date')) <= $now) {
                    $this->cbAction = 'resume-game';
                } elseif (Carbon::parse(getField($this->cb, 'start_date')) < $now && Carbon::parse(getField($this->cb, 'end_date')) > $now) {
                    $this->cbAction = 'stop-game';
                } elseif (Carbon::parse(getField($this->cb, 'start_date')) > $now) {
                    $this->cbAction = 'start-game';
                }
            } elseif (getField($this->cb, 'start_date') != null && getField($this->cb, 'end_date') == null) {
                if (Carbon::parse(getField($this->cb, 'start_date')) > $now) {
                    $this->cbAction = 'start-game';
                } else {
                    $this->cbAction = 'stop-game';
                }
            } elseif (getField($this->cb, 'start_date') == null && getField($this->cb, 'end_date') == null) {
                $this->cbAction = 'start-game';
            }
        }
    }

    public function cbStates(bool $state){
        try {
            DB::beginTransaction();

            if (isset($this->cb)) {
                if ($state) {
                    $start_date = data_set($this->cb, 'start_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm'));
                    if ($this->cbAction == 'resume-game') {
                       data_set($this->cb, 'end_date', null);
                    }
                } else {
                    data_set($this->cb, 'end_date', Carbon::now()->isoFormat('Y-MM-DD HH:mm'));
                }
            }

            if ($this->cb->save()) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                logDebug('updated cb ' . $this->cb->id);
            }
            $this->dispatchBrowserEvent('refreshCb');
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }
}
