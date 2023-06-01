<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait SushiModel
{
    use \Sushi\Sushi;

    public static ?Model $linkedModel = null;
    public ?string $staticallyLinkedModel = null;

    private $protectedFields = ['id'];

    /**
     * Boot the trait for a model.
     *
     * @return void
     */
    public static function bootSushiModel(): void
    {
        static::creating(function ($model) {
            self::checkPrimaryKey($model, 'create');
        });
        static::created(function ($model) {
            $model->performActionOnLinkedModel('create');
        });
        static::updating(function ($model) {
            self::checkPrimaryKey($model, 'update');
        });
        static::updated(function ($model) {
            $model->performActionOnLinkedModel('update');
        });

        if (method_exists(static::class, 'bootSoftDeletes')) {
            static::deleted(function ($model) {
                $model->performActionOnLinkedModel('softDelete');
            });

            static::forceDeleted(function ($model) {
                $model->performActionOnLinkedModel('delete');
            });

            static::restored(function ($model) {
                $model->performActionOnLinkedModel('restore');
            });
        } else {
            static::deleted(function ($model) {
                $model->performActionOnLinkedModel('delete');
            });
        }
    }

    /**
     * Initialize the trait for an instance.
     *
     * @return void
     */
    public function initializeSushiModel()
    {
        if( empty($this->staticallyLinkedModel) && !empty(static::$linkedModel) ) {
            $this->staticallyLinkedModel = static::$linkedModel::class;
        }
    }

    /**
     * Check if the primary key is valid for performing actions.
     *
     * @param Model $model
     * @param string $action
     * @return void
     */
    public static function checkPrimaryKey(Model $model, string $action = ''): void
    {
        $primaryKey = $model->getKeyName();

        if ( $action != 'create' && is_null($model->{$primaryKey} ?? null) ) {
            throw new \Illuminate\Database\QueryException(
                'Sushi Model '.Str::upper($action),
                [],
                new \Exception("No value defined for primary key '$primaryKey'")
            );
        }

        if ( (!$model->exists || $model->getRawOriginal($primaryKey) !== $model->{$primaryKey}) &&
            self::where($primaryKey, $model->{$primaryKey})->exists()
        ) {
            throw new \Illuminate\Database\QueryException(
                'Sushi Model '.Str::upper($action),
                [],
                new \Exception("Unique constraint failed for primary key '$primaryKey'")
            );
        }
    }

    /**
     * @throws \Throwable
     */
    public function performActionOnLinkedModel(string $action, bool $throwExceptions = true): bool
    {
        $result = false;

        try {
            if( empty($action)  ){
                throw new \Exception("The \$action argument must not be an empty string.");
            }

            if( empty(self::$linkedModel) ){
                throw new \Exception("The class ".get_class($this)." has no model linked to it.");
            }

            $result = self::$linkedModel->performSushiModelAction($action, $this);
            if( !$result ){
                throw new \Exception("Could not perform '$action' on the linked model.");
            }

        } catch (\Exception|\Throwable $e) {
            if( !$result ) self::bootSushi();
            if( $throwExceptions ) throw $e;
        }

        if( !$result ) self::bootSushi();

        return $result;
    }

    /**
     * Normalizes the row fields.
     *
     * @param array|null $rows
     * @return array
     */
    public function normalizeRows(array $rows = null): array
    {
        $rows = $rows ?? $this->rows ?? [];
        $rows = json_decode(json_encode(array_values($rows)), true);

        $uniqueFields = $this->getUniqueFields($rows);
        $this->rows = $this->normalizeFields($uniqueFields, $rows);

        return $this->rows;
    }

    /**
     * Normalizes the fields of arrays in a list.
     *
     * @param array $fieldSet       The field set to enforce.
     * @param array $list           The source arrays list.
     * @param mixed|null $default   The default value for unsetted fields.
     * @return array
     */
    public function normalizeFields(array $fieldSet, array $list, mixed $default = null): array
    {
        $normalizedRows = [];
        $fieldSet = array_diff($fieldSet, $this->protectedFields ?? []);

        foreach ($list as $key => $item) {
            $item = (array)$item;
            foreach ($fieldSet as $name) {
                $normalizedRows[$key][$name] = $item[$name] ?? $default;
                if( is_array($normalizedRows[$key][$name]) || is_object($normalizedRows[$key][$name]) ){
                    $normalizedRows[$key][$name] = json_encode($normalizedRows[$key][$name]);
                }
            }
        }

        return $normalizedRows;
    }

    /**
     * Retrieves a list of all the unique fields in all rows.
     *
     * @param array $rows   The target rows. By default, it uses the model's 'rows' property.
     * @param array $fields A starting set of fields. By default, it uses an empty array.
     * @return array
     */
    public function getUniqueFields(array $rows = [], array $fields = []) : array
    {
        $rows = $this->rows ?? $rows;
        $schemaFields = !empty($this->schema) ? array_keys($this->schema) : [];
        $fields = array_unique(array_merge($fields, $schemaFields));

        foreach ($rows as $row) {
            foreach (array_keys((array)$row) as $field) {
                if (!in_array($field, $fields)) {
                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * Whether the rows should be indexed by the primary key.
     *
     * @return bool
     */
    public function shouldIndexRows(): bool
    {
        return ($this->indexedRows ?? false) === true;
    }

    /**
     * Returns the key to be used as index.
     *
     * @return string|null
     */
    public function indexingKeyName(): ?string
    {
        return (($this->indexByInteger ?? false) === true || empty($this->primaryKey)) ? null : $this->primaryKey;
    }

    /**
     * Alias of setLinkedModel()
     *
     * @param Model $model
     * @return static
     */
    public static function linkModel(Model $model) : void
    {
        self::setLinkedModel($model);
    }

    /**
     * Sets the linked model.
     *
     * @param Model $model
     * @return static
     */
    public static function setLinkedModel(Model $model) : void
    {
        self::$linkedModel = $model;
        self::bootSushi();
    }

    public function getRows(): array
    {
        if( empty(self::$linkedModel) ){
            return [];
        }

        return $this->normalizeRows(self::$linkedModel->getSushiModelRows($this));
    }
}
