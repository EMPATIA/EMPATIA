<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use Illuminate\Http\Client\Request;
use Carbon\Carbon;
use Livewire\Component;

class ContentManager extends Component
{
    public $contentId;
    public $content;
    public $lang;
    public $updated = false;

    protected $listeners = ['sectionsMoved', 'updateSection', 'updateContent', 'reload', 'changeToVersion'];

    public function mount() {
        $this->reload();
        $this->updateStatus();

        foreach(getLanguagesFrontend() as $l) {
            if(empty($this->lang)) $this->lang = getField($l, 'locale');

            if(getField($l, 'locale') == getLang()) {
                $this->lang = getLang();
                break;
            }
        }
    }

    public function render()
    {
        $this->updated = $this->content->isDirty();
        return view('livewire.backend.cms.content.content-manager');
    }

    public function reload() {
        $this->content = \Session::get('content_draft_'.$this->contentId);
    }

    private function update() {
        \Session::put('content_draft_' . $this->contentId, $this->content);
        $this->emit('showEdited');
    }

    private function updateStatus() {
        if(!empty($this->content->deleted_at))
            $this->emit('setContentDeleted');
        else if($this->content->status == 'published')
            $this->emit('setContentPublished');
        else
            $this->emit('setContentUnpublished');
    }

    public function updateContent() {
        // Update parameters
        $this->reload();

        // If parameter is dirty update it
        if($this->content->isDirty()) {
            // Save version and update version number
            $versions = (array)$this->content->versions;
            $pos = count($versions);
            $pos++;

            $this->content->version = $pos;

            $versions[$pos] = [
                'version' => $pos,
                'user' => $this->content->updated_by,
                'date' => $this->content->updated_at,
                'user_version' => \Auth::id(),
                'date_version' => Carbon::now(),
                'title' => $this->content->title,
                'code' => $this->content->code,
                'slug' => $this->content->slug,
                'tags' => $this->content->tags,
                'status' => $this->content->status,
                'options' => $this->content->options,
                'seo' => $this->content->seo,
                'sections' => $this->content->sections,
            ];

            $this->content->versions = (object)$versions;

            // Remove temporary parameter
            unset($this->content->publishedVersion);

            // Update
            $this->content->update();

            $this->emit('hideEdited');

            $this->updateStatus();

            session()->flash('success', __('cms::content.save.success'));
        } else {
            session()->flash('fail', __('cms::content.save.fail.no_update'));
        }
    }

    public function addSection($type) {
        // Update parameters
        $this->reload();

        $sections = (array)$this->content->sections;

        $s = '{"type":"'.$type.'","value":{}}';

        $sections[] = json_decode($s);
        $this->content->sections = $sections;

        // Update content in session
        $this->update();

//        session()->flash('success', __('cms::content.added.message'));
    }

    public function deleteSection($position) {
        // Update parameters
        $this->reload();

        $sections = (array)$this->content->sections;
        unset($sections[$position]);
        $this->content->sections = $sections;

        // Update content in session
        $this->update();

//        session()->flash('success', __('cms::content.deleted.message'));
    }

    public function changeLanguage($lang) {
        $this->lang = $lang;
        $this->reload();
    }

    public function sectionsMoved($elem, $dest) {
        // Update parameters
        $this->reload();

        $sections = (array)$this->content->sections;

        $out = array_splice($sections, $elem, 1);
        array_splice($sections, $dest, 0, $out);

        $this->content->sections = $sections;

        // Update content in session
        $this->update();
    }

    /**********************
     * Content actions
     */

    public function contentUnpublish() {
        // Update parameters
        $this->reload();

        $this->content->status = 'unpublished';

        $this->update();
    }

    public function contentPublish() {
        // Update parameters
        $this->reload();

        $this->content->status = 'published';

        $this->update();
    }

    public function changeToVersion($ver) {
        // Update parameters
        $this->reload();

        $this->content->publishedVersion = $this->content->version;
        $this->content->version = $ver;

        $version = $this->content->versions->{$ver};

        $this->content->updated_by = $version->user_version ?? $version->updated_by;
        $this->content->updated_at = $version->date_version ?? $version->updated_at;
        $this->content->title = $version->title;
        $this->content->code = $version->code;
        $this->content->slug = $version->slug;
        $this->content->tags = $version->tags;
        $this->content->status = $version->status;
        $this->content->options = $version->options;
        $this->content->seo = $version->seo;
        $this->content->sections = $version->sections;

        // Enable new version
        $this->update();

        session()->flash('success', __('cms::content.show.version.changed'));
    }

    public function contentRestore() {
        // Update parameters
        $this->reload();

        $this->content->deleted_at = null;
        $this->content->deleted_by = null;

        // Update
        $this->update();
    }

    public function contentDelete() {
        // Update parameters
        $this->reload();

        $this->content->deleted_at = now();
        $this->content->deleted_by = \Auth::id();

        // Update
        $this->update();
    }
}
