{{--Create language selectors--}}
@foreach(getLanguagesFrontend() ?? [] as $language)
    @php($locale = getField($language, 'locale'))

    <span class="label-lang float-end mb-1">
        <span @if($isComponent) wire:click="changeLang('{{$locale}}')" @endif  {{--Function needed for the component know when language is changed--}}
                                class="label-lang-href text-white badge fs-6 me-1 {{$selectorLang == $locale ? 'fw-bold bg-primary' : 'bg-secondary fw-normal'}} {{empty($errors->get($name.$separator.$locale)) ? '' : ' bg-danger'}}"
                                data-lang="{{$locale}}"
                                role="button">

            @if(!empty($errors->get($name.$separator.$locale)))<i class="fa-solid fa-triangle-exclamation"></i>@endif

                {{$locale}}
        </span>
    </span>
@endforeach

{{--Script to change selector and input--}}
@pushonce('scripts')
    <script type="module">
        $(function(){
            reloadFunctions.add('languageSelector', () => {
                $(".label-lang-href").each(function () {
                    $(this).click(function () {
                        formSwitchLanguage($(this));
                    });
                });

                function formSwitchLanguage(object) {
                    let lang = $(object).data('lang');

                    @if($isComponent ?? false)
                    @this.selectorLanguage = lang;      // Send language selected to the component (The component needs to have the "selectorLanguage" property
                    @endif

                    // Show / Hide language input
                    $(".input-language").each(function () {
                        if ($(this).data('lang') == lang) {
                            $(this).removeClass('d-none');
                        } else {
                            $(this).addClass('d-none');
                        }
                    });

                    //Remove / Add classes from language selectors
                    $(".label-lang-href").each(function () {
                        if ($(this).data('lang') == lang) {
                            $(this).removeClass('bg-secondary fw-normal');
                            $(this).addClass('fw-bold bg-primary');
                        } else {
                            $(this).removeClass('fw-bold bg-primary');
                            $(this).addClass('bg-secondary fw-normal');
                        }
                    });
                }
            });
        });

    </script>
@endpushonce
