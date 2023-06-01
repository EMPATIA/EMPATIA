@php
    use App\Helpers\HForm;
@endphp

@if(HForm::isEdit() || HForm::isCreate())
    <x-backend.card-footer>
        @if(!empty($create) && HForm::getAction($action ?? null) == HForm::$CREATE)
            <x-backend.form.btn-cancel class="col-12 col-sm-auto my-1 my-sm-0" href="{{ $create }}">{{ __('backend.generic.cancel') }}</x-backend.form.btn-cancel>
        @elseif(!empty($edit) && HForm::getAction($action ?? null) == HForm::$EDIT)
            <x-backend.form.btn-cancel class="col-12 col-sm-auto my-1 my-sm-0" href="{{ $edit }}">{{ __('backend.generic.cancel') }}</x-backend.form.btn-cancel>
        @endif

        <x-backend.form.btn-submit class="col-12 col-sm-auto my-1 my-sm-0">{{ __('backend.generic.submit') }}</x-backend.form.btn-submit>
    </x-backend.card-footer>
@endif