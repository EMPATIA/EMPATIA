<?php
namespace App\Http\Livewire\Backend\CMS\File;

use App\Http\Controllers\Backend\FilesController;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    const DEFAULT_VIEW = 'livewire.backend.cms.file.file-upload';
    const DEFAULT_MAX_FILE_UPLOADS = 25;

    // Component configurations
    public string $view;            // Component view
    public string $uploadId;        // User specific ID for this FileUpload (default: random string)
    public string $type = 'files';  // files : generic uploader | images : images uploader (default: files)
    public int $maxFiles;           // Limit number of files to upload (default: not limited)
    public int $maxFileUploads;     // Limit number of file uploads per request (default: self::DEFAULT_MAX_FILE_UPLOADS)
    public string $mimes;           // Limit file type upload (e.g. '') (default: none for files, 'png,jpg,gif,webp' for images)
    public int $maxSize = 5;        // Max upload file size (per file) in Mb (confirm PHP configuration and Livewire temporary supports this size) (default: 5)
    public string $notify;          // Component to notify of file upload (default: parent component)
    public array $misc = [];        // Miscellaneous data to pass to the view (default: [])

    // GUI status
    public bool $uploadUploading = false;
    public int $uploadProgress = 0;
    public bool $uploadValidated = false;
    public bool $uploadSuccess = false;
    public bool $uploadError = false;
    public bool $filesValidationError = false;

    // Files array
    public $files = [];

    // Validation rules
    public array $uploadRule = [];

    protected $listeners = [
        'validateFilesQuantity'
    ];

    public array $ruleMessages = [];


    /**
     * Initialize component
     */
    public function mount() {
        // Set component view
        $this->view = $this->view ?? self::DEFAULT_VIEW;

        // Generate validation rules
        if($this->type == "images")
            $rule = ["image"];
        else
            $rule = ["file"];

        // Validate and set max uploads per request
        $this->setMaxFileUploads();

        if(!empty($this->mimes)) {
            // Custom mimes
            $rule[] = "mimes:".$this->mimes;
        } else if($this->type == 'images') {
            // Default images mimes
            $rule[] = "mimes:png,jpg,gif,webp";
        }

        // TODO: add proper validation messages (with translations and such)

        if(!empty($this->maxSize)) {
            $rule[] = "max:".($this->maxSize*1024);
        }

        $this->uploadRule = [
            'files.*' => $rule
        ];

        // Generate rule messages
        $this->ruleMessages = [
            'files.*.file'  => __('files.fileUpload.error.file'),
            'files.*.image' => __('files.fileUpload.error.image'),
            'files.*.mimes' => __('files.fileUpload.error.mimes', ['mimes' => $this->mimes ?? '']),
            'files.*.max'   => __('files.fileUpload.error.max', ['max' => $this->maxSize]),
        ];

        // Generate FileUpload ID if not provided
        if(empty($this->uploadId)) $this->uploadId = Str::random(8);
    }

    /**
     * Render component view
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render() {
        return view($this->view);
    }

    protected function setMaxFileUploads()
    {
        // Validate and set max uploads per request
        $ini_max_file_uploads = ini_get('max_file_uploads');
        $this->maxFileUploads = $this->maxFileUploads ?? self::DEFAULT_MAX_FILE_UPLOADS;
        $this->maxFileUploads = ($ini_max_file_uploads && $ini_max_file_uploads < $this->maxFileUploads) ? $ini_max_file_uploads : $this->maxFileUploads;
    }

    /**
     * Reset validations before updating files
     */
    public function updatingFiles($value) {
        $this->uploadUploading = false;
        $this->uploadSuccess = false;
        $this->uploadError = false;
        $this->uploadProgress = 0;
        $this->filesValidationError = false;
    }

    /**
     * Validate files on file attribute update
     */
    public function updatedFiles() {
        $this->uploadSuccess = false;
        // Handle GUI validation error
        $this->uploadValidated = false;

        // Validate max files
        // TODO: add proper error messages (with translations and such)
        if(!empty($this->maxFiles) && count($this->files) > $this->maxFiles) {
            $this->addError('files.*', __('files.fileUpload.error.max-files', ['max' => $this->maxFiles]));
            return;
        }

        // Validate max file uploads
        // TODO: add proper error messages (with translations and such)
        if(!empty($this->maxFileUploads) && count($this->files) > $this->maxFileUploads) {
            $this->addError('files.*', __('files.fileUpload.error.max-file-uploads', ['max' => $this->maxFileUploads]));
            return;
        }

        // Livewire validation
        $this->validate($this->uploadRule, $this->ruleMessages);

        // Update on validation successful
        $this->uploadValidated = true;
    }

    /**
     * Validate files quantity
     */
    public function validateFilesQuantity($files) {
        $this->uploadSuccess = false;
        // Validate max files
        // TODO: add proper error messages (with translations and such)
        if(!empty($this->maxFiles) && count($files) > $this->maxFiles) {
            $this->addError('files.*', __('files.fileUpload.error.max-files', ['max' => $this->maxFiles]));
            $this->filesValidationError = true;
            return;
        }

        // Validate max file uploads
        // TODO: add proper error messages (with translations and such)
        if(!empty($this->maxFileUploads) && count($files) > $this->maxFileUploads) {
            $this->addError('files.*', __('files.fileUpload.error.max-file-uploads', ['max' => $this->maxFileUploads]));
            $this->filesValidationError = true;
            return;
        }

        $this->filesValidationError = false;
    }

    /**
     * Store uploaded files
     */
    public function saveFiles() {
        $this->uploadSuccess = false;
        // Validate files
        $this->validate($this->uploadRule);

        // Uploaded record array for notification
        $uploaded = [];

        // Store uploaded files
        foreach ($this->files as $key => $file) {
            try {
                // Store file
                $fileId = getField(FilesController::store($file), 'name');

                if(empty($fileId)) {
                    throw new Exception('Error storing file');
                }

                // Add uploaded file to array
                $uploaded[] = [
                    'id' => $fileId,
                    'filename' => $file->getClientOriginalName()
                ];
            } catch (QueryException | Exception  | \Throwable $e) {
                logError('uploading files: '.json_encode($e->getMessage()));

                // Add error to error bag (improve error handling?)
                $this->addError('files.*', 'Error uploading file '.$key);
            }
        }

        // Update GUI for success
        $this->uploadSuccess = true;

        // Emit file upload event
        if(empty($this->notify)) {
            // Parent component
            $this->emitUp('uploadAddFiles', $uploaded);
        } else {
            // Custom component
            $this->emitTo($this->notify, 'uploadAddFiles', $uploaded);
        }

        // Cleanup
        $this->files = [];
        $this->uploadValidated = false;
        $this->uploadError = false;
    }
}
