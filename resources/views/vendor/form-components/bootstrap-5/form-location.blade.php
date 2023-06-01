@props(['displaySettings'=> '',  'mapConfiguration' => []])

@php
    use App\Helpers\HForm;

    $coordinates = HForm::getFromInputValue($value, true, false, $attributes->get('default'));
@endphp

<x-form-label class="mb-0 text-muted small" :label="$label" :for="$attributes->get('id') ?: $id()"/>

<div class="input-group">
    <input
        name="{{ $name }}"
        type="{{getField($displaySettings, 'showInput') || getField($displaySettings, 'showAutocompleteInput') ? 'text' : 'hidden'}}"
        value="{{ HForm::getFromInputValue($value, true, false, $attributes->get('default')) }}"
        placeholder="{{ $attributes->get('placeholder') ?? '' }}"
        aria-bs-describedby="map_btn_{{ $attributes->get('id') ?? $id() }}"
        class="{{ HForm::isShow($attributes->get('action')) ? 'form-control-plaintext' : 'form-control' }} {{ $hasError($name) ? ' is-invalid' : '' }}"
        {{ HForm::isShow($attributes->get('action')) ? 'readonly="readonly"' : '' }}
        id="coordinatesInput"
    >
    @if($hasErrorAndShow($name))
        <x-form-errors :name="$name"/>
    @endif

    @if(getField($displaySettings, 'showMapModal'))
        <button class="btn btn-outline-secondary" type="button"
                id="map_btn_{{ $attributes->get('id') ?? $id() }}"><i
                class="fas fa-map-marked-alt m-0"></i></button>
        </input>
    @endif
</div>

@if(!getField($displaySettings, 'showMapModal'))
    <div class="position-relative">
        <div id="map" style="height: {{getField($mapConfiguration, 'map_size', '40rem')}};"
             class="border border-primary mt-3"></div>
        @if(!HForm::isShow($attributes->get('action')))
            <div class="row row-cols-3 mt-3 justify-content-center">
                @foreach(getField($mapConfiguration, 'action_buttons') ?? [] as $button => $config)
                    @if(isset($config->value) && isset($config->code) && !empty($config->value))
                        <div class="col-auto">
                            <input id="{{$config->code}}" type="button"
                                   class="btn btn-sm btn-primary {{--border-primary--}}"
                                   value="{{ __('backend.form.location-input-component.'.$config->code) }}"/>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endif

@if(!HForm::isShow($attributes->get('action') ?? null))
    <x-backend.modal class="modal-lg" id="locationInputModal" tabindex="-1"
                     aria-labelledby="locationInputModalLabel" aria-hidden="true">
        <x-backend.modal-header>
            <h5 class="modal-title"
                id="locationInputModalLabel">{{ __('backend.form.location-input-modal.title') }}</h5>
        </x-backend.modal-header>
        <x-backend.modal-body>
            <div class="position-relative">
                <div {{--id="floating-panel"--}}>
                    <input id="delete-markers" type="button"
                           value="{{ __('backend.form.location-input-component.delete-markers') }}"/>
                </div>
                <div id="map" style="height: 40rem;"></div>
            </div>
        </x-backend.modal-body>
        <x-backend.modal-footer close="true">
        </x-backend.modal-footer>
    </x-backend.modal>
@endif
@php
    $params = '';
    if (getField($displaySettings, 'showAutocompleteInput')) {
        $params = $params.'&libraries=places';
    }
@endphp
@push('scripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAPS_API_KEY")}}&region=PT&language=pt&callback=initMap&v=weekly{{$params}}"
        async defer></script>
    <script data-script-class="x-form-location">


        let map;
        let markers = [];
        let input = document.getElementById('coordinatesInput');

        const regexLat = /^(-?[1-8]?\d(?:\.\d{1,18})?|90(?:\.0{1,18})?)$/;
        const regexLon = /^(-?(?:1[0-7]|[1-9])?\d(?:\.\d{1,18})?|180(?:\.0{1,18})?)$/;

        let defaultLocation = @json(getField($mapConfiguration, 'center_location', ''));

        function initMap() {

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: @json(getField($mapConfiguration, 'zoom', '12')),
                center: defaultLocation,
            });

            if (@json(!HForm::isShow($attributes->get('action')))) {
                // This event listener will call addMarker() when the map is clicked.
                if (@json(getField($mapConfiguration, 'allowpins', true))) {
                    map.addListener("click", (event) => {
                        deleteMarkers();
                        addMarker(event.latLng);
                    });
                }

                let actionsButtons = @json(getField($mapConfiguration, 'action_buttons', []));
                for (const [key, button] of Object.entries(actionsButtons)) {
                    if (button.value) {
                        document.getElementById(button.code)
                            .addEventListener("click", eval(key));
                    }
                }

                if (@json(getField($displaySettings, 'showAutocompleteInput'))) {
                    autocomplete = new google.maps.places.Autocomplete(input,
                        {
                            fields: ['place_id', 'geometry', 'name']
                        });

                    autocomplete.addListener("place_changed", (event) => {
                        let place = autocomplete.getPlace();
                        deleteMarkers();
                        addMarker(place.geometry['location']);
                        map.setCenter({lat: place.geometry['location'].lat(), lng: place.geometry['location'].lng()})
                    });
                }
            }
            if (@json(getField($displaySettings, 'showInput'))) {
                input.addEventListener("change", (event) => {
                    let coordInput = input.value.split(',');
                    let coordSplited = {lat: parseFloat(coordInput[0]), lng: parseFloat(coordInput[1])};
                    deleteMarkers();
                    addDefaultMarker(coordSplited)
                    map.setCenter(coordSplited);
                });
            }

            if (input.value.length != '', check_lat_lon(input)) {
                addDefaultMarker(defaultLocation);
            }

        }
        // Adds a marker to the map and push to the array. Also changes form input value
        function addMarker(position) {
            const marker = new google.maps.Marker({
                position,
                map,
            });
            markers.push(marker);
            input.value = position.toJSON().lat + ',' + position.toJSON().lng;
        }

        function addDefaultMarker(position) {
            const marker = new google.maps.Marker({
                position,
                map,
            });
            markers.push(marker);
            input.value = position.lat + ',' + position.lng;
        }

        // Sets the map on all markers in the array.
        function setMapOnAll(map) {
            for (let i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }

        // Removes the markers from the map, but keeps them in the array.
        function hideMarkers() {
            setMapOnAll(null);
        }

        // Shows any markers currently in the array.
        function showMarkers() {
            setMapOnAll(map);
        }

        // Deletes all markers in the array by removing references to them.
        function deleteMarkers() {
            hideMarkers();
            markers = [];
            document.getElementById('coordinatesInput').value = null;
        }

        window.initMap = initMap;

        if (@json(getField($displaySettings, 'showMapModal')) && @json(!HForm::isShow($attributes->get('action'))))
        {
            document.getElementById('map_btn_{{ $attributes->get('id') ?? $id() }}').addEventListener('click', MapModal);
        }

        function MapModal() {
            $('#locationInputModal').modal('show');
        }

        function check_lat_lon(input) {
            let coordInput = input.value.split(',');
            let lat = parseFloat(coordInput[0]);
            let lng = parseFloat(coordInput[1]);
            let validLat = regexLat.test(lat);
            let validLon = regexLon.test(lng);
            return validLat && validLon;
        }

    </script>
@endpush

