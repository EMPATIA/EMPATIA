<?php

use Illuminate\Database\Seeder;
use App\ParameterType;
use App\ParameterUserType;
use App\ParameterUserTypeTranslation;
use Illuminate\Support\Facades\DB;

class ParametersTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->parameterTypes();
        $this->parameterUserTypes();

        DB::commit();
    }

    private function parameterTypes() {
        $parameterTypes = array(
            array(
                "id" 					 	=> 1,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Text",
                "code"                      => "text",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 2,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Radio Buttons",
                "code"                      => "radio_buttons",
                "options"                   => 1,
            ),array(
                "id" 					 	=> 3,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Check Box",
                "code"                      => "check_box",
                "options"                   => 1,
            ),array(
                "id" 					 	=> 4,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Text Area",
                "code"                      => "text_area",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 5,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Numeric",
                "code"                      => "numeric",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 6,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Google Maps",
                "code"                      => "google_maps",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 7,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Dropdown",
                "code"                      => "dropdown",
                "options"                   => 1,
            ),array(
                "id" 					 	=> 8,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Image Map",
                "code"                      => "image_map",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 9,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Coin",
                "code"                      => "coin",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 10,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Email",
                "code"                      => "email",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 11,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Category",
                "code"                      => "category",
                "options"                   => 1,
            ),array(
                "id" 					 	=> 12,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Topic passed phases",
                "code"                      => "topic_checkpoints",
                "options"                   => 1,
            ),array(
                "id" 					 	=> 13,
                "param_add_fields_id" 	    => 0,
                "name" 					 	=> "Topic Chekpoints Decider",
                "code"                      => "topic_checkpoints_boolean",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 14,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Going to Pass the phase",
                "code"                      => "going_to_pass",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 15,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Topic Chekpoint Phase",
                "code"                      => "topic_checkpoint_phase",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 16,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Mobile Number",
                "code"                      => "mobile",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 17,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Propostas associadas",
                "code"                      => "associated_topics",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 18,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Parâmetros associados",
                "code"                      => "associated_parameters",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 19,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Data",
                "code"                      => "date",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 20,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Hora",
                "code"                      => "hour",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 21,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Ficheiros",
                "code"                      => "files",
                "options"                   => 0,
            ),array(
                "id" 					 	=> 22,
                "param_add_fields_id" 	    => null,
                "name" 					 	=> "Imagens",
                "code"                      => "images",
                "options"                   => 0,
            )
        );

        foreach ($parameterTypes as $parameterType) {

            ParameterType::firstOrCreate($parameterType);
        }
    }

    private function parameterUserTypes() {
        $parameterUserTypes = array(
            array(
                "id" 					 	=> 1,
                "parameter_user_type_key" 	=> "xIQmzBdPd2hxU7cC3yzwNpXk3SC6VDbe",
                "code" 					 	=> "birthdate",
                "parameter_type_id"         => 12,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Data de nascimento",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "parameter_user_type_key" 	=> "tUByUQvyd1GVYPQgMGnJ1weI6iQ7oDB5",
                "code" 					 	=> "",
                "parameter_type_id"         => 1,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 1,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Morada",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "parameter_user_type_key" 	=> "OQQKjwfjVXzYFvwK6A1BkxZh2Ji2Vytm",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Freguesia ",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "parameter_user_type_key" 	=> "cWeiGKdbN0VK55UcVYq8ydvTbA0yzUu4",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Código Postal",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "parameter_user_type_key" 	=> "vVGJ54BzB0urOdWoGUo4bCaV3i3CmZuW",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Concelho",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "parameter_user_type_key" 	=> "I2QP2eLMRp3zbL4i2plp0nw77S46fahaas",
                "code" 					 	=> "",
                "parameter_type_id"         => 13,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 1,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Documento de identificação",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "parameter_user_type_key" 	=> "sappnidATABHb2XM1Aa1Mkng1FefbUow",
                "code" 					 	=> null,
                "parameter_type_id"         => 2,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Género",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "parameter_user_type_key" 	=> "7epGjHEV5wXPPKUrLXncMXZEKhGJfR8y",
                "code" 					 	=> null,
                "parameter_type_id"         => 2,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Estado civil",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "parameter_user_type_key" 	=> "ReixtM9i3hYISXfCG2Q5zJdnwWq3iRIg",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Profissão",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 10,
                "parameter_user_type_key" 	=> "rHAwjATOlIgRlo4PKYIeZ2vFPuDphL0l",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Habilitações literárias",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 11,
                "parameter_user_type_key" 	=> "zYYD8P8MxCegBzJ3YMpFDpBhNqGPV4ld",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 4,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Concelho de residência",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 12,
                "parameter_user_type_key" 	=> "3yaTMIUxdWHgUyq9WN1HhQQfBPycYox6",
                "code" 					 	=> null,
                "parameter_type_id"         => 3,
                "entity_id"                 => 4,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Os tópicos do OPP que me interessam mais, para discutir e (eventualmente) apresentar propostas, são:",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 13,
                "parameter_user_type_key" 	=> "JOxZWMExIM9boHIHrJ2xfXFXABmTpNfB",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 12,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Sexo",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 14,
                "parameter_user_type_key" 	=> "riiS956vBb972ilGgUJEjAqszd0hlRrO",
                "code" 					 	=> "birth_year",
                "parameter_type_id"         => 7,
                "entity_id"                 => 12,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Ano de nascimento",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 15,
                "parameter_user_type_key" 	=> "ETJ2N4BGceumyzRYSUL2L5WUUKLKgGF0",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 12,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Habilitações Académicas",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 16,
                "parameter_user_type_key" 	=> "dHJe5wZvqf8Db07rOtNGW6klV4bNpU8Y",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 11,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "de",
                        "name" => "Geschlecht",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Gender",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 17,
                "parameter_user_type_key" 	=> "LjAn6HTbps6XRIgCN669WUbRTLpdPNbH",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 11,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "de",
                        "name" => " Höchster Bildungsabschluss",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Highest Education",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 18,
                "parameter_user_type_key" 	=> "3uKTuzX6YFywQFqGm6ESOHmwGsejr71Y",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 11,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => 1,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "de",
                        "name" => "Alter",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Age",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 19,
                "parameter_user_type_key" 	=> "ZaalJqpyRqxu5K7aP9seH7fd8O6AaPbT",
                "code" 					 	=> "",
                "parameter_type_id"         => 7,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Género",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Gender",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 20,
                "parameter_user_type_key" 	=> "dYEh6mesDJgCpZYT1QEjBA4nFjcecfQz",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Educação (nível de escolaridade que completou)",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Education (your education level)",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 21,
                "parameter_user_type_key" 	=> "ZETVkTvjLQARSPlzw2EKwxQrAOOe9RFe",
                "code" 					 	=> "",
                "parameter_type_id"         => 7,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Idade",
                        "description" => null,
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Age",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 22,
                "parameter_user_type_key" 	=> "tX99V7W36yTPEoB2C4PSLwc3nV6Nizxy",
                "code" 					 	=> "",
                "parameter_type_id"         => 7,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Freguesia de residência",
                        "description" => "",
                    ),array(
                        "language_code" 	=> "en",
                        "name" => "Neighborhood",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 23,
                "parameter_user_type_key" 	=> "72V8xUOUQEQayZ0bxE0MuDprqlKlqgch",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Se respondeu 'Outra' na questão anterior por favor...",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 24,
                "parameter_user_type_key" 	=> "CLPgWtY9Uct5d8AjIEengGxH12HgNFsc",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 5,
                "mandatory"                 => 1,
                "parameter_unique"          => 1,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Número de identificação fiscal",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 25,
                "parameter_user_type_key" 	=> "UUNgMrQ8glWWnfUTvSemBWCYDf1yQs34",
                "code" 					 	=> null,
                "parameter_type_id"         => 5,
                "entity_id"                 => 5,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Ano de nascimento",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 26,
                "parameter_user_type_key" 	=> "Lys329Iyxxp9ROw1S147qmi6b9K7ye1i",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 5,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Sexo",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 27,
                "parameter_user_type_key" 	=> "Hl835eJFy5Xia5Id8JlJGxl49Ead1W4f",
                "code" 					 	=> null,
                "parameter_type_id"         => 7,
                "entity_id"                 => 5,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Freguesia / Local",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 28,
                "parameter_user_type_key" 	=> "mf8wI4eagVN7x1kFz7g7JS6SxjHYcjMq",
                "code" 					 	=> "postal",
                "parameter_type_id"         => 5,
                "entity_id"                 => 12,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Código Postal (4 digitos)",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 29,
                "parameter_user_type_key" 	=> "hfygB3qoFAGNsbMOffyEVqYhQmxIJsYn",
                "code" 					 	=> null,
                "parameter_type_id"         => 1,
                "entity_id"                 => 11,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "de",
                        "name" => "Postleitzahl",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 30,
                "parameter_user_type_key" 	=> "Rhzw2FJaUOtoDHrsBFPqqg7qgDSCDv8l",
                "code" 					 	=> null,
                "parameter_type_id"         => 3,
                "entity_id"                 => 5,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Junta de Freguesia",
                        "description" => null,
                    )
                )
            ),array(
                "id" 					 	=> 31,
                "parameter_user_type_key" 	=> "PGYS8yDf0FOR7ck6jwujDdrN7C9AldSP",
                "code" 					 	=> "",
                "parameter_type_id"         => 7,
                "entity_id"                 => 1,
                "mandatory"                 => 1,
                "parameter_unique"          => 0,
                "anonymizable"              => 1,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Ligação ao Concelho",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 32,
                "parameter_user_type_key" 	=> "2W5FUw2YLjDMOjPaKEh6le0zXHg5CC0c",
                "code" 					 	=> "",
                "parameter_type_id"         => 14,
                "entity_id"                 => 11,
                "mandatory"                 => 0,
                "parameter_unique"          => 1,
                "anonymizable"              => 1,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "de",
                        "name" => "Handynummer",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 33,
                "parameter_user_type_key" 	=> "LfTOHpzQJp09qG1JilZl44oNfqjTognA",
                "code" 					 	=> "cc",
                "parameter_type_id"         => 1,
                "entity_id"                 => 12,
                "mandatory"                 => 1,
                "parameter_unique"          => 1,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Cartão de Cidadão",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 34,
                "parameter_user_type_key" 	=> "opPzQa9hfuH7zql9eqgs2aL1QkpnIRZM",
                "code" 					 	=> "",
                "parameter_type_id"         => 14,
                "entity_id"                 => 12,
                "mandatory"                 => 0,
                "parameter_unique"          => 1,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Número de Telemóvel",
                        "description" => "",
                    )
                )
            ),array(
                "id" 					 	=> 35,
                "parameter_user_type_key" 	=> "uJFkME0c20OET5Dkryp2DLzpI0nIjOgR",
                "code" 					 	=> "birthday",
                "parameter_type_id"         => 12,
                "entity_id"                 => 12,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Data de nascimento",
                        "description" => "Data de nascimento",
                    )
                )
            ),array(
                "id" 					 	=> 36,
                "parameter_user_type_key" 	=> "DqMrpsKXgtYlJxQcedDf5kV2j2ViXlrP",
                "code" 					 	=> "",
                "parameter_type_id"         => 6,
                "entity_id"                 => 3,
                "mandatory"                 => 0,
                "parameter_unique"          => 0,
                "anonymizable"              => 0,
                "level_parameter_id"        => null,
                "translations" 			 	=> array(
                    array(
                        "language_code" 	=> "pt",
                        "name" => "Localização",
                        "description" => "",
                    )
                )
            )
        );

        foreach ($parameterUserTypes as $parameterUserType) {
            $translations = $parameterUserType["translations"];
            unset($parameterUserType["translations"]);

            ParameterUserType::firstOrCreate($parameterUserType);

            foreach ($translations as $translation) {
                $translation = array_merge(["parameter_user_type_id"=>$parameterUserType["id"]],$translation);
                ParameterUserTypeTranslation::firstOrCreate($translation);
            }
        }
    }
}
