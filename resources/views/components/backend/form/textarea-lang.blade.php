@props(['name' => '', 'label' => '', 'action' => null, 'mandatory' => false, 'placeholder' => '', 'placeholdershow' => '-', 'value' => ''])

@php
    use App\Helpers\HForm;

    $isComponent = false;
    $separator = "->";

    // Check if blade is being called inside a Livewire Component
    if (isset($this) && !empty(class_parents($this))){
        if( array_values(class_parents($this))[0] == "Livewire\Component"){
            $isComponent = true;
            $separator = ".";
        }
    }
@endphp

@foreach(getLanguagesFrontend() ?? [] as $language)
    <div class="input-language {{ ($lang ?? getLang())== getField($language, 'locale') ? '' : 'd-none' }}" data-lang="{{ getField($language, 'locale') }}">
        @include("components.backend.form.language-selector", ['selectorLang' => ($lang ?? getLang()), 'isComponent' => $isComponent, 'separator' => $separator])

        @php
            $inputName = $name.$separator.getField($language, 'locale');
            $inputValue = HForm::getFromInputValue($value, true, getField($language, 'locale'), $attributes->get('default'));
        @endphp

        <x-form-textarea
                :name="$inputName"
                :label="$label.($mandatory === 'true' ? '*' : '').' ('.getField($language, 'name').')'"
                :placeholder="HForm::getInputPlaceholder($placeholder, $placeholdershow)"
                :class="$class ?? HForm::getInputClass(HForm::getAction())"
                :lang="getField($language, 'locale')"
                :readonly="$readonly ?? HForm::getInputReadonly()"
                :bind="[$inputName => !empty($inputValue) ? $inputValue : old($inputName)]"
                style="min-height: 150px;"
        />
    </div>
@endforeach
