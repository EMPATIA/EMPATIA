<?php


use App\DashboardElement;
use App\DashBoardElementConfiguration;
use App\DashBoardElementConfigurationTranslation;
use App\DashboardElementTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::beginTransaction();

        $this->dashboardElementConfigurations();
        $this->dashboardElements();

        DB::commit();
    }

    private function dashboardElementConfigurations() {
        $dashboardElementConfigurations = array(
            array(
                "id"                 => 1,
                "code"               => "title",
                "type"               => "text",
                "default_value"      => "Element Title",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "Title",
                        "description"    => "Element Title",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Título",
                        "description"    => "Título do Elemento",
                    )
                )
            ),array(
                "id"                 => 2,
                "code"               => "description",
                "type"               => "description",
                "default_value"      => "Element Description",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "Description",
                        "description"    => "Element Description",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Descrição",
                        "description"    => "Descrição do Elemento",
                    )
                )
            ),array(
                "id"                 => 3,
                "code"               => "pad_type",
                "type"               => "text",
                "default_value"      => "all",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "PAD/CB Type",
                        "description"    => "The PAD/CB Type to display",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Tipo de PAD/CB",
                        "description"    => "O Tipo de PAD/CB a mostrar",
                    )
                )
            ),array(
                "id"                 => 4,
                "code"               => "records_to_show",
                "type"               => "number",
                "default_value"      => "5",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "Number of records",
                        "description"    => "Number of records to show",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Número de entradas",
                        "description"    => "Número de entradas a mostrar",
                    )
                )
            ),array(
                "id"                 => 5,
                "code"               => "sort_order",
                "type"               => "text",
                "default_value"      => "desc",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "Sort Order",
                        "description"    => "Records Sort Order",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Ordenação",
                        "description"    => "Ordenação das entradas",
                    )
                )
            ),array(
                "id"                 => 6,
                "code"               => "pad_key",
                "type"               => "text",
                "default_value"      => "all",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "PAD/CB Name",
                        "description"    => "The name of the PAD/CB to use",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Nome do PAD/CB",
                        "description"    => "O nome do PAD/CB a ser usado",
                    )
                )
            ),array(
                "id"                 => 7,
                "code"               => "status_code",
                "type"               => "text",
                "default_value"      => "accepted",
                "translations"       => array(
                    array(
                        "language_code"  => "en",
                        "title"          => "List Topics By Status",
                        "description"    => "List Topics By given Status",
                    ),array(
                        "language_code"  => "pt",
                        "title"          => "Lista de Tópicos por Estado",
                        "description"    => "Lista de Tópicos por determinado Estado",
                    )
                )
            ),
