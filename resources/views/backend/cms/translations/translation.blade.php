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
        <x-backend.card container="col-12 col-md-10 col-lg-7">
            <x-backend.form
                    :store="route('cms.translations.store')"
            >
                <x-backend.card-header>
                    {{ $title }}

                    <x-slot:right>
                        <x-backend.form.header-btn
                                :list="route('cms.translations.index')"
                        />
                    </x-slot:right>
                </x-backend.card-header>

                <x-backend.card-body>

                    <x-backend.form.errors />

                    <x-backend.form.input
                            name="namespace"
                            mandatory="true"
                            :label="__('backend.cms.translations.generic.namespace')"
                            :placeholder="__('backend.cms.translations.generic.namespace')"
                    />

                    <x-backend.form.input
                            name="group"
                            mandatory="true"
                            :label="__('backend.cms.translations.generic.group')"
                            :placeholder="__('backend.cms.translations.generic.group')"
                    />

                    <x-backend.form.input
                            name="item"
                            mandatory="true"
                            :label="__('backend.cms.translations.generic.item')"
                            :placeholder="__('backend.cms.translations.generic.item')"
                    />

                    <x-backend.form.input-lang
                            name="text"
                            mandatory="true"
                            :label="__('backend.cms.translations.generic.text')"
                            :placeholder="__('backend.cms.translations.generic.text')"
                    />
                </x-backend.card-body>

                <x-backend.form.submit
                        :create="route('cms.translations.index')"
                />
            </x-backend.form>
        </x-backend.card>
    </x-backend.body>
@endsection