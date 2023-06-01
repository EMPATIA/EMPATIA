{{--

*** TEXT-HTML ***

Text section (from TinyMCE).

{
    type : text-html,
    code : <code>,
    class : <class>,
    value : {
        en : <title>,
        (... languages ...)
    },
    options : <options>
}

Helpers:
    - App\Helpers\HFrontend::getSectionCode($section);
    - App\Helpers\HFrontend::getSectionClass($section);
    - App\Helpers\HFrontend::getSectionOptions($section, [$field]);
    - App\Helpers\HFrontend::getSectionValue($section, [$lang]);

--}}
@if(App\Helpers\HFrontend::getSectionCode($section) == 'html-text-contacts')
    <div class="container-fluid html-text-contacts {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 html-text-c">
                    {!! App\Helpers\HFrontend::getSectionValue($section) !!}
                </div>
            </div>
        </div>
    </div>
@elseif(App\Helpers\HFrontend::getSectionCode($section) == 'partners-one')
    <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-2" style="margin-top: -25px;">
                <div class="col-lg-8 @if($content->code == 'use-cases') mx-lg-auto @endif html-text-c">
                    {!! App\Helpers\HFrontend::getSectionValue($section) !!}
                </div>
                <div class="col-lg-4">
                    <img class="partners-logo" src="{{ asset('/assets/img/one-source.png') }}">
                </div>
            </div>
        </div>
    </div>
@elseif(App\Helpers\HFrontend::getSectionCode($section) == 'partners-ibmc')
    <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-2" style="margin-top: -25px;">
                <div class="col-lg-8 @if($content->code == 'use-cases') mx-lg-auto @endif html-text-c">
                    {!! App\Helpers\HFrontend::getSectionValue($section) !!}
                </div>
                <div class="col-lg-4">
                    <img class="partners-logo" src="{{ asset('/assets/img/ibmc.png') }}">
                </div>
            </div>
        </div>
    </div>
@elseif(App\Helpers\HFrontend::getSectionCode($section) == 'partners-uu')
    <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-2" style="margin-top: -25px;">
                <div class="col-lg-8 @if($content->code == 'use-cases') mx-lg-auto @endif html-text-c">
                    {!! App\Helpers\HFrontend::getSectionValue($section) !!}
                </div>
                <div class="col-lg-4">
                    <img class="partners-logo" src="{{ asset('/assets/img/utretch-university.png') }}">
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-2" style="margin-top: 0px;">
                <div class="col-lg-8 @if($content->code == 'use-cases') mx-lg-auto @endif html-text-c">
                    {!! App\Helpers\HFrontend::getSectionValue($section) !!}
                </div>
            </div>
        </div>
    </div>
@endif
