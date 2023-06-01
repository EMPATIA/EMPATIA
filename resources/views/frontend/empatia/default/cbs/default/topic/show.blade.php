@php
    use App\Helpers\HFrontend;

    $projectPath = HFrontend::getProjectPath(true);
@endphp

@extends("frontend.$projectPath.layouts.master")

@section('content')
    <div class="container-fluid">
        <div class="container mt-5">
            <div class="mb-4">
                <a href="{{ '/'. $topic->cb->type .'/'. getField($topic->cb, 'slug.'.getLang(), '') .'/' }}" class="d-inline" >
                    <i class="fas fa-arrow-left me-2"></i>{{ __("frontend.$projectPath.cbs.{$cb->type}.back") }}
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-md-6 my-2">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title mb-4">{{ __("frontend.$projectPath.cbs.{$cb->type}.topic.show.heading") }}</h2>

                            @if( getField($cb, 'data.configurations.topic.create.fields.title.required') === true )
                                <div class=" form-group ">
                                    <label for="auto_id_title">{{ __("frontend.$projectPath.cbs.{$cb->type}.topic.title.label") }}</label>
                                    <div class="form-text">
                                        <span class="form-input-no-text">{{ getField($topic, 'title.'.getLang(), '-') }}</span>
                                    </div>
                                </div>
                            @endif

                            @if( getField($cb, 'data.configurations.topic.create.fields.description.required') === true )
                                <div class=" form-group ">
                                    <label for="auto_id_title">{{ __("frontend.$projectPath.cbs.{$cb->type}.topic.content.label") }}</label>
                                    <div class="form-text">
                                        <span class="form-input-no-text">{{ getField($topic, 'content.'.getLang(), '-') }}</span>
                                    </div>
                                </div>
                            @endif

                            @bind($topic ?? null)

                            @foreach($cb->parameters ?? [] as $key => $parameter)
                                @include("frontend.$projectPath.layouts.partials.parameter", [
                                    'action'        => 'show',
                                    'name'          => "parameter_$parameter->code",
                                    'id'            => "parameter_$parameter->code",
                                    'value'         => getField($topic, "parameters.$parameter->code"),
//                                    'placeholder'   => "",
                                    'view'          => "frontend.$projectPath.livewire.files.file-input"
                                ])
                            @endforeach

                            @endbind

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
