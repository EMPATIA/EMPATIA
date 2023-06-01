@foreach(getLanguagesFrontend() as $language)
    <div class="form-group mb-5">
        <label>{{ __('backend.cms.contents.show.sections.text-html.title').' ('.$language['name'].')' }}</label>
        <div class="input-group">
            <textarea class="form-control livewire-html-editor" type="text" data-name="{{ "value.".$language['locale'] }}" wire:model.lazy="value.{{ $language['locale'] }}"></textarea>
        </div>
    </div>
@endforeach
