{{-- TODO: store icons, images and configurations in CMS page 'layout' --}}

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="application-name" content="{{ config("app.name") }}"/>

<!-- icons -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/favicon/favicon.ico') }}" />
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/favicon/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/favicon/favicon-32x32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/assets/favicon/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('/assets/favicon/site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('/assets/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-config" content="{{ asset('/assets/favicon/browserconfig.xml') }}">
<meta name="msapplication-TileImage" content="{{ asset('/assets/favicon/favicon-144x144.png') }}">
<meta name="theme-color" content="#ffffff">

<!-- title -->
<title>@if(!empty(getFieldLang($content, 'seo.title'))){{ getFieldLang($seo, 'seo.title') }} | @endif{{ config("app.name") }}</title>


<!-- SEO -->
@foreach(getField(App\Helpers\HFrontend::getContentConfigurations(getField($content, "type")), "seo", []) as $type => $group)
    @foreach($group as $code => $field)
        @if(empty(getField($field, "locale")))
            <meta property="{{ $code }}" content="{{ getField($content, 'seo.'.$code, App\Helpers\HFrontend::getSeoDefault($code, $field, $content, getField($content, 'seo'))) }}">
        @else
            @foreach(getLanguagesFrontend() as $language)
                @if(getField($language, 'locale') == getLang())
                    <meta property="{{ $code }}" content="{{ getFieldLang($content, 'seo.'.$code, App\Helpers\HFrontend::getSeoDefault($code, $field, $content, getField($content, 'seo'), getField($language, 'locale'))) }}">
                @endif
            @endforeach
        @endif
    @endforeach
@endforeach
<!-- end SEO -->
