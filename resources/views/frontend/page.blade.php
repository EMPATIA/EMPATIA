@php
    $projectPath = App\Helpers\HFrontend::getProjectPath(true);
@endphp

@include("frontend.$projectPath.cms.page")
