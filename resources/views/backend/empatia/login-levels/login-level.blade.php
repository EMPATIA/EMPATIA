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
            :store="route('login-levels.store')"
            :update="route('login-levels.update', ['id' => getField($loginLevel, 'id', 0)])"
        >
            @bind($loginLevel)

            <x-backend.card-header>
                {{ $title }}

                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('login-levels.index')"
                        :show="route('login-levels.show', ['id' => getField($loginLevel, 'id', 0)])"
                        :edit="route('login-levels.edit', ['id' => getField($loginLevel, 'id', 0)])"
                        :delete="route('login-levels.delete', ['id' => getField($loginLevel, 'id', 0)])"
                    />
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>

                <x-backend.form.errors />

                @if(HForm::isShow())
                    <x-backend.form.input 
                        name="id"
                        mandatory="true"
                        action="show"
                        :value="getField($loginLevel, 'id')"
                        :label="__('backend.generic.id')"
                        :placeholder="__('backend.generic.id')"
                    />
                @endif
                
                
                <x-backend.form.input 
                    name="code"
                    mandatory="true"
                    :value="getField($loginLevel, 'code')"
                    :label="__('backend.generic.code')"
                    :placeholder="__('backend.generic.code')"
                />

                <x-backend.form.input-lang 
                    name="name"
                    mandatory="true"
                    :value="getField($loginLevel, 'name')"
                    :label="__('backend.generic.name')"
                    :placeholder="__('backend.generic.name')"
                />

                <x-backend.form.input-lang
                    name="dependencies"
                    mandatory="true"
                    :value="getField($loginLevel, 'dependencies')"
                    :label="__('backend.empatia.login-levels.dependencies')"
                    :placeholder="__('backend.empatia.login-levels.dependencies')"
                />

            </x-backend.card-body>

            <x-backend.form.submit
                :create="route('login-levels.index')"
                :edit="route('login-levels.show', ['id' => getField($loginLevel, 'id', 0)])"
            />
            
            @endbind
        </x-backend.form>
    </x-backend.card>
</x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/model-delete.js"></script>
@endpush