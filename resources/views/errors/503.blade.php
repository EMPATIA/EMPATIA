@php
    use App\Helpers\HFrontend;

    $projectAssetsPath  = HFrontend::getProjectPath();
    $projectPath        = HFrontend::getProjectPath(true);
@endphp
<section class="container py-5">
    <section class="container-fluid page-title-banner text-center">
        <div class="container">
            <div class="row py-4 justify-content-center">
                <div class="col-lg-10">
                    <img class="logo">
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid text-center py-2 mt-5">
        <div class="row py-2 justify-content-center">
            <div class="col-lg-10">
                <h1>{{ strtoupper(__('http.503.maintenance.title')) }}</h1>
            </div>
        </div>
    </div>

    <div class="container-fluid text-center">
        <div class="row py-4 justify-content-center">
            <div class="col-lg-10">
                <div class="outer">
                    <hr class="border-line">
                    <p class="text-platform">{{ __('http.503.maintenance.description') }}</p>
                </div>

            </div>
        </div>
    </div>
</section>
<style>

    h1 {
        font-size: 3em;
    }

    p {
        font-size: 25px;
    }

    .text-center {
        text-align: center;
    }

    .container {
        padding-top: 100px;
    }

    .outer {
        display: inline-block;
    }

     /*Light mode*/
    @media (prefers-color-scheme: light) {
        body {
            background-color: white !important;
            color: black !important;
        }

        img {
            content: url('/build/assets/frontend/{{$projectAssetsPath}}/images/logo.png');
            height: 10rem;

        }
    }

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #414141 !important;
            color: white !important;
        }

        img {
            content: url('/build/assets/frontend/{{$projectAssetsPath}}/images/logo.png');
            height: 10rem;
        }
    }
</style>

