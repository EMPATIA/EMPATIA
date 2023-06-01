@props(['id', 'container' => 'modal fade modal-section'])
<div class="{{ $container }}" id="{{ $id }}" tabindex="-1">
    <div {{ $attributes->merge(['class' => 'modal-dialog modal-dialog-centered modal-dialog-scrollable']) }} role='document'>
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
