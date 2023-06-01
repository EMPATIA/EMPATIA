@foreach(getField($content, "sections", []) as $key => $section)
    @if(empty(getField($section, "type")) || in_array('ignore', explode(" ", getField($section, 'class', ''))))
        @continue
    @endif

    @if(getField($section, "type") == 'code')
        @include("frontend.$projectPath.cms.code")
    @elseif(getField($section, "type") == 'text-html')
        @include("frontend.$projectPath.cms.partials.text-html")
    @elseif(getField($section, "type") == 'heading')
        @include("frontend.$projectPath.cms.partials.heading")
    @elseif(getField($section, "type") == 'text')
        @include("frontend.$projectPath.cms.partials.text")
    @elseif(getField($section, "type") == 'button')
        @include("frontend.$projectPath.cms.partials.button")
    @elseif(getField($section, "type") == 'list')
        @include("frontend.$projectPath.cms.partials.list")
    @elseif(getField($section, "type") == 'images')
        @include("frontend.$projectPath.cms.partials.images")
    @elseif(getField($section, "type") == 'files')
        @include("frontend.$projectPath.cms.partials.files")
    @else
        <div class="alert alert-danger" role="alert">
            UNKNOWN
            TYPE: {{ (getField($section, "type") ?? 'no type') . ' - ' . getField($section, "code", "no code") }}
        </div>
    @endif
@endforeach
