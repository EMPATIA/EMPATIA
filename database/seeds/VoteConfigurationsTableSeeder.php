<?php

use Illuminate\Database\Seeder;
use App\VoteConfiguration;
use App\VoteConfigurationTranslation;
use Illuminate\Support\Facades\DB;

class VoteConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->voteConfigurations();

        DB::commit();
    }

    private function voteConfigurations() {
        $voteConfigurations = array(
            array(
                "id" 					 	=> 1,
                "vote_configuration_key" 	=> "UbMN93hUiEMuOK0lrcq22637kGix2lVu",
                "code" 					 	=> "show_total_votes",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Mostrar total de votos",
                        "description" => null,
                    ),array(
                        "language_code" => "it",
                        "name" => "Mostra voti totali",
                        "description" => null,
                    ),array(
                        "language_code" => "de",
                        "name" => "Zeigen Gesamtstimmen",
                        "description" => null,
                    ),array(
                        "language_code" => "en",
                        "name" => "Show total votes",
                        "description" => null,
                    ),array(
                        "language_code" => "cz",
                        "name" => "Show total votes",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "vote_configuration_key" 	=> "mmUjgKoB7yarps9MSk86ELRBdtq8Ry6m",
                "code" 					 	=> "vote_in_list",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Voto em lista",
                        "description" => null,
                    ),array(
                        "language_code" => "it",
                        "name" => " voto in lista",
                        "description" => null,
                    ),array(
                        "language_code" => "de",
                        "name" => "Abstimmung in der Liste",
                        "description" => null,
                    ),array(
                        "language_code" => "en",
                        "name" => "Vote in list",
                        "description" => null,
                    ),array(
                        "language_code" => "cz",
                        "name" => "Vote in list",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "vote_configuration_key" 	=> "pEJqOYegoKxEl159ysT0r9FY56I9fMtu",
                "code" 					 	=> "boolean_requires_confirm",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "it",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "de",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "en",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    ),array(
                        "language_code" => "es",
                        "name" => "Requires votes confirmation",
                        "description" => "Requires votes confirmation",
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "vote_configuration_key" 	=> "rMW18CgIStmHwZnL4m8gdJTEHcFFRCX0",
                "code" 					 	=> "boolean_show_confirmation_view",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "it",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "de",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "en",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    ),array(
                        "language_code" => "es",
                        "name" => "Show vote confirmation view",
                        "description" => "Show vote confirmation view",
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "vote_configuration_key" 	=> "uDVL438TPZS0qHVeivILPub0mUhybYAA",
                "code" 					 	=> "allow_unsubmit_votes",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "it",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "de",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "en",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    ),array(
                        "language_code" => "es",
                        "name" => "Allow to re-open voting",
                        "description" => "Allow to re-open voting",
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "vote_configuration_key" 	=> "FD1pPOlqgRY5rdbT1ChP0Ech8Uf8U5Df",
                "code" 					 	=> "allow_in_person_registration",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "it",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "de",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "en",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    ),array(
                        "language_code" => "es",
                        "name" => "Allow in Person Registration",
                        "description" => "Allow in Person Registration",
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "vote_configuration_key" 	=> "Pr8o5fDwvbiq0VpZO8QrKDLTIcCco9ZP",
                "code" 					 	=> "allow_in_person_voting",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "it",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "de",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "en",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    ),array(
                        "language_code" => "es",
                        "name" => "Allow in Person Voting",
                        "description" => "Allow in Person Voting",
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "vote_configuration_key" 	=> "xlSplwwC9tZms259oNyu7KRcEuFtKqoB",
                "code" 					 	=> "show_vote_results",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "it",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "de",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "en",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    ),array(
                        "language_code" => "es",
                        "name" => "Show Vote Results (after vote end)",
                        "description" => "Show Vote Results (after vote end)",
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "vote_configuration_key" 	=> "BHF4v3YSBuX4yBvhWH1l16tQvURqPzgj",
                "code" 					 	=> "vote_in_comments",
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "cz",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "it",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "de",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "en",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "fr",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    ),array(
                        "language_code" => "es",
                        "name" => "Vote in Comments",
                        "description" => "Vote in Comments",
                    )
                )
            ),
        );

        foreach ($voteConfigurations as $voteConfiguration) {
            $translations = $voteConfiguration["translations"];
            unset($voteConfiguration["translations"]);

            VoteConfiguration::firstOrCreate($voteConfiguration);

            foreach ($translations as $translation) {
                $translation = array_merge(["vote_configuration_id"=>$voteConfiguration["id"]],$translation);
                VoteConfigurationTranslation::firstOrCreate($translation);
            }
        }
    }            
}
