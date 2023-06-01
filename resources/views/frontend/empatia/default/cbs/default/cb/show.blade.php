@php
    use Illuminate\Support\Carbon;
    use App\Models\Backend\CMS\Content;
    use App\Helpers\Empatia\Cbs\HCb;
    use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController;
    use App\Models\Empatia\Cbs\Topic;
    use App\Helpers\HFrontend;

    $projectPath = HFrontend::getProjectPath(true);
    $activeContent = $cb->activeContent();
@endphp

@extends("frontend.$projectPath.layouts.master")

@section('content')
    @include("frontend.$projectPath.partials.generic-banner", [
        'bannerHeight'  => '40vh',
        'title'         => data_lang_get($cb, 'title', __("frontend.$projectPath.cbs.$cb->type.cb.show.heading"))
    ])

    {{--  HEADING  --}}

    <div class="container-fluid">
        <div class="container my-5">
            {{-- TODO: Get title from content? --}}
            {{--            <h4 class="text-uppercase">{{ data_lang_get($cb, 'title', __("frontend.$projectPath.cbs.$cb->type.cb.show.heading")) }}</h4>--}}
            <a class="d-inline" href="{{ route('page', ['']) }}">
                <i class="fas fa-arrow-left me-2"></i>{{ __("frontend.$projectPath.back") }}
            </a>
        </div>
    </div>

    {{--  CONTENT  --}}

    @if( !empty($activeContent) )
        <section class="my-n5">
            @include("frontend.$projectPath.cms.sections", ['content' => $activeContent])
        </section>
    @endif

    {{--  TOPICS LIST  --}}

    @livewire('fe-topics-list', [
        'cb' => $cb
    ])
@endsection
