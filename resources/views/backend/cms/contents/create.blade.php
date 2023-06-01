@php
    use App\Helpers\HBackend;
    use App\Helpers\HForm;
    use App\Helpers\HContent;
@endphp

@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
@endsection

@section('content')
<x-form :action="HForm::getFormAction(route('cms.content.store', ['type' => $type]))" :method="HForm::getFormMethod()">
    @bind($content)

    <x-backend.card class="col-lg-10 col-xl-6">
        <x-backend.card-header>
            {{ $title }}

            <x-slot:right>
                <x-backend.btn-cancel :href="route('cms.content.index', ['type' => $type])" />
            </x-slot:right>
        </x-backend.card-header>

        <x-backend.card-body>

            @if($errors->has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first('error') }}
                </div>
            @elseif($errors->any())
                <div class="alert alert-danger" role="alert">
                    {{ __('backend::languages.form.has-errors') }}
                </div>
            @endif

            <x-backend.form.input-lang
                mandatory="true"
                name="title"
                :label="__('backend.cms.content.form.label.title')"
                :placeholder="__('backend.cms.content.form.label.title')"
                placeholdershow="-"
            />

            @foreach($configs->fields ?? [] as $code => $config)
                @if($config->type == 'text')
                    <x-form-input name="options->fields->{{ $code }}"
                        :label="__('backend.cms.content.form.label.'. $code)"
                        :placeholder="HForm::getInputPlaceholder(__('backend.cms.content.form.label.'. $code), '-')"
                        :class="HForm::getInputClass()"
                    />
                @elseif($config->type == 'date')
                        DATE<br>
                    {{-- <x-form-input name="options->fields->{{ $code }}" :value="Carbon\Carbon::now()->format('Y-m-d')" :lang="$config->translatable" :label="__('cms::content.form.label.' . $code)" :placeholder="__('cms::content.form.placeholder.' . $code)" /> --}}
                @endif
            @endforeach            
            </x-backend.card-body>

        <x-backend.form.submit
            :create="route('cms.content.index', ['type' => $type])"
        />
    </x-backend.card>
</x-form>
@endsection

@section('js')
<script>
    $(function() {
        console.log( "ready!" );
        $(".label-lang-href").each(function() {
            $(this).click(function() {
                formSwitchLanguage($(this));
            });
        });
    });

    function formSwitchLanguage(object) {
        let lang = $(object).data('lang');
        console.log("Lang: "+lang);

        $(".input-language").each(function() {
            if($(this).data('lang') == lang) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });

        $(".label-lang-href").each(function() {
            if($(this).data('lang') == lang) {
                $(this).addClass('fw-bold');
            } else {
                $(this).removeClass('fw-bold');
            }
        });
    }
</script>
@endsection