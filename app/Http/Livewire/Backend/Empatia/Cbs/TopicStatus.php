<?php
namespace App\Http\Livewire\Backend\Empatia\Cbs;

use App\Models\Empatia\Cbs\Cb;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Backend\Helpers\Notify;


class TopicStatus extends Component
{
    public $statusConfig = [];

    public $topic;
    public $cb;

    public $stateSelected;
    public $states = [];
    public $history = [];

    protected $listeners = [
        'openModal',
        'getTopicId'
    ];

    public function mount() {
        $this->loadCb();
        $this->loadStatusConfig();
        $this->stateSelected = !empty($this->topic->state) ? getField($this->topic, 'state') : array_key_first($this->states);
        $this->reload();
    }

    public function render() {
        return view('livewire.backend.empatia.cbs.topics.topic-status');
    }

    /**
     * Returns the formatted state history
     * @return $this->history
     */
    public function reload() {
        $this->history = [];
        if(is_array($this->topic->status)){
            foreach (array_reverse($this->topic->status) as $state) {
                $user = User::find($state->created_by ?? $state->updated_by ?? null);
                $this->history[] = [
                    'code' => $state->code,
                    'title' => $this->cb->stateLabel($state->code, getLang()),
                    'created_at' => Carbon::parse($state->created_at ?? $state->updated_at)->isoFormat('Y-MM-DD HH:mm'),
                    'created_by' => $user != null ? $user->name : '--'
                ];
            }
        }
    }

    /**
     * Save topic status
     * @param string $this->stateSelected  The state code
     */
    public function saveState() {
        $this->topic->assignState($this->stateSelected);
        $this->reload();
    }

    /**
     * Get cb
     * *  @param int $this->topic->cb_id  Cb Id
     * @return Cb
     */
    private function loadCb()
    {
        $this->cb = Cb::find($this->topic->cb_id);
    }

    /**
     * Get all status
     * @return array
     */
    private function loadStatusConfig()
    {
        $this->statusConfig = (array)data_get($this->topic->cb, 'data.configurations.topic.status', []);

        foreach ($this->statusConfig as $key => $state) {
            if (!empty($title = data_lang_get($state, 'title')))
                $this->states[$key] = $title;
        }
    }
}
