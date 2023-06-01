<x-backend.card>
    <x-backend.card-header>
        {{ __('backend.cms.contents.show.seo.header') }}

        <x-slot:right>
            <x-backend.btn 
                    class="btn-light"
                :title="__('backend.generic.edit')"
                wire:click="$emitTo('livewire.backend.c-m-s.content.content-modal-seo', 'loadModal')"
            >
                <i class="fas fa-edit"></i>
            </x-backend.btn>
        </x-slot:right>
    </x-backend.card-header>

    <x-backend.card-body class="overflow-auto" style="max-height:30vh; min-height: 150px;">
        <div class="row">
            @php
                $seo = getField($content, 'seo', []);
            @endphp
            @foreach(getField(App\Helpers\HContent::getContentConfigurations(getField($content, 'type')), 'seo', []) as $type => $group)
                @foreach($group as $code => $field)
                    <div class="col-12 mb-2">
                        <x-backend.form.input
                            :value="(empty(getField($field, 'locale'))) ? getFieldLang($seo, 'code', App\Helpers\HContent::getSeoDefault($code, $field, $content, $seo, $lang)) : getField($seo, '$code', App\Helpers\HContent::getSeoDefault($code, $field, $content, $seo, $lang))"
                            :label="$code"
                            readonly="true"
                        />
                    </div>
                @endforeach
            @endforeach
        </div>
    </x-backend.card-body>
</x-backend.card>
