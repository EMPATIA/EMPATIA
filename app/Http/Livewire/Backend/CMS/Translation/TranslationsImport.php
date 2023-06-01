<?php

namespace App\Http\Livewire\Backend\CMS\Translation;

use App\Helpers\HBackend;
use App\Helpers\HCache;
use App\Imports\ToArrayImport;
use App\Models\Backend\CMS\Translation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;


class TranslationsImport extends Component
{
    const TEMP_STORAGE_PATH = 'temp/';
    const PARAMETER_FILTERS = ['sinf', '-'];
    
    //  INPUTS
    public array $files;
    
    //  CONFIGS
    public array $properties = [];
    public array $settings = [];
    
    //  MESSAGES
    public string $errorMessage;
    public string $warningMessage;
    public string $successMessage;
    public string $loadingMessage;
    
    //  FLAGS
    public array $importResults = [];
    
    // Excel Properties
    public array $headingsBySheet = [];
    public array $rowsBySheet = [];
    
    protected $listeners = [
        'filesUpdated',
    ];
    
    public function mount() {
        $this->loadSettings();
    }
    
    public function render()
    {
        return view('livewire.backend.cms.translations.translations-import-modal');
    }
    
    public function filesUpdated($files)
    {
        $this->clearMessages();
        
        $this->files = $files;
    }
    
    private function loadSettings()
    {
        $this->settings = (array)HBackend::getConfigurationByCode('translations_import');
    }
    
    private function clearOptions()
    {
    }
    
    public function clearMessages()
    {
        $this->errorMessage     = '';
        $this->warningMessage   = '';
        $this->successMessage   = '';
        $this->loadingMessage   = '';
    }
    
    public function closingModal()
    {
        if( !empty($this->successMessage) ){
            $this->clearOptions();
        }
        
        $this->clearMessages();
    }
    
    /**
     * Perform the import
     *
     * @param bool    $confirmation   // wether the user confirmed the import
     * @return void
     */
    public function import(bool $confirmation)
    {
        $this->clearMessages();
        
        if( !$confirmation ){
            $this->errorMessage = __('backend.translations.import.message.user-didnt-confirm');
            return;
        }
        
        if( empty($this->files) ){
            $this->warningMessage = __('backend.translations.import.message.no-files-selected');
            return;
        }
        
        // import statistics
        $this->importResults = [
            'total_files'               => count($this->files ?? []),
            'processed_files'           => 0,
            'inaccessible_temp_files'   => 0,
            'total_sheets'              => 0,
            'valid_sheets'              => 0,
        ];
        
        $rawData = [];  // data extracted from files
        
        // process files
        foreach ($this->files ?? [] as $file) {
            $this->setLoadingMessage( __('backend.translations.import.message.processing-file', ['name' => $file['filename']]) );
            
            // store file in disk temporarily
            $filePath = $this->storeFileTemporarily( $file['id'] );
            
            // check if temporary file exists
            if( !Storage::exists(self::TEMP_STORAGE_PATH . $file['id']) ){
                $this->importResults['inaccessible_temp_files']++;
                continue;
            }
            
            // extract data from file
            $rawData = $this->extractImportData($filePath, $rawData);
            if( !empty($this->errorMessage)){
                return;
            }
            
            $this->importResults['processed_files']++;
        }
        // process and save extracted data
        $this->processAndSaveExtractedData($rawData);
        
        if( !empty($this->errorMessage) ){
            return;
        }
        
        $this->successMessage = !empty($this->successMessage) ? $this->successMessage : __('backend.translations.import.message.successfuly-imported');
        
        $this->files = [];
        $this->emitTo('files-input', 'removeAllFiles');
    }
    
    private function extractImportData(string $filePath, &$rawData) : array
    {
        // get data from file
        $translationsImporter   = new ToArrayImport(1);     // Get/Set heading row and sheetsNames
        $this->headingsBySheet  = Excel::toArray(new HeadingRowImport(1), $filePath, null, \Maatwebsite\Excel\Excel::XLSX);
        $this->rowsBySheet      = Excel::toArray($translationsImporter, $filePath, null, \Maatwebsite\Excel\Excel::XLSX);
    
        if (empty($this->headingsBySheet)) {
            $this->errorMessage = __('backend.translations.import.message.no-headers');
            return [];
        }
            
        $this->importResults['total_sheets'] += count($headingsBySheet ?? []);
        
        // create data variables
        $rawData = array_merge($rawData, [
            'translations'  => [],
        ]);
//dd($this->headingsBySheet, $translationsImporter, $this->rowsBySheet , $rawData);
        // process sheets
        foreach ($this->headingsBySheet as $index => $sheet) {
            $headings = first_element($sheet);
            // check if the sheet contains required columns
            if( !$this->isValidSheet($headings) ){
                continue;
            }
            $this->importResults['valid_sheets']++;

            // build columns map
            $columnsMap = $this->getColumnsMap($headings);
            
            // process data
            logDebug('Process translations data: Init');
            
            $data = $this->rowsBySheet[$index];

            // rows
            foreach ($data ?? [] as $translation) {
                if (!$this->isValidTranslation($translation)){
                    $this->errorMessage = __('backend.translations.import.message.invalid-translation');
                    break;
                }
                $translationSlug = Str::slug($translation['locale'].$translation['namespace'].$translation['group'].$translation['item']);
    
                if( empty($translationSlug) ){
                    continue;
                }
                
                if( empty($rawData['translations'][$translationSlug]) ){
                    foreach ($headings ?? [] as $heading){
                        data_set($rawData['translations'][$translationSlug], $heading , getField($translation, $heading));
                    }
                }
            }
        }
        return $rawData;
    }
    
