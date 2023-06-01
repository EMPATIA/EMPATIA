<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global Permissions
    |--------------------------------------------------------------------------
    |
    | Permissions global to all projects
    |
    */

    /**   Example   **/

    'cms' => [
        'content' => [
            'create' => [
                'name'          => 'Create Content',                        // optional
                'description'   => 'Enables a user to create contents.',    // optional
                'roles'         => ['example-role'],                        // REQUIRED
            ],
        ],
    ],

];
