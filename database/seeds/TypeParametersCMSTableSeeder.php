<?php

use Illuminate\Database\Seeder;
use App\SectionTypeParameter;
use App\SectionTypeParameterTranslation;

class TypeParametersCMSTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->sectionTypeParameters();

        DB::commit();
    }

    private function sectionTypeParameters() {
        $sectionTypeParameters = array(
            array(
                "id" 					 	    => 1,
                "section_type_parameter_key" 	=> "ebOQbYmM5Zjci17aCtcoddZBqlXX0Xrp",
                "code" 					 	    => "textParameter",
                "type_code"                     => "text",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Texto",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Text",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 2,
                "section_type_parameter_key" 	=> "9pFe7216kSZCGMF1bJIZauCos2fwhKEr",
                "code" 					 	    => "textAreaSection",
                "type_code"                     => "textarea",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Área de Texto",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Text Area",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 3,
                "section_type_parameter_key" 	=> "S0evTLPWzGh6DuBC4ObGYzUyysSwi4Ru",
                "code" 					 	    => "htmlTextArea",
                "type_code"                     => "html",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Área de Texto HTML",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "HTML Text Area",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 4,
                "section_type_parameter_key" 	=> "85n8L7GGyJMnUCW3G8SSUkDc9iq696q3",
                "code" 					 	    => "imagesSingleSection",
                "type_code"                     => "images_single",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Imagem",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Image",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 5,
                "section_type_parameter_key" 	=> "uOzZnn3T6rgKo2zmBZSxeDNU3nM4doib",
                "code" 					 	    => "multipleImages",
                "type_code"                     => "images_multiple",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Imagens",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Images",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 6,
                "section_type_parameter_key" 	=> "HdQwEIlGUlr28ganr35ASD7Nxmf9sRHh",
                "code" 					 	    => "singleFiles",
                "type_code"                     => "files_single",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Ficheiro",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "File",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 7,
                "section_type_parameter_key" 	=> "FbQHXJC9V9tgBN9dCzOu2MY041PZ7LzV",
                "code" 					 	    => "multipleFiles",
                "type_code"                     => "files_multiple",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Ficheiros",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Files",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 8,
                "section_type_parameter_key" 	=> "ivDFPdSS6FdBMNykqJXxqvBmC0kJFAcO",
                "code" 					 	    => "number",
                "type_code"                     => "number",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Número",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Number",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 9,
                "section_type_parameter_key" 	=> "fogUVpUGBppSXaaXZJCfCMyTIwO9qZiN",
                "code" 					 	    => "color",
                "type_code"                     => "color",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Código de Cor",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Color Code",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 10,
                "section_type_parameter_key" 	=> "wQQ7nxfM6W0oK06xb9LvCmoiuwqS1lvG",
                "code" 					 	    => "date",
                "type_code"                     => "date",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Data",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Date",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 11,
                "section_type_parameter_key" 	=> "cLqUTvMwoNcrJngIW6UafrgQ9JN3QbCP",
                "code" 					 	    => "time",
                "type_code"                     => "time",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Tempo",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Time",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 12,
                "section_type_parameter_key" 	=> "9Ek7NtrNYKnukHTvfguv3WNhwDOobGc2",
                "code" 					 	    => "url",
                "type_code"                     => "url",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Ligação",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Link",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 13,
                "section_type_parameter_key" 	=> "rFXL6xfO8DJi6hrvdFRv1ASTLNtWnte6",
                "code" 					 	    => "video",
                "type_code"                     => "video",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Vídeo",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Video",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 14,
                "section_type_parameter_key" 	=> "iiiFpBBSxt4lWjLkGYsiBHRLUrCLP7uU",
                "code" 					 	    => "cbKey",
                "type_code"                     => "select_cb_key",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Nome PAD",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "PAD's name",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 15,
                "section_type_parameter_key" 	=> "QtOLtB0og8q331uhnJVFe6L0ueB3Je41",
                "code" 					 	    => "numberOfTopics",
                "type_code"                     => "numberOfTopics",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Número de tópicos para listar",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Number of topics to list",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 16,
                "section_type_parameter_key" 	=> "e5JMcaBaavExTsofzcgvrEHGDcNGYnir",
                "code" 					 	    => "topicsSortOrder",
                "type_code"                     => "topicsSortOrder",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Ordem da listagem dos tópicos",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "List sort order",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 17,
                "section_type_parameter_key" 	=> "dGxrHAdlgFtx9NLdRXLCa4N1ZAvAPjzp",
                "code" 					 	    => "cbType",
                "type_code"                     => "cbType",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Tipo de PAD",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "PAD Type",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 18,
                "section_type_parameter_key" 	=> "1zv0v2NelUsqaVoXvMN0whBSiAHpFoNT",
                "code" 					 	    => "headingNumber",
                "type_code"                     => "heading_number",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Tamanho do Título",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Title Size",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 19,
                "section_type_parameter_key" 	=> "6LIvuJcZzD2htghyZS50kNkxcpg1p1XY",
                "code" 					 	    => "alignment",
                "type_code"                     => "alignment",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Alinhamento",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Alignment",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 20,
                "section_type_parameter_key" 	=> "C8Fy11XLujytndzXTly657uHpQZUr2mo",
                "code" 					 	    => "contentType",
                "type_code"                     => "contentType",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Tipo de conteudo",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Content Type",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 21,
                "section_type_parameter_key" 	=> "VMPHVPRWCC80m3GKT1X0bYhHObN5Wdlg",
                "code" 					 	    => "heading1",
                "type_code"                     => "heading_1",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 1",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 1",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 22,
                "section_type_parameter_key" 	=> "FDFy5AEXL7A4KiYOoatQBBHOm0U9bLMe",
                "code" 					 	    => "heading2",
                "type_code"                     => "heading_2",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 2",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 2",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 23,
                "section_type_parameter_key" 	=> "fDK5RfOqJ3opF8b9tv6gr7kyBoFjthOG",
                "code" 					 	    => "heading3",
                "type_code"                     => "heading_3",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 3",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 3",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 24,
                "section_type_parameter_key" 	=> "YKdat4aPCjyQ6MhRh4TF5WSfXm9M8kZh",
                "code" 					 	    => "heading4",
                "type_code"                     => "heading_4",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 4",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 4",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 25,
                "section_type_parameter_key" 	=> "6SqZlbGr4FRRmkArmiyEnf72Bgxl0rKB",
                "code" 					 	    => "heading5",
                "type_code"                     => "heading_5",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 5",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 5",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 26,
                "section_type_parameter_key" 	=> "nFab5OlxsedZ8JtIzQNeEIxuglkX8GE0",
                "code" 					 	    => "heading6",
                "type_code"                     => "heading_6",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Título 6",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Heading 6",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 27,
                "section_type_parameter_key" 	=> "a0cArxzRCRqX4OAysS5eSa7N5taJF3LJ",
                "code" 					 	    => "buttonText",
                "type_code"                     => "text",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Texto do botão",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "it",
                        "name"                  => "Button's Text",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Button's Text",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 28,
                "section_type_parameter_key" 	=> "p1VD1uiQPTdJFfaPvRe61EU7xFH805wX",
                "code" 					 	    => "buttonColor",
                "type_code"                     => "color",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Cor do botão",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "it",
                        "name"                  => "Button's Color",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Button's Color",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 29,
                "section_type_parameter_key" 	=> "8301i3C4RKGwkfRUqId3BAQQeeG6R7QT",
                "code" 					 	    => "video",
                "type_code"                     => "video",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Vídeo",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "de",
                        "name"                  => "Video",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Video",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 30,
                "section_type_parameter_key" 	=> "MfUVJyW9AGuiRYPV21ggMG6K7yO56xBv",
                "code" 					 	    => "video_title",
                "type_code"                     => "text",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Titulo do vídeo",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "de",
                        "name"                  => "Video title",
                        "description"           => "",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Video title",
                        "description"           => "",
                    )
                )
            ),array(
                "id" 					 	    => 31,
                "section_type_parameter_key" 	=> "2jPQCciX46aZdtPNGplI2QkTFGL9Nmm4",
                "code" 					 	    => "hour",
                "type_code"                     => "text",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Hora do Encontro",
                        "description"           => "Hora do Encontro",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Hora do Encontro",
                        "description"           => "Hora do Encontro",
                    )
                )
            ),array(
                "id" 					 	    => 32,
                "section_type_parameter_key" 	=> "9yiXl1jqHvbkF1yKfM2oZODIK2JqlhuA",
                "code" 					 	    => "description_contacts",
                "type_code"                     => "html",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Descrição / Contactos",
                        "description"           => "Descrição / Contactos",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Descrição / Contactos",
                        "description"           => "Descrição / Contactos",
                    )
                )
            ),array(
                "id" 					 	    => 33,
                "section_type_parameter_key" 	=> "Dazf0YYPw5wDRCn6av54hrqieXnsWH3B",
                "code" 					 	    => "location",
                "type_code"                     => "text",
                "translations" 			 	    => array(
                    array(
                        "language_code" 	    => "pt",
                        "name"                  => "Localização",
                        "description"           => "Localização",
                    ),array(
                        "language_code" 	    => "en",
                        "name"                  => "Localização",
                        "description"           => "Localização",
                    )
                )
            )
        );

        foreach ($sectionTypeParameters as $sectionTypeParameter) {
            $translations = $sectionTypeParameter["translations"];
            unset($sectionTypeParameter["translations"]);

            SectionTypeParameter::firstOrCreate($sectionTypeParameter);

            foreach ($translations as $translation) {
                $translation = array_merge(["section_type_parameter_id"=>$sectionTypeParameter ["id"]],$translation);
                SectionTypeParameterTranslation::firstOrCreate($translation);
            }
        }
    }
}
