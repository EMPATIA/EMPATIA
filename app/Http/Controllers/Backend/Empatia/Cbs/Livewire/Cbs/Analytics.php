<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use Livewire\Component;
use App\Http\Controllers\Backend\BackendController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use App\Models\Empatia\Cbs\Vote;

class Analytics extends Component
{
    const CB_TYPE = 'empaville';

    public $cbId;
    public $cb;

    public $topics = [];
    public $votes;

    public $overallStatistics;
    public $overallCharts;


    public function mount() {
        // check for cb instance
        if( !($this->cb instanceof Cb) || $this->cb->type != self::CB_TYPE ){
            $this->cb = null;
        } else{
            $this->cbId = $this->cb->id;
        }

        // find cb by id
        if( empty($this->cb) && is_numeric($this->cbId) ){
            $this->cb = Cb::findOrFail($this->cbId);
        }

        $this->topics = Topic::where('cb_id', $this->cb->id)->get();
        $this->votes = Vote::where('cb_id', $this->cb->id)->get();


        $this->overallStatistics = BackendController::getOverallStatistics($this->votes, $this->topics, $this->cbId);
        $this->overallCharts = BackendController::getPositiveNegativeVotesChart($this->votes, 'overallStatisticsChart');
        $this->dispatchBrowserEvent('updateTopicCharts', ['charts' => $this->overallCharts]);

    }

    public function render()
    {
        return view('empatia::cbs.livewire.cbs.cb-analytics', ['cbId' => $this->cbId]);
    }
}
