@extends('backend.layouts.master')

@php
    use App\Helpers\HForm;
    
    $action = HForm::getAction();
@endphp

@section('title')
    @if(HForm::isEdit($action ?? null))
        {{ $title ?? '-' }}
    @elseif(HForm::isCreate($action ?? null))
        {{ $title ?? '-' }}
    @else
        {{ $title ?? '-' }}
    @endif
@endsection

@section('content_header')
@endsection


@section('content')
    <x-backend.body>
        <x-backend.card container="col-12 col-md-10 col-lg-7">
            <x-backend.form
                    :store="route('cbs.technical-analysis-questions.store', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id])"
                    :update="route('cbs.technical-analysis-questions.update', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id, 'code' => getField($question, 'code', 0)])"
            >
                
                @bind($question)
                <x-backend.card-header>
                    {{ $title }}
                    <x-slot:right>
                        <x-backend.form.header-btn
                                :list="route('cbs.technical-analysis-questions.index', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id])"
                                :show="route('cbs.technical-analysis-questions.show', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id, 'code' => getField($question, 'code', 0)])"
                                :edit="route('cbs.technical-analysis-questions.edit', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id, 'code' => getField($question, 'code', 0)])"
                                :delete="getField($question, 'code')"
                        />
                    </x-slot:right>
                </x-backend.card-header>

                <x-backend.card-body>
                    <x-backend.form.errors />

                    <x-backend.form.input
                            name="code"
                            mandatory="true"
                            :value="getField($question, 'code')"
                            :label="__('backend.generic.code')"
                            :placeholder="__('backend.generic.code')"
                    />

                    <x-form-select
                            name="type"
                            :action="$action"
                            icon="chevron-down"
                            mandatory="true"
                            :options="$questionTypeOptions"
                            :label="__('backend.generic.type')"
                    />

                    <x-backend.form.input-lang 
                            name="value"
                            mandatory="true"
                            :value="getField($question, 'value')"
                            :label="__('backend.generic.value')"
                            :placeholder="__('backend.generic.value')"
                    />

                    <x-form-checkbox
                            name="enabled" 
                            :switch="true"
                            :disabled="HForm::isShow()" 
                            :label="__('backend.generic.enabled')" />
                    
                </x-backend.card-body>

                <x-backend.form.submit
                        :create="route('cbs.technical-analysis-questions.index', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id])"
                        :edit="route('cbs.technical-analysis-questions.show', ['type' => $cb->type ?? 'all', 'cbId' => $cb->id, 'code' => getField($question, 'code', 0)])"
                />
                @endbind
                
            </x-backend.form>
        </x-backend.card>
    </x-backend.body>
@endsection
