@php
    use App\Helpers\HForm;
    use App\Helpers\HFrontend;

    $projectAssetsPath  = HFrontend::getProjectPath();
    $projectPath        = HFrontend::getProjectPath(true);
    $parameters = $cb->getParameters(['flags.is_in_frontend_form' => true]);
    $modelId = data_get($topic ?? null, 'id', 0);
@endphp

@extends('backend.layouts.master')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
@endsection

@section('content')
    <x-backend.body class="g-3 mt-n3">
        @if(HForm::isShow())
            <div class="col-12 col-lg-4 order-lg-2">
                <div class="row g-3">
                    @include('backend.empatia.cbs.default.topics.partials.actions')
                    <div class="col-12">
                        <livewire:topic-status :topic="$topic" :cb="$cb"/>
                    </div>
                </div>
            </div>
        @endif

        <x-backend.card container="col-12 col-lg-8 order-lg-1">
            <x-backend.card-header>
                {{ $title }}
            </x-backend.card-header>

            <x-backend.card-body>
                <x-backend.form
                    :store="route('cbs.backend.topics.store', ['type' => $cb->type, 'cbId' => $cb->id ?? null])"
                    :update="route('cbs.backend.topics.update', ['type' => $cb->type, 'cbId' => $cb->id ?? null, 'id' => $modelId])"
                >
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {{ __('backend.empatia.cbs.topics.form.has-errors') }}
                        </div>
                        @dump($errors->all())
                    @endif
                    <x-backend.form.input-lang
                        :value="getField($topic, 'title')"
                        :action="HForm::getAction()"
                        mandatory="true"
                        name="title"
                        :label="__('backend.empatia.cb.topics.form.label.title')"
                        :placeholder="__('backend.empatia.cb.topics.form.label.title')"
                    />
                    <x-backend.form.textarea-lang
                        :value="getField($topic, 'content')"
                        :action="HForm::getAction()"
                        mandatory="true"
                        name="content"
                        :label="__('backend.empatia.cb.topics.form.label.content')"
                        :placeholder="__('backend.empatia.cb.topics.form.label.content')"
                    />
                    <x-backend.form.input
                        :value="getField($topic, 'number')"
                        type="number"
                        :action="HForm::getAction()"
                        name="number"
                        :label="__('backend.empatia.cbs.topics.form.topic-number.label')"
                        :placeholder="__('backend.empatia.cbs.topics.form.topic-number.placeholder')"
                    >
                    </x-backend.form.input>
                    @foreach($parameters ?? [] as $key => $parameter)
                        <div class="mb-3">
                            @include("backend.layouts.partials.parameter", [
                                'action'    => HForm::getAction(),
                                'name'      => "parameter_$parameter->code",
                                'id'        => "parameter_$parameter->code",
                                'value'     => getField($topic, "parameters.$parameter->code"),
                                'type'      => "$parameter->type",
                                'class'     => 'mb-2'
                            ])
                        </div>
                    @endforeach
                </x-backend.form>
            </x-backend.card-body>

            <x-backend.form.submit
                :create="route('cbs.show', ['type' => $cb->type, 'id' => $cb->id])"
                :edit="route('cbs.backend.topics.show', ['type' => $cb->type, 'cbId' => $cb->id,'id' => $modelId])"
            />
        </x-backend.card>
    </x-backend.body>
@endsection

