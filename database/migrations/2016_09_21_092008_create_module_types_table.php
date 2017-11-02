<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateModuleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module_type_key')->unique();
            $table->integer('module_id')->unsigned();
            $table->string('code');
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
        });


        $modulesTypes = array(
            array('id' => '1',	 'module_type_key' => 'z1q49z0YW97QFsyI4F4Sr2HfybJ1S8Jp',	 'module_id' => '3',	 'code' => 'survey',					    'name' => 'Survey',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '2',	 'module_type_key' => '574ZbsE6yQh0kR7laxL5ZwcGz4nh08Fq',	 'module_id' => '3',	 'code' => 'idea',	 					    'name' => 'Idea',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '3',	 'module_type_key' => 'fQLdG6J5R7b295zgs9z6ypxrUt287a5w',	 'module_id' => '3',	 'code' => 'proposal',					    'name' => 'Proposal',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '4',	 'module_type_key' => '8TDv844lSlc9Pg86IvMDpB7Kn5VeHGj5',	 'module_id' => '3',	 'code' => 'forum',	 					    'name' => 'Forum',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '5',	 'module_type_key' => '0eYrV5G88jNA0wazpmMcxTlXutu211h1',	 'module_id' => '3',	 'code' => 'discussion',				    'name' => 'Discussion',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '6',	 'module_type_key' => '2LQXB7FR9HDJkE4pRdOwTC202pr5hUVE',	 'module_id' => '3',	 'code' => 'tematicConsultation',		    'name' => 'Tematic Consultation',		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '7',	 'module_type_key' => 'u7mDnoZgSw4xmo6zARB7Wf5Ftdhi451G',	 'module_id' => '3',	 'code' => 'publicConsultation',		    'name' => 'Public Consultation',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '8',	 'module_type_key' => 'C7wKU8qs79j0iZX96GFZl2t9N66B0h8v',	 'module_id' => '3',	 'code' => 'parameter_template',		    'name' => 'Parameter Template',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '9',	 'module_type_key' => 'tt80mfwrThuF4OrLLiwiIPqqM7g0KSyl',	 'module_id' => '3',	 'code' => 'comment_management',		    'name' => 'Comment Management',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '10',	 'module_type_key' => 'H6b6Lpkl2eBySMU1sG62y1XGrJ7eZ17v',	 'module_id' => '2',	 'code' => 'user',	 					    'name' => 'User',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '11',	 'module_type_key' => 'KU5Nc3719t7gqaL86Tyaey172T70d7VJ',	 'module_id' => '2',	 'code' => 'manager',	 				    'name' => 'Manager',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '12',	 'module_type_key' => 'Aj5AS50tpWm6ReSW7rq8QeJ75G351K3m',	 'module_id' => '2',	 'code' => 'in_person_registration',	    'name' => 'In Person Registration',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '13',	 'module_type_key' => '6av6Y1qHcXjFBFx2Vp0d9eUM1FVV1erL',	 'module_id' => '2',	 'code' => 'confirm_user',	 			    'name' => 'Confirm User',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '14',	 'module_type_key' => 'pdWlh7B61j2SdZukGVqe24t30MhhToJ0',	 'module_id' => '2',	 'code' => 'user_parameters',	 		    'name' => 'User Parameters',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '15',	 'module_type_key' => 'N8b2rMK2JMW1BEOax36EwRj75iWZ0iHP',	 'module_id' => '4',	 'code' => 'pages',	 					    'name' => 'Page',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '16',	 'module_type_key' => 'b1HxxLka2GryKd79742cvJpC8UP11rXJ',	 'module_id' => '4',	 'code' => 'news',	 					    'name' => 'News',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '17',	 'module_type_key' => 'zrgNNRu3yQCFr30C604G81Ss8B8cFfsb',	 'module_id' => '4',	 'code' => 'events',	 				    'name' => 'Event',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '18',	 'module_type_key' => 'b0Kffu5PwCYynilMIX7y393YbsfF9ZxT',	 'module_id' => '4',	 'code' => 'menu',	 					    'name' => 'menu',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '19',	 'module_type_key' => 'xZS5rt81894ro5DrvQPoldzy07umz8dD',	 'module_id' => '4',	 'code' => 'home_page_type',	 		    'name' => 'Home Page Type',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '20',	 'module_type_key' => 'f09106tx05y1U47z19888vJ5O5SXKhnz',	 'module_id' => '14',	 'code' => 'event',	 					    'name' => 'Event',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '21',	 'module_type_key' => 'F57sMX3E3J1lN6zQieh55P7640CTCruh',	 'module_id' => '10',	 'code' => 'poll',	 					    'name' => 'Poll',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '22',	 'module_type_key' => 'kC4L9mU6wN9p6cPL9Rca2O5doHX5DU9s',	 'module_id' => '10',	 'code' => 'q',	 						    'name' => 'Q',	 						        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '23',	 'module_type_key' => 'rSeR1m8dYt5zx9W7BTxY8SK0Z1Wn1E15',	 'module_id' => '13',	 'code' => 'kiosk',	 					    'name' => 'Kiosk',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '24',	 'module_type_key' => 'fiqcQ9s46DMDZjd2AMRZl4p9e5ODXPi4',	 'module_id' => '7',	 'code' => 'mp',	 					    'name' => 'MP',	 						        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '25',	 'module_type_key' => 'D15c9CD5w0NzQL34REjcrp1e1jFY0Veo',	 'module_id' => '7',	 'code' => 'mp_template',	 			    'name' => 'MP Template',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '26',	 'module_type_key' => 'a4B9z511uYUDBZEQeuvJ36tIZe8g0kph',	 'module_id' => '9',	 'code' => 'entity',	 				    'name' => 'Entity',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '27',	 'module_type_key' => '1z7t2h0K9SX4209m3V8X0s8vSFMKIjm1',	 'module_id' => '9',	 'code' => 'role',	 					    'name' => 'Role',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '28',	 'module_type_key' => 'uDakNR3YnT6JyYc9VF9cCQ9lMHU9T69N',	 'module_id' => '1',	 'code' => 'test_code',	 				    'name' => 'test_name',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '29',	 'module_type_key' => 'EeWMHbt4DhSTa50DoP8jLA2l1z9Wstgq',	 'module_id' => '3',	 'code' => 'topic_moderation',	 		    'name' => 'Topic Moderation',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '30',	 'module_type_key' => 'suWTe1UP4cLXdUNpUuxBHLf377G7FLei',	 'module_id' => '9',	 'code' => 'entity_site',	 			    'name' => 'Entity Site',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '31',	 'module_type_key' => '93hUGpIuAxZiR56WRRIhlcA7LPKTJT7G',	 'module_id' => '9',	 'code' => 'entity_layout',	 			    'name' => 'Entity Layout',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '32',	 'module_type_key' => 'LgCyRJVdmrjrv63Q92T1mozSzUU7EKXu',	 'module_id' => '9',	 'code' => 'entity_language',	 		    'name' => 'Entity Language',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '33',	 'module_type_key' => 'GSeY5BA4WOJJXm5GRiCc3X1u2seeXhG9',	 'module_id' => '9',	 'code' => 'entity_auth_method',		    'name' => 'Entity Auth Method',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '34',	 'module_type_key' => 'H7PSJXa3UkcnPuejFe6V5NswcvhdVW9v',	 'module_id' => '9',	 'code' => 'entity_details',			    'name' => 'Entity Details',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '35',	 'module_type_key' => 'pkjtur6N4mykhFXwaYAU6q4z8pUoYYQe',	 'module_id' => '12',	 'code' => 'open_data',	 				    'name' => 'Open Data',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '36',	 'module_type_key' => 'JSPlnhfIG4sj4iCyrumRVhPEtR0NbcY7',	 'module_id' => '12',	 'code' => 'email',	 					    'name' => 'E-mail',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '37',	 'module_type_key' => 'qLI0hVrT7lUPBfduBEp6hSiWyudmf4Ni',	 'module_id' => '12',	 'code' => 'sms',	 					    'name' => 'SMS',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '38',	 'module_type_key' => 'sw1xnnL4n5CCreKCk8A9O6aNQV1T0jfh',	 'module_id' => '12',	 'code' => 'history',	 				    'name' => 'History',				 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '39',	 'module_type_key' => 'pTLvsc731RVK2gl5VaidTe9bor4CfGqp',	 'module_id' => '12',	 'code' => 'empaville',	 				    'name' => 'Empaville',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '40',	 'module_type_key' => 'FranFmKPlWIGfEn9vFgSUUfMxCFTYzhE',	 'module_id' => '12',	 'code' => 'wizard',	 				    'name' => 'Wizard',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '41',	 'module_type_key' => '5VpvSqZZvIYgZGZNgo9SBKdbDish3MmG',	 'module_id' => '3',	 'code' => 'project',	 				    'name' => 'Project',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '42',	 'module_type_key' => 'RPf2zEOtytzNzqt3lDPzEpSZerxgTOhA',	 'module_id' => '12',	 'code' => 'entity_groups',	 			    'name' => 'Entity Groups',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '43',	 'module_type_key' => '3O9wP6wKXbmwS8zRcQ2iZta4nPyO2g8G',	 'module_id' => '4',	 'code' => 'content_subtypes',	 		    'name' => 'Content sub types',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '44',	 'module_type_key' => 'DHFzM3mevOAKukehxZBPyVyuRnPU2yFJ',	 'module_id' => '12',	 'code' => 'sites',	 					    'name' => 'Sites',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '45',	 'module_type_key' => 'k9T5LJfs5GwpcLzZF2tQf0P9CNjrtkH4',	 'module_id' => '3',	 'code' => 'topics',	 				    'name' => 'Topics',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '46',	 'module_type_key' => 'dP3OkZhyv2ekPaSg1qI2M12k4pt6jhrc',	 'module_id' => '3',	 'code' => 'pad_parameters',	 		    'name' => 'Pad Parameters',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '47',	 'module_type_key' => '78mQRXjUs0Sa3oHGtiqh7ostiqqQGkqp',	 'module_id' => '3',	 'code' => 'pad_votes',	 				    'name' => 'Pad Votes',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '48',	 'module_type_key' => 'FnBGKxJP2iG5jXx8yblRCVM1W3pAnTIO',	 'module_id' => '3',	 'code' => 'moderators',	 			    'name' => 'Pad moderators',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '49',	 'module_type_key' => 'dTYIlPR0BZY0AyMdsLz0k2OhFom7Jf0n',	 'module_id' => '3',	 'code' => 'configurations',	 		    'name' => 'PAD Configurations',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '50',	 'module_type_key' => '93YPubNHfGNQLhNYNOnfpsvutW182jTj',	 'module_id' => '3',	 'code' => 'vote_analysis',	 			    'name' => 'PAD Vote Analysis',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '53',	 'module_type_key' => 'EbicedzBiWozRiSktLwbrxCTGy32K0Qj',	 'module_id' => '3',	 'code' => 'topic_status',	 			    'name' => 'Topic Status',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '54',	 'module_type_key' => 'WxW0qkUOYqCcJQqTF7dgx2Hzgr7pVGDH',	 'module_id' => '3',	 'code' => 'topic_status_history',		    'name' => 'Topic Status History',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '55',	 'module_type_key' => 'K1BTj5zdiisXhK5vIa9irPDKSWu4fyvp',	 'module_id' => '9',	 'code' => 'site_use_terms',	 		    'name' => 'Site Use Terms',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '56',	 'module_type_key' => 'NV4Kqv0K70LU2YenIWrOjM6uZNa65jYa',	 'module_id' => '9',	 'code' => 'site_privacy_policy',		    'name' => 'Site Privacy Policy',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '57',	 'module_type_key' => 'H8sdkhLzFObQzYLiTTPPY1WI39tkM3Gr',	 'module_id' => '9',	 'code' => 'site_email_template',		    'name' => 'Site Email Template',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '58',	 'module_type_key' => 'wQ6LifPdernx5ATwMgbkSPnfOZ0ksckB',	 'module_id' => '9',	 'code' => 'site_configurations',		    'name' => 'Site Configurations',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '59',	 'module_type_key' => 'me9Q4uXFN7H5UoVpnLehRIOMLrILAuxY',	 'module_id' => '9',	 'code' => 'site_login_levels',			    'name' => 'Site Login Levels',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '61',	 'module_type_key' => '4bYkthdkQT8SPrxXQvKzwzFeM32lQTaS',	 'module_id' => '11',	 'code' => 'presencial_vote',	 		    'name' => 'Presencial Vote',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '62',	 'module_type_key' => 'sCUqHnk9UkYqXdOc0NjFhKrIN7Vi9BFF',	 'module_id' => '2',	 'code' => 'authorize',	 				    'name' => 'Authorize',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '63',	 'module_type_key' => '0lDCy8vkE6UbD7rxPxBTKbN8e0JteGhE',	 'module_id' => '12',	 'code' => 'entity_groups_users',		    'name' => 'Entity Groups Users',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '64',	 'module_type_key' => 'Z5DUH3PR6NPbrxv06MbkHgefChiqbGB2',	 'module_id' => '12',	 'code' => 'entity_groups_permissions',     'name' => 'Entity Groups Permissions',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '65',	 'module_type_key' => 'vaNDspEooilBeJx9baEw5G2haXa6qngg',	 'module_id' => '9',	 'code' => 'role_permissions',	 		    'name' => 'Role Permissions',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '66',	 'module_type_key' => 'ZhvbqLvMzibstFRIDJYYm9fj8cFWvT8B',	 'module_id' => '4',	 'code' => 'home_page_types_children',      'name' => 'Home Page Type Children',	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '67',	 'module_type_key' => 'aABzanfXenoeem54u6MNXWk5aUXqthId',	 'module_id' => '12',	 'code' => 'translations',	 			    'name' => 'Translations',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '68',	 'module_type_key' => 'cB5P4TAUXi47z22rvBSp8bAXcBNHNnnd',	 'module_id' => '3',	 'code' => 'phase1',	 				    'name' => 'Phase One',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '69',	 'module_type_key' => 'dmPOkSctAFeuGTfWEXdPdZp41FxzgNYq',	 'module_id' => '3',	 'code' => 'phase2',	 				    'name' => 'Phase Two',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '70',	 'module_type_key' => '7SEa4AS5WlZU1jjWEt3IskepQnOKOyfr',	 'module_id' => '3',	 'code' => 'phase3',	 				    'name' => 'Phase Three',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '71',	 'module_type_key' => '7gjUVz8iAofQtcs16m0frO7foCJHjSig',	 'module_id' => '4',	 'code' => 'articles',	 				    'name' => 'Articles',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '72',	 'module_type_key' => 'wUT2ZrX6iL7OhgwyhUzJ2TZopm57pQ3Q',	 'module_id' => '4',	 'code' => 'municipal_faqs',	 		    'name' => 'Municipal FAQs',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '73',	 'module_type_key' => 'yEOxcmXiAyDaW7W69yyJs0OJTWQsIA4W',	 'module_id' => '4',	 'code' => 'faqs',	 					    'name' => 'FAQs',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '74',	 'module_type_key' => 'umOyU5mSXYsBFUzDD7rEFXPKsHcw64vo',	 'module_id' => '3',	 'code' => 'qa',	 					    'name' => 'Question and Answer',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '75',	 'module_type_key' => 'oanNFdkTJAbNgXZ5vE5z4U6y1xBmrzT3',	 'module_id' => '3',	 'code' => 'moderation',	 			    'name' => 'Moderation',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '77',	 'module_type_key' => 'BAw8yUxLkynYXWgax6R3Q8CNaKDpV4bU',	 'module_id' => '8',	 'code' => 'message_all_users',	 		    'name' => 'Send message to all users',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '78',	 'module_type_key' => 'HTsTC05D5366eJO9H0peSLA5vrRvbuvd',	 'module_id' => '3',	 'code' => 'comments',	 				    'name' => 'Comments',	 				        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '79',	 'module_type_key' => 'BZueAWVHYaNceF6RZnGv3s2jmxFxXKjk',	 'module_id' => '3',	 'code' => 'flags',	 					    'name' => 'Flags',	 					        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '80',	 'module_type_key' => 'm4nhPlgWDRgXQZlftE4b4mQtlL9Rbr5O',	 'module_id' => '9',	 'code' => 'technical_evaluation',	 	    'name' => 'Technical Evaluation',	 	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '81',	 'module_type_key' => 'M1wAY5fcUN4ylXljF04HX4yYPmaQbqsk',	 'module_id' => '3',	 'code' => 'notifications',	 			    'name' => 'PAD Notifications',			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '82',	 'module_type_key' => 'lwccS5QB6qtrUa1YESEWOwNCJsXqMqJR',	 'module_id' => '9',	 'code' => 'newsletter_subscriptions',      'name' => 'Newsletter Subscriptions',	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '83',	 'module_type_key' => 'rl8hgycrjF94M3MkBNv68rJKB3ShJEAk',	 'module_id' => '12',	 'code' => 'all_messages',	 			    'name' => 'Entity Messages',	 		        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '84',	 'module_type_key' => 'Xg1rukgPLdN5CR6fLWu5pmsa2hBLFw7I',	 'module_id' => '3',	 'code' => 'cooperators',	 			    'name' => 'Cooperators',	 			        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '85',	 'module_type_key' => '3k8YKWhcDUVfWUvbnVj0s8L9Hjz3gN',	 	 'module_id' => '4',	 'code' => 'dynamic_be_menu',	 		    'name' => 'Dynamic Back Office Menu',	        'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '86',	 'module_type_key' => '5REs3lEYBZLD1d4cBHgAp1YaJIwpFPVX',    'module_id' => '4',	 'code' => 'personal_dynamic_be_menu',	    'name' => 'Personal Dynamic Back Office Menu',	'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '87',	 'module_type_key' => 'eP3lK09LlDCLOB4HoYQdOEZQHS6Mx5d9',    'module_id' => '12',	 'code' => 'short_links',	                'name' => 'Short Links',	                    'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL)
        );
        DB::table('module_types')->insert($modulesTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('module_types');
    }
}
