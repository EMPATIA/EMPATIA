{{--

*** Heading ***

Heading title section type that containds the heading field with the heading format (h1, h2, h3, ...).

{
    type : heading,
    code : <code>,
    class : <class>,
    value : {
        heading : <heading>,
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
    - App\Helpers\HFrontend::getSectionHeading($section);

--}}

@if(!empty($content->type) && $content->type == 'modules' && App\Helpers\HFrontend::getSectionHeading($section) == 'h1')
    <div class="container-fluid">
        <div class="container">
            <div class="row py-4">
                <div class="col-lg-12 modules">
                    <h1>{{ App\Helpers\HFrontend::getSectionValue($section) }}</h1>
                </div>
            </div>
        </div>
    </div>
@else
    @if(App\Helpers\HFrontend::getSectionHeading($section) == 'h1')
        <div class="row py-4">
            <div class="col-lg-10">
                <h1>{{ App\Helpers\HFrontend::getSectionValue($section) }}</h1>
            </div>
        </div>         
    @else
        <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
            <div class="container">
                <div class="row py-4">
                    <div class="col-lg-10">
                        {!! '<'.App\Helpers\HFrontend::getSectionHeading($section).'>' !!}
                            {{ App\Helpers\HFrontend::getSectionValue($section) }}
                        {!! '</'.App\Helpers\HFrontend::getSectionHeading($section).'>' !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
