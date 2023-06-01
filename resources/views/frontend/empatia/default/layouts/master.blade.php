@php
    use App\Helpers\HFrontend;

    $projectAssetsPath      = HFrontend::getProjectPath();
    $projectResourcesPath   = HFrontend::getProjectPath(true);
@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(\Pam\CookieConsent\CookieConsent::isAllowed('googleAnalytics'))
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '{{config('services.google.tag_manager')}}');</script>
    <!-- End Google Tag Manager -->
    @endif

    @php
        $seo = $content->seo ?? '';
    @endphp

    @if(isset($seo) && isset($seo->title->{getLang()}))
        <title>{{$seo->title->{getLang()} }} | {{ config('app.name', 'Empatia') }}</title>
    @else
        <title>{{ config('app.name', 'Empatia') }}</title>
    @endif

    @if(isset($content) && $content != '404')
        @php
            $seo = $content->seo;
        @endphp
        @foreach(App\Helpers\HFrontend::getContentConfigurations($content->type)->seo ?? [] as $type => $group)
            @foreach($group as $code => $field)
                @if(empty($field->locale))
                    <meta property="{{$code}}"
                          content="{{ !empty($seo) && !empty($seo->$code) ? $seo->$code : App\Helpers\HFrontend::getSeoDefault($code, $field, $content, $seo) }}">
                @else
                    @foreach(getLanguagesFrontend() as $language)
                        <meta property="{{$code}}"
                              content="{{ !empty($seo) && !empty($seo->$code) && !empty($seo->$code->{$language['locale']}) ? $seo->$code->{$language['locale']} : App\Helpers\HFrontend::getSeoDefault($code, $field, $content, $seo, $language['locale']) }}">
                    @endforeach
                @endif
            @endforeach
        @endforeach
    @endif

    <!-- FAVICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('/assets/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/assets/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link rel="shortcut icon" href="{{ asset('/assets/favicon/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="{{ asset('/assets/favicon/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    @livewireStyles

    @vite([
        "resources/js/frontend/$projectAssetsPath/app.js",
        "resources/sass/frontend/$projectAssetsPath/app.scss"
    ])
    @livewireStyles
</head>
<body>
@livewireScripts

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{config('services.google.tag_manager')}}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Menu section -->
@include("frontend.$projectResourcesPath.layouts.menu")

{{-- <!-- Hero section -->
@if($content != '404')
    @include('frontend.layouts.hero')
@endif --}}

<!-- Main content section -->
@yield('content')
@livewireScripts

@if(isset($content) && $content === '404')
    <div class="container-fluid page-404">
        <div class="content-container center mt-5 mb-5">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <h1>404</h1>
                        <h2>PAGE NOT FOUND</h2>
                        <h3>The page you are looking for doesn't exist.</h3>
                        <div>
                            <a href="/" class="btn-back">
                                ← Back to homepage
                            </a>
                        </div>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- @if($content === '404')
     <div class="container-fluid page-404">
         <div class="content-container center mt-5 mb-5">
             <div class="row">
                 <div class="col-lg-12">
                     <div>
                         <h1>404</h1>
                         <h2>PAGE NOT FOUND</h2>
                         <h3>The page you are looking for doesn't exist.</h3>
                         <div>
                             <a href="/" class="btn-back">
                                 ← Back to homepage
                             </a>
                         </div>
                         <br>
                         <br>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 @endif--}}

<!-- Footer section -->
    @include("frontend.$projectResourcesPath.layouts.footer")

    @yield("scripts")

    @stack('scripts')
    @stack('modals')

    {{ CookieConsent::getCookieConsentPopup() }}
</body>
</html>
