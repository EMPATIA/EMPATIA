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
            store=""
            update=""
        >
            @bind($sms)

            <x-backend.card-header>
                {{ $title }}

                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('notifications.sms.index')"
                        :show="route('notifications.sms.show', ['id' => getField($sms, 'id', 0)])"
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
                        :value="getField($sms, 'id')"
                        :label="__('backend.generic.id')"
                        :placeholder="__('backend.generic.id')"
                    />
                @endif
                
                <x-backend.form.input 
                    name="phone_number"
                    mandatory="true"
                    :value="getField($sms, 'phone_number')"
                    :label="__('backend.notifications.sms.generic.recipient-number')"
                    :placeholder="__('backend.notifications.sms.generic.recipient-number')"
                />

                <x-backend.form.textarea
                    name="content"
                    mandatory="true"
                    :value="getField($sms, 'content')"
                    :label="__('backend.generic.content')"
                    :placeholder="__('backend.generic.content')"
                />

            </x-backend.card-body>
            
            @endbind
        </x-backend.form>
    </x-backend.card>
</x-backend.body>
@endsection

