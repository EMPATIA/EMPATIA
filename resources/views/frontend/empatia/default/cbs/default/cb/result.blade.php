@php
    use Illuminate\Support\Carbon;
    use Modules\CMS\Entities\Content;
    use Modules\Cbs\Helpers\CbHelpers;
    use Modules\Frontend\Helpers\CbsHelpers;

    $frontendDefaultSite = 'frontend::sites.'. (FrontendHelpers::getDefaultSite()->code ?? 'lisboa-participa');
    $frontendSite = 'frontend::sites.lisboa-participa';

    $cbConfigs = getField($cb, 'data.configurations');
    $contentsConfig = findObjectByProperty('code', 'contents', $cbConfigs);
    $contentId = getField($contentsConfig, 'value.0');
    $cbContent = Content::find($contentId);

    /**   Consultation Status   **/
    $consultationStatus = CbsHelpers::getCbStatus($cb);

    /**   Template Content   **/
    $template           = FrontendHelpers::getContentByCode('lp-public-consultation-result', 'unpublished');
    $templateTitle      = FrontendHelpers::getSectionByCode(getField($template, "sections"), "title");
    $templateContent    = FrontendHelpers::getSectionByCode(getField($template, "sections"), "content");

    /**   Consultation Contents   **/
    $consultationResultContent = FrontendHelpers::getSectionByCode(getField($cbContent, "sections"), "content_result");

@endphp

@extends($frontendSite.'.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="container">
            <a href="{{ route('page', [ CbHelpers::getCbTypeSlug( getField($cb, 'type') ) , getField($cb, 'slug.'.getLang(), '') ]) }}" class="d-inline" >
                <i class="fas fa-arrow-left mt-3 me-2"></i>{{ __site('back') }}
            </a>
        </div>
    </div>
    <section class="mt-4 mb-5">
        <div class="row">
            <div class="container pb-5">
                <h1 class="section-title mb-3">{{ FrontendHelpers::getSectionValue($templateTitle) }}</h1>
                <div class="">
                    @include($frontendSite.'.sections', ['content' => $cbContent, 'sections' => [$templateContent]])
                </div>
                <div class="mb-3">
                    @include($frontendSite.'.sections', ['content' => $cbContent, 'sections' => [$consultationResultContent]])
                </div>
            </div>
        </div>
    </section>
@endsection

