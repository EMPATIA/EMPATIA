<div {{ $attributes->merge(['class' => 'card-footer p-2 d-flex justify-content-center justify-content-sm-end']) }}>
    <div class="py-1 align-self-center">
        {{ $slot }}
    </div>
    <div>{{ $right ?? ''}}</div>
</div>