<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasSushiModels
{
    /**
     * Initialize the trait for an instance.
     *
     * @return void
     */
    public function initializeHasSushiModels(): void
    {
        if ( !isset($this->sushiModels) || !is_array($this->sushiModels) ) {
            $this->sushiModels = [];
        }
    }
    
    /**
     * Performs an action triggered by an associated Sushi model.
     *
     * @param string $action
     * @param Model $model
     * @return bool
     */
    public function performSushiModelAction(string $action, Model $model) : bool
    {
        $actionMethod = Str::camel($action.'_'.class_basename($model));
        
        if( !method_exists($this, $actionMethod) ){
            $actionMethod = Str::camel($action.'_sushi_model');
        }
        
        return $this->{$actionMethod}($model);
    }
    
    /**
     * Attemps to create a row in the property associated to a given Sushi model.
     *
     * @param Model $model
     * @return bool
     */
    public function createSushiModel(Model $model): bool
    {
        $linkedProperty = $this->linkedProperty($model);
        if( !$this->isValidLinkedProperty($linkedProperty) ){
            logError('Invalid Sushi model link property');
            return false;
        }
        
        $modelKeyName = $model->getKeyName();
        if( empty($model->{$modelKeyName}) ){
            logError('Model missing primary key');
            return false;
        }
        
        $rows = collect(data_get($this, $linkedProperty));
        
        $targetModelKey = $rows->search(fn ($row) => ($row->{$modelKeyName} ?? null) === $model->{$modelKeyName} );
        if( !is_bool($targetModelKey) ){
            logError("Model with key '{$model->{$modelKeyName}}' already exists");
            return false;
        }
        
        $rows->push($model->toArray());
        $rows = $this->formatRowsBeforeSave($rows, $model);
        
        [$column, $path] = $this->parseLinkedProperty($linkedProperty);
        $columnData = $this->{$column};
        $this->{$column} = empty($path) ? $rows : data_set($columnData, $path, $rows);
        
        return $this->save();
    }
    
    /**
     * Attemps to update a row in the property associated to a given Sushi model.
     *
     * @param Model $model
     * @return bool
     */
    public function updateSushiModel(Model $model): bool
    {
        $linkedProperty = $this->linkedProperty($model);
        if( !$this->isValidLinkedProperty($linkedProperty) ){
            logError('Invalid Sushi model link property');
            return false;
        }
        
        $modelKeyName = $model->getKeyName();
        if( empty($model->{$modelKeyName}) ){
            logError('Model missing primary key');
            return false;
        }
        
        $rows = collect(data_get($this, $linkedProperty));
        
        // check if original key exists
        $originalKeyValue = $model->getRawOriginal($modelKeyName);
        $targetModelKey = $rows->search(fn ($row) => ($row->{$modelKeyName} ?? null) === $originalKeyValue );
        if( is_bool($targetModelKey) ){
            logError("Model with key '{$originalKeyValue}' not found");
            return false;
        }
        
        // check if new key exists
        if( $model->{$modelKeyName} != $originalKeyValue ){
            $existingModelKey = $rows->search(fn ($row) => ($row->{$modelKeyName} ?? null) === $model->{$modelKeyName} );
            if( !is_bool($existingModelKey) ){
                logError("Model with key '{$model->{$modelKeyName}}' already exists");
                return false;
            }
        }
        
        $rows->put($targetModelKey, $model);
        $rows = $this->formatRowsBeforeSave($rows, $model);
        
        [$column, $path] = $this->parseLinkedProperty($linkedProperty);
        $columnData = $this->{$column};
        $this->{$column} = empty($path) ? $rows : data_set($columnData, $path, $rows);
        
        return $this->save();
    }
    
    /**
     * Attempts to delete a row from the property associated to a given Sushi model.
     *
     * @param Model $model
     * @return bool
     */
    public function deleteSushiModel(Model $model): bool
    {
        $linkedProperty = $this->linkedProperty($model);
        if( !$this->isValidLinkedProperty($linkedProperty) ){
            logError('Invalid Sushi model link property');
            return false;
        }
        
        $modelKeyName = $model->getKeyName();
        if( empty($model->{$modelKeyName}) ){
            logError('Model missing primary key');
            return false;
        }
        
        $rows = collect(data_get($this, $linkedProperty));
        
        $targetModelKey = $rows->search(fn ($row) => ($row->{$modelKeyName} ?? null) === $model->{$modelKeyName} );
        if( is_bool($targetModelKey) ){
            logError("Model with key '{$model->{$modelKeyName}}' not found");
            return false;
        }
        
        $rows->pull($targetModelKey);
        $rows = $this->formatRowsBeforeSave($rows, $model);
        
        [$column, $path] = $this->parseLinkedProperty($linkedProperty);
        $columnData = $this->{$column};
        $this->{$column} = empty($path) ? $rows : data_set($columnData, $path, $rows);
        
        return $this->save();
    }
    
    /**
     * Attempts to soft-delete a row from the property associated to a given Sushi model.
     *
     * @param Model $model
     * @return bool
     */
    public function softDeleteSushiModel(Model $model): bool
    {
        return $this->updateSushiModel($model);
    }
    
    /**
     * Attempts to restore a row from the property associated to a given Sushi model.
     *
     * @param Model $model
     * @return bool
     */
    public function restoreSushiModel(Model $model): bool
    {
        return $this->updateSushiModel($model);
    }
    
    /**
     * Formats the Sushi model rows before saving them.
     *
     * @param Collection $rows
     * @param Model $model
     * @return array
     */
    public function formatRowsBeforeSave(Collection $rows, Model $model): array
    {
        $indexes = null;
        
        if( $model->shouldIndexRows() ){
            $keyName = $model->indexingKeyName();
            $indexes = !empty($keyName) ? $rows->pluck($keyName) : $rows->keys();
        }
        
        return !empty($indexes) ? $indexes->combine($rows)->toArray() : $rows->values()->toArray();
    }
    
    
    /**
     * Returns the property linked to a given Sushi model.
     *
     * @param string|Model $model
     * @return string|null
     */
    public function linkedProperty(string|Model $model) : ?string
    {
        if( empty($this->sushiModels) ){
            return null;
        }
        
        $class = is_string($model) ? $model : get_class($model);
        
        return $this->sushiModels[$class] ?? null;
    }
    
    /**
     * Parses a linked Sushi model property into a column name and a "dot" notation key.
     *
     * @param string $property
     * @return array<string, string>  [$column, $path]
     */
    public function parseLinkedProperty(string $property) : array
    {
        $parts = explode('.', $property);
        
        $column = array_shift($parts);
        $path = implode('.', $parts);
        
        return [$column, $path];
    }
    
    /**
     * Whether a Sushi model has a valid link.
     *
     * @param Model $model
     * @return bool
     */
    public function hasValidSushiModelLink(Model $model) : bool
    {
        return $this->isValidLinkedProperty($this->linkedProperty($model));
    }
    
    /**
     * Whether a property is a valid linked property.
     *
     * @param string|null $property
     * @return bool
     */
    public function isValidLinkedProperty(?string $property) : bool
    {
        if( empty($property) ){
            return false;
        }
        
        [$column, $path] = $this->parseLinkedProperty($property);
        
        $columnData = $this->{$column} ?? [];
        if( !is_object($columnData) && !is_array($columnData) ){
            return false;
        }
        
        return true;
    }
    
    /**
     * Retrieves an array of a give Sushi model's rows.
     *
     * @param Model $model
     * @return array
     */
    public function getSushiModelRows(Model $model) : array
    {
        $linkedProperty = $this->linkedProperty($model);
        if( !$this->isValidLinkedProperty($linkedProperty) ){
            return [];
        }
        
        [$column, $path] = $this->parseLinkedProperty($linkedProperty);
        
        $columnData = $this->{$column} ?? [];
        if( !is_object($columnData) && !is_array($columnData) ){
            return [];
        }
        
        return (array)(empty($path) ? $columnData : data_get($columnData, $path));
    }
}
