@foreach(getLanguagesFrontend() as $language)
    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.text.label').' ('.$language['name'].')' }}</label>
        <div class="input-group">
            <textarea class="form-control" type="text" rows="5" wire:model.lazy="value.{{ $language["locale"] }}"></textarea>
        </div>
    </div>
@endforeach
