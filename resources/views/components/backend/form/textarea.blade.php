@php
    use App\Helpers\HForm;
@endphp

@props(['name' => '', 'value' => '', 'label' => '', 'action' => null, 'mandatory' => false, 'placeholder' => '', 'placeholdershow' => '-'])

<x-form-textarea
        :name="$name"
        :label="$label.($mandatory ? '*' : '')"
        :placeholder="App\Helpers\HForm::getInputPlaceholder($placeholder, $placeholdershow, $action)"
        :class="$class ?? App\Helpers\HForm::getInputClass($action)"
        :readonly="$readonly ?? App\Helpers\HForm::getInputReadonly($action)"
        :value="HForm::getFromInputValue($value) ?? old($name)"
        style="min-height: 150px;"

        {{ $attributes }}
/>