@php
    use Illuminate\Support\Carbon;
    use App\Models\Backend\CMS\Content;
    use App\Helpers\Empatia\Cbs\HCb;
    use App\Helpers\HFrontend;

    $projectAssetsPath  = HFrontend::getProjectPath();
    $projectPath        = HFrontend::getProjectPath(true);

    $parameters = $cb->getParameters();

    /*  Validations  */
//    $isAuthRequired = getField(CbHelpers::getConfig($cb, 'login_required'), 'active') == 'on';
//    $userHasResponded   = $cb->topics->where('created_by', auth()->id())->first();
//    if( ($isAuthRequired && !auth()->user()) || $userHasResponded ){
//        header("Location: " . route('page', [ CbHelpers::getCbTypeSlug( getField($cb, 'type') ) , getField($cb, 'slug.'.getLang(), '') ]), true, 302);
//        exit();
//    }

@endphp

@extends("frontend.$projectPath.layouts.master")

@section('content')
    {{--  FORM  --}}
    @include("frontend.$projectPath.partials.generic-banner", [
        'bannerHeight'  => '40vh',
        'title'         => data_lang_get($cb, 'title', __("frontend.$projectPath.cbs.$cb->type.cb.show.heading"))
    ])
    <div class="container-fluid">
        <div class="container py-5">
            <div class="mb-4">
                <a href="{{ '/'/*. HCb::getCbTypeSlug( getField($cb, 'type') ) .'/'. getField($cb, 'slug.'.getLang(), '') .'/'*/ }}"
                   class="d-inline">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('frontend.generic.back') }}
                </a>
            </div>
            <h3 class="mb-4 text-center">{{__("frontend.$projectPath.cbs.$cb->type.topic.create.heading")}}</h3>
            <div class="row justify-content-center">
                <div class="card col-12 col-md-8 my-2">
                    <div class="card-body p-4">
                        @if( $action == 'create' && !$cb->isTopicActionAuthorized($action) )
                            <div class="alert alert-warning text-center border-0 mb-0" role="alert">
                                {!! __("frontend.$projectPath.cbs.$cb->type.form.cannot-create") !!}
                            </div>
                        @elseif( $action == 'edit' && !$cb->isTopicActionAuthorized($action) )
                            <div class="alert alert-warning text-center border-0 mb-0" role="alert">
                                {!! __("frontend.$projectPath.cbs.$cb->type.form.cannot-edit") !!}
                            </div>
                        @else
                            <x-form class="mt-4" method="POST"
                                    action="{{ empty($topic) ? route('frontend.topics.store', [ 'cbType' => $cb->type, 'cbId' => $cb->id ]) : route('frontend.topics.update', [ 'cbType' => $cb->type, 'cbId' => $cb->id, 'topicId' => $topic->id ]) }}">

                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger text-center py-2 rounded-0" role="alert">
                                        {{ __("frontend.$projectPath.cbs.$cb->type.form.has-errors") }}
                                    </div>
                                    @dump($errors->all())
                                @endif

                                <input type="hidden" name="cbId" value="{{ $cb->id ?? '' }}">
                                <input type="hidden" name="cbVersion" value="{{ $cb->version ?? '' }}">

                                <x-form-input
                                    action="edit"
                                    name="title"
                                    :label='__("frontend.$projectPath.cbs.$cb->type.topic.form.title.label")." *"'
                                    :placeholder='__("frontend.$projectPath.cbs.$cb->type.topic.form.title.placeholder")'
                                    class="form-control mb-2"
                                ></x-form-input>

                                <x-form-textarea
                                    action='create'
                                    name="content"
                                    :label='__("frontend.$projectPath.cbs.$cb->type.topic.form.content.label")." *"'
                                    :placeholder='__("frontend.$projectPath.cbs.$cb->type.topic.form.content.placeholder")'
                                    class="form-control mb-2"
                                />

                                @foreach($cb->parameters ?? [] as $key => $parameter)
                                    @include("frontend.$projectPath.layouts.partials.parameter", [
                                        'action'    => 'edit',
                                        'name'      => "parameter_$parameter->code",
                                        'id'        => "parameter_$parameter->code",
                                        'value'     => null,
                                        'type'      => "$parameter->type",
                                        'view'      => "frontend.$projectPath.livewire.files.file-input",
                                        'class'     => 'mb-2'
                                    ])
                                @endforeach
                                @endbind

                                <div class="row">
                                    <div class="col-lg-12">
                                        <p class="mt-3">{{__("frontend.generic.mandatory")}}</p>
                                        <div
                                            class="row default-form questionnaire-form-btn justify-content-between">
                                            <div class="col-md-6 col-12">
                                                <a href="{{ '/'/*. HCb::getCbTypeSlug( getField($cb, 'type') ) .'/'. getField($cb, 'slug.'.getLang(), '') .'/'*/ }}"
                                                   class="btn btn-secondary w-100">
                                                    {{ __('frontend.generic.cancel') }}
                                                </a>
                                            </div>
                                            <div class="col-md-6 col-12 mt-2 mt-md-0">
                                                <button type="submit"
                                                        class="btn btn-primary w-100">{{ __('frontend.generic.confirm') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


