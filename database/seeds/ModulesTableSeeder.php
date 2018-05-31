<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\ModuleType;
use Illuminate\Support\Facades\DB;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->modules();
        $this->moduleTypes();

        DB::commit();
    }

    private function modules() {
        $modules = array(
            array(
                "id" 					 	=> 1,
                "module_key" 	            => "IU7tPOott0noF3SgCyLWLpIr2bp1U3",
                "name" 					 	=> "Analytics",
                "code"                      => "analytics",
                "token"                     => "18EsZwtuqKJ3F16XcnNhFgprdoNdKX",
            ),array(
                "id" 					 	=> 2,
                "module_key" 	            => "Dkt1hUfWzuBYXAMCysTyi10vJNeHTa",
                "name" 					 	=> "Auth",
                "code"                      => "auth",
                "token"                     => "Cm7tXirnvwPNJ3grAghc0HDT7MVAQe",
            ),array(
                "id" 					 	=> 3,
                "module_key" 	            => "R3RDk1iqiAzEAD79yA0xYrWir4h8UJ",
                "name" 					 	=> "CB",
                "code"                      => "cb",
                "token"                     => "M1mcNZe7XOkZdX7UTt5CSxm2Bhqwv1",
            ),array(
                "id" 					 	=> 4,
                "module_key" 	            => "LpSe2EBeEYZb96J994ZQqbq1RrYabM",
                "name" 					 	=> "CM",
                "code"                      => "cm",
                "token"                     => "eG0ZGydamWOvXAkKh7NTsQLkJw6Pd6",
            ),array(
                "id" 					 	=> 5,
                "module_key" 	            => "fQBMQIdN76dJKzk9du8EOzxvuerpUR",
                "name" 					 	=> "Files",
                "code"                      => "files",
                "token"                     => "HpE0oxY74Qla3p8WveSZ2HvOn4QAex",
            ),array(
                "id" 					 	=> 6,
                "module_key" 	            => "ydyth1imsTod3C7Y3i41qako4pGQcC",
                "name" 					 	=> "Logs",
                "code"                      => "logs",
                "token"                     => "Q9AipIxnIHKy7HFU7DsEiixEzHPJEg",
            ),array(
                "id" 					 	=> 7,
                "module_key" 	            => "6FZOB5C2RlYlTvuYXzavsRvl6cr4xp",
                "name" 					 	=> "MP",
                "code"                      => "mp",
                "token"                     => "2FK2MOtLvLOdtSN358rkNa3lcdBvs6",
            ),array(
                "id" 					 	=> 8,
                "module_key" 	            => "pahrcQW7bqNSmzqUfeAoAXNfKTMDok",
                "name" 					 	=> "Notify",
                "code"                      => "notify",
                "token"                     => "4L0ITcy0A6j1EIPPRw4Zt2IVGy0kC9",
            ),array(
                "id" 					 	=> 9,
                "module_key" 	            => "izpU5d99sjgNIYuXigUPoh54LAwDdr",
                "name" 					 	=> "Orchestrator",
                "code"                      => "orchestrator",
                "token"                     => "k5BHG8ChQLjIw35WOW8CK768hHqKzi",
            ),array(
                "id" 					 	=> 10,
                "module_key" 	            => "B8NLUW2s0H6wuljgmlHgnjgjN27bTU",
                "name" 					 	=> "Q",
                "code"                      => "q",
                "token"                     => "JJeKqI6amnSHxQ25UtXrd9sJyursft",
            ),array(
                "id" 					 	=> 11,
                "module_key" 	            => "SSnDv6kBSAjj8Ng0kwr7lhf52o3Gza",
                "name" 					 	=> "Vote",
                "code"                      => "vote",
                "token"                     => "gW2NgAEmQY4EsYNIuzfVApDd3xKPH7",
            ),array(
                "id" 					 	=> 12,
                "module_key" 	            => "poSAZprANHQiYq0KxyGuxXTq6N6Hgn",
                "name" 					 	=> "WUI",
                "code"                      => "wui",
                "token"                     => "xx4DCBqsXBxsHNnO2IYEEGm3s5g5BW",
            ),array(
                "id" 					 	=> 13,
                "module_key" 	            => "yuSAZprANHQiYq0KxyGuxXTq6N6Hgn",
                "name" 					 	=> "KIOSK",
                "code"                      => "kiosk",
                "token"                     => "cc4DCBqsXBxsHNnO2IYEEGm3s5g545",
            ),array(
                "id" 					 	=> 14,
                "module_key" 	            => "trSAZprANHQiYq0KxyGuxXTq6N6Hgn",
                "name" 					 	=> "Events",
                "code"                      => "events",
                "token"                     => "aa4DCBqsFGxsHNnO2IYEEGm3s5g5BW",
            )
        );

        foreach ($modules as $module) {

            Module::firstOrCreate($module);
        }
    }

    private function moduleTypes() {
        $moduleTypes = array(
            array(
                "id" 			    => 1,
                "module_type_key"   => "uDakNR3YnT6JyYc9VF9cCQ9lMHU9T69N",
                "module_id"         => 1,
                "code"              => "test_code",
                "name"              => "test_name",
            ),array(
                "id" 			    => 2,
                "module_type_key"   => "H6b6Lpkl2eBySMU1sG62y1XGrJ7eZ17v",
                "module_id"         => 2,
                "code"              => "user",
                "name"              => "User",
            ),array(
                "id" 			    => 3,
                "module_type_key"   => "KU5Nc3719t7gqaL86Tyaey172T70d7VJ",
                "module_id"         => 2,
                "code"              => "manager",
                "name"              => "Manager",
            ),array(
                "id" 			    => 4,
                "module_type_key"   => "Aj5AS50tpWm6ReSW7rq8QeJ75G351K3m",
                "module_id"         => 2,
                "code"              => "in_person_registration",
                "name"              => "In Person Registration",
            ),array(
                "id" 			    => 5,
                "module_type_key"   => "6av6Y1qHcXjFBFx2Vp0d9eUM1FVV1erL",
                "module_id"         => 2,
                "code"              => "confirm_user",
                "name"              => "Confirm User",
            ),array(
                "id" 			    => 6,
                "module_type_key"   => "pdWlh7B61j2SdZukGVqe24t30MhhToJ0",
                "module_id"         => 2,
                "code"              => "user_parameters",
                "name"              => "User Parameters",
            ),array(
                "id" 			    => 7,
                "module_type_key"   => "sCUqHnk9UkYqXdOc0NjFhKrIN7Vi9BFF",
                "module_id"         => 2,
                "code"              => "authorize",
                "name"              => "Authorize",
            ),array(
                "id" 			    => 8,
                "module_type_key"   => "z1q49z0YW97QFsyI4F4Sr2HfybJ1S8Jp",
                "module_id"         => 3,
                "code"              => "survey",
                "name"              => "Survey",
            ),array(
                "id" 			    => 9,
                "module_type_key"   => "574ZbsE6yQh0kR7laxL5ZwcGz4nh08Fq",
                "module_id"         => 3,
                "code"              => "idea",
                "name"              => "Idea",
            ),array(
                "id" 			    => 10,
                "module_type_key"   => "fQLdG6J5R7b295zgs9z6ypxrUt287a5w",
                "module_id"         => 3,
                "code"              => "proposal",
                "name"              => "Proposal",
            ),array(
                "id" 			    => 11,
                "module_type_key"   => "8TDv844lSlc9Pg86IvMDpB7Kn5VeHGj5",
                "module_id"         => 3,
                "code"              => "forum",
                "name"              => "Forum",
            ),array(
                "id" 			    => 12,
                "module_type_key"   => "0eYrV5G88jNA0wazpmMcxTlXutu211h1",
                "module_id"         => 3,
                "code"              => "discussion",
                "name"              => "Discussion",
            ),array(
                "id" 			    => 13,
                "module_type_key"   => "2LQXB7FR9HDJkE4pRdOwTC202pr5hUVE",
                "module_id"         => 3,
                "code"              => "tematicConsultation",
                "name"              => "Tematic Consultation",
            ),array(
                "id" 			    => 14,
                "module_type_key"   => "u7mDnoZgSw4xmo6zARB7Wf5Ftdhi451G",
                "module_id"         => 3,
                "code"              => "publicConsultation",
                "name"              => "Public Consultation",
            ),array(
                "id" 			    => 15,
                "module_type_key"   => "C7wKU8qs79j0iZX96GFZl2t9N66B0h8v",
                "module_id"         => 3,
                "code"              => "parameter_template",
                "name"              => "Parameter Template",
            ),array(
                "id" 			    => 16,
                "module_type_key"   => "tt80mfwrThuF4OrLLiwiIPqqM7g0KSyl",
                "module_id"         => 3,
                "code"              => "comment_management",
                "name"              => "Comment Management",
            ),array(
                "id" 			    => 17,
                "module_type_key"   => "EeWMHbt4DhSTa50DoP8jLA2l1z9Wstgq",
                "module_id"         => 3,
                "code"              => "topic_moderation",
                "name"              => "Topic Moderation",
            ),array(
                "id" 			    => 18,
                "module_type_key"   => "5VpvSqZZvIYgZGZNgo9SBKdbDish3MmG",
                "module_id"         => 3,
                "code"              => "project",
                "name"              => "Project",
            ),array(
                "id" 			    => 19,
                "module_type_key"   => "k9T5LJfs5GwpcLzZF2tQf0P9CNjrtkH4",
                "module_id"         => 3,
                "code"              => "topics",
                "name"              => "Topics",
            ),array(
                "id" 			    => 20,
                "module_type_key"   => "dP3OkZhyv2ekPaSg1qI2M12k4pt6jhrc",
                "module_id"         => 3,
                "code"              => "pad_parameters",
                "name"              => "Pad Parameters",
            ),array(
                "id" 			    => 21,
                "module_type_key"   => "78mQRXjUs0Sa3oHGtiqh7ostiqqQGkqp",
                "module_id"         => 3,
                "code"              => "pad_votes",
                "name"              => "Pad Votes",
            ),array(
                "id" 			    => 22,
                "module_type_key"   => "FnBGKxJP2iG5jXx8yblRCVM1W3pAnTIO",
                "module_id"         => 3,
                "code"              => "moderators",
                "name"              => "Pad moderators",
            ),array(
                "id" 			    => 23,
                "module_type_key"   => "dTYIlPR0BZY0AyMdsLz0k2OhFom7Jf0n",
                "module_id"         => 3,
                "code"              => "configurations",
                "name"              => "PAD Configurations",
            ),array(
                "id" 			    => 24,
                "module_type_key"   => "93YPubNHfGNQLhNYNOnfpsvutW182jTj",
                "module_id"         => 3,
                "code"              => "vote_analysis",
                "name"              => "PAD Vote Analysis",
            ),array(
                "id" 			    => 25,
                "module_type_key"   => "EbicedzBiWozRiSktLwbrxCTGy32K0Qj",
                "module_id"         => 3,
                "code"              => "topic_status",
                "name"              => "Topic Status",
            ),array(
                "id" 			    => 26,
                "module_type_key"   => "WxW0qkUOYqCcJQqTF7dgx2Hzgr7pVGDH",
                "module_id"         => 3,
                "code"              => "topic_status_history",
                "name"              => "Topic Status History",
            ),array(
                "id" 			    => 27,
                "module_type_key"   => "cB5P4TAUXi47z22rvBSp8bAXcBNHNnnd",
                "module_id"         => 3,
                "code"              => "phase1",
                "name"              => "Phase One",
            ),array(
                "id" 			    => 28,
                "module_type_key"   => "dmPOkSctAFeuGTfWEXdPdZp41FxzgNYq",
                "module_id"         => 3,
                "code"              => "phase2",
                "name"              => "Phase Two",
            ),array(
                "id" 			    => 29,
                "module_type_key"   => "7SEa4AS5WlZU1jjWEt3IskepQnOKOyfr",
                "module_id"         => 3,
                "code"              => "phase3",
                "name"              => "Phase Three",
            ),array(
                "id" 			    => 30,
                "module_type_key"   => "umOyU5mSXYsBFUzDD7rEFXPKsHcw64vo",
                "module_id"         => 3,
                "code"              => "qa",
                "name"              => "Question and Answer",
            ),array(
                "id" 			    => 31,
                "module_type_key"   => "oanNFdkTJAbNgXZ5vE5z4U6y1xBmrzT3",
                "module_id"         => 3,
                "code"              => "moderation",
                "name"              => "Moderation",
            ),array(
                "id" 			    => 32,
                "module_type_key"   => "xpRZyjfDtsObnswrrNPIRzXTFKZghfol",
                "module_id"         => 3,
                "code"              => "project_2c",
                "name"              => "Project Second Cycle",
            ),array(
                "id" 			    => 33,
                "module_type_key"   => "HTsTC05D5366eJO9H0peSLA5vrRvbuvd",
                "module_id"         => 3,
                "code"              => "comments",
                "name"              => "Comments",
            ),array(
                "id" 			    => 34,
                "module_type_key"   => "BZueAWVHYaNceF6RZnGv3s2jmxFxXKjk",
                "module_id"         => 3,
                "code"              => "flags",
                "name"              => "Flags",
            ),array(
                "id" 			    => 35,
                "module_type_key"   => "M1wAY5fcUN4ylXljF04HX4yYPmaQbqsk",
                "module_id"         => 3,
                "code"              => "notifications",
                "name"              => "PAD Notifications",
            ),array(
                "id" 			    => 36,
                "module_type_key"   => "Xg1rukgPLdN5CR6fLWu5pmsa2hBLFw7I",
                "module_id"         => 3,
                "code"              => "cooperators",
                "name"              => "Cooperators",
            ),array(
                "id" 			    => 37,
                "module_type_key"   => "N8b2rMK2JMW1BEOax36EwRj75iWZ0iHP",
                "module_id"         => 4,
                "code"              => "pages",
                "name"              => "Page",
            ),array(
                "id" 			    => 38,
                "module_type_key"   => "b1HxxLka2GryKd79742cvJpC8UP11rXJ",
                "module_id"         => 4,
                "code"              => "news",
                "name"              => "News",
            ),array(
                "id" 			    => 39,
                "module_type_key"   => "zrgNNRu3yQCFr30C604G81Ss8B8cFfsb",
                "module_id"         => 4,
                "code"              => "events",
                "name"              => "Event",
            ),array(
                "id" 			    => 40,
                "module_type_key"   => "b0Kffu5PwCYynilMIX7y393YbsfF9ZxT",
                "module_id"         => 4,
                "code"              => "menu",
                "name"              => "menu",
            ),array(
                "id" 			    => 41,
                "module_type_key"   => "xZS5rt81894ro5DrvQPoldzy07umz8dD",
                "module_id"         => 4,
                "code"              => "home_page_type",
                "name"              => "Home Page Type",
            ),array(
                "id" 			    => 42,
                "module_type_key"   => "3O9wP6wKXbmwS8zRcQ2iZta4nPyO2g8G",
                "module_id"         => 4,
                "code"              => "content_subtypes",
                "name"              => "Content sub types",
            ),array(
                "id" 			    => 43,
                "module_type_key"   => "ZhvbqLvMzibstFRIDJYYm9fj8cFWvT8B",
                "module_id"         => 4,
                "code"              => "home_page_types_children",
                "name"              => "Home Page Type Children",
            ),array(
                "id" 			    => 44,
                "module_type_key"   => "7gjUVz8iAofQtcs16m0frO7foCJHjSig",
                "module_id"         => 4,
                "code"              => "articles",
                "name"              => "Articles",
            ),array(
                "id" 			    => 45,
                "module_type_key"   => "wUT2ZrX6iL7OhgwyhUzJ2TZopm57pQ3Q",
                "module_id"         => 4,
                "code"              => "municipal_faqs",
                "name"              => "Municipal FAQs",
            ),array(
                "id" 			    => 46,
                "module_type_key"   => "yEOxcmXiAyDaW7W69yyJs0OJTWQsIA4W",
                "module_id"         => 4,
                "code"              => "faqs",
                "name"              => "FAQs",
            ),array(
                "id" 			    => 47,
                "module_type_key"   => "5REs3lEYBZLD1d4cBHgAp1YaJIwpFPVX",
                "module_id"         => 4,
                "code"              => "dynamic_be_menu",
                "name"              => "Dynamic Back Office Menu",
            ),array(
                "id" 			    => 48,
                "module_type_key"   => "3k8YKWhcDUVfWUvbnVj0s8L9Hjz3gN2",
                "module_id"         => 4,
                "code"              => "personal_dynamic_be_menu",
                "name"              => "Personal Dynamic Back Office Menu",
            ),array(
                "id" 			    => 49,
                "module_type_key"   => "fiqcQ9s46DMDZjd2AMRZl4p9e5ODXPi4",
                "module_id"         => 7,
                "code"              => "mp",
                "name"              => "MP",
            ),array(
                "id" 			    => 50,
                "module_type_key"   => "D15c9CD5w0NzQL34REjcrp1e1jFY0Veo",
                "module_id"         => 7,
                "code"              => "mp_template",
                "name"              => "MP Template",
            ),array(
                "id" 			    => 51,
                "module_type_key"   => "BAw8yUxLkynYXWgax6R3Q8CNaKDpV4bU",
                "module_id"         => 8,
                "code"              => "message_all_users",
                "name"              => "Send message to all users",
            ),array(
                "id" 			    => 52,
                "module_type_key"   => "a4B9z511uYUDBZEQeuvJ36tIZe8g0kph",
                "module_id"         => 9,
                "code"              => "entity",
                "name"              => "Entity",
            ),array(
                "id" 			    => 53,
                "module_type_key"   => "1z7t2h0K9SX4209m3V8X0s8vSFMKIjm1",
                "module_id"         => 9,
                "code"              => "role",
                "name"              => "Role",
            ),array(
                "id" 			    => 54,
                "module_type_key"   => "suWTe1UP4cLXdUNpUuxBHLf377G7FLei",
                "module_id"         => 9,
                "code"              => "entity_site",
                "name"              => "Entity Site",
            ),array(
                "id" 			    => 55,
                "module_type_key"   => "93hUGpIuAxZiR56WRRIhlcA7LPKTJT7G",
                "module_id"         => 9,
                "code"              => "entity_layout",
                "name"              => "Entity Layout",
            ),array(
                "id" 			    => 56,
                "module_type_key"   => "LgCyRJVdmrjrv63Q92T1mozSzUU7EKXu",
                "module_id"         => 9,
                "code"              => "entity_language",
                "name"              => "Entity Language",
            ),array(
                "id" 			    => 57,
                "module_type_key"   => "GSeY5BA4WOJJXm5GRiCc3X1u2seeXhG9",
                "module_id"         => 9,
                "code"              => "entity_auth_method",
                "name"              => "Entity Auth Method",
            ),array(
                "id" 			    => 58,
                "module_type_key"   => "H7PSJXa3UkcnPuejFe6V5NswcvhdVW9v",
                "module_id"         => 9,
                "code"              => "entity_details",
                "name"              => "Entity Details",
            ),array(
                "id" 			    => 59,
                "module_type_key"   => "K1BTj5zdiisXhK5vIa9irPDKSWu4fyvp",
                "module_id"         => 9,
                "code"              => "site_use_terms",
                "name"              => "Site Use Terms",
            ),array(
                "id" 			    => 60,
                "module_type_key"   => "NV4Kqv0K70LU2YenIWrOjM6uZNa65jYa",
                "module_id"         => 9,
                "code"              => "site_privacy_policy",
                "name"              => "Site Privacy Policy",
            ),array(
                "id" 			    => 61,
                "module_type_key"   => "H8sdkhLzFObQzYLiTTPPY1WI39tkM3Gr",
                "module_id"         => 9,
                "code"              => "site_email_template",
                "name"              => "Site Email Template",
            ),array(
                "id" 			    => 62,
                "module_type_key"   => "wQ6LifPdernx5ATwMgbkSPnfOZ0ksckB",
                "module_id"         => 9,
                "code"              => "site_configurations",
                "name"              => "Site Configurations",
            ),array(
                "id" 			    => 63,
                "module_type_key"   => "me9Q4uXFN7H5UoVpnLehRIOMLrILAuxY",
                "module_id"         => 9,
                "code"              => "site_login_levels",
                "name"              => "Site Login Levels",
            ),array(
                "id" 			    => 64,
                "module_type_key"   => "vaNDspEooilBeJx9baEw5G2haXa6qngg",
                "module_id"         => 9,
                "code"              => "role_permissions",
                "name"              => "Role Permissions",
            ),array(
                "id" 			    => 65,
                "module_type_key"   => "m4nhPlgWDRgXQZlftE4b4mQtlL9Rbr5O",
                "module_id"         => 9,
                "code"              => "technical_evaluation",
                "name"              => "Technical Evaluation",
            ),array(
                "id" 			    => 66,
                "module_type_key"   => "lwccS5QB6qtrUa1YESEWOwNCJsXqMqJR",
                "module_id"         => 9,
                "code"              => "newsletter_subscriptions",
                "name"              => "Newsletter Subscriptions",
            ),array(
                "id" 			    => 67,
                "module_type_key"   => "F57sMX3E3J1lN6zQieh55P7640CTCruh",
                "module_id"         => 10,
                "code"              => "poll",
                "name"              => "Poll",
            ),array(
                "id" 			    => 68,
                "module_type_key"   => "kC4L9mU6wN9p6cPL9Rca2O5doHX5DU9s",
                "module_id"         => 10,
                "code"              => "q",
                "name"              => "Q",
            ),array(
                "id" 			    => 69,
                "module_type_key"   => "4bYkthdkQT8SPrxXQvKzwzFeM32lQTaS",
                "module_id"         => 11,
                "code"              => "presencial_vote",
                "name"              => "Presencial Vote",
            ),array(
                "id" 			    => 70,
                "module_type_key"   => "pkjtur6N4mykhFXwaYAU6q4z8pUoYYQe",
                "module_id"         => 12,
                "code"              => "open_data",
                "name"              => "Open Data",
            ),array(
                "id" 			    => 71,
                "module_type_key"   => "JSPlnhfIG4sj4iCyrumRVhPEtR0NbcY7",
                "module_id"         => 12,
                "code"              => "email",
                "name"              => "E-mail",
            ),array(
                "id" 			    => 72,
                "module_type_key"   => "qLI0hVrT7lUPBfduBEp6hSiWyudmf4Ni",
                "module_id"         => 12,
                "code"              => "sms",
                "name"              => "SMS",
            ),array(
                "id" 			    => 73,
                "module_type_key"   => "sw1xnnL4n5CCreKCk8A9O6aNQV1T0jfh",
                "module_id"         => 12,
                "code"              => "history",
                "name"              => "History",
            ),array(
                "id" 			    => 74,
                "module_type_key"   => "pTLvsc731RVK2gl5VaidTe9bor4CfGqp",
                "module_id"         => 12,
                "code"              => "empaville",
                "name"              => "Empaville",
            ),array(
                "id" 			    => 75,
                "module_type_key"   => "FranFmKPlWIGfEn9vFgSUUfMxCFTYzhE",
                "module_id"         => 12,
                "code"              => "wizard",
                "name"              => "Wizard",
            ),array(
                "id" 			    => 76,
                "module_type_key"   => "RPf2zEOtytzNzqt3lDPzEpSZerxgTOhA",
                "module_id"         => 12,
                "code"              => "entity_groups",
                "name"              => "Entity Groups",
            ),array(
                "id" 			    => 77,
                "module_type_key"   => "DHFzM3mevOAKukehxZBPyVyuRnPU2yFJ",
                "module_id"         => 12,
                "code"              => "sites",
                "name"              => "Sites",
            ),array(
                "id" 			    => 78,
                "module_type_key"   => "0lDCy8vkE6UbD7rxPxBTKbN8e0JteGhE",
                "module_id"         => 12,
                "code"              => "entity_groups_users",
                "name"              => "Entity Groups Users",
            ),array(
                "id" 			    => 79,
                "module_type_key"   => "Z5DUH3PR6NPbrxv06MbkHgefChiqbGB2",
                "module_id"         => 12,
                "code"              => "entity_groups_permissions",
                "name"              => "Entity Groups Permissions",
            ),array(
                "id" 			    => 80,
                "module_type_key"   => "aABzanfXenoeem54u6MNXWk5aUXqthId",
                "module_id"         => 12,
                "code"              => "translations",
                "name"              => "Translations",
            ),array(
                "id" 			    => 81,
                "module_type_key"   => "rl8hgycrjF94M3MkBNv68rJKB3ShJEAk",
                "module_id"         => 12,
                "code"              => "all_messages",
                "name"              => "Entity Messages",
            ),array(
                "id" 			    => 82,
                "module_type_key"   => "eP3lK09LlDCLOB4HoYQdOEZQHS6Mx5d9",
                "module_id"         => 12,
                "code"              => "short_links",
                "name"              => "Short Links",
            ),array(
                "id" 			    => 83,
                "module_type_key"   => "rSeR1m8dYt5zx9W7BTxY8SK0Z1Wn1E15",
                "module_id"         => 13,
                "code"              => "kiosk",
                "name"              => "Kiosk",
            ),array(
                "id" 			    => 84,
                "module_type_key"   => "f09106tx05y1U47z19888vJ5O5SXKhnz",
                "module_id"         => 14,
                "code"              => "event",
                "name"              => "Event",
            )
        );

        foreach ($moduleTypes as $moduleType) {

            ModuleType::firstOrCreate($moduleType);

        }
    }
}
