@props(['container' => 'col-12'])
<div class="{{ $container }}">
    <div {{ $attributes->merge(["class" => "card"]) }}>
        {{ $slot }}
    </div>
</div>