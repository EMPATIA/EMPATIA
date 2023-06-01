@php
    $consortium = [
        [ 'id' => '1', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '2', 'name' => 'ICT-Ficial', 'acronym' => 'ICT-FI', 'link' => 'http://www.ictficial.com/' ],
        [ 'id' => '3', 'name' => 'OneSource', 'acronym' => 'ONE', 'link' => 'https://onesource.pt/' ],
        [ 'id' => '4', 'name' => 'Hewlett Packard Italiana', 'acronym' => 'HPE', 'link' => 'https://www.hpe.com/it/it/home.html' ],
        [ 'id' => '5', 'name' => 'Telefonica Investigacion Y Desarrollo', 'acronym' => 'TID', 'link' => 'https://www.tid.es/' ],
        [ 'id' => '6', 'name' => 'United Technologies Research Centre Ireland', 'acronym' => 'UTRC', 'link' => 'https://www.rtx.com/' ],
        [ 'id' => '7', 'name' => 'ORAMAVR', 'acronym' => 'ORAMA', 'link' => 'https://oramavr.com/' ],
        [ 'id' => '8', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '9', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '10', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '11', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '12', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '13', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '14', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ],
        [ 'id' => '15', 'name' => 'Eurescom', 'acronym' => 'EURES', 'link' => 'https://www.eurescom.eu/' ]
    ];

    $consortium = (object) $consortium;
@endphp

<div class="container-fluid my-4">
    <div class="container">
        <div class="row pb-4">
            @foreach($consortium as $partner)
                @php
                    $partner = (object) $partner;
                @endphp
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="consortium-partner" data-link="{{ $partner->link }}">
                        <img alt="{{ $partner->name }}" href="{{ asset('/charity/partners/img-'.$partner->acronym.'.png') }}" />
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>

</script>
