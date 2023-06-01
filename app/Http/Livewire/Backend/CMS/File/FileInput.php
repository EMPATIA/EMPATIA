<?php

namespace App\Http\Livewire\Backend\CMS\File;

use Livewire\Component;
use App\Models\Backend\File;

class FileInput extends Component
{
    const TYPES = ['files','images'];

    const DEFAULT_VIEW = 'livewire.backend.cms.file.file-input';
    const DEFAULT_TYPE = 'files';
    const DEFAULT_MAXSIZE = 10;  // MBs

    // Component configurations
    public string   $view;              // view for the component to render (default: const DEFAULT_VIEW)
    public string   $type;              // type of files (default: const DEFAULT_TYPE)
    public string   $name;              // name of the associated file input
    public string   $action = 'show';   // form action (default: show)
    public int|null $maxFiles;
    public int|null $maxSize;
    public string $mimes = '';           // Limit file type upload (e.g. '') (default: none for files, 'png,jpg,gif,webp' for images)

    public array $emits = [];
    public array $class = [];

    // Component data
    public string   $label;             // input label
    public array    $files = [];        // array of files to be listed (default: [])
    public string   $value;

    protected $listeners = [
        'loadFileList',
        'uploadAddFiles' => 'filesUploaded',
        'removeAllFiles',
    ];


    protected function getListeners()
    {
        return ($this->listeners ?? []) + ['removeFile:'.$this->name => 'removeFile'];
    }


    public function mount()
    {
        $this->view = $this->view ?? self::DEFAULT_VIEW;
        $this->type = in_array($this->type ?? null, self::TYPES) ? $this->type : self::DEFAULT_TYPE;
        $this->maxSize  = !empty($this->maxSize) ? $this->maxSize : self::DEFAULT_MAXSIZE;
        $this->maxFiles = empty($this->maxFiles) ? 0 : $this->maxFiles ;

        $this->loadFiles();
        $this->updateValue();
    }

    public function render()
    {
        return view($this->view);
    }


    /**
     * Update input value with json file list
     */
    public function loadFiles()
    {
        if( empty($this->files) ){
            return;
        }

        $files = File::whereIn('name', array_map(function($item){ return getField($item, 'id'); }, $this->files))->get();

        $this->files = [];
        if( !empty($files) && !$files->isEmpty() ){
            $this->files = $files->map(function($file){
                return [
                    'id'        => $file->name,
                    'filename'  => $file->original,
                    'type'      => $file->type,
                    'size'      => $file->size,
                ];
            })->toArray();
        }
    }

    /**
     * Update input value with json file list
     */
    public function updateValue()
    {
        if( $this->maxFiles == 1 ){
            $this->value = first_element(array_map(function($item){ return getField($item, 'id'); }, $this->files)) ?? '';
        } else {
            $this->value = json_encode(array_map(function($item){ return getField($item, 'id'); }, $this->files));
        }
    }

    /**
     * Update list of files
     *
     * @param $files
     */
    public function filesUploaded($files)
    {
        $updates = 0;

        foreach ($files as $file) {
            if( $this->maxFiles == 1 ){
                $this->files = [];
            }

            if( findObjectByProperty('id', getField($file, 'id'), $this->files) ){
                continue;
            }

            $this->files[] = $file;
            $updates++;

            if( $this->maxFiles == 1 && count($this->files) > 1 ){
                unset($this->files[1]);
                break;
            }
        }

        if( $updates < 1 ){
            return;
        }

        $this->updateValue();
        $this->emitTo('file-preview', 'loadFileList:'.$this->name, $this->files, $this->name);
        $this->fireEmits();
    }

    public function removeFile($id)
    {
        if(!is_string($id)){
            return;
        }

        foreach ($this->files as $key => $file) {
            if( $id == getField($file, 'id') ){
                unset($this->files[$key]);
                break;
            }
        }

        $this->updateValue();
    }

    public function removeAllFiles()
    {
        $this->files = [];

        $this->updateValue();
        $this->emitTo('file-preview', 'loadFileList:'.$this->name, $this->files, $this->name);
    }

    public function fireEmits(string $set = null)
    {
        $emitsSet = data_get($this, 'emits' . ($set ? ".$set" : ''), []);

        foreach ($emitsSet ?? [] as $emit) {
            if( empty($emit['listener']) ){
                continue;
            }

            if( !in_array($emit['type'], ['self', 'up', 'to', 'all']) ) {
                continue;
            }

            switch ($emit['type']){
                case 'self':
                    $this->emitSelf($emit['listener'], $this->files, $this->name);
                    break;

                case 'up':
                    $this->emitUp($emit['listener'], $this->files, $this->name);
                    break;

                case 'to':
                    if( empty($emit['component']) || !is_string($emit['component']) ) {
                        break;
                    }
                    $this->emitTo($emit['component'], $emit['listener'], $this->files, $this->name);
                    break;

                default:
                    $this->emit($emit['listener'], $this->files, $this->name);
                    break;
            }
        }
    }
}
