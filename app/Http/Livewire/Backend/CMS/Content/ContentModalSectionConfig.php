<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use Livewire\Component;
use App\Traits\CMSContentWithSections;

class ContentModalSectionConfig extends Component
{
    use CMSContentWithSections;

    public $contentId;
    public $position;
    public $code, $class, $options, $name;
    public $sectionName;
    public $type;

    protected $listeners = ['loadModal'];

    protected $rules = [
        'code' => 'string',
        'class' => 'string',
        'name.*' => 'string',
        'options' => 'string',
    ];

    public function mount() {
    }

    public function render()
    {
        return view('livewire.backend.cms.content.content-modal-section-config');
    }

    public function loadModal($position) {
        $this->reload($position);
        $this->emit('toggleModalSectionConfig');
    }

    public function reload($position) {
        $this->position = $position;

        $content = $this->getContent();

        $this->code = data_get($content, 'sections.'.$position.'.code', '');
        $this->class = (array)data_get($content, 'sections.'.$position.'.class', '');
        $this->name = (array)data_get($content, 'sections.'.$position.'.name', '');
        $this->sectionName = data_get($content, 'sections.'.$position.'.name.'.getLang(), '');
        $this->type = data_get($content, 'sections.'.$position.'.type', '');

        // Handle better the options
        $this->options = data_get($content, 'sections.'.$position.'.options', []);
    }

    public function updated($name, $value) {
        // Validate field
        $this->validateOnly($name);

        try {
            // Get sections
            $sections = $this->getSections();

            // Update field
            data_set($sections[$this->position], $name, $value);

            // Update sections
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }
}
