@extends('backend::layouts.master')
@section('header')
    @if(HForm::isEdit($action ?? null))
        {{ __('cbs::cbs.configurations.form.edit.header') }}
    @elseif(HForm::isCreate($action ?? null))
        {{ __('cbs::cbs.configurations.form.create.header') }}
    @else
        {{ __('cbs::cbs.configurations.form.show.header') }}
    @endif

@endsection

@section('content')
    @php
        // TODO: move this to controller
        use Modules\CMS\Entities\Content;use Modules\Sites\Entities\Site;

        $sites = Site::get()->all();
        $contents = Content::get()->all();
    @endphp


    <div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-12 mb-4">
                <div class="card">
                    @if (HForm::isShow($action ?? null) || HForm::isEdit($action ?? null))
                        <div class="card-header action-header d-flex justify-content-between">
                            <span class="font-weight-bold">{{ $cb->title->{getLang()} }}</span>
                        </div>
                    @endif

                    <div class="card-body">
                        <x-form class="col-sm-12 p-0 m-0"
                                action="{{ action([\Modules\EMPATIA\Http\Controllers\Cbs\CbsConfigurationsController::class, 'update'], ['type' => $cb->type, 'id' => $cb->id]) }}"
                                method="PUT"
                        >
                            @foreach($configurations as $config)
                                @if( $config->type == 'checkbox' )
                                    <div class="row mb-2">
                                        <div class="cols-6 col-sm-6 col-md-4 col-lg-5 ">
                                            <div class="custom-control custom-switch">
                                                <input type="{{ $config->type }}" class="custom-control-input"
                                                       name="{{ $config->code }}"
                                                       data-input="{{ $config->input ?? '' }}"
                                                       @if( getField($cb->data, "configurations.$config->code.enabled") ) checked
                                                       @endif
                                                       @if(!HForm::isEdit()) disabled @endif
                                                       id="{{ $config->code }}"
                                                >
                                                <label class="custom-control-label"
                                                       for="{{ $config->code }}"
                                                >{{ getField($config, 'title.'.getLang()) ?? __('cbs::cbs.configurations.switch.'.$config->code . 'label') }} </label>
                                            </div>
                                        </div>

                                        @if( $configInput = findObjectByProperty('code', getField($config, 'input'), $configurations) )
                                            <div class="cols-6 col-sm-6 col-md-8 col-lg-7">
                                                @if( $configInput->type == 'text' )
                                                    @if(HForm::isEdit())
                                                        <input type="{{ $configInput->type }}" class="form-control"
                                                               name="{{ $configInput->code }}"
                                                               @if( !getField($cb->data, "configurations.$config->code.enabled", false) ) disabled
                                                               @endif
                                                               id="{{ $configInput->code }}"
                                                               value="{{ getField($cb->data, "configurations.$config->code.value", '') }}"
                                                        >
                                                    @else
                                                        {{ getField($cb->data, "configurations.$config->code.value", '-') }}
                                                    @endif
                                                @elseif( $configInput->type == 'select' )
                                                    @php
                                                        $options = $configInput->options ?? [];

                                                        if( $config->code == 'languages' ){
                                                            $options = [];
                                                            foreach (getLanguagesFrontend() as $lang){
                                                                $options[$lang['locale']] = $lang['locale'];
                                                            }
                                                        }
                                                    @endphp

                                                    @if(HForm::isEdit())
                                                        <x-form-select name="{{ $configInput->code }}[]"
                                                                       :options="$options ?? []"
                                                                       icon="chevron-down"
                                                                       outerClass="mb-0"
                                                                       :placeholder="__('cbs::cbs.configurations.select.default.placeholder')"
                                                                       :disabled="!getField($cb->data, 'configurations.'.$config->code.'.enabled', false) ? 'true' : 'false'"
                                                        >
                                                        </x-form-select>
                                                    @else
                                                        @if( $config->code == 'contents' )
                                                            @php
                                                                $arrayList = collect($contents)->whereIn('id', getField($cb->data, "configurations.$config->code.value", []))->pluck( 'title.'.getLang() )->toArray();
                                                            @endphp
                                                        @else
                                                            @php
                                                                $arrayList = getField($cb->data, "configurations.$config->code.value", []);
                                                            @endphp
                                                        @endif
                                                        {{ implode(',', $arrayList) }}
                                                    @endif
                                                @elseif( $configInput->type == 'select2' )
                                                    @php
                                                        $options = $configInput->options ?? [];

                                                        if( $config->code == 'languages' ){
                                                            $options = [];
                                                            foreach (getLanguagesFrontend() as $lang){
                                                                $options[$lang['locale']] = $lang['locale'];
                                                            }
                                                        }
                                                    @endphp

                                                    @if(HForm::isEdit())
                                                        <select
                                                                multiple="true"
                                                                class="form-control select2-multiple w-100"
                                                                name="{{ $configInput->code }}[]"
                                                                data-disabled="{{ !getField($cb->data, "configurations.$config->code.enabled", false) ? 'true' : 'false' }}"
                                                                data-width="100%"
                                                                @if(HForm::isShow())
                                                                    action="{{App\Helpers\HForm::getAction()}}"
                                                                @endif
                                                        >
                                                            @if( $config->code == 'contents' )
                                                                @foreach($sites as $site)
                                                                    <optgroup label="{{ $site->name}}">
                                                                        @foreach(collect($contents)->where('site_id', $site->id) ?? [] as $siteContent)
                                                                            <option
                                                                                    value="{{ $siteContent->id }}"
                                                                                    @if( in_array($siteContent->id, getField($cb->data, "configurations.$config->code.value", [])) ) selected @endif
                                                                            >
                                                                            {{ getField($siteContent, 'title.'.getLang()) }}
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            @else
                                                                @foreach($options as $optVal => $optLabel)
                                                                    <option
                                                                            value="{{ $optVal }}"
                                                                            @if( in_array($optVal, getField($cb->data, "configurations.$config->code.value", [])) ) selected @endif
                                                                    >
                                                                    {{ !is_string($optLabel) ? getField($optLabel, getLang(), '-') : $optLabel }}
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @else
                                                        @if( $config->code == 'contents' )
                                                            @php
                                                                $arrayList = collect($contents)->whereIn('id', getField($cb->data, "configurations.$config->code.value", []))->pluck( 'title.'.getLang() )->toArray();
                                                            @endphp
                                                        @else
                                                            @php
                                                                $arrayList = getField($cb->data, "configurations.$config->code.value", []);
                                                            @endphp
                                                        @endif
                                                        {{ implode(',', $arrayList) }}
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            <div class="mt-4">
                                <x-form-submit></x-form-submit>
                                <x-backend::form-cancel class="border-0"
                                                        href="{{action([\Modules\EMPATIA\Http\Controllers\Cbs\CbsController::class, 'show'], ['type' => $cb->type, 'id' => $cb->id])}}"
                                >
                                </x-backend::form-cancel>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
            @if (HForm::isShow($action ?? null))
                <div class="col-lg-4 col-md-12 side-cards">
                    <div class="card card-sections mb-4">
                        <ul class="list-group list-group-flush section-right-buttons">
                            <button class="list-group-item list-group-item-action bg-light"
                                    onclick="location.href='{{ action([\Modules\EMPATIA\Http\Controllers\Cbs\CbsConfigurationsController::class, 'edit'],['type' => $cb->type, 'id' =>  $cb->id]) }}'">
                                <i class=" fas fa-pencil-alt"></i>
                                <span>{{ __('cbs::cbs.configurations.btn.edit.label') }}</span>
                            </button>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('.select2-multiple').each(function () {
                let disabled = $(this).data('disabled');
                $(this).select2({
                    placeholder: "{{ __('cbs::cbs.configurations.select2.placeholder') }}",
                    allowClear: true,
                    disabled: disabled
                });
            });
            $('input[type="checkbox"]').change(function () {
                let name = $(this).data('input');
                let checked = $(this).prop('checked');
                $('*[name*="' + name + '"]').prop('disabled', !checked);
            });
        });
    </script>
@endsection
