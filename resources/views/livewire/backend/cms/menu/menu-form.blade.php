@php
    $disabled = $action == 'show';
@endphp

<x-backend.card container="col-12 col-md-6 col-lg-4 mt-3 mt-md-0">
    <x-backend.card-header>
        {{ __('backend.cms.menus.form.title') }}

        <x-slot:right>
            @if(!empty($menu) && $action=='show')
                <x-backend.btn-edit wire:click="editMenu()"/>
                <x-backend.btn-delete wire:click="deleteMenu()"/>
            @elseif(!empty($menu) && $action=='edit')
                <x-backend.btn-cancel wire:click="showMenu()"/>
            @endif
        </x-slot:right>
    </x-backend.card-header>
    
    <x-form wire:submit.prevent="updateMenu">
        <x-backend.card-body class="p-3">
            @if(empty($menu))
                <h5 class="mb-0">{{ __('backend.cms.menus.form.no-menu-selected') }}</h5>
            @else
                <x-backend.form.errors/>

                @if($action == 'show')
                    <x-backend.form.input
                            name="id"
                            :value="getField($menu, 'id')"
                            :label="__('backend.generic.id')"
                            :placeholder="__('backend.generic.id')"
                    />
                @endif

                <x-backend.form.input
                        wire:model="code"
                        :action="$action"
                        name="code"
                        :label="__('backend.generic.code')"
                        :placeholder="__('backend.generic.code')"
                />

                <x-backend.form.input-lang
                        wire:model="title"
                        :lang="$selectorLanguage"
                        :action="$action"
                        name="title"
                        mandatory="true"
                        :label="__('backend.generic.title')"
                        :placeholder="__('backend.generic.title')"
                />

                <x-form-select
                        wire:model="menu_type"
                        :action="$action"
                        icon="chevron-down"
                        name="menuType"
                        mandatory="true"
                        :options="$menuTypeOptions"
                        :disabled="$disabled"
                        :label="__('backend.cms.menus.filters.menu-type.filter')"/>

                @if($parent_id != 0)
                    <x-form-select
                            wire:model="parent_id"
                            :action="$action"
                            icon="chevron-down"
                            name="menuParentId"
                            mandatory="true"
                            :options="$menuParentOptions"
                            :disabled="$disabled"
                            :label="__('backend.cms.menus.filters.menu-parent.filter')"/>
                @endif
            
                <x-backend.form.input-lang
                        wire:model="link"
                        :lang="$selectorLanguage"
                        :action="$action"
                        name="link"
                        :label="__('backend.generic.link')"
                        :placeholder="__('backend.generic.link')"
                />
            
                <x-backend.form.input-lang
                        wire:model="options"
                        :lang="$selectorLanguage"
                        :action="$action"
                        name="options"
                        :label="__('backend.generic.options')"
                        :placeholder="__('backend.generic.options')"
                />

                {{--TODO: Select2 com as roles--}}

            @endif
        </x-backend.card-body>
        
        @if($action == 'edit')
            <x-backend.card-footer>
                <x-backend.form.btn-submit>{{ __('backend.generic.submit') }}</x-backend.form.btn-submit>
            </x-backend.card-footer>
        @endif
        
    </x-form>
</x-backend.card>
