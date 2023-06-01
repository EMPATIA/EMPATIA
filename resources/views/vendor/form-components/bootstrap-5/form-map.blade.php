@php
    $coordinates = BackendForm::getFromInputValue($value, true, false, $attributes->get('default'));
    $componentId = $attributes->get('id') ?? $id();
@endphp

<div class="@if($type === 'hidden') d-none @else form-group {!! $attributes->get('class') !!} @endif">
    <div id="map_{{ $attributes->get('id') ?? $id() }}" class="rounded mb-3"
         style="min-height: 200px; width: 100%; height: 100%;"></div>
</div>

@push('scripts-stack')
    <script data-script-class="x-form-map">
        {{ $componentId }}_position = [-8.54106298095553, 39.5827588227302];
        {{ $componentId }}_marker = null;
        {{ $componentId }}_mapContainer = 'map_{{ $componentId }}';

        mapboxgl.accessToken = '{{ env('MAPBOX_ACCESS_TOKEN') }}';

        const {{ $componentId }}_map = new mapboxgl.Map({
            container: {{ $componentId }}_mapContainer, // container id
            style: 'mapbox://styles/mapbox/streets-v11',
            center: {{ $componentId }}_position, // starting position
            zoom: 5 // starting zoom
        });

        setTimeout(() => {
            {{ $componentId }}_map.resize();
        }, 100)

        {{ $componentId }}_MapTools = {
            value: null,
            locations: [],
            markers: [],

            init: function (options) {
                if (typeof options != 'object') {
                    return false;
                }

                if (typeof Array.isArray(options.value) && options.value.length > 0) {
                    options.value.forEach((location) => {
                        let coords = this.stringToCoords(location);
                        this.locations.push(coords);
                        this.markers.push(new mapboxgl.Marker().setLngLat(coords).addTo({{ $componentId }}_map));
                    });
                }
            },

            stringToCoords: function (value) {
                let coords = null;

                if (typeof value == 'string') {
                    value = value.split(',');
                }

                if (typeof value == 'object' && value.length === 2) {
                    coords = {
                        lng: parseFloat(value[1]),
                        lat: parseFloat(value[0]),
                    }
                }

                return coords;
            },

            coordsToString: function (coords) {
                let string = '';

                if (typeof coords == 'object' && coords != null && typeof coords.lng == 'number' && typeof coords.lat == 'number') {
                    string = `${coords.lat},${coords.lng}`;
                }

                return string;
            }
        };

        $(function () {
            $('#locationInputModal').on('shown.bs.modal', function () {
                {{ $componentId }}_map.resize();
            })
            {{ $componentId }}_MapTools.init({
                readonly: true,
                value: JSON.parse('{!! BackendForm::getFromInputValue($value, true, false, $attributes->get('default')) ?? '[]' !!}')
            });
        });
    </script>
@endpush
