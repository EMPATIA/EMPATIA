<?php
namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use App\Models\Empatia\Cbs\Topic;
use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController;



class TopicManager extends Component
{

    public $topicId;
    public $topics;
    public $topic;
    public $cbId;
    public $lang;
    public $topicParameters;
    public $isDirty = false;


    protected $listeners = ['updateTopic', 'topicDelete', 'reload', 'changeToVersion', 'changeToState'];

    public function mount() {
        $this->reload();
        if((new App\Helpers\HForm)->isEdit() && $this->topic == null){
            logError("No topic found on session");
            abort(404);
        }
        $this->updateStatus();
        $this->lang = getLang();

    }

    public function render()
    {
        return view('empatia::cbs.livewire.topic-manager');
    }

    public function reload() {
        $this->topic = \Session::get('topic_draft_'.$this->topicId);

    }

    private function update() {
        if($this->topic->isDirty()){
            $this->dispatchBrowserEvent('showEdited');
        }
        else
            $this->dispatchBrowserEvent('hideEdited');

        \Session::put('topic_draft_' . $this->topicId, $this->topic);
    }

    private function updateStatus() {
        if(!empty($this->topic->deleted_at))
            $this->emit('setTopicDeleted');
        else
            $this->emit('setTopicRestored');

    }

    public function updateTopic() {
        try {

            $this->reload();

            //Save status
            DB::beginTransaction();

            // Update
            Topic::findOrFail($this->topic->id)->update([
                'title' => $this->topic->title,
                'slug' => $this->topic->slug,
                'content' => $this->topic->content,
                'parameters' => $this->topic->parameters,
                'status' => $this->topic->status,
                'state' => $this->topic->state,
                'proponents' => $this->topic->proponents,
                'data' => $this->topic->data
            ]);

            DB::commit();
            $this->emit('reloadVersions');
            $this->isDirty = false;
//            session()->flash('success', __('topics::show.save.success'));
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('fail', __('cms::content.save.fail.no_update'));
        }
    }


    public function changeLanguage($lang) {
        $this->lang = $lang;
        $this->reload();
    }


    public function changeToVersion($ver) {
        $newVersionTopic = clone $this->topic;

        foreach ($this->topic->versions as $version) {
            if ($version->version == $ver) {
                $newVersionTopic->version = $version->version;
                $newVersionTopic->title = $version->title;
                $newVersionTopic->slug = $version->slug;
                $newVersionTopic->content = $version->content;
                $newVersionTopic->parameters = $version->parameters;
                $newVersionTopic->status = $version->status;
                $newVersionTopic->state = $version->state;
                $newVersionTopic->proponents = $version->proponents;
                $newVersionTopic->data = $version->data;
                \Session::put('topic_draft_' . $this->topicId, $newVersionTopic);
                $this->isDirty = !$this->isDirty;
            }
        }

        // Update parameters
        $this->reload();

//        // Enable new version
        $this->update();
//
//        session()->flash('success', __('cbs::topics.show.version.changed'));
    }

    public function changeToState($st) {
        // Update parameters
        $this->reload();

        $this->topic->state = $st;

        // Enable new state
        $this->update();

        session()->flash('success', __('cbs::topics.show.version.changed'));
    }

    public function topicRestore() {
        // Update parameters
        $this->reload();

        $this->topic->deleted_at = null;
        $this->topic->deleted_by = null;

        // Update
        $this->update();

    }


    public function topicDelete() {
        // Update parameters

        $this->reload();

        $this->topic->deleted_at = now();
        $this->topic->deleted_by = \Auth::id();
        $this->topic->save();

        // Update
        $this->update();
    }

}
