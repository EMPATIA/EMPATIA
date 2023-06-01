@php
    use App\Helpers\HForm;
@endphp

@if( !empty($parameter))
    @php
        $bind = $bind ?? [$name => $value ?? null];
        $name = $name ?? first_element(array_keys((array)($bind ?? [])));

        $value = $value ?? first_element($bind ?? []) ?? old($name);

        if(in_array($type, ['images','files']) && !is_array($value)){
           $filesSplit =  explode(",", $value);
           $filesList = [];
           foreach ($filesSplit ?? [] as $img){
               array_push($filesList, trim($img, '[]"'));

           }
           $value = $filesList;
        }

        $prefix = ($prefix ?? 'frontend.generic.form').'.';
        $action = $action ?? 'create';

        $label = $label ?? getField($parameter, 'title.'.getLang()) ??
            first_element(getField($parameter, 'title')) ??
            __($prefix.'label.parameter.'.$name);
        $label .= ($parameter->mandatory ?? false) ? ' *' : '';

        $placeholder = $placeholder ?? getField($parameter, 'placeholder.'.getLang()) ??
            first_element(getField($parameter, 'placeholder')) ??
            __($prefix.'placeholder.parameter.'.$name);

        $validInputTypes = [
            'heading','textarea','select','checkbox','radio',
            'image','images','file','files',
            'location', 'geolocation',
            'color','date','datetime-local','email','hidden','month','number','password','search','tel','text','time','url','week'
        ];

        $type       = $type ?? getField($parameter, 'type');
        $type       = in_array($type, $validInputTypes) ? $type : 'text';

        $id         = $id ?? getField($parameter, 'id') ?? null;
        $typeClass  = ['textarea' => 'form-control', 'select'=> 'form-select'];
        $class      = ($typeClass[$type] ?? '') . ' ' . ($class ?? getField($parameter, 'class') ?? '');

        $default    = $default ?? getField($parameter, 'default') ?? null;
        $multiple   = $multiple ?? getField($parameter, 'multiple') ?? false;
        $localized  = $localized ?? getField($parameter, 'multilang') ?? false;
        $select2    = $select2 ?? getField($parameter, 'select2') ?? false;
        $inline     = $inline ?? getField($parameter, 'inline') ?? false;
        $disabled   = $disabled ?? ($action == 'show');

        $wire       = $wire ?? getField($parameter, 'wire') ?? null;

        $view       = $view ?? getField($parameter, 'view') ?? null;

        if(in_array($type, ['location','geolocation'])){
            $flags = getField($parameter, 'flags', []);
            $displaySettings = [];
            $mapConfiguration = [];
            if(!empty($flags)){
                data_set($displaySettings, 'showInput', $flags->show_input ?? true);
                data_set($displaySettings, 'showMapModal', $flags->show_map_modal ?? true);
                data_set($displaySettings, 'showAutocompleteInput', $flags->show_autocomplete_input ?? true);
                $mapConfiguration = getField($flags, 'map_configuration', []);
                if(!empty($value)){
                    $valueCoords = explode(',', $value);
                    if(isset($valueCoords[0]) && isset($valueCoords[1])){
                        $newCoords = ['lat' => (float)$valueCoords[0], 'lng' => (float)$valueCoords[1]];
                        data_set($mapConfiguration, 'center_location', (object)$newCoords);
                    }
                }
            }
        }

        if( strtolower(getField($parameter, 'target')) == 'model' && class_exists($model = getField($parameter, 'model')) ){
            $options = App\Helpers\HFrontend::optionsFromCollection($model::get());
        } else {
            $options = App\Helpers\HFrontend::optionsFromParameter($parameter);
        }

         $wireDefer = ($wireDefer ?? false) === true;
    @endphp
    @if( $type == 'heading' )
        <h3 class="{{$class}}">{{$label}}</h3>

    @elseif( $type == 'textarea' )
        <x-form-textarea
            :id="$id"
            :class="(HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' . ' ' . $class"
            :action="$action"
            :bind="$bind"
            :name="$name"
            :lang="$localized"
            :label="$label"
            :placeholder="$placeholder"
        />

    @elseif( $type == 'select' )
        <x-form-select
            :id="$id"
            :class="$class.( $select2 ? ' select-multiple' : '')"
            :action="$action"
            :bind="$bind"
            :name="$name"
            :options="$options"
            :icon="( !$select2 ? 'chevron-down' : null)"
            :default="$default"
            :multiple="$multiple"
            :label="$label"
            :placeholder="$placeholder"
            :wire-defer="$wireDefer"
        />

    @elseif( $type == 'checkbox' )
        @if( count($options) > 0 )
            @if( $action != 'show' || ($showType ?? '') == 'all-options' )
                <x-form-group
                    :id="$id"
                    :class="$class"
                    :name="$name"
                    :label="$label"
                    :inline="$inline"
                    :disabled="$disabled"
                >
                    @php
                        // TODO: make function to deal with this
                        if( is_string($value) ) {
                            $value = [$value];
                        } else if( !is_array($value) ){
                            $value = json_decode(json_encode($value), true);
                        }
                    @endphp

                    @foreach($options as $optionValue => $optionLabel)
                        <x-form-checkbox
                            :action="$action"
                            :bind="(object)[$name => in_array($optionValue, $value ?? []) ? $optionValue : null]"
                            :name="$name . '[]'"
                            :label="$optionLabel"
                            :value="$optionValue"
                            :checked="is_array($value) && in_array($optionValue, $value)"
                            :disabled="$disabled"
                            :showErrors="false"
                            :class="$class"
                        />
                    @endforeach
                </x-form-group>
            @else
                @php
                    // TODO: make function to deal with this
                    if( is_string($value) ) {
                        $value = [$value];
                    } else if( !is_array($value) ){
                        $value = json_decode(json_encode($value), true);
                    }

                    $checkboxLabels = [];
                    foreach ($options as $optionValue => $optionLabel){
                        if( !empty($value) && in_array($optionValue, $value) ){
                            $checkboxLabels[] = $optionLabel;
                        }
                    }
                    $checkboxValue = implode(', ', $checkboxLabels);
                @endphp
                <x-form-input
                    :id="$id"
                    :class="(HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' . ' ' . $class"
                    :readonly="App\Helpers\HForm::getInputReadonly(\App\Helpers\HForm::getAction())"
                    :action="$action"
                    :bind="[$name => $checkboxValue]"
                    :name="$name"
                    type="text"
                    :label="$label"
                />
            @endif
        @endif

    @elseif( $type == 'radio' )
        @if( count($options) > 0 && is_array($value) )
            @if( $action != 'show' || ($showType ?? '') == 'all-options' )
                <x-form-group
                    :id="$id"
                    :class="$class"
                    :name="$name"
                    :label="$label"
                    :inline="$inline"
                    :disabled="$disabled"
                >
                    @foreach($options as $optionValue => $optionLabel)
                        <x-form-radio
                            :action="$action"
                            :bind="(object)[$name => in_array($optionValue, $value ?? []) ? $optionValue : null]"
                            :name="$name"
                            :label="$optionLabel"
                            :value="$optionValue"
                            :checked="is_array($value) && in_array($optionValue, $value)"
                            :disabled="$disabled"
                        />
                    @endforeach
                </x-form-group>
            @else
                <x-form-input
                    :id="$id"
                    :class="$class"
                    :action="$action"
                    :bind="[$name => getField($options, $value)]"
                    :name="$name"
                    type="text"
                    :label="$label"
                    :emits="$emits"
                />
            @endif
        @endif

    @elseif( in_array($type, ['image','images','file','files']) )
        @php
            $single = in_array($type, ['image','file']);
            $decodedValue = json_decode(json_encode($value), true);
            $files = is_array($decodedValue) ? array_map(function($item){ return ['id' => $item]; },$decodedValue) : (!empty($value) ? [['id' => $value]] : []);
            if(!isset($view)){
                $view = '';
            }
        @endphp
        @livewire('file-input', array_merge([
            'action'    => $action,
            'label'     => $label,
            'name'      => $name,
            'files'     => $files,
            'type'      => $type . (substr($type, -1) != 's' ? 's' : ''),
            'maxFiles'  => $single ? 1 : null,
        ], !empty($view) ? ['view' => $view] : []))

    @elseif( in_array($type, ['location','geolocation']) )
        <x-form-location
            :id="$id"
            :class=" 'form-control' . ' ' . $class"
            :action="$action"
            :bind="$bind"
            :name="$name"
            :lang="$localized"
            :type="$type"
            :label="$label"
            :placeholder="$placeholder"
            :displaySettings="$displaySettings"
            :mapConfiguration="$mapConfiguration"
        />

    @elseif( in_array($type, $validInputTypes) )
        <x-form-input
            :id="$id"
            :class=" 'form-control' . ' ' . $class"
            :action="$action"
            :bind="$bind"
            :name="$name"
            :lang="$localized"
            :type="$type"
            :label="$label"
            :placeholder="$placeholder"
        />
    @endif
@endif
