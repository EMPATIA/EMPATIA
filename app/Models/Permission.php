<?php

namespace App\Models;

use App\Traits\SushiModel;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use SushiModel;

    protected $schema = [
        'id'            => 'integer',
        'key'           => 'string',
        'action'        => 'string',
        'roles'         => 'json',
    ];

    protected $casts = [
        'key'           => 'string',
        'action'        => 'string',
        'roles'         => 'object',
        'name'          => 'string',
        'description'   => 'string',
    ];

    public function getRows()
    {
        $config = self::config();
        $db = app("permissions") ?? [];

        $permissions = array_values(self::flattenSourceArray(array_merge($config, $db)));

        return $this->normalizeRows($permissions);
    }

    /**
     * Get the configuration clean of any excess keys.
     *
     * Excess keys come from config() function returning all folder files with the same name.
     *
     * @return array
     */
    public static function config() : array
    {
        $excludedKeys = [];
        $config = config("permissions", []);

        try {
            // get permissions config files to extract their names and exclude from config array
            $excludedKeys = collect(\File::files(config_path('permissions')))
                ->map(fn($item) => $item?->getFileName())
                ->filter(fn($item) => stripos($item, '.php'))
                ->map(fn($item) => str_replace('.php', '', $item ?? ''))
                ->unique()
                ->toArray();
        } catch ( \Exception $e ){
        }

        foreach ($excludedKeys as $key) {
            if( data_get($config, $key) != null ) data_unset($config, $key);
        }

        return $config;
    }

    /**
     * Gets a permission by its key.
     *
     * @param string $key
     * @return Permission|null
     */
    public function byKey(string $key) : ?Permission
    {
        return Permission::whereKey($key)->first();
    }


    /**
     * Checks wether a given object is a valid permission.
     *
     * @param mixed $permission
     * @return bool
     */
    public static function isValid(mixed $permission) : bool
    {
        return is_array($permission) && isset($permission['roles']);
    }

    /**
     * Retrieves an array of permission objects.
     *
     * @param mixed $array
     * @param array $levels
     * @return array
     */
    public static function flattenSourceArray(mixed $array, array $levels = []) : array
    {
        $result = [];

        if( !is_array($array) ){
            return $result;
        }

        foreach ($array as $key => $item) {
            $itemLevels = $levels;
            $itemLevels[] = $key;

            if( !is_array($item) ){
                continue;
            } else if( self::isValid($item) ){
                $item['key'] = implode('.', $itemLevels);
                $item['action'] = $key;
                $result[ $item['key'] ] = $item;
            } else {
                $values = self::flattenSourceArray($item, $itemLevels);

                foreach ($values as $valueKey => $value) {
                    $valueLevels = $itemLevels;
                    $valueLevels[] = $valueKey;
                    $value['key'] = $value['key'] ?? implode('.', $valueLevels);
                    $result[ $value['key'] ] = $value;
                }
            }
        }

        return $result;
    }

}
