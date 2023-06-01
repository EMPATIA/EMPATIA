@php
    use \Illuminate\Support\Carbon;
    use App\Helpers\Empatia\Cbs\HCb;
    use App\Helpers\HFrontend;
    use App\Models\Empatia\Cbs\Cb;

    $projectAssetsPath  = HFrontend::getProjectPath();
    $projectPath        = HFrontend::getProjectPath(true);

    $cbs = CB::whereType($type ?? 'default')->whereDate('start_date', '<=', Carbon::now())->whereDate('end_date', '>=', Carbon::now())->get();
    $content = HFrontend::getContentByCode($type ?? 'default', 'unpublished');

    $logo = !empty($content) ? getField(HFrontend::getSectionWithCode(getField($content, "sections"), "logo"), 'value.0') : null;

    $openCbs = 0;
    $closedCbs = 0;
    foreach ($cbs ?? [] as $cb){
        if( Carbon::now() >= Carbon::parse($cb->start_date) && Carbon::now() <= Carbon::parse($cb->end_date) ){
            $openCbs++;
        } else {
            $closedCbs++;
        }
    }
@endphp

@extends("frontend.$projectPath.layouts.master")

@section('content')
    <div class="container-fluid">
        <div class="container mb-5">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-5 col-lg-5">
                    <img class="w-50 py-5"
                         src="{{ HFrontend::getSectionItemImage($logo ?? (object)[], 'default-image') }}"
                         alt="">
                </div>
                <div class="col-12 col-sm-6 col-md-7 col-lg-7">
                    <div class="row h-100 align-items-center">
                        <div class="col-12 col-md-6">
                            <div class="stats-container my-2">
                                <span class="big-txt">{{ __("frontend.$projectPath.cbs.$type.on-going") }}</span>
                                <span class="number">{{ $openCbs ?? '0' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="stats-container my-2">
                                <span class="big-txt">{{ __("frontend.$projectPath.cbs.$type.closed") }}</span>
                                <span class="number">{{ $closedCbs ?? '0' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse ($cbs as $cb)
                    <div class="col-12 col-md-6 py-3">
                        <div class="border container">
                            <div class="row justify-content-between p-2" style="background-color: #f1f1f1">
                                <div class="col-12 col-md-4 py-2">
                                    <span class="fw-bold" style="color: #8cc63f"><i
                                            class="ps-2 fas fa-inbox me-2"></i>{{ __("frontend.$projectPath.cbs.$type.active") }}</span>
                                </div>
                                <div class="col-12 col-md-6 row justify-content-end">
                                    <div class="col-2">
                                        <i class="fas fa-clock me-2 py-2" style="color: #8cc63f; font-size:30px"></i>
                                    </div>
                                    <div class="col-10">
                                        <div>{{ __("frontend.$projectPath.cbs.$type.start") .' '. Carbon::parse($cb->start_date)->format('d-m-Y') }}</div>
                                        <div>{{ __("frontend.$projectPath.cbs.$type.end") .' '. Carbon::parse($cb->end_date)->format('d-m-Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('page', [ HCb::getCbTypeSlug( getField($cb, 'type') ) , getField($cb, 'slug.'.getLang(), '') ]) }}"
                               class="cb-details"
                            >
                                @php
                                    $title = HFrontend::getSectionValue( HFrontend::getObjectsByCode(getField($cb, "info.sections"), "title") ?? (object)[]);
                                    $description = HFrontend::getSectionValue( HFrontend::getObjectsByCode(getField($cb, "info.sections"), "description") ?? (object)[]);
                                @endphp
                                <div class="p-1 pt-3">
                                    <h5 class="fw-bold">
                                        {{ !empty($title) ? $title : getfield($cb, 'title.' . getLang()) }}
                                    </h5>
                                    <p>
                                        {{ !empty($description) ? $description : getfield($cb, 'content.' . getLang()) ?? __("frontend.$projectPath.cbs.$type.cb.description.empty") }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5">
                        <h5 class="text-center"> {{ __("frontend.$projectPath.cbs.$type.index.empty") }}</h5>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection
