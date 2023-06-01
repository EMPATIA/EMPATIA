@php
    use App\Helpers\HForm;
@endphp
@props(["readonly" => HForm::getInputReadonly(), "label", "name"])
<div class="form-check">
    <input id="checkbox_{{ $name }}" name="{{ $name }}" type="checkbox" {{ $attributes->merge(["class" => "form-check-input"]) }} {!! $readonly ? 'onclick="return false;"' : '' !!}>
    <label for="checkbox_{{ $name }}" class="form-check-label">{{ $label }}</label>
</div>
