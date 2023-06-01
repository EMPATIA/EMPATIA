<div class="modal-content">
    <x-backend.modal-header>
        {{ empty($sectionName) ? __('backend.cms.contents.show.sections.config.'.$type.'.title') : $sectionName }}
    </x-backend.modal-header>

    <x-backend.modal-body>

        <x-backend.form.input
            :label="__('backend.generic.code')"
            :placeholder="__('backend.generic.code')"
            :action="App\Helpers\HForm::$EDIT"
            wire:model.lazy="code"
        />

        <x-backend.form.input
            :label="__('backend.cms.contents.show.modal.class.label')"
            :placeholder="__('backend.cms.contents.show.modal.class.label')"
            :action="App\Helpers\HForm::$EDIT"
            wire:model.lazy="class"
        />

        <x-backend.form.input
            :label="__('backend.generic.options')"
            :placeholder="__('backend.generic.options')"
            :action="App\Helpers\HForm::$EDIT"
            wire:model.lazy="options"
        />

        @foreach(getLanguagesFrontend() as $language)
            <x-backend.form.input
                :label="__('backend.generic.name').' ('.$language['name'].')'"
                :placeholder="__('backend.generic.name').' ('.$language['name'].')'"
                :action="App\Helpers\HForm::$EDIT"
                :wire:model.lazy="'name.'.$language['locale']"
            />
        @endforeach
        
        <small>{{__('backend.cms.contents.show.modal.custom-cms-classes.label')}}
            <span title={{__('backend.cms.contents.show.modal.custom-cms-classes.title')}} data-bs-toggle="tooltip" data-placement="bottom">
                {{__('backend.cms.contents.show.modal.custom-cms-classes.options')}}
            </span>
        </small>

    </x-backend.modal-body>

    <x-backend.modal-footer close="true" />
</div>
