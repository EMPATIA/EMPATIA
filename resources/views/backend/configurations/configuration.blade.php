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
        <x-backend.card container="col-12 col-md-7 col-lg-6">
            <x-backend.form
                    :store="route('configurations.store')"
                    :update="route('configurations.update', ['id' => getField($configuration, 'id', 0)])"
            >
                @bind($configuration)

                <x-backend.card-header>
                    {{ $title }}

                    <x-slot:right>
                        <x-backend.form.header-btn
                                :list="route('configurations.index')"
                                :show="route('configurations.show', ['id' => getField($configuration, 'id', 0)])"
                                :edit="route('configurations.edit', ['id' => getField($configuration, 'id', 0)])"
                                :delete="getField($configuration, 'id')"
                        />
                    </x-slot:right>
                </x-backend.card-header>

                <x-backend.card-body>

                    <x-backend.form.errors />

                    <x-backend.form.input
                            name="code"
                            mandatory="true"
                            :value="getField($configuration, 'code')"
                            :label="__('backend.generic.code')"
                            :placeholder="__('backend.generic.code')"
                    />

                    <x-backend.form.textarea
                            name="configurations"
                            mandatory="false"
                            :lang="true"
                            :value="!HForm::isCreate() ? json_encode(getField($configuration, 'configurations'), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : ''"
                            :label="__('backend.generic.configurations')"
                            :placeholder="__('backend.generic.configurations')"
                    />
                </x-backend.card-body>

                <x-backend.form.submit
                        :create="route('configurations.index')"
                        :edit="route('configurations.show', ['id' => getField($configuration, 'id', 0)])"
                />

                @endbind
            </x-backend.form>
        </x-backend.card>
    </x-backend.body>
@endsection

