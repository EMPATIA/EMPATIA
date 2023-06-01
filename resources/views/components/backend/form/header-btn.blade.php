@if(!empty($delete) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$SHOW)
    <x-backend.btn-delete class="delete-entry-show" data-delete={{$delete}} data-index={{App\Helpers\HForm::getActionIndex()}}/>
@endif

@if(!empty($create) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$INDEX)
    <x-backend.btn-create :href="$create" />
    @endif

@if(!empty($edit) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$SHOW)
    <x-backend.btn-edit :href="$edit" />
@endif

{{-- Cancel buttons --}}
@if(!empty($list) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$CREATE)
    <x-backend.btn-cancel :href="$list" />
@endif

@if(!empty($show) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$EDIT)
    <x-backend.btn-cancel :href="$show" />
@endif

{{-- Back button --}}
@if(!empty($list) && App\Helpers\HForm::getAction($action ?? null) == App\Helpers\HForm::$SHOW)
    <x-backend.btn-back :href="$list" />
@endif
