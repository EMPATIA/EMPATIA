<?php

namespace App\Objects;

use App\Objects\Empatia\AllowDynamicProperties;
use Exception;
use Illuminate\Support\Str;

#[AllowDynamicProperties]
class Parameter extends \stdClass
{
    const VALID_TYPES = [
        'textarea','select','checkbox','radio',
        'image','images','file','files',
        'location', 'geolocation',
        'color','date','datetime-local','email','hidden','month','number','password','search','tel','text','time','url','week'
    ];
    const REQUIRED_ATTRIBUTES = ['id', 'type', 'code', 'title', 'mandatory'];
    const TYPES_WITH_OPTIONS = ['select', 'checkbox', 'radio'];

    public bool                 $enabled        = true;
    public ?string              $type           = null;
    public ?string              $code           = null;
    public array|object|null    $title          = null;
    public array|object|null    $description    = null;
    public array|object|null    $placeholder    = null;
    public ?string              $rules          = null;
    public bool                 $mandatory      = false;
    public bool                 $multilang      = false;
    public bool                 $pii            = false;
    public ?array               $options;
    public array|object|null    $flags;

    /**
     * @throws Exception
     */
    public function __construct(array|object $attributes = null)
    {
        if ( !$this->hasRequiredAttributes($attributes) ) {
            throw new Exception('Parameter constructor: Missing required attributes');
        }

        if ( !in_array(data_get($attributes, 'type', null), self::VALID_TYPES) ) {
            throw new Exception('Parameter constructor: Invalid type');
        }

        foreach ($attributes ?? [] as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * Checks whether a parameter has the required attributes.
     *
     * @param array|object $attributes  The parameter
     * @return bool
     */
    public static function hasRequiredAttributes(array|object $attributes): bool
    {
        $attributes = !is_array($attributes) ? (array)$attributes : $attributes;

        return
            count(array_intersect_key(self::REQUIRED_ATTRIBUTES, array_keys($attributes)))
            === count(self::REQUIRED_ATTRIBUTES);
    }

    /**
     * Add option to parameter options.
     *
     * @param array|object $properties
     * @return bool
     */
    public function addOption(array|object $properties) : bool
    {
        if( !in_array($this->type, self::TYPES_WITH_OPTIONS) ) {
            return false;
        }

        $properties = !is_array($properties) ? (array)$properties : $properties;

        if( empty($properties['label']) || !is_array($properties['label']) ) {
            return false;
        }

        $option = (object)$properties;

        // process code
        $option->code = $option->code ?? null;
        if( !is_string($option->code) || (!empty($option->code) && $option->code !== Str::slug($option->code, '_')) ){
            $option->code = null;
        }
        $option->code = (empty($option->code) || !is_string($option->code)) ?
            Str::slug(data_lang_get($option, 'label', null, true)) :
            $option->code;
        if( empty($option->code) ) {
            return false;
        }

        // check if option already exists
        if( findObjectByProperty('code', $option->code, $this->options) ){
            return false;
        }

        if( empty($this->options) ){
            $this->options = [];
        }
        $this->options[] = $option;

        return true;
    }

    /**
     * Toggle the parameter enabled state.
     *
     * @param bool|null $state
     * @return bool
     */
    public function toggle(bool $state = null) : bool
    {
        return $this->enabled = $state ?? !$this->enabled;
    }
}
