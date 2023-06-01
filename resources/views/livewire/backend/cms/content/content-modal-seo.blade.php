<div class="modal-content">
    <x-backend.modal-header>
        {{ __('backend.cms.contents.show.seo.title') }}
    </x-backend.modal-header>

    <x-backend.modal-body>
        @foreach(App\Helpers\HContent::getContentConfigurations($contentType)->seo ?? [] as $type => $group)
            <div class="col-12 @if(!$loop->first) mt-4 @endif">
                <h5>{{ __('backend.cms.contents.show.seo.type.'.$type.'.header') }}</h5>
            </div>

            @foreach($group as $code => $field)
                @if(empty($field->locale))
                    <x-backend.form.input 
                        :label="$code.(empty($field->max) ? '' : ' [max: '.$field->max.' ]').(empty($field->seo) ? '' : ' <small> = SEO '.$field->seo.'</small>').(empty($field->code) ? '' : ' <small> = Section '.$field->code.'</small>')"
                        :placeholder="__('backend.cms.contents.show.details.' . $code .'.placeholder')"
                        :action="App\Helpers\HForm::$EDIT"
                        :wire:model.lazy="'seo.'.$code"
                    />
                    <small class="form-text text-muted mb-3">
                        &lt;meta property="{{$code}}" content="{{ empty($seo[$code]) ? App\Helpers\HContent::getSeoDefault($code, $field, $content, $seo) : $seo[$code] }}"&gt;
                    </small>
                @else
                    @foreach(getLanguagesFrontend() as $language)
                        <x-backend.form.input 
                            :label="$code.'  ('.$language['name'].')'.(empty($field->max) ? '' : ' [max: '.$field->max.' ]').(empty($field->seo) ? '' : ' <small> = SEO '.$field->seo.'</small>').(empty($field->code) ? '' : ' <small> = Section '.$field->code.'</small>')"
                            :placeholder="__('backend.cms.contents.show.details.' . $code .'.placeholder')"
                            :action="App\Helpers\HForm::$EDIT"
                            :wire:model.lazy="'seo.'.$code.'.'.$language['locale']"
                        />
                        <small class="form-text text-muted mb-3">
                            &lt;meta property="{{$code}}" content="{{ empty($seo[$code]->{$language['locale']}) ? App\Helpers\HContent::getSeoDefault($code, $field, $content, $seo, $language['locale']) : $seo[$code]->{$language['locale']} }}"&gt;
                        </small>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </x-backend.modal-body>

    <x-backend.modal-footer close="true" />
</div>
