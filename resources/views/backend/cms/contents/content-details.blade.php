<x-backend.card>
    <x-backend.card-header>
        {{ __('backend.cms.contents.show.details.header') }}

        <x-slot:right>
            <x-backend.btn 
                class="btn-light"
                title="{{ __('backend.generic.edit') }}"
                wire:click="$emitTo('livewire.backend.c-m-s.content.content-modal-details', 'loadModal')"
            >
                <i class="fas fa-edit"></i>
            </x-backend.btn>
        </x-slot:right>
    </x-backend.card-header>

    <x-backend.card-body>
        <div class="row">
            <div class="col-12">
                <x-backend.form.input
                    :value="getField($content, 'code', '-')"
                    :label="__('backend.generic.code')"
                    readonly="true"
                />
            </div>
            <div class="col-12">
                <x-backend.form.input
                    :value="getFieldLang($content, 'title', '-')"
                    :label="__('backend.generic.title')"
                    readonly="true"
                />
            </div>
            <div class="col-12">
                <x-backend.form.input
                    :value="getFieldLang($content, 'slug', '-')"
                    :label="__('backend.generic.slug')"
                    readonly="true"
                />
            </div>
            <div class="col-12">
                <x-backend.form.input
                    :value="getField($content, 'tags', '-')"
                    :label="__('backend.generic.tags')"
                    readonly="true"
                />
            </div>

            @foreach(getField(App\Helpers\HContent::getContentConfigurations($content->type), "fields", []) as $code => $field)
                <div class="col-12">
                    <x-backend.form.input
                        :value="getField($content, 'options.fields.' . $code, '-')"
                        :label="__('backend.cms.contents.show.details.tags.'.$code)"
                        readonly="true"
                    />
                </div>
            @endforeach

            <div class="col-12">
                <x-backend.form.input
                    :value="getField($content, 'version', '-')"
                    :label="__('backend.generic.version')"
                    readonly="true"
                />
            </div>

        </div>
    </x-backend.card-body>
</x-backend.card>