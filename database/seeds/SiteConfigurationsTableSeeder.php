<?php

use Illuminate\Database\Seeder;
use App\SiteConfGroup;
use App\SiteConfGroupTranslation;
use App\SiteConf;
use App\SiteConfTranslation;
use Illuminate\Support\Facades\DB;

class SiteConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->siteConfigurationsGroup();
        $this->siteConfigurations();

        DB::commit();
    }

    private function siteConfigurationsGroup() {
        $siteConfigurationsGroup = array(
            array(
                "id" 					 	=> 1,
                "site_conf_group_key" 	    => "aEu1c9dUrAjh1IykyUuG0xnQgpaHPvnU",
                "code" 					 	=> "facebook",
                "translations"              => array(
                    array(
                        "name"              => "Facebook",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Facebook",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 2,
                "site_conf_group_key" 	    => "WVBHDX2s1CxU7bFIDr6ncxnPMernLV8w",
                "code" 					 	=> "google_analytics",
                "translations"              => array(
                    array(
                        "name"              => "Google Analytics",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Google Analytics",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 3,
                "site_conf_group_key" 	    => "mfLBXiIjPxEZW4xJOzovpgShVq71BNjh",
                "code" 					 	=> "google_maps",
                "translations"              => array(
                    array(
                        "name"              => "Mapas Google",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Google Maps",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 4,
                "site_conf_group_key" 	    => "Vq10pdF22EDme6DCWPnQrOEeidJt3HhG",
                "code" 					 	=> "google_recaptcha",
                "translations"              => array(
                    array(
                        "name"              => "Google Recaptcha",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Google Recaptcha",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 5,
                "site_conf_group_key" 	    => "HEyfV2cyQPvXldYR35y8xKoipuZli4eq",
                "code" 					 	=> "optionsReedirect",
                "translations"              => array(
                    array(
                        "name"              => "Opções de redireccionamento",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Options Reedirect",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 6,
                "site_conf_group_key" 	    => "MRUPpccvjVmVgBXYjTHWQGDYJeLL19N6",
                "code" 					 	=> "homepage",
                "translations"              => array(
                    array(
                        "name"              => "Configurações  Homepage",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Homepage Configurations",
                        "description"       => "",
                        "lang_code"         => "en",
                    )
                )
            ),array(
                "id" 					 	=> 7,
                "site_conf_group_key" 	    => "p1TViLyj4dvyVcNfS2zxToQMPNWmWEpc",
                "code" 					 	=> "piwik_analytics",
                "translations"              => array(
                    array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "cz",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "it",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "de",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "en",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "fr",
                    ),array(
                        "name"              => "Piwik Analytics",
                        "description"       => "Piwik Analytics",
                        "lang_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 8,
                "site_conf_group_key" 	    => "aFTDb8mC6RKotdfeFCNjqP0Wls9AWQsV",
                "code" 					 	=> "libertriumAuth",
                "translations"              => array(
                    array(
                        "name"              => "Autenticação Libertrium",
                        "description"       => "Autenticação Libertrium",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 9,
                "site_conf_group_key" 	    => "CgubIL3ZnyPAECQ6XAE0IKTtJKcYBcbj",
                "code" 					 	=> "other_configurations",
                "translations"              => array(
                    array(
                        "name"              => "Outras Configurações",
                        "description"       => "",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "cz",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "it",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "de",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "en",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "fr",
                    ),array(
                        "name"              => "Other Configurations",
                        "description"       => "",
                        "lang_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 10,
                "site_conf_group_key" 	    => "LcZ5wa6z0ZIdznBiGPXpGp6ulzl9SIbU",
                "code" 					 	=> "auth",
                "translations"              => array(
                    array(
                        "name"              => "Autenticação",
                        "description"       => "Autenticação",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 11,
                "site_conf_group_key" 	    => "7Zk0N3McTI5L8dd1hn4AePpF3MjSNGiy",
                "code" 					 	=> "sms",
                "translations"              => array(
                    array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "cz",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "it",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "de",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "en",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "fr",
                    ),array(
                        "name"              => "Sms Configurations",
                        "description"       => "Sms Configurations",
                        "lang_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 12,
                "site_conf_group_key" 	    => "QX4BGVCs5WyCTaQ4S8sbt53UQdmnbNdu",
                "code" 					 	=> "open_graph_protocol",
                "translations"              => array(
                    array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "cz",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "it",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "de",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "en",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "fr",
                    ),array(
                        "name"              => "Open Graph protocol - default values",
                        "description"       => "Open Graph protocol - default values",
                        "lang_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 13,
                "site_conf_group_key" 	    => "15ty89lqMc1DM0vBHo8BCsqpSln5PP37",
                "code" 					 	=> "website_metadata",
                "translations"              => array(
                    array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "cz",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "it",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "de",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "en",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "fr",
                    ),array(
                        "name"              => "Website metadata",
                        "description"       => "Meta tags are snippets of text that describe a page’s content; the meta tags don’t appear on the page itself, but only in the page’s code. We all know tags from blog culture, and meta tags are more or less the same thing, little content descriptors that help tell search engines what a web page is about.",
                        "lang_code"         => "es",
                    )
                )
            ),array(
                "id" 					 	=> 14,
                "site_conf_group_key" 	    => "wipiheezgi1drqgbEKELOTmMOe4gRqWU",
                "code" 					 	=> "genericDesignConfigurations",
                "translations"              => array(
                    array(
                        "name"              => "Configurações Genéricas do Design",
                        "description"       => "Configurações Genéricas do Design",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 15,
                "site_conf_group_key" 	    => "v8gS1mMZ1SEn1MNSww5019jUDFpKXMjv",
                "code" 					 	=> "genericFooterConfigurations",
                "translations"              => array(
                    array(
                        "name"              => "Configurações Genéricas do Footer",
                        "description"       => "Configurações Genéricas do Footer",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 16,
                "site_conf_group_key" 	    => "J4ydzR4vUA3nbBuYFFXDUpfPiGkF1YY3",
                "code" 					 	=> "genericBannerButtonsConfigurations",
                "translations"              => array(
                    array(
                        "name"              => "Configurações Genéricas dos Banner Buttons",
                        "description"       => "Configurações Genéricas dos Banner Buttons",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 17,
                "site_conf_group_key" 	    => "sSffr9fG96Jl1e95AZ0u34HVZHkH1ERV",
                "code" 					 	=> "genericSplashScreen",
                "translations"              => array(
                    array(
                        "name"              => "Splash screen genérico",
                        "description"       => "Splash screen genérico",
                        "lang_code"         => "pt",
                    )
                )
            ),array(
                "id" 					 	=> 18,
                "site_conf_group_key" 	    => "FtS4N9gh7L4UppnpMWq183HF03EHAlBr",
                "code" 					 	=> "authentication",
                "translations"              => array(
                    array(
                        "name"              => "Autenticação",
                        "description"       => "Autenticação",
                        "lang_code"         => "pt",
                    ),array(
                        "name"              => "Autenticação",
                        "description"       => "Autenticação",
                        "lang_code"         => "en",
                    )
                )
            )
        );

        foreach ($siteConfigurationsGroup as $siteConfigurationGroup) {
            $translations = $siteConfigurationGroup["translations"];
            unset($siteConfigurationGroup["translations"]);

            SiteConfGroup::firstOrCreate($siteConfigurationGroup);

            foreach ($translations as $translation) {
                $translation = array_merge(["site_conf_group_id"=>$siteConfigurationGroup["id"]],$translation);
                SiteConfGroupTranslation::firstOrCreate($translation);
            }
        }
    }

    private function siteConfigurations() {
        $siteConfigurations = array(
            array(
                "id"                => 1,
                "site_conf_key" 	=> "lIXqtUM3EguCVK7dPcwdhp81tV5pK223",
                "code"              => "facebook_id",
                "site_conf_group_id" => 1,
                "translations"      => array(
                    array(
                        "name"      => "Id do Facebook",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Id from Facebook",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 2,
                "site_conf_key" 	=> "NnnXsWUNEqHzJteLN748UM9awZZJNyJD",
                "code"              => "facebook_secret",
                "site_conf_group_id" => 1,
                "translations"      => array(
                    array(
                        "name"      => "Código Secreto do Facebook",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Secret code from Facebook",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 3,
                "site_conf_key" 	=> "BX6kuwRonvtbeRQ6adMZfJCL3q8Lsfq9",
                "code"              => "google_analytics",
                "site_conf_group_id" => 2,
                "translations"      => array(
                    array(
                        "name"      => "Código do google analytics",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Google Analytics code",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 4,
                "site_conf_key" 	=> "MqkuCGddlxX6RHIhzPt2ESZbm63uy0KM",
                "code"              => "maps_api_key",
                "site_conf_group_id" => 3,
                "translations"      => array(
                    array(
                        "name"      => "API do Google Maps",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Google Maps API",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 5,
                "site_conf_key" 	=> "2Zkg1HMansJE2PUWD2qG7y9MzXriV9z2",
                "code"              => "maps_default_latitude",
                "site_conf_group_id" => 3,
                "translations"      => array(
                    array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Google Maps Default Latitude",
                        "description" => "Google Maps Default Latitude",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 6,
                "site_conf_key" 	=> "L7lANOMe74eHGgdV9WoHq6r4Er2K4A7U",
                "code"              => "maps_default_longitude",
                "site_conf_group_id" => 3,
                "translations"      => array(
                    array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Google Maps Default Longitude",
                        "description" => "Google Maps Default Longitude",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 7,
                "site_conf_key" 	=> "gcH3NAiPXaxAVzDS8HyfHiRWsAV5OFrx",
                "code"              => "file_marker_icon",
                "site_conf_group_id" => 3,
                "translations"      => array(
                    array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Google Maps Marker",
                        "description" => "Google Maps Marker",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 8,
                "site_conf_key" 	=> "975kW7fIAooP64Hwf12H2PWxC86D7vhd",
                "code"              => "file_marker_icon_small",
                "site_conf_group_id" => 3,
                "translations"      => array(
                    array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Small Marker",
                        "description" => "Small Marker",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 9,
                "site_conf_key" 	=> "XZ6zndbbscWIiW4PhxWNN0P5hVFsEy0Z",
                "code"              => "recaptcha_secret_key",
                "site_conf_group_id" => 4,
                "translations"      => array(
                    array(
                        "name"      => "Secret Key",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Secret Key",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 10,
                "site_conf_key" 	=> "aJamlf3tb6qZq4sI1iNMYFWH3a5y4q39",
                "code"              => "recaptcha_site_key",
                "site_conf_group_id" => 4,
                "translations"      => array(
                    array(
                        "name"      => "Site Key",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Site Key",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 11,
                "site_conf_key" 	=> "74ZbAup6EmSu77atsyuUzBFh5qFMkuKW",
                "code"              => "boolean_recaptcha_register",
                "site_conf_group_id" => 4,
                "translations"      => array(
                    array(
                        "name"      => "Recaptcha no registo",
                        "description" => "Recapcha in register",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Recapcha in register",
                        "description" => "",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 12,
                "site_conf_key" 	=> "yAwOVrM6LXKyXCfzE2r2qBH9MGvNPUuZ",
                "code"              => "reedirectQuestionnaire",
                "site_conf_group_id" => 5,
                "translations"      => array(
                    array(
                        "name"      => "Redireccionamento do Questionário",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Reedirect questionnaire",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 13,
                "site_conf_key" 	=> "gTXsVWD9swqzrAbJ5jYQ7ckameRmgD2J",
                "code"              => "introPage",
                "site_conf_group_id" => 5,
                "translations"      => array(
                    array(
                        "name"      => "Intro Page",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Intro Page",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 14,
                "site_conf_key" 	=> "6uPLgWd5ctA8guoRWG4dduNwFC2zrpqG",
                "code"              => "cbkey",
                "site_conf_group_id" => 6,
                "translations"      => array(
                    array(
                        "name"      => "Botão HomePage",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Homepage Button",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 15,
                "site_conf_key" 	=> "XdegFXRU8ZKwoWiG1ZonKOGAeRz8H6R6",
                "code"              => "piwik_analytics",
                "site_conf_group_id" => 7,
                "translations"      => array(
                    array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Piwik Analytics",
                        "description" => "Piwik Analytics",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 16,
                "site_conf_key" 	=> "2vv4Pmw0ix3rULYodnphReGqy6c23Ekn",
                "code"              => "boolean_libertrium_authentication",
                "site_conf_group_id" => 8,
                "translations"      => array(
                    array(
                        "name"      => "Boolean Autenticação Libertrium",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Boolean Libertrium Authentication",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 17,
                "site_conf_key" 	=> "4TsMqyU8c1pLeDhAqBYABWmkcoPqDmw4",
                "code"              => "libertrium_server_link",
                "site_conf_group_id" => 8,
                "translations"      => array(
                    array(
                        "name"      => "Link para o servidor Libertrium",
                        "description" => "Link para o servidor Libertrium",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Libertrium Server Link",
                        "description" => "Libertrium Server Link",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 18,
                "site_conf_key" 	=> "2vv4Pmw0ix3rULYodnphReGqy6c23Ekn",
                "code"              => "boolean_libertrium_authentication",
                "site_conf_group_id" => 9,
                "translations"      => array(
                    array(
                        "name"      => "Boolean Autenticação Libertrium",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Boolean Libertrium Authentication",
                        "description" => "",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 19,
                "site_conf_key" 	=> "20emaf72cDwhBfF98QcbpfIxxOWZ4fqI",
                "code"              => "second_cycle_parameters",
                "site_conf_group_id" => 9,
                "translations"      => array(
                    array(
                        "name"      => "Parametros do segundo ciclo",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Second Cycle Parameters",
                        "description" => "",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 20,
                "site_conf_key" 	=> "fL86g0KwbXMaih4GlB8HUZDRClxUuImp",
                "code"              => "current_phase",
                "site_conf_group_id" => 9,
                "translations"      => array(
                    array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Current Phase",
                        "description" => "Current Phase",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 21,
                "site_conf_key" 	=> "PNl8teTxbDzyzwq4IUXQhwXb2qqXgnz9",
                "code"              => "boolean_register_login",
                "site_conf_group_id" => 9,
                "translations"      => array(
                    array(
                        "name"      => "Disable register and login",
                        "description" => "Disable register and login",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Disable register and login",
                        "description" => "Disable register and login",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 22,
                "site_conf_key" 	=> "1ReIqKaSLp7tbcnOV9hKatQzJpwAUGU9",
                "code"              => "active_cb",
                "site_conf_group_id" => 9,
                "translations"      => array(
                    array(
                        "name"      => "Cb Activo",
                        "description" => "Cb Activo",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Active Cb",
                        "description" => "Active Cb",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 23,
                "site_conf_key" 	=> "HOabAGLtXX83FBAde3hFNmM3LUOdKSTn",
                "code"              => "server_link",
                "site_conf_group_id" => 10,
                "translations"      => array(
                    array(
                        "name"      => "Ligação para o Servidor de Autenticação",
                        "description" => "Ligação para o Servidor de Autenticação",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 24,
                "site_conf_key" 	=> "m4WC6lecZ8MZ9c6GTuZGZ3WrRyGDeKi0",
                "code"              => "sms_service_code",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Sms Service Code",
                        "description" => "Sms Service Code",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 25,
                "site_conf_key" 	=> "NKzbjjzIR5gID3POJ6l7r2Os3gIfeNzr",
                "code"              => "sms_service_username",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Sms Service Username",
                        "description" => "Sms Service Username",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 26,
                "site_conf_key" 	=> "UIuOFzszyti79cIv2WSu5P3wSrfSq964",
                "code"              => "sms_service_password",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Sms Service Password",
                        "description" => "Sms Service Password",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 27,
                "site_conf_key" 	=> "wyGOOQDsPmqr1vjG0K7wH9xH8X1i8q2b",
                "code"              => "sms_service_sender_name",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Sms Service Sender name",
                        "description" => "Sms Service Sender name",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 28,
                "site_conf_key" 	=> "gj8yKn9WmwYC9UstfhlwZszKqTiqRfaP",
                "code"              => "sms_token_text",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "SMS Token",
                        "description" => "SMS Token",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 29,
                "site_conf_key" 	=> "rXHLcenGdPJIdKBwII6Nq6KWiGugX3cB",
                "code"              => "sms_max_send",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Maximum number of Sms to send",
                        "description" => "Maximum number of Sms to send",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 30,
                "site_conf_key" 	=> "mE0lDp2zCu2jYwnRzIT5qVt0xbJKEW7N",
                "code"              => "sms_indicative_number",
                "site_conf_group_id" => 11,
                "translations"      => array(
                    array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Sms Indicative",
                        "description" => "Sms Indicative",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 31,
                "site_conf_key" 	=> "NucfOSTZinPsZZeDHIdt2FodkJNx3MyR",
                "code"              => "og_title",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:title",
                        "description" => "The title of your object as it should appear within the graph, e.g., 'EMPATIA'.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 32,
                "site_conf_key" 	=> "9BlfZ4Or4bcmzKre3vfvxQjHB5qxWaa5",
                "code"              => "og_description",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:description",
                        "description" => "A one to two sentence description of your object.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 33,
                "site_conf_key" 	=> "7Oc4ffFCgQI8rvh3ogqbVdSpcAAr8qgh",
                "code"              => "file_og_image",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:image",
                        "description" => "An image URL which should represent your object within the graph.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 34,
                "site_conf_key" 	=> "NPGOFMMFSXq5ZJsQmHlBAssLLdBigwPj",
                "code"              => "og_site_name",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:site_name",
                        "description" => "If your object is part of a larger web site, the name which should be displayed for the overall site.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 35,
                "site_conf_key" 	=> "eS2Wt684PlrJ79d4mojSeu5wQEcSGdTX",
                "code"              => "file_og_audio",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:audio",
                        "description" => "A URL to an audio file to accompany this object.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 36,
                "site_conf_key" 	=> "wSa8J2LlUYL5sMDlYrrBqOZDnCfAUX4B",
                "code"              => "file_og_video",
                "site_conf_group_id" => 12,
                "translations"      => array(
                    array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "og:video",
                        "description" => "A URL to a video file that complements this object.",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 37,
                "site_conf_key" 	=> "Wz8ux8v2fd1LqOY9FH6hrr3fPIR5rfO1",
                "code"              => "website_description",
                "site_conf_group_id" => 13,
                "translations"      => array(
                    array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Description",
                        "description" => "Unlike the keywords attribute, the description attribute is supported by most major search engines, like Yahoo! and Bing, while Google will fall back on this tag when information about the page itself is requested (e.g. using the related: query). The description attribute provides a concise explanation of a Web page's content. This allows the Web page authors to give a more meaningful description for listings than might be displayed if the search engine was unable to automatically create its own description based on the page content. The description is often, but not always, displayed on search engine results pages, so it can affect click-through rates. While clicks for a result can be a positive sign of effective title and description writing, Google does not recognize this meta element as a ranking factor,[12] so using target keyword phrases in that element will not help a site rank better. W3C doesn't specify the size of this description meta tag, but almost all search engines recommend it to be shorter than 155 characters of plain text.[citation needed]",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 38,
                "site_conf_key" 	=> "vFH3lCypqT7FcKlkggVmH7o0usw34M0U",
                "code"              => "website_author",
                "site_conf_group_id" => 13,
                "translations"      => array(
                    array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Author",
                        "description" => "",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 39,
                "site_conf_key" 	=> "JQFnvvUGcS8FVGFlWMQ7DKjHsh8dSZaP",
                "code"              => "website_keywords",
                "site_conf_group_id" => 13,
                "translations"      => array(
                    array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "cz",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "it",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "de",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "en",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "fr",
                    ),array(
                        "name"      => "Keywords",
                        "description" => "The keywords attribute was popularized by search engines such as Infoseek and AltaVista in 1995, and its popularity quickly grew until it became one of the most commonly used meta elements.[2]",
                        "lang_code" => "es",
                    )
                )
            ),array(
                "id"                => 40,
                "site_conf_key" 	=> "PlxgkrSm1RWxP5bzQKuh0pw3w1gThC7P",
                "code"              => "file_homepage_image",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "Homepage Image",
                        "description" => "Homepage Image",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 41,
                "site_conf_key" 	=> "NxX1uaGdPoeGPGML0ErYL2mb2enncYmX",
                "code"              => "color_primary",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "color_primary",
                        "description" => "color_primary",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 42,
                "site_conf_key" 	=> "WAo9f4nQzpgWunwTYBDCHKmlCFaksp5q",
                "code"              => "color_secondary",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "color_secondary",
                        "description" => "color_secondary",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 43,
                "site_conf_key" 	=> "VPG4FgRzoxiS24VWfA24umgbrzcC6uGm",
                "code"              => "site_title",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "site_title",
                        "description" => "site_title",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 44,
                "site_conf_key" 	=> "AVwGni2TOc2Ej9ypQBwBLr7Tq9rVouvY",
                "code"              => "file_logo_first",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "Primeiro Logo",
                        "description" => "Primeiro Logo",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 45,
                "site_conf_key" 	=> "A0CJju8ECEQDtIpsoF7Pj8YWD5BmSWQP",
                "code"              => "file_header_background",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "Background do header",
                        "description" => "Background do header",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 46,
                "site_conf_key" 	=> "iqeVS4ZlNOxtXmaAaEcdYdCfKpv0PGHf",
                "code"              => "color_primary_on_hover",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "Primary color on hover",
                        "description" => "Primary color on hover",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 47,
                "site_conf_key" 	=> "HXePBx6jQfD74a9rLeOFLY2Yv3mVcC8f",
                "code"              => "file_background_login",
                "site_conf_group_id" => 14,
                "translations"      => array(
                    array(
                        "name"      => "Imagem de background do login",
                        "description" => "Imagem de background do login",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "background image login",
                        "description" => "background image login",
                        "lang_code" => "en",
                    )
                )
            ),array(
                "id"                => 48,
                "site_conf_key" 	=> "S8AGsZj41GBVKioXTODcCmtA7Tq2MQ9C",
                "code"              => "html_left_column",
                "site_conf_group_id" => 15,
                "translations"      => array(
                    array(
                        "name"      => "Coluna da esquerda",
                        "description" => "Coluna da esquerda",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 49,
                "site_conf_key" 	=> "odiSZBvdc2oTIkIuQINJL9PumS9dkbr1",
                "code"              => "html_mid_column",
                "site_conf_group_id" => 15,
                "translations"      => array(
                    array(
                        "name"      => "Coluna do meio",
                        "description" => "Coluna do meio",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 50,
                "site_conf_key" 	=> "Pzev4dsHNqv7LheErycjcfF8uUae3fY3",
                "code"              => "html_right_column",
                "site_conf_group_id" => 15,
                "translations"      => array(
                    array(
                        "name"      => "Coluna da direita",
                        "description" => "Coluna da direita",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 51,
                "site_conf_key" 	=> "M68nY4dl8kGODFEyt7X8MxNTCJLvFayq",
                "code"              => "html_banner_left_column",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "Coluna da esquerda",
                        "description" => "",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 52,
                "site_conf_key" 	=> "gIZy5Eof9OiubSOYHm6Hr1rLodAYjOdT",
                "code"              => "html_banner_mid_column",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "Coluna do meio",
                        "description" => "Coluna do meio",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 53,
                "site_conf_key" 	=> "TI3GnJyW7tdarXXB5WUoFCfWdf8a4EMO",
                "code"              => "html_banner_right_column",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "Coluna da direita",
                        "description" => "Coluna da direita",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 54,
                "site_conf_key" 	=> "D6ZxWfjwwOsSJ0GwQSf6c5iA2M4wkHc8",
                "code"              => "url_banner_column_left",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "url_banner_column_left",
                        "description" => "url_banner_column_left",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 55,
                "site_conf_key" 	=> "t0zpl34iurlY5jAozyrqHYHXC6JB6sM8",
                "code"              => "url_banner_column_mid",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "url_banner_column_mid",
                        "description" => "url_banner_column_mid",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 56,
                "site_conf_key" 	=> "JFqPyRAy57IE8GNCDedOntAixkeu53Wp",
                "code"              => "url_banner_column_right",
                "site_conf_group_id" => 16,
                "translations"      => array(
                    array(
                        "name"      => "url_banner_column_right",
                        "description" => "url_banner_column_right",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 57,
                "site_conf_key" 	=> "WsccutCAEpj419EPHX2WuOljkBz0dmP3",
                "code"              => "boolean_splash_screen",
                "site_conf_group_id" => 17,
                "translations"      => array(
                    array(
                        "name"      => "Splash screen está activo ou não",
                        "description" => "Splash screen está activo ou não",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 58,
                "site_conf_key" 	=> "gry2cZaN2ANpcVhCqba9a97X7Bxye4uZ",
                "code"              => "html_splash_screen_content",
                "site_conf_group_id" => 17,
                "translations"      => array(
                    array(
                        "name"      => "Conteudo do splash screen",
                        "description" => "Conteudo do splash screen",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 59,
                "site_conf_key" 	=> "AoOUQhM18ozpbzeAXVYCx8VNEj8ZEeVR",
                "code"              => "splash_screen_page_title",
                "site_conf_group_id" => 17,
                "translations"      => array(
                    array(
                        "name"      => "Titulo para o head da página de splash",
                        "description" => "Titulo para o head da página de splash",
                        "lang_code" => "pt",
                    )
                )
            ),array(
                "id"                => 60,
                "site_conf_key" 	=> "tk5AlqBIDWW8ndrHsCSQdXsEdSpShENH",
                "code"              => "boolean_no_email_needed",
                "site_conf_group_id" => 18,
                "translations"      => array(
                    array(
                        "name"      => "Não necessita de email para registar",
                        "description" => "Não necessita de email para registar",
                        "lang_code" => "pt",
                    ),array(
                        "name"      => "Não necessita de email para registar",
                        "description" => "Não necessita de email para registar",
                        "lang_code" => "en",
                    )
                )
            )
        );

        foreach ($siteConfigurations as $siteConfiguration) {
            $translationsSiteConf = $siteConfiguration["translations"];
            unset($siteConfiguration["translations"]);

            SiteConf::firstOrCreate($siteConfiguration);

            foreach ($translationsSiteConf as $translationSiteConf) {
                $translationSiteConf = array_merge(["site_conf_id"=>$siteConfiguration["id"]],$translationSiteConf);
                SiteConfTranslation::firstOrCreate($translationSiteConf);
            }
        }
    }
}
