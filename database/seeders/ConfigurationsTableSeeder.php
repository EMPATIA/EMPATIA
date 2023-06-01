<?php

namespace Database\Seeders;

use App\Models\Backend\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfigurationsTableSeeder extends Seeder
{
    protected array $configurations = [
        'cb_settings' => [
            'configurations' => [
                'types' => [
                    'default' => [
                        'name' => [
                            'en' => 'Default',
                            'pt' => 'Default',
                        ],
                        'slug' => [
                            'en' => 'default',
                            'pt' => 'default',
                        ],
                        'public' => true,
                        'site' => 'default',
                        'default_content_type' => 'pages',
                        'configurations' => [

                        ]
                    ],
                ],
                'configurations' => [
                    'contents' => [
                        'code' => 'contents',
                        'title' => [
                            'en' => 'Contents',
                            'pt' => 'Conteúdos',
                        ],
                        'type' => 'select2',
                    ],
                    'login_required' => [
                        'code' => 'login_required',
                        'title' => [
                            'en' => 'Login Required',
                            'pt' => 'Necessita de Login',
                        ],
                        'type' => null,
                    ],
                    'login_levels' => [
                        'code' => 'login_levels',
                        'title' => [
                            'en' => 'Login Levels',
                            'pt' => 'Níveis de Login',
                        ],
                        'type' => 'select2',
                    ],
                ],
            ],
        ],
        'content_types' => [
            'configurations' => [
                'pages' => [
                    'fields' => [
                    ],
                    'sections' => [
                        0 => [
                            'code' => 'title',
                            'type' => 'heading',
                            'value' => [
                                'en' => '',
                            ],
                            'class' => '',
                            'name' => [
                                'en' => 'Title',
                                'heading' => 'h1',
                            ],
                            'options' => '',
                        ],
                        1 => [
                            'code' => 'description',
                            'type' => 'text',
                            'value' => [
                                'en' => '',
                            ],
                            'class' => '',
                            'name' => [
                                'en' => 'Description',
                            ],
                            'options' => '',
                        ],
                        2 => [
                            'code' => 'content',
                            'type' => 'text-html',
                            'value' => [
                                'en' => 'Content',
                            ],
                            'class' => '',
                            'name' => [
                                'en' => 'Content',
                            ],
                            'options' => '',
                        ],
                    ],
                    'seo' => [
                        'basic' => [
                            'title' => [
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'description' => [
                                'code' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                        ],
                        'opengraph' => [
                            'og:title' => [
                                'seo' => 'title',
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'og:description' => [
                                'seo' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                            'og:type' => [
                                'default' => 'website',
                            ],
                            'og:image' => [
                                'type' => 'image',
                                'code' => 'images',
                            ],
                            'og:image:alt' => [
                                'type' => 'image-alt',
                                'code' => 'images',
                                'locale' => true,
                            ],
                            'og:url' => [
                                'locale' => true,
                                'type' => 'url',
                            ],
                            'og:locale' => [
                                'type' => 'locale',
                                'locked' => 'true',
                            ],
                            'og:locale:alternate' => [
                                'type' => 'locale-list',
                                'locked' => 'true',
                            ],
                            'og:site_name' => [
                                'type' => 'site-name',
                                'locale' => true,
                            ],
                            'og:updated_time' => [
                                'type' => 'date-updated',
                                'locked' => 'true',
                            ],
                        ],
                    ],
                ],
                'news' => [
                    'fields' => [
                        'date_news' => [
                            'type' => 'date',
                            'translatable' => false,
                        ],
                        'date_publish' => [
                            'type' => 'date',
                            'translatable' => false,
                        ],
                    ],
                    'sections' => [
                        0 => [
                            'code' => 'title',
                            'type' => 'heading',
                            'value' => [
                                'en' => 'Title',
                                'heading' => 'h1',
                            ],
                            'class' => '',
                            'options' => '',
                        ],
                        1 => [
                            'code' => 'summary',
                            'type' => 'text-html',
                            'value' => [
                                'en' => 'Summary',
                                'pt' => 'Sumario',
                            ],
                            'class' => '',
                            'options' => '',
                        ],
                        2 => [
                            'code' => 'description',
                            'type' => 'text-html',
                            'value' => [
                                'en' => 'Content',
                            ],
                            'class' => '',
                            'options' => '',
                        ],
                        3 => [
                            'code' => 'images',
                            'type' => 'images',
                            'value' => [
                            ],
                            'class' => '',
                            'options' => '',
                        ],
                    ],
                    'seo' => [
                        'basic' => [
                            'title' => [
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'description' => [
                                'code' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                        ],
                        'opengraph' => [
                            'og:title' => [
                                'seo' => 'title',
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'og:description' => [
                                'seo' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                            'og:type' => [
                                'default' => 'website',
                            ],
                            'og:image' => [
                                'type' => 'image',
                                'code' => 'images',
                            ],
                            'og:image:alt' => [
                                'type' => 'image-alt',
                                'code' => 'images',
                                'locale' => true,
                            ],
                            'og:url' => [
                                'locale' => true,
                                'type' => 'url',
                            ],
                            'og:locale' => [
                                'type' => 'locale',
                                'locked' => 'true',
                            ],
                            'og:locale:alternate' => [
                                'type' => 'locale-list',
                                'locked' => 'true',
                            ],
                            'og:site_name' => [
                                'type' => 'site-name',
                                'locale' => true,
                            ],
                            'og:updated_time' => [
                                'type' => 'date-updated',
                                'locked' => 'true',
                            ],
                        ],
                    ],
                ],
                'events' => [
                    'fields' => [
                        'date_events' => [
                            'type' => 'date',
                            'translatable' => false,
                        ],
                        'date_publish' => [
                            'type' => 'date',
                            'translatable' => false,
                        ],
                    ],
                    'sections' => NULL,
                    'seo' => [
                        'basic' => [
                            'title' => [
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'description' => [
                                'code' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                        ],
                        'opengraph' => [
                            'og:title' => [
                                'seo' => 'title',
                                'code' => 'title',
                                'locale' => true,
                                'max' => '70',
                            ],
                            'og:description' => [
                                'seo' => 'description',
                                'locale' => true,
                                'max' => '200',
                            ],
                            'og:type' => [
                                'default' => 'website',
                            ],
                            'og:image' => [
                                'type' => 'image',
                                'code' => 'images',
                            ],
                            'og:image:alt' => [
                                'type' => 'image-alt',
                                'code' => 'images',
                                'locale' => true,
                            ],
                            'og:url' => [
                                'locale' => true,
                                'type' => 'url',
                            ],
                            'og:locale' => [
                                'type' => 'locale',
                                'locked' => 'true',
                            ],
                            'og:locale:alternate' => [
                                'type' => 'locale-list',
                                'locked' => 'true',
                            ],
                            'og:site_name' => [
                                'type' => 'site-name',
                                'locale' => true,
                            ],
                            'og:updated_time' => [
                                'type' => 'date-updated',
                                'locked' => 'true',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'user_parameters' => [
            'configurations' => [
                'age' => [
                    'code' => 'age',
                    'title' => [
                        'en' => 'Age',
                        'pt' => 'Idade',
                    ],
                    'placeholder' => [
                        'en' => 'Age',
                        'pt' => 'Idade',
                    ],
                    'description' => [
                        'en' => 'age',
                        'pt' => 'idade',
                    ],
                    'type' => 'number',
                    'rules' => 'integer|min:1|max:120',
                    'mandatory' => false,
                    'options' => NULL,
                    'pii' => true,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'gender' => [
                    'code' => 'gender',
                    'title' => [
                        'en' => 'Gender',
                        'pt' => 'Sexo',
                    ],
                    'placeholder' => [
                        'en' => 'Gender',
                        'pt' => 'Sexo',
                    ],
                    'description' => [
                        'en' => 'Gender',
                        'pt' => 'Sexo',
                    ],
                    'type' => 'select',
                    'rules' => 'string',
                    'mandatory' => false,
                    'options' => [
                        0 => [
                            'code' => 'default',
                            'value' => 'default',
                            'label' => [
                                'en' => 'Prefer not to say',
                                'pt' => '-- Prefiro não revelar --',
                            ],
                        ],
                        1 => [
                            'code' => 'male',
                            'value' => 'male',
                            'label' => [
                                'en' => 'Male',
                                'pt' => 'Masculino',
                            ],
                        ],
                        2 => [
                            'code' => 'female',
                            'value' => 'female',
                            'label' => [
                                'en' => 'Female',
                                'pt' => 'Feminino',
                            ],
                        ],
                        3 => [
                            'code' => 'other',
                            'value' => 'other',
                            'label' => [
                                'en' => 'Other',
                                'pt' => 'Outro',
                            ],
                        ],
                    ],
                    'pii' => true,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'nationality' => [
                    'code' => 'nationality',
                    'title' => [
                        'en' => 'Nationality',
                        'pt' => 'Nacionalidade',
                    ],
                    'placeholder' => [
                        'en' => 'Nationality',
                        'pt' => 'Nacionalidade',
                    ],
                    'description' => [
                        'en' => 'Nationality',
                        'pt' => 'Nacionalidade',
                    ],
                    'type' => 'select',
                    'rules' => 'string',
                    'mandatory' => false,
                    'options' => [
                        0 => [
                            'code' => 'nacionality1',
                            'value' => 'nacionality1',
                            'label' => [
                                'en' => 'Nacionality1',
                                'pt' => 'Nacionalidade1',
                            ],
                        ],
                    ],
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'country' => [
                    'code' => 'country',
                    'title' => [
                        'en' => 'Country',
                        'pt' => 'País',
                    ],
                    'placeholder' => [
                        'en' => 'Country',
                        'pt' => 'País',
                    ],
                    'description' => [
                        'en' => 'Country',
                        'pt' => 'País',
                    ],
                    'type' => 'select',
                    'rules' => 'string',
                    'mandatory' => false,
                    'options' => [
                        0 => [
                            'code' => 'coutry1',
                            'value' => 'coutry1',
                            'label' => [
                                'en' => 'Coutry1',
                                'pt' => 'País1',
                            ],
                        ],
                    ],
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'county' => [
                    'code' => 'county',
                    'title' => [
                        'en' => 'Parishes',
                        'pt' => 'Município',
                    ],
                    'placeholder' => [
                        'en' => 'Parishes',
                        'pt' => 'Município',
                    ],
                    'description' => [
                        'en' => 'Parishes',
                        'pt' => 'Município',
                    ],
                    'type' => 'select',
                    'rules' => 'string',
                    'mandatory' => false,
                    'options' => [
                        0 => [
                            'code' => 'parishe1',
                            'value' => 'parishe1',
                            'label' => [
                                'en' => 'Parishe1',
                                'pt' => 'Município1',
                            ],
                        ],
                    ],
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'nif' => [
                    'code' => 'nif',
                    'title' => [
                        'en' => 'Tax Identification Number',
                        'pt' => 'Número de Identificação Fiscal',
                    ],
                    'placeholder' => [
                        'en' => 'Tax Identification Number',
                        'pt' => 'Número de Identificação Fiscal',
                    ],
                    'description' => [
                        'en' => 'Tax Identification Number',
                        'pt' => 'Número de Identificação Fiscal',
                    ],
                    'type' => 'text',
                    'rules' => 'unique:users,parameters->nif|numeric',
                    'mandatory' => false,
                    'options' => NULL,
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'phone_number' => [
                    'code' => 'phone_number',
                    'title' => [
                        'en' => 'Phone Number',
                        'pt' => 'Número de Telemóvel',
                    ],
                    'placeholder' => [
                        'en' => 'Phone Number',
                        'pt' => 'Número de Telemóvel',
                    ],
                    'description' => [
                        'en' => 'Phone Number',
                        'pt' => 'Número de Telemóvel',
                    ],
                    'type' => 'number',
                    'rules' => 'unique:users,parameters->phone_number|numeric',
                    'mandatory' => false,
                    'options' => NULL,
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'profession' => [
                    'code' => 'profession',
                    'title' => [
                        'en' => 'Profession',
                        'pt' => 'Profissão',
                    ],
                    'placeholder' => [
                        'en' => 'Profession',
                        'pt' => 'Profissão',
                    ],
                    'description' => [
                        'en' => 'Profession',
                        'pt' => 'Profissão',
                    ],
                    'type' => 'text',
                    'rules' => 'string',
                    'mandatory' => false,
                    'options' => NULL,
                    'pii' => false,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => false,
                            'editable' => false,
                        ],
                    ]
                ],
                'profile_photo' => [
                    'code' => 'profile_photo',
                    'title' => [
                        'en' => 'Profile Photo',
                        'pt' => 'Foto de Perfil'
                    ],
                    'placeholder' => [
                        'en' => 'Profile Photo',
                        'pt' => 'Foto de Perfil'
                    ],
                    'description' => [
                        'en' => 'Profile Photo',
                        'pt' => 'Foto de Perfil'
                    ],
                    'type' => 'image',
                    'mandatory' => false,
                    'pii' => true,
                    'login_level' => false,
                    'views' => [
                        'register' => [
                            'show' => false,
                        ],
                        'profile' => [
                            'show' => true,
                            'editable' => false,
                        ],
                    ]
                ],
            ],
        ],
        'social_providers' => [
            'configurations' => [
                'facebook' => [
                    'code' => 'facebook',
                    'name' => 'Facebook',
                ],
                'twitter' => [
                    'code' => 'twitter',
                    'name' => 'Twitter',
                ],
                'google' => [
                    'code' => 'google',
                    'name' => 'Google',
                ],
            ],
        ],
        'menu_types' => [
            'configurations' => [
                [
                    'code' => 'private',
                    'name' => [
                        'pt' => 'Privado',
                        'en' => 'Private'
                    ],
                ],
                [
                    'code' => 'public',
                    'name' => [
                        'pt' => 'Público',
                        'en' => 'Public'
                    ],
                ]
            ],
        ],
        'notification_channels' => [
            'configurations' => [
                [
                    'code' => 'email',
                    'name' => [
                        'pt' => 'email',
                        'en' => 'email'
                    ],
                ],
                [
                    'code' => 'sms',
                    'name' => [
                        'pt' => 'sms',
                        'en' => 'sms'
                    ],
                ]
            ],
        ],
        'translations_import' => [
            'columns' => [
                'locale' => [
                    'code' => 'locale',
                    'required' => true,
                    'aliases' => [
                        0 => 'locale',
                    ],
                ],
                'namespace' => [
                    'code' => 'namespace',
                    'required' => true,
                    'aliases' => [
                        0 => 'namespace',
                    ],
                ],
                'group' => [
                    'code' => 'group',
                    'required' => true,
                    'aliases' => [
                        0 => 'group',
                    ],
                ],
                'item' => [
                    'code' => 'item',
                    'required' => true,
                    'aliases' => [
                        0 => 'item',
                    ],
                ],
                'text' => [
                    'code' => 'text',
                    'required' => true,
                    'aliases' => [
                        0 => 'text',
                    ],
                ],
                'created_by' => [
                    'code' => 'created_by',
                    'required' => false,
                    'aliases' => [
                        0 => 'created_by',
                    ],
                ],
                'created_at' => [
                    'code' => 'created_at',
                    'required' => false,
                    'aliases' => [
                        0 => 'created_at',
                    ],
                ],
                'updated_by' => [
                    'code' => 'updated_by',
                    'required' => false,
                    'aliases' => [
                        0 => 'updated_by',
                    ],
                ],
                'updated_at' => [
                    'code' => 'updated_at',
                    'required' => false,
                    'aliases' => [
                        0 => 'updated_at',
                    ],
                ],
                'deleted_by' => [
                    'code' => 'deleted_by',
                    'required' => false,
                    'aliases' => [
                        0 => 'deleted_by',
                    ],
                ],
                'deleted_at' => [
                    'code' => 'deleted_at',
                    'required' => false,
                    'aliases' => [
                        0 => 'deleted_at',
                    ],
                ],
            ],
        ]
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            if (Schema::hasTable('configurations')) {
                Model::unguard();
                
                $command = $command ?: $this->command;
                if ($options['clear'] ?? []) {
                    if ($command->confirm('Are you sure that you want to delete all configurations\'s table data?', false)) {
                        $command->info("Deleting configurations table data...");
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::table('configurations')->truncate();
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        $command->info("Configurations table data deleted!");
                    }
                }
                
                $command->info("Seeding configurations types table...");
                foreach ($this->configurations as $code => $config) {
                    Configuration::create([
                        'code' => $code,
                        'configurations' => $config['configurations'],
                        'version' => 1,
                        'versions' => [] //TODO: Trait Versionable
                    ]);
                }
                $command->comment("Configurations table seeding completed successfully!");
            } else {
                $command->error("There isn't any configurations table");
                return null;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding configurations table!");
            logError('Configurations seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }
    }
}
