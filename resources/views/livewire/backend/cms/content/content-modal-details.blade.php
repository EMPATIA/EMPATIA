<div class="modal-content">
    <x-backend.modal-header>
        {{ __('backend.cms.contents.show.details.header') }}
    </x-backend.modal-header>

    <x-backend.modal-body>
        <x-backend.form.input 
            :label="__('backend.generic.code')"
            :placeholder="__('backend.generic.code')"
            :action="App\Helpers\HForm::$EDIT"
            wire:model.lazy="code"
        />

        @foreach(getLanguagesFrontend() as $language)
            <x-backend.form.input 
                :label="__('backend.generic.title').' ('.$language['name'].')'"
                :placeholder="__('backend.generic.title').' ('.$language['name'].')'"
                :action="App\Helpers\HForm::$EDIT"
                :wire:model.lazy="'title.'.$language['locale']"
            />
        @endforeach
        
        @foreach(getLanguagesFrontend() as $language)
            <x-backend.form.input 
                :label="__('backend.generic.slug').' ('.$language['name'].')'"
                :placeholder="__('backend.generic.slug').' ('.$language['name'].')'"
                :action="App\Helpers\HForm::$EDIT"
                :wire:model.lazy="'slug.'.$language['locale']"
            />
        @endforeach

        <x-backend.form.input 
            :label="__('backend.cms.contents.show.details.tags.label')"
            :placeholder="__('backend.cms.contents.show.details.tags.placeholder')"
            :action="App\Helpers\HForm::$EDIT"
            wire:model.lazy="tags"
        />

        @foreach(App\Helpers\HContent::getContentConfigurations($contentType ?? '')->fields ?? [] as $code => $field)
            <x-backend.form.input 
                :label="__('backend.cms.contents.show.details.fields.'.$code.'.label')"
                :placeholder="__('backend.cms.contents.show.details.fields.'.$code.'.placeholder')"
                :action="App\Helpers\HForm::$EDIT"
                :wire:model.lazy="'fields.'.$code"
            />
        @endforeach
    </x-backend.modal-body>

    <x-backend.modal-footer close="true" />
</div>
