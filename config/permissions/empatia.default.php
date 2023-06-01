<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Empatia Permissions
    |--------------------------------------------------------------------------
    |
    | Permissions specific of Empatia
    |
    */

    'empatia' => [
        'cbs' => [
            'technical-analysis' => [
              'questions' => [
                  'view-any' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ],
                  'view' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ],
                  'create' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ],
                  'update' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ],
                  'delete' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ],
                  'restore' => [
                      'roles' => ['laravel-admin'],
                      'tags'  => ['empatia', 'cbs']
                  ]
              ]  
            ],
            'access-cbs-index' => [
                'roles' => ['laravel-admin'],
                'tags'  => ['empatia', 'cbs']
            ],
            'create-cb' => [
                'roles' => ['laravel-admin'],
                'tags'  => ['empatia', 'cbs']
            ],
            'edit-cb' => [
                'roles' => ['laravel-admin'],
                'tags'  => ['empatia', 'cbs']
            ],

            'topics' => [
                'create-topic' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'edit-topic' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],


                'create-topic-in-any-table' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'create-topic-in-own-table' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'create-any-topic' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'edit-own-topics' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'edit-own-table-topics' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'delete-own-topics' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'delete-own-table-topics' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'delete-any-topics' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'cbs-can-create-all' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'cbs-can-list-all' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'cbs-can-show-all' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],
                'cbs-can-delete-all' => [
                    'roles' => ['laravel-admin'],
                    'tags'  => ['empatia', 'cbs', 'topics']
                ],


                'comments' => [
                    'create-topic-comment' => [
                        'roles' => ['laravel-admin'],
                        'tags'  => ['empatia', 'cbs', 'topics', 'comments']
                    ],
                ],
            ],
        ],
    ],

];
