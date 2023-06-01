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
            :store="null"
            :update="null"
        >
            @bind($email)

            <x-backend.card-header>
                {{ $title }}

                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('notifications.emails.index')"
                        :show="route('notifications.emails.show', ['id' => getField($email, 'id', 0)])"
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
                        :value="getField($email, 'id')"
                        :label="__('backend.generic.id')"
                        :placeholder="__('backend.generic.id')"
                    />
                @endif
                
                <x-backend.form.input 
                    name="from_email"
                    mandatory="true"
                    :value="getField($email, 'from_email')"
                    :label="__('backend.notifications.emails.generic.sender-email')"
                    :placeholder="__('backend.notifications.emails.generic.sender-email')"
                />

                <x-backend.form.input 
                    name="from_name"
                    mandatory="true"
                    :value="getField($email, 'from_name')"
                    :label="__('backend.notifications.emails.generic.sender-name')"
                    :placeholder="__('backend.notifications.emails.generic.sender-name')"
                />

                <x-backend.form.input 
                    name="user_email"
                    mandatory="true"
                    :value="getField($email, 'user_email')"
                    :label="__('backend.notifications.emails.generic.recipient-email')"
                    :placeholder="__('backend.notifications.emails.generic.recipient-email')"
                />

                <x-backend.form.input 
                    name="subject"
                    mandatory="true"
                    :value="getField($email, 'subject')"
                    :label="__('backend.notifications.emails.generic.subject')"
                    :placeholder="__('backend.notifications.emails.generic.subject')"
                />

                <x-backend.form.textarea
                    name="content"
                    mandatory="true"
                    :value="getField($email, 'content')"
                    :label="__('backend.notifications.emails.generic.content')"
                    :placeholder="__('backend.notifications.emails.generic.content')"
                />

            </x-backend.card-body>
            
            @endbind
        </x-backend.form>
    </x-backend.card>
</x-backend.body>
@endsection

