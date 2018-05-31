<?php

use Illuminate\Database\Seeder;
use App\FlagType;
use App\FlagTypeTranslation;
use Illuminate\Support\Facades\DB;

class FlagTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->flagTypes();

        DB::commit();
    }

    private function flagTypes() {
        $flagTypes = array(
            array(
                "id" 					 	=> 1,
                "code" 					 	=> "topics",
                "translations"              => array(
                    array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "pt",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "cz",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "it",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "de",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "en",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "fr",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Topics",
                        "language_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "code" 					 	=> "posts",
                "translations"              => array(
                    array(
                        "title"              => "Posts",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "pt",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "cz",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "it",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "de",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "en",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "fr",
                    ),array(
                        "title"              => "Topics",
                        "description"       => "Flag Types for Posts",
                        "language_code"         => "es",
                    )
                )
            )
        );

        foreach ($flagTypes as $flagType) {
            $translations = $flagType["translations"];
            unset($flagType["translations"]);

            FlagType::firstOrCreate($flagType);

            foreach ($translations as $translation) {
                $translation = array_merge(["flag_type_id"=>$flagType ["id"]],$translation);
                FlagTypeTranslation::firstOrCreate($translation);
            }
        }
    }
}
