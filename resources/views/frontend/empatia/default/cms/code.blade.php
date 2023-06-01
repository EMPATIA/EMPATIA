@php
    // List with mapping between section codes and the blade to load
    // example: "home" => "frontend.project.cms.code.home.blade.php"
    $sectionCodes = [
    ];


    // **** DO NOT CHANGE AFTER THIS POINT ****

    $view = getField($sectionCodes, getField($section, "code"));
@endphp

@if(empty($view))
    <div class="alert alert-danger" role="alert">UNKNOWN CODE: {{ getField($section, "code", "no code") }}</div>
@elseif(\View::exists($view) )
    @include( $sectionCodes[getField($section, "code")] )
@else
    <div class="alert alert-danger" role="alert">MISSING FILE: {{ $sectionCodes[getField($section, "code")] }}</div>
@endif
