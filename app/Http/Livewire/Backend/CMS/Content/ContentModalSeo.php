<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use Livewire\Component;

class ContentModalSeo extends Component
{
    public $contentId;
    public $contentType;
    public $content;
    public $seo;

    protected $listeners = ['loadModal'];

    protected $rules = [
        'seo.*.*' => 'string',
    ];

    public function mount() {
    }

    public function render()
    {
        return view('livewire.backend.cms.content.content-modal-seo');
    }

    public function loadModal() {
        $this->reload();
        $this->emit('toggleModalSeo');
    }

    public function reload() {
        $content = \Session::get('content_draft_'.$this->contentId);
        $this->content = $content;
        $this->contentType = $content->type;
        $this->seo = (array)($content->seo ?? []);
    }

    public function updated($name, $value) {
        $this->validateOnly($name);

        try {
            $content = \Session::get('content_draft_' . $this->contentId);

            $content->fill([str_replace('.', '->', $name) => $value]);

            $this->content = $content;

            \Session::put('content_draft_' . $this->contentId, $content);

            $this->reload();

            $this->emitTo('livewire.backend.c-m-s.content.content-manager', 'reload');
            $this->emit('showEdited');
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }
    }
}
