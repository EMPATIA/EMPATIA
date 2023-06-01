<?php
    use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
    /** @var CookieConsentViewModel $model */
?>

<div id="{{ $model->ihmIds['popupNoticeId'] }}" class="popup-noticepopup-notice cookie-consent-popup-tab cookie-consent-popup-hider">
    <span class="description">{!!__("frontend.$projectPath.cookie-consent.description") !!}</span>
</div>