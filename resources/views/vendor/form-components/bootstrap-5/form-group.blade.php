<div {!! $attributes->merge(['class' => ($hasError($name) ? 'is-invalid' : '')]) !!}>
    <x-form-label class="mb-0 text-muted small" :label="$label" :for="$name" />

    <div class="@if($inline) d-flex flex-row flex-wrap inline-space @endif">
        {!! $slot !!}
    </div>

    {!! $help ?? null !!}

    @if($hasErrorAndShow($name))
        <x-form-errors :name="$name" class="d-block" />
    @endif
</div>
