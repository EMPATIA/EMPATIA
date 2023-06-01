<?php

namespace App\View\Components\FormComponents;

use ProtoneMedia\LaravelFormComponents\Components;

class FormMap extends Components\Component
{
    use Components\HandlesValidationErrors;
    use Components\HandlesDefaultAndOldValue;

    public string $name;
    public string $label;
    public string $type;
    public bool $floating;

    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        string $name,
        string $label = '',
        string $type = 'text',
               $bind = null,
               $default = null,
               $language = null,
        bool $showErrors = true,
        bool $floating = false
    ) {
        $this->name       = $name;
        $this->label      = $label;
        $this->type       = $type;
        $this->showErrors = $showErrors;
        $this->floating   = $floating && $type !== 'hidden';

        if ($language) {
            $this->name = "{$name}[{$language}]";
        }

        $this->setValue($name, $bind, $default, $language);
    }
}
