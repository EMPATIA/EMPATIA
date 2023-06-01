<div class="row h-100">
    <div class="col-8 items-list overflow-auto h-100" id="section_items_list">
        @forelse($list as $key => $item)
            <div class="row py-2 @if($itemPosition !== null && $key == $itemPosition) be-bg-light @endif">
                <div class="col-8 text-truncate">{{ ($item->{getLang()}->value ?? '-') }}</div>
                <div class="col-4 text-right">
                    <button class="btn btn-secondary btn-sm drag_handle" style="font-size: .7rem;"><i class="fas fa-expand-arrows-alt"></i></button>
                    <button class="btn btn-danger btn-sm" style="font-size: .7rem;" wire:click="itemDelete('{{ $key }}')"><i class="far fa-trash-alt"></i></button>
                    <button class="btn btn-primary btn-sm" style="font-size: .7rem;" wire:click="itemSelected('{{ $key }}')"><i class="fas fa-cog"></i></button>
                </div>
            </div>
        @empty
            {{ __('backend.cms.contents.show.modal.list.no-items') }}
        @endforelse
    </div>

    <div class="col-4 h-100">
        <div class="col alert alert-secondary h-100 overflow-auto mb-0">
            @if($itemPosition !== null)
                <div class="row">
                    <div class="col-12">
                        <div class=" form-group ">
                            <label>{{ __('backend.cms.contents.show.modal.list.code.label') }}</label>
                            <div class="input-group">
                                <input class="form-control" type="text" wire:model.lazy="list.{{ $itemPosition }}.code">
                            </div>
                            @error('list.{{ $itemPosition }}.code')
                                <div class="error invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @foreach(getLanguagesFrontend() as $language)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="language-{{ $language['locale'] }}" wire:model="list.{{ $itemPosition }}.{{ $language['locale'] }}.enabled">
                                <label class="form-check-label font-weight-bold" for="language-{{ $language['locale'] }}">{{ $language['name']." ".__('backend.cms.contents.show.modal.list.enabled.label') }}</label>
                            </div>
                        </div>
                        <div class="col-12 @if(!data_get($list, $itemPosition.".".$language['locale'].'.enabled', false)) d-none @endif">
                            <div class=" form-group">
                                <label>{{ __('backend.cms.contents.show.modal.list.value.label').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="list.{{ $itemPosition }}.{{ $language['locale'] }}.value">
                                </div>
                            </div>
                            <div class=" form-group ">
                                <label>{{ __('backend.cms.contents.show.modal.list.desc.label').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="list.{{ $itemPosition }}.{{ $language['locale'] }}.desc">
                                </div>
                            </div>
                            <div class=" form-group ">
                                <label>{{ __('backend.cms.contents.show.modal.list.options.label').' ('.$language['name'].')' }}</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" wire:model.lazy="list.{{ $itemPosition }}.{{ $language['locale'] }}.options">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{ __('backend.cms.contents.show.modal.list.not-selected.label') }}
            @endif
        </div>
    </div>
</div>

<script>
    new Sortable(document.getElementById('section_items_list'), {
        handle: '.drag_handle',
        animation: 600,
        ghostClass: 'drag_drop_class',

        onEnd: function (evt) {
            @this.emitSelf('sectionItemMoved', evt.oldIndex, evt.newIndex)
        },
    });
</script>
