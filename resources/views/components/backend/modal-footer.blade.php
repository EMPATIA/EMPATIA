@props(['close' => false])
<div {{ $attributes->merge(['class' => 'modal-footer bg-light p-2']) }}>
    {{ $slot }}

    @if($close)
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('backend.generic.close') }}</button>
    @endif
</div>
