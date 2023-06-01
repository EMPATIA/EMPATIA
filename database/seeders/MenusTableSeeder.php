<?php

namespace Database\Seeders;

use App\Helpers\HCache;
use App\Models\Backend\CMS\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MenusTableSeeder extends Seeder
{
    protected array $menus = [
        ['code' => 'dashboard',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Dashboard',
                'pt' => 'Dashboard'
            ],
            'link' => [
                'en' => '/private',
                'pt' => '/private'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-home',
                ],
                'pt' => [
                    'icon' => 'fa fa-home',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'statistics',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Statistics',
                'pt' => 'Estatísticas'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-chart-column',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-chart-column',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'cbs',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'CB\'s',
                'pt' => 'CB\'s'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-brands fa-wpforms',
                ],
                'pt' => [
                    'icon' => 'fa-brands fa-wpforms',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'cms',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Contents',
                'pt' => 'Conteúdos'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-pager',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-pager',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'users',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Users',
                'pt' => 'Utilizadores'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-users',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-users',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'notifications',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Notifications',
                'pt' => 'Notificações'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-mail-bulk',
                ],
                'pt' => [
                    'icon' => 'fa fa-mail-bulk',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'menus',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Menus',
                'pt' => 'Menus'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-bars',
                ],
                'pt' => [
                    'icon' => 'fa fa-bars',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'text',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Text',
                'pt' => 'Textos'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-closed-captioning',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-closed-captioning',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'configurations',
            'menu_type' => 'private',
            'parent_id' => 0,
            'position' => 0,
            'title' => [
                'en' => 'Configurations',
                'pt' => 'Configurações'
            ],
            'link' => [
                'en' => '',
                'pt' => ''
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-gears',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-gears',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'statistics-summary',
            'menu_type' => 'private',
            'parent_id' => 2,
            'position' => 0,
            'title' => [
                'en' => 'Summary',
                'pt' => 'Resumo'
            ],
            'link' => [
                'en' => '/private/statistics',
                'pt' => '/private/statistics'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-regular fa-chart-bar',
                ],
                'pt' => [
                    'icon' => 'fa-regular fa-chart-bar',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'cbs-list',
            'menu_type' => 'private',
            'parent_id' => 3,
            'position' => 0,
            'title' => [
                'en' => 'CB\'s',
                'pt' => 'CB\'s'
            ],
            'link' => [
                'en' => '/private/cbs',
                'pt' => '/private/cbs'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fas fa-poll-h',
                ],
                'pt' => [
                    'icon' => 'fas fa-poll-h',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'topics-list',
            'menu_type' => 'private',
            'parent_id' => 3,
            'position' => 1,
            'title' => [
                'en' => 'Topics',
                'pt' => 'Tópicos'
            ],
            'link' => [
                'en' => '/private/topics',
                'pt' => '/private/topics'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-regular fa-rectangle-list',
                ],
                'pt' => [
                    'icon' => 'fa-regular fa-rectangle-list',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'votes-list',
            'menu_type' => 'private',
            'parent_id' => 3,
            'position' => 2,
            'title' => [
                'en' => 'Votes',
                'pt' => 'Votos'
            ],
            'link' => [
                'en' => '/private/votes',
                'pt' => '/private/votos'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-check-to-slot',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-check-to-slot',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'operation-schedules',
            'menu_type' => 'private',
            'parent_id' => 3,
            'position' => 3,
            'title' => [
                'en' => 'Operation Schedules',
                'pt' => 'Horários de Operações'
            ],
            'link' => [
                'en' => '/private/operation-schedules',
                'pt' => '/private/operation-schedules'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-calendar-days',
                ],
                'pt' => [
                    'icon' => 'fa fa-calendar-days',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'contents',
            'menu_type' => 'private',
            'parent_id' => 4,
            'position' => 0,
            'title' => [
                'en' => 'Contents',
                'pt' => 'Conteúdos'
            ],
            'link' => [
                'en' => '/private/cms/all',
                'pt' => '/private/cms/all'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-laptop-file',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-laptop-file',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'pages',
            'menu_type' => 'private',
            'parent_id' => 4,
            'position' => 1,
            'title' => [
                'en' => 'Pages',
                'pt' => 'Páginas'
            ],
            'link' => [
                'en' => '/private/cms/pages',
                'pt' => '/private/cms/pages'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-regular fa-copy',
                ],
                'pt' => [
                    'icon' => 'fa-regular fa-copy',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'news',
            'menu_type' => 'private',
            'parent_id' => 4,
            'position' => 2,
            'title' => [
                'en' => 'News',
                'pt' => 'Notícias'
            ],
            'link' => [
                'en' => '/private/cms/news',
                'pt' => '/private/cms/news'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-newspaper',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-newspaper',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'events',
            'menu_type' => 'private',
            'parent_id' => 4,
            'position' => 3,
            'title' => [
                'en' => 'Events',
                'pt' => 'Eventos'
            ],
            'link' => [
                'en' => '/private/cms/events',
                'pt' => '/private/cms/events'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-calendar-days',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-calendar-days',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'users-list',
            'menu_type' => 'private',
            'parent_id' => 5,
            'position' => 0,
            'title' => [
                'en' => 'Users',
                'pt' => 'Utilizadores'
            ],
            'link' => [
                'en' => '/private/users',
                'pt' => '/private/users'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-user',
                ],
                'pt' => [
                    'icon' => 'fa fa-user',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'email',
            'menu_type' => 'private',
            'parent_id' => 6,
            'position' => 0,
            'title' => [
                'en' => 'Email',
                'pt' => 'Email'
            ],
            'link' => [
                'en' => '/private/notifications/emails',
                'pt' => '/private/notifications/emails'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-envelope',
                ],
                'pt' => [
                    'icon' => 'fa fa-envelope',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'sms',
            'menu_type' => 'private',
            'parent_id' => 6,
            'position' => 1,
            'title' => [
                'en' => 'SMS',
                'pt' => 'SMS'
            ],
            'link' => [
                'en' => '/private/notifications/sms',
                'pt' => '/private/notifications/sms'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-comment-sms',
                ],
                'pt' => [
                    'icon' => 'fa fa-comment-sms',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'notification_templates',
            'menu_type' => 'private',
            'parent_id' => 6,
            'position' => 2,
            'title' => [
                'en' => 'Templates',
                'pt' => 'Modelos de Notificação'
            ],
            'link' => [
                'en' => '/private/notifications/templates',
                'pt' => '/private/notifications/templates'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-file-signature',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-file-signature',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'menus',
            'menu_type' => 'private',
            'parent_id' => 7,
            'position' => 0,
            'title' => [
                'en' => 'Menus',
                'pt' => 'Menus'
            ],
            'link' => [
                'en' => '/private/cms/menus',
                'pt' => '/private/cms/menus'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa-solid fa-bars-staggered',
                ],
                'pt' => [
                    'icon' => 'fa-solid fa-bars-staggered',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'menu-types',
            'menu_type' => 'private',
            'parent_id' => 7,
            'position' => 1,
            'title' => [
                'en' => 'Menu Types',
                'pt' => 'Tipos de Menu'
            ],
            'link' => [
                'en' => '/private/cms/menu-types',
                'pt' => '/private/cms/menu-types'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-list',
                ],
                'pt' => [
                    'icon' => 'fa fa-list',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'translations',
            'menu_type' => 'private',
            'parent_id' => 8,
            'position' => 0,
            'title' => [
                'en' => 'Translations',
                'pt' => 'Traduções'
            ],
            'link' => [
                'en' => '/private/cms/translations',
                'pt' => '/private/cms/translations'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-language',
                ],
                'pt' => [
                    'icon' => 'fa fa-language',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'languages',
            'menu_type' => 'private',
            'parent_id' => 8,
            'position' => 1,
            'title' => [
                'en' => 'Languages',
                'pt' => 'Línguas'
            ],
            'link' => [
                'en' => '/private/cms/languages',
                'pt' => '/private/cms/languages'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-globe',
                ],
                'pt' => [
                    'icon' => 'fa fa-globe',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'configurations-list',
            'menu_type' => 'private',
            'parent_id' => 9,
            'position' => 0,
            'title' => [
                'en' => 'Configurations',
                'pt' => 'Configurações'
            ],
            'link' => [
                'en' => '/private/configurations',
                'pt' => '/private/configurations'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-sliders-h',
                ],
                'pt' => [
                    'icon' => 'fa fa-sliders-h',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'cache',
            'menu_type' => 'private',
            'parent_id' => 9,
            'position' => 1,
            'title' => [
                'en' => 'Cache',
                'pt' => 'Cache'
            ],
            'link' => [
                'en' => '/private/cache',
                'pt' => '/private/cache'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-database',
                ],
                'pt' => [
                    'icon' => 'fa fa-database',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'terms',
            'menu_type' => 'private',
            'parent_id' => 9,
            'position' => 2,
            'title' => [
                'en' => 'Terms & Conditions',
                'pt' => 'Termos e Condições'
            ],
            'link' => [
                'en' => '/private/terms',
                'pt' => '/private/terms'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-clipboard-list',
                ],
                'pt' => [
                    'icon' => 'fa fa-clipboard-list',
                ]
            ],
            'roles' => [],
        ],
        ['code' => 'login-levels',
            'menu_type' => 'private',
            'parent_id' => 9,
            'position' => 3,
            'title' => [
                'en' => 'Login Levels',
                'pt' => 'Níveis de Login'
            ],
            'link' => [
                'en' => '/private/login-levels',
                'pt' => '/private/login-levels'
            ],
            'options' => [
                'en' => [
                    'icon' => 'fa fa-layer-group',
                ],
                'pt' => [
                    'icon' => 'fa fa-layer-group',
                ]
            ],
            'roles' => [],
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            if (Schema::hasTable('menus')) {
                Model::unguard();
                
                $command = $command ?: $this->command;
                if ($options['clear'] ?? []) {
                    if ($command->confirm('Are you sure that you want to delete all menus\'s table data?', false)) {
                        $command->info("Deleting menus table data...");
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::table('menus')->truncate();
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        $command->info("Menus table data deleted!");
                        HCache::flushMenus();
                    }
                }
                $command->info("Seeding menus table...");
                foreach ($this->menus as $menu) {
                    Menu::create([
                        'code' => $menu['code'],
                        'menu_type' => $menu['menu_type'],
                        'parent_id' => $menu['parent_id'],
                        'position' => $menu['position'],
                        'title' => $menu['title'],
                        'link' => $menu['link'],
                        'options' => $menu['options'],
                        'roles' => $menu['roles'],
                        'version' => 1,
                        'versions' => [] //TODO: Trait Versionable
                    ]);
                }
                $command->comment("Menus table seeding completed successfully!");
            } else {
                $command->error("There isn't any menus table");
                return null;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding menus table!");
            logError('Menus seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }
    }
}
