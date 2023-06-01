@props(['store' => '', 'update' => '', 'method' => null, 'action' => ''])

@php
    use App\Helpers\HForm;
@endphp

<x-form :action="HForm::getFormAction($store, $update)" :method="$method ?? HForm::getFormMethod($action)">
    {{ $slot }}
</x-form>