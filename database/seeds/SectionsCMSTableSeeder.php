<?php

use Illuminate\Database\Seeder;
use App\Section;
use App\SectionType;
use App\SectionTypeTranslation;

class SectionsCMSTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->sectionTypes();

        DB::commit();
    }

    private function sectionTypes() {
        $sectionTypes = array(
            array(
                "id" 					 	=> 1,
                "section_type_key" 	        => "3tHJ5TJ369bLBfeUHvfOj65SggZOLxZF",
                "code" 					 	=> "contentSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Conteúdo",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Content",
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "section_type_key" 	        => "V85jrSv1tgwN1zue5dVbHL9XiDlKsIIm",
                "code" 					 	=> "singleImageSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Imagem Única",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Single Image",
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "section_type_key" 	        => "MxjHVy3pqjWaeVzmSGwyfGMQhNhc287u",
                "code" 					 	=> "multipleImagesSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Imagens",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Images",
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "section_type_key" 	        => "H6Jw4cz1nJpziHqG7hW1qejXftBitsSB",
                "code" 					 	=> "slideShowSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Slideshow",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Slideshow",
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "section_type_key" 	        => "SPPtlhEaGZc8WMJ9vqhhgP8m91NIRCVB",
                "code" 					 	=> "singleFileSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Ficheiro",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "File",
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "section_type_key" 	        => "2Qli2FadzHRVNeHrnYYdycffWSb24pfN",
                "code" 					 	=> "multipleFilesSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Ficheiros",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Files",
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "section_type_key" 	        => "HgvvJBSlbxgMO58UZR4dcfpD8yuSOLgq",
                "code" 					 	=> "externalVideoSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Vídeo Externo",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "External Video",
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "section_type_key" 	        => "wGcFtUfu6SkXbryaFifsqMRXESvvFi2K",
                "code" 					 	=> "internalVideoSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Vídeo",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Video",
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "section_type_key" 	        => "a9q1buDVfUs8zGaBGTSPdYqxWPdSgW2s",
                "code" 					 	=> "padsList",
                "translatable"              => 0,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Lista de Pads",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Pads List",
                    )
                )
            ),array(
                "id" 					 	=> 10,
                "section_type_key" 	        => "iJoPZRWvnSKBf2LrQWHLaKQEYmzKxrlL",
                "code" 					 	=> "headingSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Título",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Heading",
                    )
                )
            ),array(
                "id" 					 	=> 11,
                "section_type_key" 	        => "FhASZJLwi1w1EFP88zHpUrYETcAxhfMB",
                "code" 					 	=> "bannerSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Banner",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Banner",
                    )
                )
            ),array(
                "id" 					 	=> 12,
                "section_type_key" 	        => "tqGf8D11LRRRxITmcoxaCiWUtQYgXkRa",
                "code" 					 	=> "contentsList",
                "translatable"              => 0,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Lista de Conteudos",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Contents List",
                    )
                )
            ),array(
                "id" 					 	=> 13,
                "section_type_key" 	        => "j8XNsTgK8t0BtCSIJ89mRrgE0tAEpGOA",
                "code" 					 	=> "linkedBanner",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Banner com Ligação",
                    ),array(
                        "language_code" 	=> "it",
                        "value"             => "Linked Banner",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Linked Banner",
                    )
                )
            ),array(
                "id" 					 	=> 14,
                "section_type_key" 	        => "AHHiW6ZsE46iHQsSvShMV2jS9q0Z6yFO",
                "code" 					 	=> "buttonSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Botão",
                    ),array(
                        "language_code" 	=> "de",
                        "value"             => "Button",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Button",
                    )
                )
            ),array(
                "id" 					 	=> 15,
                "section_type_key" 	        => "6xFvQGKxMq6wxzC4XnbqCdu5SgJCS0lk",
                "code" 					 	=> "gathering_item",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "cz",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "it",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "de",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "fr",
                        "value"             => "Encontro Item",
                    ),array(
                        "language_code" 	=> "es",
                        "value"             => "Encontro Item",
                    )
                )
            ),array(
                "id" 					 	=> 16,
                "section_type_key" 	        => "gajIUuk958oMz8BtxDtXtP3nTyqxvkdS",
                "code" 					 	=> "dateSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "cz",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "it",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "de",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "fr",
                        "value"             => "Data",
                    ),array(
                        "language_code" 	=> "es",
                        "value"             => "Data",
                    )
                )
            ),array(
                "id" 					 	=> 17,
                "section_type_key" 	        => "E9KmOvT1yAxJQAHtKGVsl4sKGwBiiEZ1",
                "code" 					 	=> "homepageItemSection",
                "translatable"              => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "value"             => "Homepage Item",
                    ),array(
                        "language_code" 	=> "en",
                        "value"             => "Homepage Item",
                    )
                )
            )
        );

        foreach ($sectionTypes as $sectionType) {
            $translations = $sectionType["translations"];
            unset($sectionType["translations"]);

            SectionType::firstOrCreate($sectionType);

            foreach ($translations as $translation) {
                $translation = array_merge(["section_type_id"=>$sectionType ["id"]],$translation);
                SectionTypeTranslation::firstOrCreate($translation);
            }
        }
    }
}
