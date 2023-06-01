<?php
namespace App\Http\Livewire\Backend\CMS\Content;

use App\Helpers\HFrontend;
use Illuminate\Http\JsonResponse;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Helpers\HBackend;
use App\Traits\CMSContentWithSections;
use App\Http\Controllers\Backend\FilesController;

class ContentModalSection extends Component
{
    use WithFileUploads, CMSContentWithSections;

    public $contentId;
    public $position;
    public $type;
    public $sectionName;
    public $value;

    // Type: files & images
    public $filePosition;
    public $files;

    // Type: list
    public $itemPosition;
    public $list;

    //Type: video
    public $videoErrorMessage = null;
    public $videoPreview = false;

    protected $listeners = ['loadModal', 'updateSectionTextHtml', 'uploadAddFiles', 'sectionFileMoved', 'sectionItemMoved'];

    public $validationRules = [];

    /**
     * Initialize component
     */
    public function mount() {
        $this->videoErrorMessage = null;
        $this->videoPreview = false;
    }

    /**
     * Render view
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render() {
        return view('livewire.backend.cms.content.content-modal-section');
    }

    /**
     * Trigger section modal show for specific section (position in array)
     *
     * @param $position
     */
    public function loadModal($position) {
        $this->filePosition = null;
        $this->itemPosition = null;
        $this->reload($position);
        $this->initRules();
        $this->emit('toggleModalSection');
    }

    /**
     * Load selected section details
     *
     * @param $position
     */
    public function reload($position) {
        $this->position = $position;

        $content = $this->getContent();

        $this->type = data_get($content, 'sections.'.$position.'.type', '');
        $this->sectionName = data_get($content, 'sections.'.$position.'.name.'.getLang(), '');
        $this->value = (array)data_get($content, 'sections.'.$position.'.value', []);

        if($this->type == 'images' || $this->type == 'files') {
            $this->files = $this->value;
        } elseif($this->type == 'list') {
            $this->list = $this->value;
        }
    }

    /**
     * Initialize custom section rules
     */
    public function initRules() {
        if($this->type == 'button') {
            $this->validationRules = [
                'value.*.title' => 'string',
                'value.*.link' => 'string',
                'value.*.first' => 'string',
                'value.*.second' => 'string',
                'value.*.third' => 'string',
            ];
        } else if($this->type == 'files' || $this->type == 'images') {
            $this->validationRules = [
                'files.code' => 'string',
                'files.filename' => 'string',
                'files.*.enabled' => 'string',
                'files.*.name' => 'string',
                'files.*.desc' => 'string',
                'files.*.alt' => 'string',
                'files.*.link' => 'string',
            ];
        } else if($this->type == 'list') {
            $this->validationRules = [
                'list.code' => 'string',
                'list.*.enabled' => 'string',
                'list.*.name' => 'string',
                'list.*.desc' => 'string',
                'list.*.options' => 'string',
            ];
        } else {
            $this->validationRules = [
                'value.*' => 'nullable|string',
            ];
        }
    }

    /**
     * Update TinyMCE HTML editor fields when modal hides
     *
     * @param $arr Array with key = parameter name
     */
    public function updateSectionTextHtml($arr) {
        foreach ($arr as $name => $value) {
            $this->updated($name, $value);
        }
    }

    /**
     * Validate and update attributes change
     *
     * @param $name
     * @param $value
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updated($name, $value) {
        // logDebug("Init: ".$name." - ".$value);

         // Set value to empty if not exists
         if( !data_get($this, $name) ){
            data_set($this, $name, '');
        }

        // Validate field
        $this->validateOnly($name, $this->validationRules);

        // logDebug("Validated: ".$name);

        try {
            // Get sections
            $sections = $this->getSections();

            // Update field
            if($this->type == 'images' || $this->type == 'files') {
                // files update value
                data_set($sections[$this->position], 'value', (object)$this->files);
            } elseif($this->type == 'list') {
                // list update value
                data_set($sections[$this->position], 'value', (object)$this->list);
            }elseif ($this->type == 'video'){
                $link = $this->getYoutubeEmbedUrl($value);

                if (!empty($link)) {
                    $this->videoErrorMessage = null;
                    $this->videoPreview = true;
                    foreach (getLanguagesBackend() as $language){
                        $videoLangLinks[$language['locale']] = $link;
                    }
                } else {
                    $this->videoErrorMessage = __('backend.cms.content.section.video-error.message');
                    $this->videoPreview = false;
                }
                data_set($sections[$this->position], 'value', $videoLangLinks ?? $link);
            } else {
                // Default update value
                data_set($sections[$this->position], $name, $value);
            }

            // Update sections
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update section: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    /*************************************
     * TYPE: FILES & IMAGES
     */

    /**
     * Change selected file
     *
     * @param $filePosition
     */
    public function fileSelected($filePosition) {
        $this->filePosition = $filePosition;
        $this->reload($this->position);
    }

