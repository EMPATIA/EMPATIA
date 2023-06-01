<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use Livewire\Component;

class ContentModalVersions extends Component
{
    public $contentId;
    public $contentType;
    public $content;

    protected $listeners = ['loadModal'];

    public function mount() {
    }

    public function render()
    {
        return view('livewire.backend.cms.content.content-modal-versions');
    }

    public function loadModal() {
        $this->reload();
        $this->emit('toggleModalVersions');
    }

    public function reload() {
        $this->content = \Session::get('content_draft_'.$this->contentId);
    }
}
