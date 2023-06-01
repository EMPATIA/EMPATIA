<div class="row py-2" id="section-{{ $key }}" data-position="{{ $key }}" wire:key="content-{{ $key }}">
        <x-backend.card class="shadow">
            <x-backend.card-header>
                @if(!empty(getField($section, 'name')))
                    {{ getFieldLang($section, 'name') ?? __('backend.cms.contents.show.sections.' .getField($section,'type',"") . '.title') }}
                @else
                    {{ __('backend.cms.contents.show.sections.' .getField($section,'type',""). '.title') }}
                @endif

                @if(!empty(getField($section, 'code')))
                    <span class="content-code">({{ getField($section, 'code') }})</span>
                @endif

                <x-slot:right>
                    @if(App\Helpers\HFrontend::isSectionIgnore($section))
                        <i class="fa-solid fa-eye-slash text-danger"></i>
                    @endif

                    <x-backend.btn
                        class="btn-light drag_handle"
                        title="{{ __('backend.generic.reorder') }}"
                    >
                        <i class="fas fa-expand-arrows-alt"></i>
                    </x-backend.btn>

                    <x-backend.btn
                        class="btn-light"
                        title="{{ __('backend.generic.delete') }}"
                        wire:click="deleteSection({{ $key }})"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </x-backend.btn>

                    <x-backend.btn
                        class="btn-light"
                        title="{{ __('backend.generic.configure') }}"
                        wire:click="$emitTo('livewire.backend.c-m-s.content.content-modal-section-config', 'loadModal', '{{$key}}')"
                    >
                        <i class="fas fa-cog"></i>
                    </x-backend.btn>

                    <x-backend.btn
                        :class="getField($section,'type') == 'code' ? 'btn-light disabled' : 'btn-light'"
                        title="{{ __('backend.generic.edit') }}"
                        wire:click="$emitTo('livewire.backend.c-m-s.content.content-modal-section', 'loadModal', '{{$key}}')"
                    >
                        <i class="fas fa-pencil-alt"></i>
                    </x-backend.btn>
                </x-slot:right>
            </x-backend.card-header>

            <x-backend.card-body>
                @if(getField($section,'type') == 'heading' || getField($section,'type') == 'text')
                    {!! getFieldLang($section, 'value', '-', $lang) !!}
                @elseif(getField($section,'type') == 'code')
                    {{ getField($section, 'code', '-') }}
                @elseif(getField($section,'type') == 'text-html')
                    <div class="content-text-html">{!! getFieldLang($section, 'value', '-', $lang) !!}</div>
                @elseif(getField($section,'type') == 'button')
                    {{ getField($section, 'value.'.$lang.'.title', '-') }}
                @elseif(getField($section,'type') == 'files')
                    @if(count((array)getField($section,'value', [])) <= 0)
                        {{ __('backend.cms.contents.show.sections.files.no-files') }}
                    @else
                        @foreach(getField($section,'value', []) as $file)
                            <div class="col-12 p-1">
                                <i class="fas fa-file"></i> {{ getField($file, 'filename') }}
                            </div>
                        @endforeach
                    @endif
                @elseif(getField($section,'type') == 'images')
                    @if(count((array)getField($section,'value', [])) <= 0)
                        {{ __('backend.cms.contents.show.sections.images.no-images') }}
                    @else
                        @foreach(getField($section,'value', []) as $image)
                            @php
                                $url = "";
                                if(!empty(getField($image, 'id'))) {
                                    $url = App\Http\Controllers\Backend\FilesController::getImageUrlByName(getField($image, 'id', ''), 200, 200, 'webp');
                                }
                            @endphp
                            <div class="mr-2 mb-2" style="display: inline-block">
                                <img style="width: 150px; height: 150px; object-fit: cover;" src="{{ $url }}" />
                            </div>
                        @endforeach
                    @endif
                @elseif(getField($section,'type') == 'list')
                    @if(count((array)getField($section,'value', [])) <= 0)
                        {{ __('backend.cms.contents.show.sections.list.no-items') }}
                    @else
                        @foreach(getField($section,'value', []) as $item)
                            <div class="col-12 p-1">
                                {{ getField($item, $lang.'.value', '-') }}
                            </div>
                        @endforeach
                    @endif
                @elseif(getField($section,'type') == 'video')
                    @if(count((array)($section->value ?? [])) <= 0)
                        {{ __('frontend.cms.content.show.sections.video.no-video') }}
                    @else
                        <div class="col-12 p-1">

                            {!! data_lang_get($section, 'value')  ?? '-' !!}
                        </div>
                    @endif
                @endif
            </x-backend.card-body>
        </x-backend.card>
</div>
