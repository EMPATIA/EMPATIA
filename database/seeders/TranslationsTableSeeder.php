<?php

namespace Database\Seeders;

use App\Helpers\HCache;
use App\Models\Backend\CMS\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class TranslationsTableSeeder extends Seeder
{
    protected array $languages = ['pt', 'en'];
    private array $sourceFiles = ['translations'];
    public array $seedResults = [];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            if (Schema::hasTable('translations')) {
                Model::unguard();
                
                $command = $command ?: $this->command;
                if ($options['clear'] ?? []) {
                    if ($command->confirm('Are you sure that you want to delete all translations\'s table data?', false)) {
                        $command->info("Deleting translations table data...");
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::table('translations')->truncate();
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    }
                }
                
                $command->info("Seeding translations table...");
                if (!empty($options['language'])) {
                    $this->languages = explode(",", $options['language'][0]);   // Get languages passed by command line
                }
                
                foreach ($this->sourceFiles ?? [] as $fileName) {
                    foreach ($this->languages ?? [] as $lang) {
                        $this->seedResults['updated_translations'] = 0;
                        $this->seedResults['created_translations'] = 0;
                        
                        $translationsFile = Config::get("$fileName-$lang");     // Get file from config folder
                        
                        if (is_array($translationsFile) && !empty($translationsFile)) {
                            $newTranslations = [];
                            $updateTranslations = [];
                            
                            foreach ($translationsFile as $key => $text) {
                                if(empty($text) || !is_string($text)) {
                                    throw new \Exception('Translation text is empty or isn\'t a string');
                                }
                                $translation = new Translation;
                                
                                $explodedLocale = explode(":", $key, 2);
                                $explodedNamespace = explode(".", $explodedLocale[1], 2);
                                $explodedGroup = explode(".", $explodedNamespace[1], 2);
                                
                                $locale = first_element($explodedLocale);
                                $namespace = first_element($explodedNamespace);
                                $group = first_element($explodedGroup);

                                if (isset($explodedGroup[1]))
                                    $item = $explodedGroup[1];
                                else
                                    throw new \Exception('Translation without item');
                                
                                // Fill new translations fields
                                $translation->locale = $locale;
                                $translation->namespace = $namespace;
                                $translation->group = $group;
                                $translation->item = $item;
                                $translation->text = $text;
                                

                                // If translations exists
                                if (!empty($dbTranslation = Translation::where([
                                    ['locale', $translation->locale],
                                    ['namespace', '=', $translation->namespace],
                                    ['group', '=', $translation->group],
                                    ['item', '=', $translation->item]])
                                    ->first())) {
                                    
                                    if ($dbTranslation->text != $translation->text) { // If translation text is different from the imported one
                                        $translation->id = $dbTranslation->id;
                                        array_push($updateTranslations, $translation);  // Translation will be updated
                                    }
                                    
                                } else { // If translation doesn't exist
                                    array_push($newTranslations, $translation); // Translation will be created
                                }
                            }

                            DB::beginTransaction();
                            foreach ($updateTranslations ?? [] as $updateTranslation) {
                                if (!empty($updateTranslation->id))
                                    if (Translation::whereId($updateTranslation->id)->update(['text' => $updateTranslation->text])) {
                                        HCache::flushTranslationId($updateTranslation->id);
                                        $this->seedResults['updated_translations']++;
                                    }
                            }
    
                            foreach ($newTranslations ?? [] as $newTranslation) {
                                if ($newTranslation->save())
                                    $this->seedResults['created_translations']++;
                            }
                            DB::commit();
                        }
                        
                        $command->comment("Translations seeded to language => " . $lang);
                        $command->info("Translations updated => " . $this->seedResults['updated_translations']);
                        $command->info("Translations created => " . $this->seedResults['created_translations']);
                    }
                }
                $command->comment("Translations table seeding completed successfully!");
                
            } else {
                $command->error("There isn't any translations table");
                return null;
            }
            
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding translations table!");
            logError('Translations seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }
    }
}
