{{--

*** List ***

Items list for custom features.

{
    type : list,
    code : <code>,
    class : <class>,
    value : {
        0 : {
            code : <code>,
            en : {
                enabled : true/false
                value : <value>,
                desc: <description>,
                options: <options>
            },
            (... languages ...)
        },
        (... list ...)
    },
    options : <options>
}

Helpers:
    - App\Helpers\HFrontend::getSectionCode($section);
    - App\Helpers\HFrontend::getSectionClass($section);
    - App\Helpers\HFrontend::getSectionOptions($section, [$field]);
    - App\Helpers\HFrontend::getSectionEnabledItems($section, [$lang]);
    - App\Helpers\HFrontend::getSectionItemField($item, $field, [$lang]);

--}}

<div class="container-fluid {{ App\Helpers\HFrontend::getSectionClass($section) }}">
    <div class="container">
        <div class="row py-4 justify-content-center">
            @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                <div class="col-lg-10">
                    {{ App\Helpers\HFrontend::getSectionItemField($item, 'value') ?: '--' }} | {{ App\Helpers\HFrontend::getSectionItemField($item, 'desc') ?: '--' }} | {{ App\Helpers\HFrontend::getSectionItemField($item, 'options') ?: '--' }} (CODE: {{ $item->code ?? '--' }})
                </div>
            @empty
                <div class="col-lg-10">
                    {{ __('frontend:section.list.empty') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
