<div>
    <div class="container my-5">
        @if ($message = Session::get('success'))
            <div class="alert alert-success" role="alert">
                <div class="row mx-3">
                    <p class="col">{{ $message }}</p>
                    <button type="button" class="btn btn-close" data-dismiss="alert">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        <div class="card bg-light-grey">
            <div class="card-body">
                <div class="container mt-5">
                    <div class="row">

                        <!-- Account Sidebar-->
                        <div class="col-lg-4 pb-5">
                            <div class="p-3 profile-card pb-4 profile-card-profile">
                                <div class="row">
                                    <div class="align-items-center col-2 d-flex justify-content-end"><i
                                            class="far fa-user"></i></div>
                                    <div class="col-10 profile-card-details">
                                        <h5 class="profile-card-name">{{ $userName }}</h5>
                                        <p>{{ __("frontend.$projectPath.profile.email-verified.label") }}  {{getField($user,'emailVerified')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-tabs">
                                <ul class="nav list-group list-group-flush">
                                    @foreach($tabs as $tab)
                                        <li class="nav-item">
                                            <button type="button"
                                                    class="btn btn-primary list-group-item @if($activeTab==getField($tab,'code')) active @endif w-100"
                                                    wire:click="changeTabToShow('{{getField($tab,'code')}}')">
                                                {{getField($tab,'label.'.getLang())}}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- Profile Tabs-->
                        @if($activeTab==='generic')
                            @wire
                            <div id="profile" class="pb-5 col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <div class="form-group">
                                            <x-form-input action="{{$viewMode}}"
                                                          name="user.firstName"
                                                          :class="'form-control form-control-sm'"
                                                          :label="'Nome'"
                                                          wire:model="user.firstName"
                                            ></x-form-input>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-form-input action="{{$viewMode}}"
                                                          class="form-control form-control-sm"
                                                          name="user.lastName"
                                                          :label="'Apelido'"
                                                          wire:model="user.lastName"
                                            >
                                            </x-form-input>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-form-input action="show"
                                                          class="form-control form-control-sm"
                                                          name="email"
                                                          wire:model="user.email"
                                                          :label="'Email'"
                                            >
                                            </x-form-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endwire
                        @endif
                        <!-- Profile Parameters-->
                        @if($activeTab==='details')
                            @wire
                            <div id="parameters" class="pb-5 col-lg-8">
                                <div class="row">
                                    @foreach(json_decode(json_encode($userParameters)) ?? [] as $parameter)
                                        <div class="form-group">
                                            @include('frontend.'.$projectPath.'.layouts.partials.parameter', [
                                            'action'    => $viewMode,
                                            'name'      => "user.parameters.".getField($parameter,'code'),
                                            'value'     => getField($user, "parameters.".getField($parameter,'code'))
                                        ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endwire
                        @endif
                        <!-- Profile Proposals-->
                        @if($activeTab==='proposals')
                            <div class="col-lg-8">
                                <div class="row">
                                    <h6 class="col profile-card-name">{{ __("frontend.$projectPath.profile.proposals.title.header") }}</h6>
                                    <h6 class="col profile-card-name">{{ __("frontend.$projectPath.profile.proposals.category.header") }}</h6>
                                    <h6 class="col profile-card-name">{{ __("frontend.$projectPath.profile.proposals.created-at.header") }}</h6>
                                </div>

                                @foreach($userProposals as $proposal)
                                    <div class="row">
                                        <p class="col">
                                            {{getField($proposal,'title')}}
                                        </p>
                                        <div class="col">
                                            @foreach(getField($proposal,'category') as $category)
                                                <p>{{ $category }}</p>
                                            @endforeach
                                        </div>
                                        <p class="col">
                                            {{getField($proposal,'created_at')}}
                                        </p>
                                    </div>
                                    <hr class="mt-2 mb-3">
                                @endforeach
                            </div>

                        @endif
                        <!-- Update Password-->
                        @if($activeTab==='password')
                            @wire
                            <div id="password" class="pb-5 col-lg-8">
                                <div>
                                    <label class="mb-0 small text-muted">
                                        {{ __("frontend.$projectPath.profile.email-confirmation.label") }}
                                    </label>
                                    <div class="form-outline col-6">
                                        <x-form-input action="{{$viewMode}}"
                                                      class="form-control form-control-sm"
                                                      name="confirmEmail"
                                                      wire:model="confirmEmail"
                                        >
                                        </x-form-input>
                                    </div>
                                </div>
                            </div>
                            @endwire
                        @endif

                        @if($activeTab!=='proposals')
                            <!-- Form Actions -->
                            <div class="col-12 form-actions">
                                <hr class="mt-2 mb-3">
                                <div
                                    class="align-items-center d-flex flex-wrap justify-content-end">
                                    @if ($viewMode=='edit')
                                        <button class="btn btn-secondary mx-3" type="button"
                                                wire:click="changeViewMode('show')">
                                            {{ __("frontend.generic.cancel") }}
                                        </button>
                                        <button class="btn btn-primary mx-3"
                                                wire:click="@if($activeTab==='generic') updateUserGenericData @elseif($activeTab==='details') updateUserDetails @elseif($activeTab==='password') updateUserPassword @else @endif">{{ __("frontend.generic.save") }}
                                        </button>
                                    @else
                                        <button class="btn btn-primary" type="button"
                                                wire:click="changeViewMode('edit')">
                                            {{ __("frontend.$projectPath.profile.btn.edit-profile.label") }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Spinner -->
                    <div wire:loading class="loading-overlay">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="spinner-border text-info" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

