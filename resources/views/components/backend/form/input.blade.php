@props(['name' => '', 'label' => '', 'action' => null, 'mandatory' => false, 'placeholder' => '', 'placeholdershow' => '-'])
<x-form-input
    :name="$name"
    :label="$label.($mandatory ? '*' : '')"
    :placeholder="App\Helpers\HForm::getInputPlaceholder($placeholder, $placeholdershow, $action)"
    :class="$class ?? App\Helpers\HForm::getInputClass($action)"
    :readonly="$readonly ?? App\Helpers\HForm::getInputReadonly($action)"
    :value="$value ?? old($name)"

    {{ $attributes }}
/>
