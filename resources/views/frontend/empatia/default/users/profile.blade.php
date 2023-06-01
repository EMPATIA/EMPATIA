@php use App\Http\Controllers\Frontend\Users\UsersController; @endphp
@extends("frontend.$projectPath.layouts.master")

@section('content')
    @if (!env('PROFILE_LIVEWIRE'))
        <div class="container my-5">
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
                                            <h5 class="profile-card-name">{{ getField($user,'name') }}</h5>
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
                                                        onclick="window.location='{{ route("profile.show",['tab'=>getField($tab,'code')]) }}'">
                                                    {{getField($tab,'label.'.getLang())}}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Profile Tabs-->
                            @if($activeTab==='generic')
                                <x-form action="{{ route('profile.updateUserGenericData') }}" method="PUT"
                                        class="col-lg-8">
                                    @bind($user)
                                    <div id="profile" class="pb-5">
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <div class="form-group">
                                                    <x-form-input action="{{App\Helpers\HForm::getAction()}}"
                                                                  class="{{ (HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' }}"
                                                                  name="firstName"
                                                                  :label="__('frontend.'.$projectPath.'.profile.generic.firt-name.label')"
                                                                  :readonly="App\Helpers\HForm::getInputReadonly(\App\Helpers\HForm::getAction())"
                                                    >
                                                    </x-form-input>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <x-form-input action="{{App\Helpers\HForm::getAction()}}"
                                                                  class="{{ (HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' }}"
                                                                  name="lastName"
                                                                  :label="__('frontend.'.$projectPath.'.profile.generic.last-name.label')"
                                                                  :readonly="App\Helpers\HForm::getInputReadonly(\App\Helpers\HForm::getAction())"
                                                    >
                                                    </x-form-input>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <x-form-input action="show"
                                                                  class="{{ (HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' }}"
                                                                  name="email"
                                                                  :label="__('frontend.'.$projectPath.'.profile.generic.email.label')"
                                                                  :readonly="App\Helpers\HForm::getInputReadonly(\App\Helpers\HForm::getAction())"
                                                    >
                                                    </x-form-input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Form Actions -->
                                    <div class="col-12 form-actions">
                                        <hr class="mt-2 mb-3">
                                        <div
                                            class="align-items-center d-flex flex-wrap justify-content-end">
                                            @if (App\Helpers\HForm::isEdit())
                                                <button class="btn btn-secondary mx-3" type="button"
                                                        onclick="window.location='{{ route("profile.show",['tab'=>$activeTab]) }}'">
                                                    {{ __("frontend.generic.cancel") }}
                                                </button>
                                                <button class="btn btn-primary mx-3"
                                                        type="submit">{{ __("frontend.generic.save") }}
                                                </button>
                                            @else
                                                <button class="btn btn-primary" type="button"
                                                        onclick="window.location='{{ route("profile.edit",['tab'=>$activeTab]) }}'">
                                                    {{ __("frontend.$projectPath.profile.btn.edit-profile.label") }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endbind
                                </x-form>

                            @endif

                            <!-- Profile Parameters-->
                            @if($activeTab==='details')
                                <x-form action="{{ route('profile.updateUserDetails') }}" method="PUT" class="col-lg-8">
                                    @bind($user)
                                    <div id="parameters" class="pb-5">
                                        <div class="row">
                                            @foreach(json_decode(json_encode($userParameters)) ?? [] as $parameter)
                                                <div class="form-group">
                                                    @include('frontend.'.$projectPath.'.layouts.partials.parameter', [
                                                    'action'    => App\Helpers\HForm::getAction(),
                                                    'name'      => "parameters_".getField($parameter,'code'),
                                                    'value'     => getField($user, "parameters.".getField($parameter,'code'))
                                                ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Form Actions -->
                                    <div class="col-12 form-actions">
                                        <hr class="mt-2 mb-3">
                                        <div
                                            class="align-items-center d-flex flex-wrap justify-content-end">
                                            @if (App\Helpers\HForm::isEdit())

                                                <button class="btn btn-secondary mx-3" type="button"
                                                        onclick="window.location='{{ route("profile.show",['tab'=>$activeTab]) }}'">
                                                    {{ __("frontend.generic.cancel") }}
                                                </button>
                                                <button class="btn btn-primary mx-3"
                                                        type="submit">{{ __("frontend.generic.save") }}
                                                </button>
                                            @else
                                                <button class="btn btn-primary" type="button"
                                                        onclick="window.location='{{ route("profile.edit",['tab'=>$activeTab]) }}'">
                                                    {{ __("frontend.$projectPath.profile.btn.edit-profile.label") }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endbind
                                </x-form>
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
                                <x-form action="{{ route('profile.updateUserPassword') }}" method="PUT"
                                        class="col-lg-8">
                                    @bind($user)
                                    <div id="password" class="pb-5">
                                        <div>
                                            <label class="mb-0 small text-muted">
                                                {{ __("frontend.$projectPath.profile.email-confirmation.label") }}
                                            </label>
                                            <div class="form-outline col-6">
                                                <x-form-input action="{{App\Helpers\HForm::getAction()}}"
                                                              class="{{ (HForm::getAction() == 'show') ? 'form-control-plaintext' : 'form-control' }}"
                                                              name="confirmEmail"
                                                              :label="__('frontend.'.$projectPath.'.profile.generic.confirm-email.label')"
                                                              :readonly="App\Helpers\HForm::getInputReadonly(\App\Helpers\HForm::getAction())"
                                                >
                                                </x-form-input>
                                            </div>
                                        </div>
                                        <!-- Form Actions -->
                                        <div class="col-12 form-actions">
                                            <hr class="mt-2 mb-3">
                                            <div
                                                class="align-items-center d-flex flex-wrap justify-content-end">
                                                @if (App\Helpers\HForm::isEdit())

                                                    <button class="btn btn-secondary mx-3" type="button"
                                                            onclick="window.location='{{ route("profile.show",['tab'=>$activeTab]) }}'">
                                                        {{ __("frontend.generic.cancel") }}
                                                    </button>
                                                    <button class="btn btn-primary mx-3"
                                                            type="submit">{{ __("frontend.generic.save") }}
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary" type="button"
                                                            onclick="window.location='{{ route("profile.edit",['tab'=>$activeTab]) }}'">
                                                        {{ __("frontend.$projectPath.profile.btn.edit-profile.label") }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endbind
                                </x-form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <livewire:user-profile :projectPath="$projectPath" :activeTab="$activeTab" :user="$user"/>
    @endif
@endsection
