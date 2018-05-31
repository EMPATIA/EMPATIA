<?php

use App\BEEntityMenu;
use App\BEEntityMenuElement;
use App\BEEntityMenuElementParameter;
use App\BEEntityMenuElementTranslation;
use App\BEMenuElement;
use App\BEMenuElementParameterTranslation;
use App\BEMenuElementTranslation;
use Illuminate\Database\Seeder;
use App\BEMenuElementParameter;
use Illuminate\Support\Facades\DB;

class BEMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::beginTransaction();

        $this->BEMenuElementParameters();
        $this->BEMenuElements();
        $this->basicMenu();

        DB::commit();
    }

    private function BEMenuElementParameters() {
        $BEMenuElementParameters = array(
            array(
               "id"        => 1,
               "code"      => "url",
               "translations" => array(
                   array(
                       "language_code" => "en",
                       "name"          => "URL",
                       "description"   => "",
                   ),array(
                       "language_code" => "pt",
                       "name"          => "URL",
                       "description"   => "",
                   )
               )
           ), array(
                "id"        => 2,
                "code"      => "cb_type",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "CB Type",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Tipo de CB",
                        "description"   => "",
                    )
                )
            ), array(
                "id"        => 3,
                "code"      => "cb_key",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "CB Key",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Chave do CB",
                        "description"   => "",
                    )
                )
            ), array(
                "id"        => 4,
                "code"      => "content_type_old",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Type (Old)",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Tipo de Conteúdo (Antigo)",
                        "description"   => "",
                    )
                )
            ), array(
                "id"        => 5,
                "code"      => "content_type",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Type (New)",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Tipo de Conteúdo (Novo)",
                        "description"   => "",
                    )
                )
            ), /*array(
                "id"        => 6,
                "code"      => "",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "",
                        "description"   => "",
                    )
                )
            )*/
        );

        foreach ($BEMenuElementParameters as $BEMenuElementParameter) {
            $translations = $BEMenuElementParameter["translations"];
            unset($BEMenuElementParameter["translations"]);

            do {
                $rand = str_random(32);

                if (!($exists = BEMenuElementParameter::where("key",$rand)->exists())) {
                    $BEMenuElementParameter["key"] = $rand;
                }
            } while ($exists);

            BEMenuElementParameter::firstOrCreate($BEMenuElementParameter);

            foreach ($translations as $translation) {
                $translation = array_merge(["be_menu_element_parameter_id"=>$BEMenuElementParameter["id"]],$translation);
                BEMenuElementParameterTranslation::firstOrCreate($translation);
            }
        }
    }
    private function BEMenuElements() {
        $BEMenuElements = array(
            array(
                "id"            => 1,
                "code"          => "section_header",
                "controller"    => "",
                "method"        => "",
                "module_code"   => null,
                "module_type_code"=> null,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Section Header",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Título da Secção",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 2,
                "code"          => "external_url",
                "controller"    => "",
                "method"        => "",
                "module_code"   => null,
                "module_type_code"=> null,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "External URL",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "URL Externo",
                        "description"   => "",
                    )
                ),
                "parameters"    => array(
                    array("id"=>1, "code" => null)
                )
            ), array(
                "id"            => 3,
                "code"          => null,
                "controller"    => "CbsController",
                "method"        => "indexManager",
                "module_code"   => "cb",
                "module_type_code"=> -1,
                "permission"    => -1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "CB Type List",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Lista de Tipo de CB",
                        "description"   => "",
                    )
                ),
                "parameters"    => array(
                    array("id" => 2, "code" => "typeFilter")
                )
            ), array(
                "id"            => 4,
                "code"          => null,
                "controller"    => "MPsController",
                "method"        => "index",
                "module_code"   => "mp",
                "module_type_code"=> null,
                "permission"    => "mp",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "MP",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "MP",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 5,
                "code"          => null,
                "controller"    => "KiosksController",
                "method"        => "index",
                "module_code"   => "kiosk",
                "module_type_code"=> null,
                "permission"    => "kiosk",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Kiosks",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Kiosks",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 6,
                "code"          => null,
                "controller"    => "EntitiesSitesController",
                "method"        => "index",
                "module_code"   => "orchestrator",
                "module_type_code"=> "entity_site",
                "permission"    => "entity_site",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Sites",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Sites",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 7,
                "code"          => null,
                "controller"    => "AccessMenusController",
                "method"        => "index",
                "module_code"   => "cm",
                "module_type_code"=> "menu",
                "permission"    => "menu",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Menus",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Menus",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 8,
                "code"          => null,
                "controller"    => "ContentsController",
                "method"        => "index",
                "module_code"   => "cm",
                "module_type_code"=> -1,
                "permission"    => -1,
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Manager (Old)",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Gestor de Conteúdos (Antigo)",
                        "description"   => "",
                    )
                ),
                "parameters"    => array(
                    array("id"=>4, "code"=>"access-control")
                )
            ), array(
                "id"            => 9,
                "code"          => null,
                "controller"    => "ContentManagerController",
                "method"        => "index",
                "module_code"   => "cm",
                "module_type_code"=> -1,
                "permission"    => -1,
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Manager (New)",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Gestor de Conteúdos (Novo)",
                        "description"   => "",
                    )
                ),
                "parameters"    => array(
                    array("id"=>5, "code"=>"access-control")
                )
            ), array(
                "id"            => 10,
                "code"          => null,
                "controller"    => "ContentTypeTypesController",
                "method"        => "index",
                "module_code"   => "cm",
                "module_type_code"=> "content_subtypes",
                "permission"    => "content_subtypes",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Sub Types",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Sub Tipos de Conteúdos",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ),
            array(
                "id"            => 11,
                "code"          => null,
                "controller"    => "\App\Modules\Translations\Controllers\TranslationsController",
                "method"        => "index",
                "module_code"   => "wui",
                "module_type_code"=> "translations",
                "permission"    => "translations",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Translation Manager",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Gestor de Traduções",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 12,
                "code"          => null,
                "controller"    => "UsersController",
                "method"        => "index",
                "module_code"   => "auth",
                "module_type_code"=> "user",
                "permission"    => null,
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Users",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Utilizadores",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 13,
                "code"          => null,
                "controller"    => "ParameterUserTypesController",
                "method"        => "index",
                "module_code"   => "auth",
                "module_type_code"=> "user_parameters",
                "permission"    => "user_parameters",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "User Parameters",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Parâmetros de Utilizador",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 14,
                "code"          => null,
                "controller"    => "QuestionnairesController",
                "method"        => "index",
                "module_code"   => "q",
                "module_type_code"=> "q",
                "permission"    => "q",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Questionnaires",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Questionnaires",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 15,
                "code"          => null,
                "controller"    => "EmailsController",
                "method"        => "index",
                "module_code"   => "wui",
                "module_type_code"=> "email",
                "permission"    => "email",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Emails",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Email",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 16,
                "code"          => null,
                "controller"    => "PrivateNewslettersController",
                "method"        => "index",
                "module_code"   => "notify",
                "module_type_code"=> "message_all_users",
                "permission"    => "message_all_users",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Newsletters",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Newsletters",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 17,
                "code"          => null,
                "controller"    => "EntitiesDividedController",
                "method"        => "showEntity",
                "module_code"   => "orchestrator",
                "module_type_code"=> "entity",
                "permission"    => "entity",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Entity",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Entidade",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 18,
                "code"          => null,
                "controller"    => "RolesController",
                "method"        => "index",
                "module_code"   => "orchestrator",
                "module_type_code"=> "role",
                "permission"    => "role",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Functions",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Funções",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 19,
                "code"          => null,
                "controller"    => "HomePageTypesController",
                "method"        => "index",
                "module_code"   => "cm",
                "module_type_code"=> "home_page_type",
                "permission"    => "home_page_type",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Home Pages Types",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Tipos de Página Inicial",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 20,
                "code"          => null,
                "controller"    => "ParametersTemplateController",
                "method"        => "index",
                "module_code"   => "cb",
                "module_type_code"=> "parameter_template",
                "permission"    => "parameter_template",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Parameter Templates",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Modelos de Parâmetros",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 21,
                "code"          => null,
                "controller"    => "UsersController",
                "method"        => "indexCompleted",
                "module_code"   => "auth",
                "module_type_code"=> "in_person_registration",
                "permission"    => "in_person_registration",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Moderation",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Moderação",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 22,
                "code"          => null,
                "controller"    => "EventSchedulesController",
                "method"        => "index",
                "module_code"   => "q",
                "module_type_code"=> "poll",
                "permission"    => "poll",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Polls",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Polls",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ), array(
                "id"            => 23,
                "code"          => "entity_groups",
                "controller"    => "EntityGroupsController",
                "method"        => "showGroups",
                "module_code"   => null,
                "module_type_code"=> null,
                "permission"    => "entity_groups",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Entity User Groups",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Entity User Groups",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ),
            array(
                "id"            => 24,
                "code"          => null,
                "controller"    => "ShortLinksController",
                "method"        => "index",
                "module_code"   => "wui",
                "module_type_code"=> "short_links",
                "permission"    => "short_links",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Short Links",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Ligações Curtas",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ),
            /*array(
                "id"            => 22,
                "code"          => null,
                "controller"    => "",
                "method"        => "",
                "module_code"   => null,
                "module_type_code"=> null,
                "permission"    => "entity_groups",
                "translations" => array(
                    array(
                        "language_code" => "en",
                        "name"          => "",
                        "description"   => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "",
                        "description"   => "",
                    )
                ),
                "parameters"    => array()
            ),*/
        );

        foreach ($BEMenuElements as $BEMenuElement) {
            $translations = $BEMenuElement["translations"];
            $parameters = $BEMenuElement["parameters"];
            unset($BEMenuElement["translations"],$BEMenuElement["parameters"]);

            do {
                $rand = str_random(32);

                if (!($exists = BEMenuElement::where("key",$rand)->exists())) {
                    $BEMenuElement["key"] = $rand;
                }
            } while ($exists);

            $BEMenuElementFromBD = BEMenuElement::firstOrCreate($BEMenuElement);
            foreach ($parameters as $index=>$parameter) {
                $BEMenuElementFromBD->parameters()->attach($parameter["id"],["position"=>$index,"code"=>$parameter["code"] ?? null]);
            }

            foreach ($translations as $translation) {
                $translation = array_merge(["be_menu_element_id"=>$BEMenuElement["id"]],$translation);
                BEMenuElementTranslation::firstOrCreate($translation);
            }
        }
    }

    private function basicMenu($BEEntityMenu = false) {
        $BEEntityMenu = array(
            "id"        => 1,
            "menu_key"  => "defaultEntityMenu",
            "entity_id" => 0
        );

        BEEntityMenu::firstOrCreate($BEEntityMenu);

        $BEEntityMenuElements = array(
            array(
                "id"                => 1,
                "position"          => 0,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Participation",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Participação",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 2,
                "position"          => 1,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Phase 1",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Fase 1",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "phase1",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 3,
                "position"          => 2,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Phase 2",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Fase 2",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "phase2",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 4,
                "position"          => 3,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Phase 3",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Fase 3",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "phase3",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 5,
                "position"          => 4,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Ideas",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Ideias",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "idea",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ),array(
                "id"                => 6,
                "position"          => 5,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Forums",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Fóruns",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "forum",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 7,
                "position"          => 6,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Discussion",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Discussões",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "discussion",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 8,
                "position"          => 7,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Proposals",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Propostas",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "proposal",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 9,
                "position"          => 8,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Public Consultations",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Consultas Públicas",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "publicConsultation",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 10,
                "position"          => 9,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Tematic Consultations",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Consultas Temáticas",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "tematicConsultation",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 11,
                "position"          => 10,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Surveys",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Inquéritos",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "survey",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 12,
                "position"          => 11,
                "be_menu_element_id"=> 3,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Projects",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Projetos",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "project",
                        "be_menu_element_parameter_id"  => 2
                    )
                )
            ), array(
                "id"                => 14,
                "position"          => 13,
                "be_menu_element_id"=> 4,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "MP",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "MP",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 15,
                "position"          => 14,
                "be_menu_element_id"=> 22,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Polls",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Polls",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 16,
                "position"          => 15,
                "be_menu_element_id"=> 5,
                "be_entity_menu_id" => 1,
                "parent_id"         => 1,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Kiosks",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Kiosks",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 17,
                "position"          => 16,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Contents",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Conteúdos",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 18,
                "position"          => 17,
                "be_menu_element_id"=> 6,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Sites",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Sites",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 19,
                "position"          => 18,
                "be_menu_element_id"=> 7,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Menus",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Menus",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 20,
                "position"          => 19,
                "be_menu_element_id"=> 8,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Pages",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Páginas",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "pages",
                        "be_menu_element_parameter_id"  => 4
                    )
                )
            ), array(
                "id"                => 21,
                "position"          => 20,
                "be_menu_element_id"=> 9,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Pages NEW",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Páginas NEW",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "pages",
                        "be_menu_element_parameter_id"  => 5
                    )
                )
            ),array(
                "id"                => 22,
                "position"          => 21,
                "be_menu_element_id"=> 8,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "FAQs",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "FAQs",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "faqs",
                        "be_menu_element_parameter_id"  => 4
                    )
                )
            ), array(
                "id"                => 23,
                "position"          => 22,
                "be_menu_element_id"=> 8,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "News",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Notícias",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "news",
                        "be_menu_element_parameter_id"  => 4
                    )
                )
            ), array(
                "id"                => 24,
                "position"          => 23,
                "be_menu_element_id"=> 8,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Events",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Eventos",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "events",
                        "be_menu_element_parameter_id"  => 4
                    )
                )
            ), array(
                "id"                => 25,
                "position"          => 24,
                "be_menu_element_id"=> 10,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Content Sub Types",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Sub Tipos de Conteúdo",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 26,
                "position"          => 25,
                "be_menu_element_id"=> 11,
                "be_entity_menu_id" => 1,
                "parent_id"         => 17,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Translations",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Traduções",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 27,
                "position"          => 26,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Users",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Utilizadores",
                    )
                ),
                "parameters"  => array()
            ),array(
                "id"                => 28,
                "position"          => 27,
                "be_menu_element_id"=> 12,
                "be_entity_menu_id" => 1,
                "parent_id"         => 27,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Users",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Utilizadores",
                    )
                ),
                "parameters"  => array()
            ),array(
                "id"                => 29,
                "position"          => 28,
                "be_menu_element_id"=> 13,
                "be_entity_menu_id" => 1,
                "parent_id"         => 27,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "User Parameters",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Parâmetros de Utilizador",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 30,
                "position"          => 29,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Research",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Investigação",
                    )
                ),
                "parameters"  => array()
            ),array(
                "id"                => 31,
                "position"          => 30,
                "be_menu_element_id"=> 14,
                "be_entity_menu_id" => 1,
                "parent_id"         => 30,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Questionnaires",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Questionários",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 32,
                "position"          => 31,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Communication",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Comunicações",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 33,
                "position"          => 32,
                "be_menu_element_id"=> 15,
                "be_entity_menu_id" => 1,
                "parent_id"         => 32,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Emails",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Emails",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 34,
                "position"          => 33,
                "be_menu_element_id"=> 16,
                "be_entity_menu_id" => 1,
                "parent_id"         => 32,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Send to All Users",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Enviar para todos os Utilizadores",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 35,
                "position"          => 34,
                "be_menu_element_id"=> 1,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Configurations",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Configurações",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 36,
                "position"          => 35,
                "be_menu_element_id"=> 17,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Entity",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Entidade",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 37,
                "position"          => 36,
                "be_menu_element_id"=> 23,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Departments",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Departamentos",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 38,
                "position"          => 37,
                "be_menu_element_id"=> 18,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Functions",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Funções",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 39,
                "position"          => 38,
                "be_menu_element_id"=> 19,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Home Pages Types",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Tipos de Página Inicial",
                    )
                ),
                "parameters"  => array()
            ), array(
                "id"                => 40,
                "position"          => 39,
                "be_menu_element_id"=> 20,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Parameter Templates",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Modelos de Parâmetros",
                    )
                ),
                "parameters"  => array()
            ),
            array(
                "id"                => 41,
                "position"          => 40,
                "be_menu_element_id"=> 24,
                "be_entity_menu_id" => 1,
                "parent_id"         => 35,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "Short Links",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "Ligações Curtas",
                    )
                ),
                "parameters"  => array()
            ),
            /*array(
                "id"                => ,
                "position"          => ,
                "be_menu_element_id"=> 0,
                "be_entity_menu_id" => 1,
                "parent_id"         => 0,
                "translations"  => array(
                    array(
                        "language_code" => "en",
                        "name"          => "",
                    ),array(
                        "language_code" => "pt",
                        "name"          => "",
                    )
                ),
                "parameters"  => array(
                    array(
                        "value"                         => "",
                        "be_menu_element_parameter_id"  => ""
                    )
                )
            ),*/
        );

        foreach ($BEEntityMenuElements as $BEEntityMenuElement) {
            $translations = $BEEntityMenuElement["translations"];
            $parameters = $BEEntityMenuElement["parameters"];
            unset($BEEntityMenuElement["translations"], $BEEntityMenuElement["parameters"]);

            do {
                $rand = str_random(32);

                if (!($exists = BEEntityMenuElement::where("menu_key", $rand)->exists()))
                    $BEEntityMenuElement["menu_key"] = $rand;
            } while ($exists);

            $BEEntityMenuElementFromBD = BEEntityMenuElement::firstOrCreate($BEEntityMenuElement);
            foreach ($parameters as $index => $parameter) {
                $parameter = array_merge(["be_entity_menu_element_id" => $BEEntityMenuElement["id"]], $parameter);
                BEEntityMenuElementParameter::firstOrCreate($parameter);
            }

            foreach ($translations as $translation) {
                $translation = array_merge(["be_entity_menu_element_id" => $BEEntityMenuElement["id"]], $translation);
                BEEntityMenuElementTranslation::firstOrCreate($translation);
            }
        }
    }
}
