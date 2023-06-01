<?php

namespace App\Http\Livewire\Backend\CMS\File;

use App\Http\Controllers\Backend\FilesController;
use Livewire\Component;

class FilePreview extends Component
{
    const TYPES = ['files','images'];

    const DEFAULT_VIEW = 'livewire.backend.cms.file.file-preview';
    const DEFAULT_TYPE = 'files';

    // Component configurations
    public string   $view;              // the view for the component to render (default: const DEFAULT_VIEW)
    public string   $type;              // the type of files being previewed (default: const DEFAULT_TYPE)
    public string   $name;              // the name of the associated file input
    public bool     $single = false;    // whether to preview a single file or not (default: false, meaning multiple)

    // Images type configurations
    public int      $width = 200;       // image width (default: 200, pixels)
    public int      $height = 200;      // image height (default: 200, pixels)
    public string   $format = 'webp';   // image format (default: webp)
    public int      $quality;           // image quality

    // Container settings
    public string   $containerClass = '';   // files container class
    public string   $containerStyle = '';   // files container style

    // Component data
    public array $files = [];           // the array of files to be listed (default: [])

    protected $listeners = [];


    protected function getListeners()
    {
        return ($this->listeners ?? []) + ['loadFileList:'.$this->name => 'loadFileList'];
    }


    public function mount()
    {
        $this->view = $this->view ?? self::DEFAULT_VIEW;
        $this->type = in_array($this->type ?? null, self::TYPES) ? $this->type : self::DEFAULT_TYPE;

        $this->loadFileList($this->files, $this->name);
    }

    public function render()
    {
        return view($this->view);
    }


    /**
     * Load a list of files
     *
     * @param $files
     * @param $name
     */
    public function loadFileList($files, $name = null) {
        if(!is_array($files) || $name != $this->name){
            return;
        }

        $this->files = [];

        foreach ($files as $file) {
            if( $this->single ){
                $this->files = [];
            }

            if( findObjectByProperty('id', getField($file, 'id'), $this->files) ){
                continue;
            }

            if( $this->type == 'images' ){
                $url = '';
                if( !empty( $fileName = getField($file, 'id') ) ) {
                    $url = FilesController::getImageUrlByName($fileName, $this->width, $this->height, $this->format, $this->quality ?? null);
                }
                $file = data_set($file, 'url', $url);
            }

            $this->files[] = $file;

            if( $this->single && count($this->files) > 1 ){
                unset($this->files[1]);
                break;
            }
        }
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

        $this->emitTo('file-input', 'removeFile:'.$this->name, $id);
    }
}
