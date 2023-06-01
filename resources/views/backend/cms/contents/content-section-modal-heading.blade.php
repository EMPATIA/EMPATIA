@foreach(getLanguagesFrontend() as $language)
    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.heading.label').' ('.$language['name'].')' }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale']) is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}">
        </div>
        @error('value.'.$language['locale'])
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
@endforeach
<div class=" form-group ">
    <label>{{ __('backend.cms.contents.show.modal.heading-number.label') }}</label>
    <div class="input-group">
        <input class="form-control @error('value.heading') is-invalid @enderror" type="text" wire:model.lazy="value.heading">
    </div>
    @error('value.heading')
    <div class="error invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