    private function isValidTranslation($translation) : bool {
        if(empty($translation))
            return false;
        
        $requiredColumns = [];
        $columns = getField($this->settings, 'columns', []);
    
        foreach ($columns as $column) {
            if( getField($column, 'required') ){
                array_push($requiredColumns, getField($column, 'code'));
            }
        }
        
        if(empty($requiredColumns))
            return true;
        
        foreach ($requiredColumns as $requiredColumn){
            if(empty(getField($translation, $requiredColumn)))
                return false;
        }
        return true;
    }
    
    private function processAndSaveExtractedData(&$rawData)
    {
        try {
            $this->importResults = array_merge($this->importResults, [
                'total_translations'     => count($rawData['translations'] ?? []),
                'created_translations'   => 0,
                'updated_translations'   => 0
            ]);
            
            $newTranslations = [];
            $updateTranslations = [];
            
            foreach ($rawData['translations'] ?? [] as $rawTranslation) {
                $translation = new Translation;
                
                $headings = first_element(first_element($this->headingsBySheet));
                foreach ($headings as $heading) {
                    $translation->{$heading} = getField($rawTranslation, $heading);
                }
                // If translations exists
                if(!empty($dbTranslation = Translation::where([ 
                    ['locale', $translation->locale],
                    ['namespace', '=', $translation->namespace],
                    ['group', '=', $translation->group],
                    ['item', '=', $translation->item],])
                    ->first())){

                    if($dbTranslation->text != $translation->text){ //If translation text is different from the imported one
                        $translation->id = $dbTranslation->id;
                        array_push($updateTranslations, $translation);
                    }
                    
                }else { // If translation doesn't exist
                    array_push($newTranslations, $translation);
                }
            }

            DB::beginTransaction();
            foreach ($updateTranslations ?? [] as $updateTranslation){
                if(!empty($updateTranslation->id))
                    if(Translation::whereId($updateTranslation->id)->update(['text' => $updateTranslation->text])){
                        HCache::flushTranslationId($updateTranslation->id);
                        $this->importResults['updated_translations']++;
                    }
            }

            foreach ($newTranslations ?? [] as $newTranslation){
                if($newTranslation->save())
                    $this->importResults['created_translations']++;
            }
            DB::commit();
            $this->successMessage = __('backend.cms.translations.import.message.successfully-imported', $this->importResults);
            
        } catch (\Exception $e) {
            DB::rollback();
            logError( $e->getMessage() .' at line '. $e->getLine() );
            $this->errorMessage = __('backend.cms.translations.import.message.error-saving');
        }
    }
    
    private function isValidSheet(array $headings) : bool
    {
        try{
            $columns = getField($this->settings, 'columns', []);
            foreach ($columns as $key => $column) {
                if( empty($code = getField($column, 'code')) ){
                    continue;
                }
                
                if( !getField($column, 'required') ){
                    continue;
                }
                
                $allowedColumnHeadings = array_merge([$code], getField($column, 'aliases', []));
                
                $hasColumn = false;
                
                foreach ($headings as $heading) {
                    if( in_array($heading, $allowedColumnHeadings) ){
                        $hasColumn = true;
                        break;
                    }
                }
                
                if( !$hasColumn ){
                    return false;
                }
            }
            
            return true;
            
        } catch (\Exception $e) {
            logError( $e->getMessage() );
        }
        
        return false;
    }
    
    private function storeFileTemporarily(string $fileId) : string
    {
        $filePath = '';
        
        try{
            $storageFilePath = self::TEMP_STORAGE_PATH . $fileId;
            Storage::disk()->put($storageFilePath, Storage::cloud()->get($fileId));
            $filePath = Storage::disk()->path($storageFilePath);
        } catch (\Exception $e) {
            logError( $e->getMessage() );
        }
        
        return $filePath;
    }
    
    private function getColumnsMap(array $headings) : array
    {
        $map = [];
        
        try{
            $columns = getField($this->settings, 'columns', []);
            
            foreach ($headings as $heading) {
                // search for heading in required columns
                foreach ($columns as $column) {
                    $code = getField($column, 'code');
                    if( empty($code) ){
                        continue;
                    }
                    
                    $aliases = array_merge([$code], getField($column, 'aliases', []));
                    
                    if( in_array($heading, $aliases) ){
                        $map[$heading] = $code;
                    }
                }
            }
            
        } catch (\Exception $e) {
            logError( $e->getMessage() );
        }
        
        return $map;
    }
    
    private function setLoadingMessage($message)
    {
        if( is_string($message) ){
            $this->loadingMessage = $message;
        }
    }
}
