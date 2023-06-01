@php($languages = \App\Helpers\HBackend::getBackendMenuLanguages())

<nav class="top-menu navbar navbar-light shadow p-3 bg-white">
    <div class="d-flex col-12 col-md-3 col-lg-2 mb-lg-0 flex-wrap flex-md-nowrap justify-content-between">
        <a class="navbar-brand" href="/">
            {{env('APP_NAME', 'Empatia')}}
        </a>
        <button class="navbar-toggler d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#side-menu" aria-controls="side-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="col-md-5 col-lg-8 d-none d-md-flex align-items-center justify-content-md-end">
        @include('backend.layouts.partials.top-menu-buttons')
    </div>
</nav>