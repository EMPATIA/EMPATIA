<?php
    use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
    /** @var CookieConsentViewModel $model */
    $projectPath = App\Helpers\HFrontend::getProjectPath(true);
 ?>

<link href="{{ asset('cookie-consent/css/app.css') }}" rel="stylesheet" />


{{-- if user hasn't consented to popup, puts everything behind the popup disabled/with a different color --}}
@if(!$model->hasConsented)
    <div class="position-fixed top-0 left-0 w-100 h-100 bg-dark" style="opacity: 0.6; z-index: 9999;"></div>
        {{-- Prevents user from scrolling --}}
        <style>
            body {
                overflow: hidden;
            }
        </style>
    </div>
@endif

{{-- {{ App::setLocale('en') }}; --}}


<div id="{{ $model->ihmIds['cookieConsentPopupContainerId'] }}" class="cookie-consent-popup-container {{ $model->positionClass }} cookie-consent-popup-hider {{ !empty($model->customClass) ? $model->customClass : '' }}">
    <span class="title">{{ __("frontend.$projectPath.cookie-consent.title") }}</span>

    @if($model->hasConsented)
        <button id="{{ $model->ihmIds['closeButtonId'] }}" class="cookie-consent-close-btn">X</button>
    @endif

    <div class="cookie-consent-popup-content">
        @include('cookie-consent::popup-notice', ['model' => $model])
        @include('cookie-consent::popup-preferences', ['model' => $model])
    </div>

    @include('cookie-consent::popup-actions', ['model' => $model])
</div>

<script type="text/javascript">
    authorizeAllButtonId = "{{ $model->ihmIds['authorizeAllButtonId'] }}";
    backButtonId = "{{ $model->ihmIds['backButtonId'] }}";
    closeButtonId = "{{ $model->ihmIds['closeButtonId'] }}";
    cookieConsentPopupContainerId = "{{ $model->ihmIds['cookieConsentPopupContainerId'] }}";
    hasConsented = @json($model->hasConsented);
    openPreferencesLinkId = "{{ $model->ihmIds['openPreferencesLinkId'] }}";
    popupNoticeId = "{{ $model->ihmIds['popupNoticeId'] }}";
    popupPreferencesId = "{{ $model->ihmIds['popupPreferencesId'] }}";
    refuseAllButtonId = "{{ $model->ihmIds['refuseAllButtonId'] }}";
    savePreferencesButtonId = "{{ $model->ihmIds['savePreferencesButtonId'] }}";
    updatePreferencesButtonIds = @json($model->updatePreferencesButtonIds);
</script>
<script src="{{ asset('cookie-consent/js/app.js') }}"></script>