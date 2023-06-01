{{--

*** Buttons & Cards ***

Button/card section type that containds 3 optional fields for additional information. Options should hold the dynamic
configurations not being changed by the content editor. Icons can be set in the options field or in one of the optional
fields. If card requires image the images section type should be used.

{
    type : button,
    code : <code>,
    class : <class>,
    value : {
        en : {
            title   : <title>,
            link    : <link>,
            first   : <first>,
            second  : <second>,
            third   : <third>
        },
        (... languages ...)
    },
    options : <options>
}

Helpers:
    - App\Helpers\HFrontend::getSectionCode($section);
    - App\Helpers\HFrontend::getSectionClass($section);
    - App\Helpers\HFrontend::getSectionOptions($section, [$field]);
    - App\Helpers\HFrontend::getSectionButtonValue($section, $field);

--}}

@if(App\Helpers\HFrontend::getSectionCode($section) == 'btn-reflection-group')
    <div class="container-fluid btn-reflection-group {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-4">
                <div class="col-lg-10">
                    <a href="{{ App\Helpers\HFrontend::getSectionButtonValue($section, 'link') }}" target="_blank" class="btn btn-primary btn-reflection-group">
                        {{ App\Helpers\HFrontend::getSectionButtonValue($section, 'title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
        <div class="container">
            <div class="row py-4">
                <div class="col-lg-10">
                    <a href="{{ App\Helpers\HFrontend::getSectionButtonValue($section, 'link') }}" class="btn btn-primary">
                        {{ App\Helpers\HFrontend::getSectionButtonValue($section, 'title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif