@php
    $pagescontent = App\Helpers\HFrontend::getContentByCode('pages-content');

    $w = 100;
	$h = 100;
	$f = 'webp';

	/* if(App\Helpers\HFrontend::getSectionOptions($section) == 'no-resize') {
		$w = null;
		$h = null;
		$f = null;
	} */
@endphp

<footer>
    <div class="footer pt-md-3 py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <p class="footer-title">{{ __('frontend::footer.contacts') }}</p>
                    <div class="contacts">
                        @foreach($pagescontent->sections ?? [] as $key => $section)
                            @if(empty($section->type))
                                @continue
                            @endif

                            @if($section->type == 'text-html')
                                @include('frontend.cms.partials.text-html')
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bar py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 15px; margin-top: 5px;">
                    <div style="display: inline-block;">
                        <p class="pr-4">{{ __('frontend::footer.supported_by') }}</p>
                        @foreach($pagescontent->sections ?? [] as $key => $section)
                            @if(empty($section->type))
                                @continue
                            @endif

                            @if($section->type == 'images')
                                @if(App\Helpers\HFrontend::getSectionCode($section) == 'supported-by')
                                    @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                                        <div style="float: left">
                                            <div class="supported-by">
                                                <img class="supported-by-img" alt="{{ App\Helpers\HFrontend::getSectionItemField($item, 'alt') }}"
                                                     title="{{ App\Helpers\HFrontend::getSectionItemField($item, 'name') }}"
                                                     src="{{ App\Helpers\HFrontend::getSectionItemImage($item, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                                                >
                                            </div>
                                        </div>
                                    @empty
                                        {{ __('frontend:section.list.empty') }}
                                    @endforelse
                                @endif
                            @endif
                        @endforeach
                    </div>
                    <div class="footer-logo" style="display: inline-block;">
                        <p>{{ __('frontend::footer.parteners') }}</p>
                        <div style="float: left">
                            <div class="col-lg-12 d-flex flex-wrap partners-img">
                                @foreach($pagescontent->sections ?? [] as $key => $section)
                                    @if(empty($section->type))
                                        @continue
                                    @endif

                                    @if($section->type == 'images')
                                        @if(App\Helpers\HFrontend::getSectionCode($section) == 'partners')
                                            @forelse(App\Helpers\HFrontend::getSectionEnabledItems($section)  as $item)
                                                <a class="partners-a" href="{{ App\Helpers\HFrontend::getSectionItemField($item, 'link') }}" target="_blank">
                                                    <img alt="{{ App\Helpers\HFrontend::getSectionItemField($item, 'alt') }}"
                                                        title="{{ App\Helpers\HFrontend::getSectionItemField($item, 'name') }}"
                                                        src="{{ App\Helpers\HFrontend::getSectionItemImage($item, 'http://default.image.url.pt/image.png', $w, $h, $f) }}"
                                                ></a>
                                            @empty
                                                {{ __('frontend:section.list.empty') }}
                                            @endforelse
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- <!-- Modal -->
<div class="modal fade" id="unsubModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-subscribe" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('charity/img/mail-cancel-icon.svg')}}" class="d-block mx-auto mb-2 mt-4">
                <p class="h6 mb-4">{{__('frontend::footer.newsletter.unsubscribe_prompt')}}</p>
                <div class="form-group">
                    <label for="exampleInputEmail1">{{ __('frontend::footer.newsletter.email') }}</label>
                    <input name="unsub" type="email" class="form-control"  aria-describedby="emailHelp" placeholder="{{ __('frontend::footer.newsletter.enter_email') }}">
                </div>
                <button id="unsubscribe-button" class="regular-btn mt-4 mb-3 blue">{{ __('frontend::footer.newsletter.confirm_unsubscribe') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalMailSent" tabindex="-1" role="dialog" aria-labelledby="modalMailSentLabel" aria-hidden="true">
    <div class="modal-dialog modal-subscribe" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('charity/img/mail-sent.svg')}}" class="d-block mx-auto mb-2 mt-4">
                <div id="confirm-subscription" class="d-none">
                    <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.thank_you_sign_up') }}</p>
                    <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.confirm_subscription_with_email') }}</p>
                    <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.confirm_subscription_agree') }} <a href="/use-terms" target="_blank">{{__('frontend::footer.newsletter.terms_link')}}</a> {{ __('frontend::footer.newsletter.and') }} <a href="/privacy-policy" target="_blank">{{__('frontend::footer.newsletter.privacy_policy_link')}}</a></p>
                </div>
                <div id="confirm-unsubscription" class="d-none">
                    <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.sad_to_see_you_gone') }}</p>
                    <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.confirm_unsubscription_with_email') }}</p>
                </div>
                <p class="h6 mb-4 mt-4">{{ __('frontend::footer.newsletter.check_spam') }}</p>

                <button data-dismiss="modal" class="regular-btn mt-4 mb-3 blue">{{ __('frontend::newsletter.close') }}</button>
            </div>
        </div>
    </div>
</div> --}}
