<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use App\Helpers\HBackend;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Models\Empatia\Cbs\Cb;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;

class Events extends Component
{
    public $cbId;
    public $vote;
    public $title;
    public $code;
    public $max_votes;
    public $vote_place;
    public $vote_place_options = [];
    public $start_date;
    public $end_date;
    public $votes;
    public $isShow = false;
    public $isCreate = false;
    public $isEdit = false;
    public $showActive = true;
    public $index = -1;

    private $cb;

    protected $listeners = [
        'startVoting',
        'deleteVote' => 'delete',
        'restore'
    ];

    protected $rules = [
        'title' => 'required|string|min:3|max:50',
        'code' => 'required|string|min:3|max:15',
        'max_votes' => 'required|numeric|min:1',
        'start_date' => 'date',
        'end_date' => 'date|after:start_date'
    ];

    public function mount() {
        $configurations = (array)HBackend::getConfigurationByCode('user_parameters');
        if (isset($configurations->parish)) {
            foreach ($configurations->parish->options as $option) {
                $this->vote_place_options[$option->value] = $option->label->{getLang()};
            }
        }
        $this->reload();
//        foreach ($this->votes as $key => $vote) {
//            if ($vote['deleted_at'] == null) {
//                $this->vote = $vote;
//                $this->index = $key;
//                break;
//            }
//        }
    }

    public function reload() {
        $showActive = $this->showActive;
        $this->votes = collect(Cb::find($this->cbId)->votes)->map(function ($item) use ($showActive) {
            return $showActive && $item['deleted_at'] == null || !$showActive && $item['deleted_at'] != null ? $item : null;
        })->filter();
    }

    public function render()
    {
        return view('empatia::cbs.livewire.cbs.events');
    }

    public function index() {
        $this->isShow = false;
        $this->isEdit = false;
        $this->isCreate = false;
    }

    public function show($id) {
        $this->isCreate = false;
        $this->isEdit = false;
        $this->isShow = true;
        $this->vote = $this->votes[$id];
        $this->index = $id;
    }

    public function create() {
        $this->isShow = false;
        $this->isEdit = false;
        $this->isCreate = true;
        $this->title = $this->code = $this->max_votes = $this->start_date = $this->end_date = null;
    }

    public function store() {
        $this->validate($this->rules);
        CbsController::saveNewVersion($this->cbId);
        $this->start_date = date('d-m-Y H:i', strtotime($this->start_date));
        $this->end_date = date('d-m-Y H:i', strtotime($this->end_date));
        $newVote = count($this->votes);
        $this->votes[$newVote] = [
            'id' => $newVote + 1,
            'title' => $this->title,
            'code' => $this->code,
            'start_date' => date('d-m-Y H:i', strtotime($this->start_date)),
            'end_date' => date('d-m-Y H:i', strtotime($this->end_date)),
            'configurations' => [
                'max_votes' => $this->max_votes,
                'vote_place' => $this->vote_place
            ],
            'created_by' => auth()->user()->id,
            'created_at' => date('d-m-Y H:i'),
            'updated_by' => null,
            'updated_at' => null,
            'deleted_by' => null,
            'deleted_at' => null
        ];
        Cb::find($this->cbId)
            ->update([
                'votes' => $this->votes
            ]);
        $this->reload();
        $this->index();
    }

    public function edit($index)
    {
        $this->isCreate = false;
        $this->isShow = false;
        $this->isEdit = true;
        $this->vote = $this->votes[$index];
        $this->title = $this->vote['title'];
        $this->code = $this->vote['code'];
        $this->start_date = date('Y-m-d\TH:i:s', strtotime($this->vote['start_date']));
        $this->end_date = date('Y-m-d\TH:i:s', strtotime($this->vote['end_date']));
        $this->max_votes = $this->vote['configurations']['max_votes'] ?? null;
        $this->vote_place = $this->vote['configurations']['vote_place'] ?? null;
        $this->index = $index;
    }

    public function update($id) {
        try {
            $this->validate($this->rules);
            CbsController::saveNewVersion($this->cbId);
            $this->votes[$id] = [
                'id' => $this->votes[$id]['id'],
                'title' => $this->title,
                'code' => $this->code,
                'start_date' => date('d-m-Y H:i', strtotime($this->start_date)),
                'end_date' => date('d-m-Y H:i', strtotime($this->end_date)),
                'configurations' => [
                    'max_votes' => $this->max_votes,
                    'vote_place' => $this->vote_place,
                ],
                'created_at' => $this->votes[$id]['created_at'],
                'created_by' => $this->votes[$id]['created_by'],
                'updated_by' => auth()->user()->id,
                'updated_at' => date('d-m-Y H:i'),
                'deleted_by' => null,
                'deleted_at' => null,
            ];

            Cb::find($this->cbId)->update([
                'votes' => $this->votes
            ]);
            $this->reload();
            $this->show($id);
        }
        catch (\Exception $e) {
            dd($e);
        }
    }

    public function delete($id) {
        CbsController::saveNewVersion($this->cbId);
        foreach ($this->votes as $key => $vote) {
            if ($key == $id) {
                $this->votes[$key]['deleted_at'] = date('d-m-Y H:i');
                $this->votes[$key]['deleted_by'] = auth()->user()->id;
            }
        }
        $this->cb = Cb::find($this->cbId);
        $this->cb->votes = $this->votes;
        $this->cb->save();
        $this->reload();
        $this->isShow = false;
    }

    public function restore($id) {
        foreach ($this->votes as $key => $vote) {
            if ($vote['id'] == $id) {
                $this->votes[$key]['deleted_at'] = null;
                $this->votes[$key]['deleted_by'] = null;
            }
        }
        $this->cb = Cb::find($this->cbId);
        $this->cb->votes = $this->votes;
        $this->emit('saveDetails', $this->cb);
        $this->reload();
    }

    public function startVoting(Request $request) {
        if ($request->vote_place != null)
            $this->vote_place = $request->vote_place;
        if ($this->vote_place == null) {
            $this->dispatchBrowserEvent('vote-place-required', ['title' => __('cbs::events.modal.error.title'), 'message' => __('cbs::events.modal.message.vote_place.required')]);
        }
        else {
            $this->cb = $request->vote_place == null ? Cb::findOrFail($this->cbId) : Cb::where('slug->' . getLang(), 'maia')->first();
            Cookie::queue('voting', $this->vote_place, env('COOKIE_EXPIRATION_DAYS', 2592000));  // Cookie for 30 days

            if ($request == null)
                Auth::logout();
            else {
                foreach ($this->cb->votes as $vote) {
                    if ($vote['deleted_at'] == null) {
                        $this->vote = $vote;
                        break;
                    }
                }
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('vote-in-person', ['slug' => $this->cb->slug->{getLang()}, 'eventId' => $this->vote['id']]);
        }
    }

    public function updatedShowActive() {
        $this->reload();
    }
}
