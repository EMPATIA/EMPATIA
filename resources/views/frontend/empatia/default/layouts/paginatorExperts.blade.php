@if ($paginator->hasPages())
<div class="container-fluid">
    <div class="container">
        <div class="row py-4 info-numerica">
            {{-- <div class="col-lg-4">
                <div>
                    Showing {{ $paginator->count() }} of {{ $paginator->total() }} results
                </div>
            </div> --}}
            <div class="col-lg-12 info-pages">
                @if ($paginator->onFirstPage())
                    <button class="disabled pageButton">
                        <a class="a-button"> < </a>
                    </button>
                @else
                    <button class="pageButton">
                        {{-- <a class="a-button arrow-experts" data-increment="-1" rel="prev"> --}}
                            <a class="a-button arrow-experts" onclick="searchModules({{$paginator->currentPage()-1}})" rel="prev">
                            <
                            {{-- <img src="{{ asset('/assets/svg/icon-seta-esquerda-listas.svg') }}" alt="seta para a esquerda"> --}}
                        </a>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="disabled"><span>{{ $element }}</span></li>
                    @endif


                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="active my-active"><span>{{ $page }}</span></li>
                            @else
                                {{-- <li><a class="num-button num-experts" data-page="{{ $page }}" >{{ $page }}</a></li> --}}
                                <li><a class="num-button num-experts" onclick="searchModules({{$page}})" >{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- <h3>&nbsp;{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}&nbsp;</h3> --}}

                @if ($paginator->hasMorePages())
                    <button class="pageButton">
                        {{-- <a class="a-button arrow-experts" data-increment="1" rel="next"> --}}
                            <a class="a-button arrow-experts" onclick="searchModules({{$paginator->currentPage()+1}})" rel="next">
                            >
                            {{-- <img src="{{ asset('/assets/svg/icon-seta-direita-listas.svg') }}" alt="seta para a direita"> --}}
                        </a>
                    </button>
                @else
                    <button class="disabled pageButton">
                        <a class="a-button"> > </a>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@section("scripts")
    <script>

    function dropdownSelect(id, title){
        $("#moduleSelect").val(id);
        //console.log(title);
        $("#modules-select-span").html(title);
        let page = $("#modulePage").val();
        searchModules(page);
    }

    /* $(function() {
        $("#moduleSelect").change(function(){
            let page = $("#modulePage").val();
            searchModules(page);
        });
    }); */
    
    $(function() {
        $("#moduleSearch").keyup(delay(function(){
            let page = $("#modulePage").val();
            searchModules(page);
        },300))
    });

    function searchModules(page){
        let search = $('#moduleSearch').val();
        let moduleId = $('#moduleSelect').val();
        $.ajax({
            url: "/experts",
            type:"POST",
            data:{
                page: page,
                moduleId: moduleId,
                search: search,
            },
            success:function(response){
                $()
                $("#expertsArrayDiv").html(response);
            },
        });
        if(moduleId || search){
            $(".clear-filters-div").show();
        }
        else{
            $(".clear-filters-div").hide()
        }
    }

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
    </script>
@endsection