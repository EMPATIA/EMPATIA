<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use Livewire\Component;

class ContentModalDetails extends Component
{
    public $contentId;
    public $contentType;
    public $code, $title, $slug, $tags, $fields;

    protected $listeners = ['loadModal'];

    protected $rules = [
        'code' => 'string|min:2|max:255',
        'title.*' => 'required|string|min:2|max:255',
        'slug.*' => 'required|string|min:2|max:255',
        'tags' => 'string',
        'fields.*' => 'string',
    ];

    public function mount() {
    }

    public function render()
    {
        return view('livewire.backend.cms.content.content-modal-details');
    }

    public function loadModal() {
        $this->reload();
        $this->emit('toggleModalDetails');
    }

    public function reload() {
        $content = \Session::get('content_draft_'.$this->contentId);
        $this->contentType = $content->type;
        $this->code = $content->code;
        $this->title = (array)($content->title ?? []);
        $this->slug = (array)($content->slug ?? []);
        $this->tags = $content->tags;
        $this->fields = (array)($content->options->fields ?? []);
    }

    public function updated($name, $value) {
        $this->validateOnly($name);

        try {
            $content = \Session::get('content_draft_' . $this->contentId);

            if (str_starts_with($name, 'fields.')) {
                $content->fill(['options->' . str_replace('.', '->', $name) => $value]);
            } else {
                $content->fill([str_replace('.', '->', $name) => $value]);
            }

            \Session::put('content_draft_' . $this->contentId, $content);

            $this->emitTo('livewire.backend.c-m-s.content.content-manager', 'reload');
            $this->emit('showEdited');
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }
    }
}
