@php
    use \App\Helpers\Empatia\Cbs\HCb;
    use \App\Http\Controllers\Backend\FilesController;
@endphp
<style>
    .ideas-grid .idea-grid-image {
        object-fit: cover;
        height: 10rem;
    }
</style>
<div class="container-fluid overflow-hidden">
    <div class="container py-5">
        <div class="ideas-grid">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 mb-5 g-4">
                @foreach($topics ?? [] as $topic)
                    <div class="col d-flex align-items-stretch">
                        <div class="proposal-card zoom w-100 bg-white border border-primary-light">
                            <a href="{{ route('page', [HCb::getCbTypeSlug( getField($cb, 'type') ) , getField($cb, 'slug.'.getLang(), '') . '/' . getField($topic, 'slug.'.getLang(), $cb->type)]) }}"
                               class="text-decoration-none"
                            >
                                @php
                                    $coverImage = null;
                                    $coverImageCode = $topic->parameter('cover_image');
                                        if(!empty($coverImageCode) && is_string($coverImageCode)){
                                            $coverImage = FilesController::getFileUrlByName($topic->parameter('cover_image'));
                                        }
                                        $coverImage = $coverImage ?? $defaultImage;
                                @endphp
                                <img src="{{ $coverImage }}" class="idea-grid-image w-100">
                                <div class="card-body p-3 d-flex flex-column">
                                    <h3 class="fw-semibold">{{ getField($topic, 'title.'.getLang(), '-') }}</h3>
                                    <span
                                        class="small text-break text-dark">{{ substr(getField($topic, 'content.'.getLang(), '-'), 0, 147) .'...' }}</span>
                                    <div class="see-more-btn">
                                        <hr>
                                        <div class="float-start text-dark">
                                            @php
                                                $parameterOption = getField($cb->getParameterByProperty('code', 'estimated_value'), 'options', []);
                                                $parameterOptionLabels = collect($parameterOption)->filter(function ($option) use ($topic){
                                                    $topicCategory = $topic->parameter('estimated_value');
                                                    if(!empty($topicCategory) && is_array($topicCategory)){
                                                        if(in_array($option->code, $topic->parameter('estimated_value')))
                                                            return $option;
                                                    }

                                                })->pluck('label.'.getLang())->toArray();
                                            @endphp
                                            <p class="m-0"><i
                                                    class="fa-sharp fa-solid fa-tags me-2"></i>{{ implode(', ', $parameterOptionLabels) }}
                                            </p>
                                            @php
                                                $parameterOption = getField($cb->getParameterByProperty('code', 'category'), 'options', []);
                                                $parameterOptionLabels = collect($parameterOption)->filter(function ($option) use ($topic){
                                                    $topicCategory = $topic->parameter('category');
                                                    if(!empty($topicCategory) && is_array($topicCategory)){
                                                        if(in_array($option->code, $topic->parameter('category')))
                                                            return $option;
                                                    }

                                                })->pluck('label.'.getLang())->toArray();
                                            @endphp
                                            <p class="m-0"><i
                                                    class="fa-sharp fa-solid fa-tags me-2"></i>{{ implode(', ', $parameterOptionLabels) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if( empty($topics) || $topics->isEmpty() )
                <h4 class="text-center text-primary">{{ __("frontend.$projectPath.cbs.$cb->type.topics.list.empty") }}</h4>
            @endif
        </div>
    </div>
</div>








