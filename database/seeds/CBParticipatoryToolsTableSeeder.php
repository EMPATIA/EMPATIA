<?php

use Illuminate\Database\Seeder;
use App\ConfigurationType;
use App\ConfigurationTypeTranslation;
use App\Configuration;
use App\ConfigurationTranslation;

class CBParticipatoryToolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->configurationTypes();
        $this->configurations();

        DB::commit();
    }

    private function configurationTypes() {
        $configurationTypes = array(
            array(
                "id" 					 	=> 1,
                "code"                      => "security",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Segurança",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Security",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Security",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Security",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Security",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "code"                      => "topic_options",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Opções dos Tópicos",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Topics Options",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Topics Options",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Topics Options",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Topics Options",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "code"                      => "topic_comments",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Comentários do tópico",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Topic Comments",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "code"                      => "general_configurations",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Configurações Gerais",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "General Configurations",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "code"                      => "notifications",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Benachrichtigungen",
                        "description"       => "Senden Sie Benachrichtigungen, wenn bestimmte Aktionen durchgeführt werden",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    ),array(
                        "language_code" 	=> "fr",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    ),array(
                        "language_code" 	=> "es",
                        "title" => "Notifications",
                        "description" => "Send notifications when certain actions are performed",
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "code"                      => "notifications_owners",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Owners Notifications",
                        "description"       => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "fr",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    ),array(
                        "language_code" 	=> "es",
                        "title" => "Owners Notifications",
                        "description" => "Notifications to owner",
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "code"                      => "notifications_topic",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Topic Notifications",
                        "description"       => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "fr",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    ),array(
                        "language_code" 	=> "es",
                        "title" => "Topic Notifications",
                        "description" => "Topic Notifications",
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "code"                      => "notification_deadline",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Notification Deadline",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Notification Deadline",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Notification Deadline",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Notification Deadline",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "fr",
                        "title" => "Notification Deadline",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "es",
                        "title" => "Notification Deadline",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "code"                      => "secondcycle_configuration",
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "cz",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "it",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "de",
                        "title" => "Second Cycle Configuration",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "en",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "fr",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "es",
                        "title" => "Second Cycle Configuration",
                        "description" => null,
                    )
                )
            )
        );

        foreach ($configurationTypes as $configurationType) {
            $translations = $configurationType["translations"];
            unset($configurationType["translations"]);

            ConfigurationType::firstOrCreate($configurationType);

            foreach ($translations as $translation) {
                $translation = array_merge(["configuration_type_id"=>$configurationType ["id"]],$translation);
                ConfigurationTypeTranslation::firstOrCreate($translation);
            }
        }
    }

    private function configurations() {
        $configurations = array(
            array(
                "id" 					 	=> 1,
                "code"                      => "allow_moderation",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Allow moderation",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "code"                      => "allow_comments",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Allow comments",
                        "description"       => "The user can write comments.",
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "code"                      => "allow_report_abuse",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Allow report abuse",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "code"                      => "allow_users_to_be_moderators",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Allow users to be moderators",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "code"                      => "read",
                "configuration_type_id"     => 2,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Read",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "code"                      => "write",
                "configuration_type_id"     => 2,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "en",
                        "title"             => "Write",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "code"                      => "security_public_access",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Acesso público",
                        "description"       => "Acesso público",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Public Access",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Public Access",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Public Access",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Public Access",
                        "description"       => "Public access description",
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "code"                      => "security_anonymous_comments",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Comentários anónimos",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Anonymous Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Anonymous Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Anonymous Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Anonymous Comments",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "code"                      => "security_create_topics",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Criação de Tópicos",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Create Topics",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Create Topics",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Create Topics",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Create Topics",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 10,
                "code"                      => "security_create_topics_anonymous",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Criação de Tópicos anónimos",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Create Topics Anonymous",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Create Topics Anonymous",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Create Topics Anonymous",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Create Topics Anonymous",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 11,
                "code"                      => "security_comment_authorization",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Comentários requerem autorização",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Comment needs authorization",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Comment needs authorization",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Comment needs authorization",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Comment needs authorization",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 12,
                "code"                      => "security_allow_report_abuses",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Reportar abuso",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Report Abuses",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Report Abuses",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Report Abuses",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Report Abuses",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 13,
                "code"                      => "topic_options_allow_files",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Ficheiros",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Files",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Files",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Files",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Files",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 14,
                "code"                      => "topic_options_allow_pictures",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Imagens",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Pictures",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Pictures",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Pictures",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Pictures",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 15,
                "code"                      => "topic_options_allow_co_op",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir cooperação",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Co-op",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Co-op",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Co-op",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Co-op",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 16,
                "code"                      => "topic_options_allow_video_link",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Links de Video",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Video Link",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Video Link",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Video Link",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Video Link",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 17,
                "code"                      => "topic_options_allow_share",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Partilhar",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Share",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Share",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Share",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Share",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 18,
                "code"                      => "topic_options_allow_user_count",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Contar Utilizadores",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow User Count",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow User Count",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow User Count",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow User Count",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 19,
                "code"                      => "topic_options_allow_follow",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Seguir",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Follow",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Follow",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Follow",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Follow",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 20,
                "code"                      => "topic_comments_allow_comments",
                "configuration_type_id"     => 6,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir Comentários",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow Comments",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow Comments",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 21,
                "code"                      => "topic_need_moderation",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Tópicos com moderação",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Topic need moderation",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Topic need moderation",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Topic need moderation",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Topic need moderation",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 22,
                "code"                      => "topic_comments_normal",
                "configuration_type_id"     => 6,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Comentários Normais",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Normal Comments",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 23,
                "code"                      => "topic_comments_positive_negative",
                "configuration_type_id"     => 6,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Comentários Positivos e Negativos",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Positive and Negative Comments",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 24,
                "code"                      => "topic_comments_positive_neutral_negative",
                "configuration_type_id"     => 6,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Comentários Positivos, Neutros e Negativos",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Positive, Neutral and Negative Comments",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 25,
                "code"                      => "only_one_topic",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "One Topic",
                        "description"       => "User can only create one topic",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "One Topic",
                        "description"       => "User can only create one topic",
                    )
                )
            ),array(
                "id" 					 	=> 26,
                "code"                      => "topic_as_public_questionnaire",
                "configuration_type_id"     => 51,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Tópicos como questionário público",
                        "description"       => "Tópicos como questionário público",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Topics as Public Questionnaire",
                        "description"       => "Topics as Public Questionnaire",
                    )
                )
            ),array(
                "id" 					 	=> 27,
                "code"                      => "topic_as_private_questionnaire",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Tópicos como questionário privado",
                        "description"       => "Tópicos como questionário privado",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Topics as private questionnaire",
                        "description"       => "Topics as private questionnaire",
                    )
                )
            ),array(
                "id" 					 	=> 28,
                "code"                      => "allow_alliance",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "allow_alliance",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "allow_alliance",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 29,
                "code"                      => "security_anonymous_create_topic_access",
                "configuration_type_id"     => 4,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Acesso anónimo à criação de tópicos",
                        "description"       => "Acesso anónimo à criação de tópicos",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Allow user to access creation form before registration",
                        "description"       => "Allow user to access creation form before registration",
                    )
                )
            ),array(
                "id" 					 	=> 30,
                "code"                      => "notification_content_change",
                "configuration_type_id"     => 8,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Inhalt ändern",
                        "description"       => "Benachrichtigen Sie die Themenfolger, wenn es eine Änderung des Themeninhalts gibt",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Content Change",
                        "description"       => "Notify topic followers when there is a change in the topic content",
                    )
                )
            ),array(
                "id" 					 	=> 31,
                "code"                      => "notification_new_comments",
                "configuration_type_id"     => 8,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Neue Kommentare",
                        "description"       => "Benachrichtigung senden, wenn es neue Kommentare gibt",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "New Comments",
                        "description"       => "Send notification when there are new comments",
                    )
                )
            ),array(
                "id" 					 	=> 32,
                "code"                      => "notification_status_change",
                "configuration_type_id"     => 8,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Status Änderung",
                        "description"       => "Benachrichtigung senden, wenn sich Status ändert",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Status Change",
                        "description"       => "Send notification when status changes",
                    )
                )
            ),array(
                "id" 					 	=> 33,
                "code"                      => "notification_delete",
                "configuration_type_id"     => 8,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when topic is deleted",
                    )
                )
            ),array(
                "id" 					 	=> 34,
                "code"                      => "notification_create_topic",
                "configuration_type_id"     => 10,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Create Topic",
                        "description"       => "Send notification when new topic is created",
                    )
                )
            ),array(
                "id" 					 	=> 35,
                "code"                      => "notification_delete_topic",
                "configuration_type_id"     => 10,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification when new topic is deleted",
                    )
                )
            ),array(
                "id" 					 	=> 36,
                "code"                      => "notification_edit_topic",
                "configuration_type_id"     => 10,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification when new topic is updated",
                    )
                )
            ),array(
                "id" 					 	=> 37,
                "code"                      => "notification_topic_status_change",
                "configuration_type_id"     => 10,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Status change",
                        "description"       => "Send notification when topic status is updated",
                    )
                )
            ),array(
                "id" 					 	=> 38,
                "code"                      => "notification_owner_create_topic",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Create Topic",
                        "description"       => "Send notification to owners when new topic is created",
                    )
                )
            ),array(
                "id" 					 	=> 39,
                "code"                      => "notification_owner_delete_topic",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Delete Topic",
                        "description"       => "Send notification to owners when new topic is deleted",
                    )
                )
            ),array(
                "id" 					 	=> 40,
                "code"                      => "notification_owner_edit_topic",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Edit Topic",
                        "description"       => "Send notification to owners when new topic is updated",
                    )
                )
            ),array(
                "id" 					 	=> 41,
                "code"                      => "notification_owner_new_comments",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "New comments",
                        "description"       => "Send notification to owners when there are new comments",
                    )
                )
            ),array(
                "id" 					 	=> 42,
                "code"                      => "notification_owner_change_status",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Status Change",
                        "description"       => "Send notification to owners when status change",
                    )
                )
            ),array(
                "id" 					 	=> 43,
                "code"                      => "disable_comments_functionality",
                "configuration_type_id"     => 6,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Disable Comments Functionality",
                        "description"       => "Disable all funcionalities of the comments sections.",
                    )
                )
            ),array(
                "id" 					 	=> 44,
                "code"                      => "notification_deadline",
                "configuration_type_id"     => 11,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Notification Deadline",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 45,
                "code"                      => "notification_owner_added_cooperator",
                "configuration_type_id"     => 9,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Notify Added Cooperator",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 46,
                "code"                      => "topic_options_allow_event_association",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Permitir associar um tópico a um evento",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 47,
                "code"                      => "show_status",
                "configuration_type_id"     => 5,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Permitir mostrar o estado do tópico",
                        "description"       => "Permitir mostrar o estado do tópico",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Permitir mostrar o estado do tópico",
                        "description"       => "Permitir mostrar o estado do tópico",
                    )
                )
            ),array(
                "id" 					 	=> 48,
                "code"                      => "subpad_news",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Active News",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Active News",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 49,
                "code"                      => "subpad_documents",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Active Documents",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Active Documents",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 50,
                "code"                      => "subpad_expenditures",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Active Expenditures",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 51,
                "code"                      => "subpad_phases",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Active Phases",
                        "description"       => null,
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Active Phases",
                        "description"       => null,
                    )
                )
            ),array(
                "id" 					 	=> 52,
                "code"                      => "topic_options_allow_news",
                "configuration_type_id"     => 1,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "cz",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "it",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "de",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "fr",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "es",
                        "title"             => "Allow linked news to topic",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 53,
                "code"                      => "submit_proposals_dates",
                "configuration_type_id"     => 7,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Datas para submissão de propostas",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Dates to submit proposals",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 54,
                "code"                      => "technical_analysis_dates",
                "configuration_type_id"     => 7,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Datas para análises técnicas",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Dates to technical analyses",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 55,
                "code"                      => "complaint_dates",
                "configuration_type_id"     => 7,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Datas para as reclamações",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Dates to complaints",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 56,
                "code"                      => "vote_dates",
                "configuration_type_id"     => 7,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Datas para as votações",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Dates to vote",
                        "description"       => "",
                    )
                )
            ),array(
                "id" 					 	=> 57,
                "code"                      => "show_results_dates",
                "configuration_type_id"     => 7,
                "created_by"                => "defaultUSERprojectEMPATIA2016JAN",
                "updated_by"                => "",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "title"             => "Datas para mostrar os resultados",
                        "description"       => "",
                    ),array(
                        "language_code" 	=> "en",
                        "title"             => "Dates to show results",
                        "description"       => "",
                    )
                )
            )
        );

        foreach ($configurations as $configuration) {
            $translations = $configuration["translations"];
            unset($configuration["translations"]);

            Configuration::firstOrCreate($configuration);

            foreach ($translations as $translation) {
                $translation = array_merge(["configuration_id"=>$configuration ["id"]],$translation);
                ConfigurationTranslation::firstOrCreate($translation);
            }
        }
    }
}
