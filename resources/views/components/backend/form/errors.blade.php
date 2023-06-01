@if($errors->has('error'))
<div class="alert alert-danger" role="alert">
    {{ $errors->first('error') }}
</div>
@elseif($errors->any())
<div class="alert alert-danger" role="alert">
    {{ __('backend::form.has-errors') }}
</div>
@endif
