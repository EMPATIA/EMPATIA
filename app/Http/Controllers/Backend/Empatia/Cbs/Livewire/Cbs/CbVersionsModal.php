<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Empatia\Cbs\Cb;


class CbVersionsModal extends Component
{
    public $cbId;
    public $cb;
    public $versions;

    protected $listeners = [
        'versionsUpdate'
    ];

    public function mount() {
        $this->cb = Cb::findOrFail($this->cbId);
        \Session::put($this->cbId. 'cb', $this->cb);
        $this->versions = array_reverse((array)$this->cb->versions);
        foreach ($this->versions as $version) {
            $version->date = Carbon::parse($version->date)->format('Y-m-d');
            $version->user = User::find($version->user)->name;
        }
    }

    public function hydrate() {
        $this->cb = \Session::get($this->cbId.'cb');
    }
    public function render()
    {
        return view('empatia::cbs.livewire.cbs.cb-versions-modal');
    }

    public function makeActive($v) {
        $versions = (array)$this->cb->versions;
        $version = $versions[$v];
        Cb::findOrFail($this->cb->id)->update([
            'version' => $v,
            'title' => $version->title,
            'template' => $version->template,
            'code' => $version->code,
            'start_date' => $version->start_date,
            'end_date' => $version->end_date,
            'content' => $version->content,
            'slug' => $version->slug,
            'parameters' => $version->parameters,
            'data' => $version->data,
            'votes' => $version->votes,
        ]);
        return redirect()->to((new App\Helpers\HForm)->getActionShow($this->cb->id, '\App\Http\Controllers\Backend\Empatia\Cbs\CbsController'));
    }

    public function versionsUpdate() {
        $this->mount();
    }
}
