@php
    use App\Helpers\HForm;

    $action = HForm::getAction();
@endphp

@extends('backend.layouts.master')

@section('title')
    @if(HForm::isEdit($action ?? null))
        {{ $code ?? '-' }}
    @elseif(HForm::isCreate($action ?? null))
        {{ $code ?? '-' }}
    @else
        {{ $code ?? '-' }}
    @endif
@endsection

@section('content_header')
@endsection
@section('content')
    <x-backend.body>
        <x-backend.card container="col-12 col-md-7 col-lg-6 col-xl-4">

            <x-backend.card-header>
                {{ $code ?? 'Create' }}
                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('cbs.operation-schedules.index', ['type' => $type, 'cbId' => $cbId])"
                        :show="route('cbs.operation-schedules.show', ['type' => $type, 'cbId' => $cbId, 'code' => $code ?? ' '])"
                        :edit="route('cbs.operation-schedules.edit', ['type' => $type, 'cbId' => $cbId, 'code' => $code ?? ' '])"
                        :delete="getField($model, 'code')"
                    />
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                <x-backend.form
                    :store="route('cbs.operation-schedules.store', ['type' => $type, 'cbId' => $cbId])"
                    :update="route('cbs.operation-schedules.update', ['type' => $type, 'cbId' => $cbId, 'code' => $code ?? ' '])"
                >
                    @bind($model ?? null)
                    <x-backend.form.errors/>
                    <x-backend.form.input
                        name="code"
                        :value="getField($model, 'code')"
                        :label="__('backend.generic.code')"
                        :placeholder="__('backend.generic.code')"
                    ></x-backend.form.input>
                    <x-backend.form.input-lang
                        name="description"
                        mandatory="true"
                        :value="getField($model, 'description')"
                        :label="__('backend.generic.description')"
                        :placeholder="__('backend.generic.description')"
                    />
                    <x-backend.form.input
                        type="datetime-local"
                        name="start_date"
                        :value="getField($model, 'start_date')"
                        :label="__('backend.generic.start_date')"
                        :placeholder="__('backend.generic.start_date')"
                    ></x-backend.form.input>
                    <x-backend.form.input
                        type="datetime-local"
                        name="end_date"
                        :value="getField($model, 'end_date')"
                        :label="__('backend.generic.end_date')"
                        :placeholder="__('backend.generic.end_date')"
                    ></x-backend.form.input>
                    <x-form-checkbox
                        :switch="true"
                        :disabled="HForm::isShow()"
                        :label="__('backend.generic.enabled')"
                        name="enabled"
                    ></x-form-checkbox>
                    @endbind
                </x-backend.form>
            </x-backend.card-body>
            <x-backend.form.submit
                :create="route('cbs.operation-schedules.store', ['type' => $type, 'cbId' => $cbId])"
                :edit="route('cbs.operation-schedules.update', ['type' => $type, 'cbId' => $cbId, 'code' => $code ?? ' '])"
            />

        </x-backend.card>
    </x-backend.body>
@endsection
