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
            :store="route('notifications.templates.store')"
            :update="route('notifications.templates.update', ['id' => getField($template, 'id', 0)])"
        >
            @bind($template)

            <x-backend.card-header>
                {{ $title }}

                <x-slot:right>
                    <x-backend.form.header-btn
                        :list="route('notifications.templates.index')"
                        :show="route('notifications.templates.show', ['id' => getField($template, 'id', 0)])"
                        :edit="route('notifications.templates.edit', ['id' => getField($template, 'id', 0)])"
                        :delete="route('notifications.templates.delete', ['id' => getField($template, 'id', 0)])"
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
                        :value="getField($template, 'id')"
                        :label="__('backend.generic.id')"
                        :placeholder="__('backend.generic.id')"
                    />
                @endif
                
                
                <x-backend.form.input 
                    name="code"
                    mandatory="true"
                    :value="getField($template, 'code')"
                    :label="__('backend.generic.code')"
                    :placeholder="__('backend.generic.code')"
                />

                <x-backend.form.input 
                    name="channel"
                    mandatory="true"
                    :value="getField($template, 'channel')"
                    :label="__('backend.notifications.templates.generic.channel')"
                    :placeholder="__('backend.notifications.templates.generic.channel')"
                />

                <x-backend.form.input-lang 
                    name="subject"
                    mandatory="true"
                    :value="getField($template, 'subject')"
                    :label="__('backend.notifications.templates.generic.subject')"
                    :placeholder="__('backend.notifications.templates.generic.subject')"
                    placeholdershow="-"
                />

                <x-backend.form.textarea-lang
                    name="content"
                    mandatory="true"
                    :value="getField($template, 'content')"
                    :label="__('backend.generic.content')"
                    :placeholder="__('backend.generic.content')"
                    placeholdershow="-"
                />

            </x-backend.card-body>

            <x-backend.form.submit
                :create="route('notifications.templates.index')"
                :edit="route('notifications.templates.show', ['id' => getField($template, 'id', 0)])"
            />
            
            @endbind
        </x-backend.form>
    </x-backend.card>
</x-backend.body>
@endsection

@push('scripts')
    <script src="/build/js/backend/model-delete.js"></script>
@endpush