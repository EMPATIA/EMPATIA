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
            :store="route('cms.languages.store')"
            :update="route('cms.languages.update', ['id' => getField($language, 'id', 0)])"
        >
            @bind($language)

            <x-backend.card-header>
                {{ $title }}

                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('cms.languages.index')"
                        :show="route('cms.languages.show', ['id' => getField($language, 'id', 0)])"
                        :edit="route('cms.languages.edit', ['id' => getField($language, 'id', 0)])"
                        :delete="getField($language, 'id')"
                    />
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>

                <x-backend.form.errors />

                <x-backend.form.input 
                    name="locale"
                    mandatory="true"
                    :value="getField($language, 'locale')"
                    :label="__('backend.generic.locale')"
                    :placeholder="__('backend.generic.locale')"
                />
                
                <x-backend.form.input 
                    name="name"
                    mandatory="true"
                    :value="getField($language, 'name')"
                    :label="__('backend.generic.name')"
                    :placeholder="__('backend.generic.name')"
                />
                
                <x-form-group :label="__('backend.generic.enabled')">
                    <x-form-checkbox class="form-check-input" name="default" :disabled="HForm::isShow()" :label="__('backend.cms.languages.form.default.label')" />
                    <x-form-checkbox class="form-check-input" name="backend" :disabled="HForm::isShow()" :label="__('backend.cms.languages.form.backend.label')" />
                    <x-form-checkbox class="form-check-input" name="frontend" :disabled="HForm::isShow()" :label="__('backend.cms.languages.form.frontend.label')" />
                </x-form-group>
            </x-backend.card-body>

            <x-backend.form.submit
                :create="route('cms.languages.index')"
                :edit="route('cms.languages.show', ['id' => getField($language, 'id', 0)])"
            />
            
            @endbind
        </x-backend.form>
    </x-backend.card>
</x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/model-delete.js"></script>
@endpush