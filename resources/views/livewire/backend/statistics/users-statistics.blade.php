<div>
    <style>
        .card-dark{
            background-color: var(--be-primary);
            color: white;
            box-shadow: none;
        }
        .card-dark hr{
            border-color: rgba(255 255 255 / .2);
        }
        .loading-overlay .spinner-border{
            color: var(--be-primary);
        }
        .nav-tabs .nav-link.active{
            background-color: #FBFBFB;
            border-bottom-color: #FBFBFB;
        }
        .tabs-container{
            background-color: #FBFBFB !important;
        }

        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
            color: #495057;
            border-color: #dee2e6 #dee2e6 #fff !important;
        }
    </style>
    {{--   LOADING   --}}
    <div class="loading-overlay" wire:loading style="z-index:15; position: fixed;">
        <div class="d-flex align-items-center justify-content-center h-100">
            <div wire:loading.block class="spinner-border" role="status">
                <span class="sr-only">{{ __('cms.menus.loading') }}</span>
            </div>
        </div>
    </div>

    {{--   GRAPHS & TABLES   --}}
    <div class="card my-4">
        <div class="card-body pb-0">
            <div class="row row-cols-4">
                <div class="col">
                    <x-form-label :label="__('backend.statistics.users-parameters.parameters.label')"
                                  class="bold"></x-form-label>
                    <x-form-select name="activeParameter"
                                   :placeholder="__('backend.statistics.users-parameters.parameters.placeholder')"
                                   :options="$param_options ?? []"
                                   icon="chevron-down"
                                   wire:model="activeParameter"
                    >
                    </x-form-select>
                </div>
                <div class="col">
                    <x-form-input
                        :bind="['startDateUser' => $startDateUser]"
                        name="startDateUser"
                        type="date"
                        wire:model.debounce.500ms="startDateUser"
                        action="edit"
                        :label="__('backend.generic.start-date')"
                    />
                </div>
                <div class="col">
                    <x-form-input
                        :bind="['endDateUser' => $endDateUser]"
                        name="endDateUser"
                        type="date"
                        wire:model.debounce.500ms="endDateUser"
                        action="edit"
                        :label="__('backend.generic.end-date')"
                    />
                </div>
            </div>
        </div>
            <div class="card-body pb-0">
                {{--   CITY TABS   --}}
                <ul class="nav nav-tabs mx-n4 px-4 w-100" id="statsBreakdownTab" role="tablist">
                    @foreach($panes ?? [] as $pane)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activePane == $pane ? 'active' : '' }}" id="{{$pane}}-tab" role="tab"
                               aria-controls="{{$pane}}-tab"
                               aria-selected="false"
                               wire:click="changePane('{{$pane}}')"
                            >{{ __("backend.statistics.$type.tab.$pane") }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body tabs-container">
                <div class="tab-content">
                    @foreach($panes ?? [] as $pane)
                        <div class="tab-pane {{ $activePane == $pane ? 'active' : '' }}"
                             id="{{$pane}}-pane"
                             role="tabpanel" aria-labelledby="{{$pane}}-tab"
                        >
                            @includeIf("livewire.backend.statistics.$type-$pane-tab")
                        </div>
                    @endforeach
                </div>
            </div>
    </div>
</div>
