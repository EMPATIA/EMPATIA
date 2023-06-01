@foreach(getLanguagesFrontend() as $language)
    <div class=" form-group ">
        <label class="font-weight-bold">{{ $language['name'] }}</label>
    </div>

    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.button.title.label') }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale'].'.title') is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}.title">
        </div>
        @error('value.'.$language['locale'].'.title')
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.button.link.label') }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale'].'.link') is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}.link">
        </div>
        @error('value.'.$language['locale'].'.link')
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.button.first.label') }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale'].'.first') is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}.first">
        </div>
        @error('value.'.$language['locale'].'.first')
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.button.second.label') }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale'].'.second') is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}.second">
        </div>
        @error('value.'.$language['locale'].'.second')
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class=" form-group ">
        <label>{{ __('backend.cms.contents.show.modal.button.third.label') }}</label>
        <div class="input-group">
            <input class="form-control @error('value.'.$language['locale'].'.third') is-invalid @enderror" type="text" wire:model.lazy="value.{{ $language["locale"] }}.third">
        </div>
        @error('value.'.$language['locale'].'.third')
            <div class="error invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    @if(!$loop->last)
        <hr>
    @endif
@endforeach
