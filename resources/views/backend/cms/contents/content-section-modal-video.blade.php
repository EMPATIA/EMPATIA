<div class="form-group">
    <label>{{ __('backend.cms.content.show.modal.video.label')}}</label>
    <div class="input-group">
        <input class="form-control @error('value') is-invalid @enderror" type="text"
               wire:model.lazy="value.{{ getLang() }}" placeholder="{{ __('backend.cms.content.show.modal.video.placeholder')}}">
    </div>
    @if(!empty($videoErrorMessage))
        <div class="error invalid-feedback d-block">{{ $videoErrorMessage }}</div> 
    @endif
</div>

@php
    $sections = $this->getSections();
@endphp
@if($videoPreview)
    <iframe width="100%" height="415" src="{{getField($sections[$position], 'value.'.getLang())}}" title="YouTube video player"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen="" class="responsive-iframe"></iframe>
@endif


