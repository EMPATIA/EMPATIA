@php
    use App\Helpers\HFrontend;

    $projectAssetsPath  = HFrontend::getProjectPath();
    $projectPath        = HFrontend::getProjectPath(true);

    $isHome = getField($content, "code") == 'home';

@endphp

@extends("frontend.$projectPath.layouts.master")

@section('content')
    {{-- **** DO NOT CHANGE THIS FILE **** --}}
    {{-- If changes are needed please use the dynamic features of CMS and create custom configurations inside the partials --}}

    <section class="main-content content-section">
        @include("frontend.$projectPath.partials.generic-banner", [
            'bannerHeight'  => $isHome ? '70vh' : '40vh',
            'isCarousel'    => $isHome,
        ])

        @include("frontend.$projectPath.cms.sections")
    </section>
@endsection

