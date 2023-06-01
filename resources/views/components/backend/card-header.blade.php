<div {{ $attributes->merge(['class' => 'card-header px-2 py-1 bg-light d-flex']) }}>
    <div class="py-1 align-self-center flex-grow-1">
        <h5 class="m-0">{{ $slot }}</h5>
    </div>
    <div class="d-flex align-items-center">{{ $right ?? ''}}</div>
</div>