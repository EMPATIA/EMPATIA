<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Framework Variant
    |--------------------------------------------------------------------------
    |
    | This value is used to differenciate between Framework variants.
    | The app's behavior may change according to the variant.
    |
    */

    'framework_variant' => env('FRAMEWORK_VARIANT', 'base'),

    /*
    |--------------------------------------------------------------------------
    | Project path
    |--------------------------------------------------------------------------
    |
    | The project path tells where in the resources/frontend directory the
    | project resources are located.
    |
    */

    'project_path' => env('PROJECT_PATH', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Frontend Slug Processors (IDEA; NOT IMPLEMENTED)
    |--------------------------------------------------------------------------
    |
    | The methods listed here will be automatically called in
    | FrontendController 'pageBySlug' method before it's default behaviour.
    | This way, when a project has specific requirements, the 'pageBySlug'
    | method stays intact.
    |
    */

    'frontend_slug_processors' => [
        'base' => [],
        'empatia' => [
            // App\Http\Controllers\Frontend\Empatia\FrontendController@pageBySlug
        ],
    ],

];