//            array(
//                "id"                 => ,
//                "code"               => "",
//                "type"               => "",
//                "default_value"      => "",
//                "translations"       => array(
//                    array(
//                        "language_code"  => "en",
//                        "title"          => "",
//                        "description"    => "",
//                    ),array(
//                        "language_code"  => "pt",
//                        "title"          => "",
//                        "description"    => "",
//                    )
//                )
//            ),
        );

        foreach ($dashboardElementConfigurations as $dashboardElementConfiguration) {
            $translations = $dashboardElementConfiguration["translations"];
            unset($dashboardElementConfiguration["translations"]);

            DashBoardElementConfiguration::firstOrCreate($dashboardElementConfiguration);

            foreach ($translations as $translation) {
                $translation = array_merge(["dashboard_element_configuration_id"=>$dashboardElementConfiguration["id"]],$translation);
                DashBoardElementConfigurationTranslation::firstOrCreate($translation);
            }
        }
    }

    private function dashboardElements() {
        $dashBoardElements = array(
            array(
               "id"                 => 1,
               "code"               => "last_topics",
               "default_position"   => 1,
               "translations"       => array(
                   array(
                       "language_code"  => "en",
                       "title"          => "Last Topics",
                       "description"    => "List of the last created topics",
                   ),array(
                       "language_code"  => "pt",
                       "title"          => "Últimos Tópicos",
                       "description"    => "Lista dos últimos tópicos criados",
                   )
               ),
                "configurations"    => array(
                    array(
                        "dashboard_element_configuration_id"    => 1,
                        "default_value"                         => "Last Topics"
                    ), array(
                        "dashboard_element_configuration_id"    => 2,
                        "default_value"                         => "List of the last created topics"
                    ), array(
                        "dashboard_element_configuration_id"    => 3,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 6,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 4,
                        "default_value"                         => "5"
                    ), array(
                        "dashboard_element_configuration_id"    => 5,
                        "default_value"                         => "desc"
                    ),
                )
           ),array(
               "id"                 => 2,
               "code"               => "comments_moderation",
               "default_position"   => 2,
               "translations"       => array(
                   array(
                       "language_code"  => "en",
                       "title"          => "Comments Moderation",
                       "description"    => "List of Comments pending moderation",
                   ),array(
                       "language_code"  => "pt",
                       "title"          => "Moderação de Comentários",
                       "description"    => "Lista de Comentários pendentes de moderação",
                   )
               ),
                "configurations"    => array(
                    array(
                        "dashboard_element_configuration_id"    => 1,
                        "default_value"                         => "Comments Moderation"
                    ), array(
                        "dashboard_element_configuration_id"    => 2,
                        "default_value"                         => "List of Comments pending moderation"
                    ), array(
                        "dashboard_element_configuration_id"    => 3,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 6,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 4,
                        "default_value"                         => "5"
                    ), array(
                        "dashboard_element_configuration_id"    => 5,
                        "default_value"                         => "desc"
                    ),
                )
           ), array(
               "id"                 => 3,
               "code"               => "pads_moderation",
               "default_position"   => 3,
               "translations"       => array(
                   array(
                       "language_code"  => "en",
                       "title"          => "List of topics with status",
                       "description"    => "Filter topics by a status",
                   ),array(
                       "language_code"  => "pt",
                       "title"          => "Lista de Tópicos com estado",
                       "description"    => "Filtrar tópicos por Estado",
                   )
               ),
                "configurations"    => array(
                    array(
                        "dashboard_element_configuration_id"    => 1,
                        "default_value"                         => "List of topics with status"
                    ), array(
                        "dashboard_element_configuration_id"    => 2,
                        "default_value"                         => "Filter topics by a status"
                    ), array(
                        "dashboard_element_configuration_id"    => 3,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 6,
                        "default_value"                         => "all"
                    ), array(
                        "dashboard_element_configuration_id"    => 4,
                        "default_value"                         => "5"
                    ), array(
                        "dashboard_element_configuration_id"    => 5,
                        "default_value"                         => "desc"
                    ), array(
                        "dashboard_element_configuration_id"    => 7,
                        "default_value"                         => "accepted"
                    ),
                )
           ),array(
               "id"                 => 4,
               "code"               => "user_registration_confirmation",
               "default_position"   => 4,
               "translations"       => array(
                   array(
                       "language_code"  => "en",
                       "title"          => "User moderation",
                       "description"    => "List of Users pending moderation",
                   ),array(
                       "language_code"  => "pt",
                       "title"          => "Moderação de Utilizadores",
                       "description"    => "Lista de Utilizadores pendentes de moderação",
                   )
               ),
                "configurations"    => array(
                    array(
                        "dashboard_element_configuration_id"    => 1,
                        "default_value"                         => "User moderation"
                    ), array(
                        "dashboard_element_configuration_id"    => 2,
                        "default_value"                         => "List of Users pending moderation"
                    ), array(
                        "dashboard_element_configuration_id"    => 4,
                        "default_value"                         => "5"
                    ), array(
                        "dashboard_element_configuration_id"    => 5,
                        "default_value"                         => "desc"
                    ),
                )
           ),array(
               "id"                 => 5,
               "code"               => "unread_messages",
               "default_position"   => 5,
               "translations"       => array(
                   array(
                       "language_code"  => "en",
                       "title"          => "Unreaded Messages",
                       "description"    => "List of unreaded Messages",
                   ),array(
                       "language_code"  => "pt",
                       "title"          => "Mensagens não lidas",
                       "description"    => "Lista das Mensagens não lidas",
                   )
               ),
                "configurations"    => array(
                    array(
                        "dashboard_element_configuration_id"    => 1,
                        "default_value"                         => "Unreaded Messages"
                    ), array(
                        "dashboard_element_configuration_id"    => 2,
                        "default_value"                         => "List of unreaded Messages"
                    ), array(
                        "dashboard_element_configuration_id"    => 4,
                        "default_value"                         => "5"
                    ), array(
                        "dashboard_element_configuration_id"    => 5,
                        "default_value"                         => "desc"
                    ),
                )
           ),
//            array(
//               "id"                 => 6,
//               "code"               => "",
//               "default_position"   => ,
//               "translations"       => array(
//                   array(
//                       "language_code"  => "en",
//                       "title"          => "",
//                       "description"    => "",
//                   ),array(
//                       "language_code"  => "pt",
//                       "title"          => "",
//                       "description"    => "",
//                   )
//               )
//           ),
        );

        foreach ($dashBoardElements as $dashBoardElement) {
            $translations = $dashBoardElement["translations"];
            unset($dashBoardElement["translations"]);

            $configurations = $dashBoardElement["configurations"];
            unset($dashBoardElement["configurations"]);

            $dashboardElementFromBD = DashboardElement::firstOrCreate($dashBoardElement);

            foreach ($translations as $translation) {
                $translation = array_merge(["dashboard_element_id"=>$dashBoardElement["id"]],$translation);
                DashboardElementTranslation::firstOrCreate($translation);
            }

            foreach ($configurations as $configuration) {
                $dashboardElementFromBD->configurations()->attach($configuration["dashboard_element_configuration_id"],["default_value"=>$configuration["default_value"]]);
            }
        }
    }

}