    /**
     * Delete file
     *
     * @param $deletePosition
     */
    public function fileDelete($deletePosition) {
        // Unselect file
        $this->filePosition = null;

        try {
            // Get updated sections
            $sections = $this->getSections();
            $files = (array)$sections[$this->position]->value;

            // Remove file
            unset($files[$deletePosition]);

            // Update array to component
            $sections[$this->position]->value = (object)$files;
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }
    
    /**
     * Get download URL
     *
     * @param  mixed $downloadPosition
     * @return void
     */
    public function fileDownload($downloadPosition) {
        try {
            // Get file ID
            $arr = (array)$this->files;
            $file = $arr[$downloadPosition] ?? [];
            $id = $file['id'] ?? 0;

            // Get file URL
            $file = FilesController::getImageUrlByName($id);
            
            Redirect($file);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // If file does not exist reload
        $this->reload($this->position);
    }

    /**
     * FileUpload notification to add uploaded files
     *
     * @param $files
     */
    public function uploadAddFiles($files) {
        try {
            foreach ($files as $file) {
                $this->addFile($file["id"], $file["filename"]);
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    /**
     * Add new file to component
     *
     * @param $fileId
     * @param $filename
     */
    private function addFile($fileId, $filename) {
        try {
            $sections = $this->getSections();

            $file = '{"id":"'.$fileId.'","filename":"'.$filename.'"';

            foreach(getLanguagesFrontend() as $language) {
                $file.= ',"'.$language['locale'].'":{"enabled":1,"name":"'.$filename.'"}';
            }
            $file.= '}';

            $this->files[] = json_decode($file);

            data_set($sections[$this->position], 'value', (object)$this->files);

            if($this->type == 'images'){
                $this->dealWithImageSeo();
            }

            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }
    }

    public function sectionFileMoved($elem, $dest) {
        // Unselect file
        $this->filePosition = null;

        try {
            // Get updated sections
            $sections = $this->getSections();
            $files = (array)$sections[$this->position]->value;

            // Move file
            $out = array_splice($files, $elem, 1);
            array_splice($files, $dest, 0, $out);

            // Update array to component
            $sections[$this->position]->value = (object)$files;
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    /*************************************
     * TYPE: LIST
     */

    /**
     * Add new item to list and select added item
     */
    public function listAddItem() {
        try {
            $item = '{';

            foreach(getLanguagesFrontend() as $i => $language) {
                if($i != 0) $item.= ',';
                $item.= '"'.$language['locale'].'":{"enabled":1,"value":"'.__('cms::content.show.modal.list.name.default').'"}';
            }
            $item.= '}';

            $this->list[] = json_decode($item);

            logDebug(json_encode($this->list));

            $sections = $this->getSections();
            data_set($sections[$this->position], 'value', (object)$this->list);

            $this->setSections($sections);

            // Select added item
            $this->itemPosition = count($this->list)-1;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    /**
     * Select item in position
     *
     * @param $itemPosition
     */
    public function itemSelected($itemPosition) {
        $this->itemPosition = $itemPosition;
        $this->reload($this->position);
    }

    /**
     * Delete item in position
     *
     * @param $deletePosition
     */
    public function itemDelete($deletePosition) {
        // Unselect item
        $this->itemPosition = null;

        try {
            // Get updated sections
            $sections = $this->getSections();
            $list = (array)$sections[$this->position]->value;

            // Remove item
            unset($list[$deletePosition]);

            // Update array to component
            $sections[$this->position]->value = (object)$list;
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    /**
     * Move items
     *
     * @param $elem
     * @param $dest
     */
    public function sectionItemMoved($elem, $dest) {
        // Unselect item
        $this->itemPosition = null;

        try {
            // Get updated sections
            $sections = $this->getSections();
            $list = (array)$sections[$this->position]->value;

            // Move item
            $out = array_splice($list, $elem, 1);
            array_splice($list, $dest, 0, $out);

            // Update array to component
            $sections[$this->position]->value = (object)$list;
            $this->setSections($sections);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }

        // Emit and reload
        $this->emitAndReload();
    }

    private function dealWithImageSeo(){
        try {
            $content = $this->getContent();
            $seo = $content->seo;
            if(empty($seo)){
                $seo = (object)[];
            }
            $files = array_values($this->files);
            foreach(HFrontend::getContentConfigurations($content->type)->seo ?? [] as $type => $group){
                foreach($group as $code => $field){
                    if($code == 'og:image' && !empty($files)){
                        $file = !empty($files[0]) ? $files[0] : null;

                        if(!empty($file)) {
                            $seo->{$code} = FilesController::getFileById(getField($file, "id"));
                        }
                    }
                }
            }
            data_set($content, 'seo', (object)$seo);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('update: '.json_encode($e->getMessage()));
        }
    }

    /**
     * Returns a youtube embed url
     * @param string $url Youtube url
     * @return string
     */
    public function getYoutubeEmbedUrl($url)
    {
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

        $youtube_id = null;

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if(empty($youtube_id)){
            return null;
        }else{
            return 'https://www.youtube.com/embed/' . $youtube_id ;
        }

    }
}
