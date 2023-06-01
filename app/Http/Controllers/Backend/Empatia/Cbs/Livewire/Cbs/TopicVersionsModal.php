<?php
namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use Livewire\Component;
use App\Models\Empatia\Cbs\Topic;


class TopicVersionsModal extends Component
{
    public $topicId;
    public $topic;
    public $versions;

    protected $listeners = ['reloadVersions' => 'reload'];

    public function mount() {
        $this->reload();
    }

    public function render()
    {
        return view('empatia::cbs.livewire.topic.topic-versions-modal');
    }

    public function reload() {
        $this->topic = Topic::findOrFail($this->topicId);
        $this->versions = $this->topic->versions;
        $this->versions[] = (object)['user' => $this->topic->updated_by ?? $this->topic->created_by, 'date' => $this->topic->updated_at, 'version' => $this->topic->version];
        $this->versions = array_reverse($this->versions);
    }
}
