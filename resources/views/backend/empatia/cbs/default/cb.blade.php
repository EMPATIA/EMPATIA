@php
    use App\Helpers\HForm;
    $modelId = data_get($model ?? null, 'id', 0);
@endphp

@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection
@section('content_header')
@endsection

@section('content')
    <x-backend.body class="g-3 mt-n3">
        @if(HForm::isShow())
            <div class="col-12 col-lg-8">
                <div class="row g-3">
                    @include("backend.empatia.cbs.default.partials.details")
                    @include("backend.empatia.cbs.default.partials.topics")

                    {{-- TODO: temporary; bound to be removed --}}
                    @if( Auth::user()->hasAnyRole(['admin','laravel-admin']) )
                       {{-- @include('backend.empatia.cbs.default.partials._debug') --}}
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="row g-3">
                    @include('backend.empatia.cbs.default.partials.actions')
                </div>
            </div>
        @else
            <x-backend.card container="col-12 col-lg-8">
                <x-backend.card-header>
                    {{ $title }}

                    <x-slot:right>
                        <x-backend.form.header-btn
                            :list="route('cbs.index', ['type' => $type])"
                            :show="route('cbs.show', ['type' => $type, 'id' => $modelId])"
                            :edit="route('cbs.edit', ['type' => $type, 'id' => $modelId])"
                            :delete="$modelId"
                        />
                    </x-slot:right>
                </x-backend.card-header>

                <x-backend.card-body>
                    <x-backend.form
                        :store="route('cbs.store', ['type' => $type])"
                        :update="route('cbs.update', ['type' => $type, 'id' => $modelId])"
                    >
                        @bind($model ?? null)

                        @if($errors->any())
                            <div class="alert alert-danger" role="alert">
                                {{ __('backend.empatia.cbs.form.has-errors') }}
                            </div>
                            @dump($errors->all())
                        @endif
                        <x-backend.form.input-lang
                            :action="HForm::getAction()"
                            mandatory="true"
                            name="title"
                            :value="getField($model, 'title')"
                            :label="__('backend.empatia.cb.form.label.title')"
                            :placeholder="__('backend.empatia.cb.form.label.title')"
                        />
                        @if(!empty($cb) && HForm::isCreate($action ?? null))
                            <x-form-select
                                :action="HForm::getAction()"
                                name="type"
                                :options="$cbTypes ?? []"
                                :label="__('backend.empatia.cbs.form.type.label')"
                                :placeholder="__('backend.empatia.cbs.form.type.placeholder')"
                                disabled
                            >
                            </x-form-select>
                        @else
                            <x-form-select
                                :action="HForm::getAction()"
                                name="type"
                                :options="$cbTypes ?? []"
                                :label="__('backend.empatia.cbs.form.type.label')"
                                :placeholder="__('backend.empatia.cbs.form.type.placeholder')"
                            >
                            </x-form-select>
                        @endif
                        <x-backend.form.input
                            :action="HForm::getAction()"
                            name="template"
                            :label="__('backend.empatia.cbs.form.template.label')"
                            :placeholder="__('backend.empatia.cbs.form.template.placeholder')"
                        >
                        </x-backend.form.input>
                        <x-backend.form.input
                            :action="HForm::getAction()"
                            name="code"
                            :label="__('backend.empatia.cbs.form.code.label')"
                            :placeholder="__('backend.empatia.cbs.form.code.placeholder')"
                        >
                        </x-backend.form.input>
                        <x-backend.form.input
                            type="datetime-local"
                            :action="HForm::getAction()"
                            name="start_date"
                            :label="__('backend.empatia.cbs.form.start_date.label')"
                            :placeholder="__('backend.empatia.cbs.form.start_date.placeholder')"
                        >
                        </x-backend.form.input>
                        <x-backend.form.input
                            type="datetime-local"
                            :action="HForm::getAction()"
                            name="end_date"
                            :label="__('backend.empatia.cbs.form.end-date.label')"
                            :placeholder="__('backend.empatia.cbs.form.end-date.placeholder')"
                        >
                        </x-backend.form.input>
                        <x-backend.form.input-lang
                            :action="HForm::getAction()"
                            name="slug"
                            mandatory="true"
                            :value="getField($model, 'slug')"
                            :label="__('backend.empatia.cbs.form.slug.label')"
                            :placeholder="__('backend.empatia.cbs.form.slug.placeholder')"
                        />
                        <x-form-textarea
                            :action="HForm::getAction()"
                            name="content"
                            :lang="true"
                            :bind="['content' => data_lang_get($model, 'content')]"
                            :label="__('backend.empatia.:cbs.form.content.label')"
                            :placeholder="__('backend.empatia.cbs.form.content.placeholder')"
                        ></x-form-textarea>
                        <x-form-textarea
                            :action="HForm::getAction()"
                            name="data"
                            :bind="['data' => json_encode(data_get($model, 'data'))]"
                            :label="__('backend.empatia.cbs.form.data.label')"
                            :placeholder="__('backend.empatia.cbs.form.data.placeholder')"
                        ></x-form-textarea>
                        @endbind
                    </x-backend.form>
                </x-backend.card-body>

                <x-backend.form.submit
                    :create="route('cbs.index', ['type' => $type])"
                    :edit="route('cbs.show', ['type' => $type, 'id' => $modelId])"
                />
            </x-backend.card>
        @endif
    </x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/indexes.js"></script>
@endpush
