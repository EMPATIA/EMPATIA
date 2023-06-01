<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use Livewire\Component;
use App\Models\Empatia\Cbs\Cb;



class Configurations extends Component
{
    public $cbId;
    public $cb;

    public $create_in_person;
    public $vote_in_person;


    public function mount() {
        $cb = Cb::whereId($this->cbId)->firstOrFail();
        $this->cb = $cb;
        $this->reload();
    }

    public function render()
    {
        return view('empatia::cbs.livewire.cbs.configurations');
    }

    public function reload(){
        $this->getConfigurations();
    }

    public function getConfigurations(){
        $data = $this->cb->data;

        foreach ($data ?: [] as $k => $d)
        {
            if($k == 'configurations') {
                foreach ($d as $key => $value) {
                    if ($key == 'create_in_person') {
                        $this->create_in_person = $value;
                    }
                    if ($key == 'vote_in_person') {
                        $this->vote_in_person = $value;
                    }
                }
            }
        }
    }

    public function updateConfigurations(){
        $data = $this->cb->data;

        foreach ($data ?: [] as $k => $d)
        {
            if($k == 'configurations') {
                foreach ($d as $key => $value) {
                    if ($key == 'create_in_person') {
                        $d->$key = $this->create_in_person;
                    }
                    if ($key == 'vote_in_person') {
                        $d->$key = $this->vote_in_person;
                    }
                }
            }
        }


        $this->cb->data = (object)$data;
        $this->cb->update();
        $this->reload();
    }
}
