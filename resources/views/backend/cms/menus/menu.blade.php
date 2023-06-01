@php
    use App\Helpers\HForm;
@endphp

@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
@endsection

@section('content')
    <x-backend.body>
        <x-backend.card container="col-12 col-md-7 col-lg-6 col-xl-4">
            <x-backend.form
                    :store="route('cms.menus.store')"
            >
                <x-backend.card-header>
                    {{ $title }}

                    <x-slot:right>
                        <x-backend.form.header-btn
                                :list="route('cms.menus.index')"
                        />
                    </x-slot:right>
                </x-backend.card-header>

                <x-backend.card-body>

                    <x-backend.form.errors />
                    
                    <x-backend.form.input
                            name="code"
                            :label="__('backend.generic.code')"
                            :placeholder="__('backend.generic.code')"
                    />

                    <x-backend.form.input-lang
                            name="title"
                            mandatory="true"
                            :label="__('backend.generic.title')"
                            :placeholder="__('backend.generic.title')"
                    />

                    <x-form-select
                            icon="chevron-down"
                            name="menu_type"
                            mandatory="true"
                            :options="$menuTypeOptions"
                            :label="__('backend.cms.menus.filters.menu-type.filter')"/>


                    <x-form-select
                            icon="chevron-down"
                            name="parent_id"
                            :options="$menuParentOptions"
                            :label="__('backend.cms.menus.filters.menu-parent.filter')"
                    />

                    <x-backend.form.input-lang
                            name="link"
                            :label="__('backend.generic.link')"
                            :placeholder="__('backend.generic.link')"
                    />

                    <x-backend.form.input-lang
                            name="options"
                            :label="__('backend.generic.options')"
                            :placeholder="__('backend.generic.options')"
                    />
                </x-backend.card-body>

                <x-backend.form.submit
                        :create="route('cms.menus.index')"
                />
            </x-backend.form>
        </x-backend.card>
    </x-backend.body>
@endsection