<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- Title --}}
    <title>
        @yield('title', __('backend::backend.title') .' | '. config('app.name', 'Empatia'))
    </title>
    @livewireStyles

    {{--    <script src="/build/assets/backend-2bc12b24.js"></script>--}}
    {{--    <link rel="stylesheet" href="/build/assets/backend-37e5b79f.css">--}}
    {{--    <link rel="stylesheet" href="/build/assets/backend-d31a2d4c.css">--}}

    @vite(['resources/js/backend/app.js', 'resources/sass/backend/app.scss'])

    {{--    <!-- FAVICON -->--}}
    {{--    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/backend/favicon/apple-touch-icon.png') }}">--}}
    {{--    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/backend/favicon/favicon-32x32.png') }}">--}}
    {{--    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/backend/favicon/favicon-16x16.png') }}">--}}
    {{--    <link rel="manifest" href="{{ asset('/backend/favicon/site.webmanifest') }}">--}}
    {{--    <link rel="mask-icon" href="{{ asset('/backend/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">--}}
    {{--    <link rel="shortcut icon" href="{{ asset('/backend/favicon/favicon.ico') }}">--}}
    {{--    <meta name="msapplication-TileColor" content="#ffffff">--}}
    {{--    <meta name="msapplication-config" content="{{ asset('/backend/favicon/browserconfig.xml') }}">--}}
    {{--    <meta name="theme-color" content="#ffffff">--}}
    {{--    <!-- End FAVICON -->--}}
</head>

<body class="bg-light">

{{-- Body Content --}}
@include('backend.layouts.partials.top-menu')
<div class="container-fluid">
    <div class="row">
        @include('backend.layouts.partials.side-menu')
        <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @yield('header')
            @yield('content')
        </main>
    </div>
</div>

{{-- Custom Scripts --}}
@livewireScripts
@yield('scripts')
@stack('scripts')


<script>
    // Translations used in delete and restore entries modals (Indexes)
    indexesTranslations = {};
    indexesTranslations['buttonActions'] = {};
    indexesTranslations['buttonActions']['cancel'] = '{{__('backend.generic.cancel')}}';
    indexesTranslations['buttonActions']['confirm'] = '{{__('backend.generic.confirm')}}';

    indexesTranslations['delete'] = {};
    indexesTranslations['delete']['title'] = '{{__('backend.generic.modal.delete-title')}}';
    indexesTranslations['delete']['message'] = '{{__('backend.generic.modal.delete-message')}}';
    indexesTranslations['delete']['title-success'] = '{{__('backend.generic.modal.delete-title-success')}}';
    indexesTranslations['delete']['message-success'] = '{{__('backend.generic.modal.delete-message-success')}}';

    indexesTranslations['restore'] = {};
    indexesTranslations['restore']['title'] = '{{__('backend.generic.modal.restore-title')}}';
    indexesTranslations['restore']['message'] = '{{__('backend.generic.modal.restore-message')}}';
    indexesTranslations['restore']['title-success'] = '{{__('backend.generic.modal.restore-title-success')}}';
    indexesTranslations['restore']['message-success'] = '{{__('backend.generic.modal.restore-message-success')}}';
</script>

<script type="module">

    $(document).ready(function () {
        // Calls all functions when page loads
        if (typeof modelIndexScripts == "function") {
            reloadFunctions.add('indexScripts', modelIndexScripts);
        }
        if (typeof modelDeleteScripts == "function") {
            reloadFunctions.add('deleteScripts', modelDeleteScripts);
        }
        reloadFunctions.do();
    });

    //    Calls all functions at each Livewire request
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.hook('component.initialized', (component) => {
            reloadFunctions.do();
        })
        Livewire.hook('message.processed', (message, component) => {
            reloadFunctions.do();
        })
    });
</script>


{{-- Pacakges Scripts --}}

</body>

</html>
