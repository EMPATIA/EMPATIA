{{-- @if ($paginator->hasPages()) --}}
<div class="container-fluid">
    <div class="container">
        <div class="row py-4 info-numerica">
            <div class="col-lg-4">
                <div>
                    Showing {{ $paginator->count() }} of {{ $paginator->total() }} results
                </div>
            </div>
            <div class="col-lg-4 info-pages">
                @if ($paginator->onFirstPage())
                    <button class="disabled pageButton">
                        <a class="a-button"> < </a>
                    </button>
                @else
                    <button class="pageButton">
                        <a class="a-button" onclick="listGroup({{$paginator->currentPage()-1}})" rel="prev">
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
                                <li><a class="num-button" onclick="listGroup({{$page}})" >{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- <h3>&nbsp;{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}&nbsp;</h3> --}}

                @if ($paginator->hasMorePages())
                    <button class="pageButton">
                        <a class="a-button" onclick="listGroup({{$paginator->currentPage()+1}})" rel="next">
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
{{-- @endif --}}


@section("scripts")
    <script>

    function dropdownSelect(id, title){
        $("#moduleSelect-group").val(id);
        //console.log(title);
        $("#modules-select-span-group").html(title);
        let page = $("#modulePage-group").val();
        listGroup(page);
    }

    $(function() {
        $("#moduleSelect-group").change(function(){
            let page = $("#modulePage-group").val();
            listGroup(page);
        });
    });
    
    $(function() {
        $("#moduleSearch-group").keyup(delay(function(){
            let page = $("#modulePage-group").val();
            listGroup(page);
        },300))
    });

    function listGroup(page){
        let search = $('#moduleSearch-group').val();
        let moduleId = $('#moduleSelect-group').val();
        $.ajax({
            url: "/reflection-group",
            type:"POST",
            data:{
                page: page,
                moduleId: moduleId,
                search: search,
            },
            success:function(response){
                $()
                $("#groupDiv").html(response);
            },
        });
        if(moduleId || search){
            $(".clear-filters-div-group").show();
        }
        else{
            $(".clear-filters-div-group").hide()
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