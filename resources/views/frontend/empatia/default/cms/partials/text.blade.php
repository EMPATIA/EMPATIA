{{--

*** TEXT ***

Text section from a textarea input.

{
    type : text,
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

<div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
    <div class="container">
        <div class="row py-4 justify-content-center">
            <div class="col-lg-10">
                {{ App\Helpers\HFrontend::getSectionValue($section) }}
            </div>
        </div>
    </div>
</div>
