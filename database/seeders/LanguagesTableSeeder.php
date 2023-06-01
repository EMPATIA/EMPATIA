<?php

namespace Database\Seeders;

use App\Models\Backend\CMS\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguagesTableSeeder extends Seeder
{
    protected array $languages = [
        [
            'locale'        => 'pt',
            'name'          => 'Portugues',
            'default'       => '1',
            'backend'       => '1',
            'frontend'      => '1',
        ],
        [
            'locale'        => 'en',
            'name'          => 'Ingles',
            'default'       => '0',
            'backend'       => '1',
            'frontend'      => '1',
        ],
        [
            'locale'        => 'es',
            'name'          => 'Espanhol',
            'default'       => '0',
            'backend'       => '1',
            'frontend'      => '0',
        ],
        [
            'locale'        => 'fr',
            'name'          => 'Frances',
            'default'       => '0',
            'backend'       => '1',
            'frontend'      => '0',
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            if (Schema::hasTable('languages')) {
                Model::unguard();
                
                $command = $command ?: $this->command;
                if ($options['clear'] ?? []) {
                    if ($command->confirm('Are you sure that you want to delete all languages\'s table data?', false)) {
                        $command->info("Deleting languages table data...");
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        DB::table('languages')->truncate();
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        $command->info("Languages table data deleted!");
                    }
                }
                
                $command->info("Seeding languages table...");
                foreach ($this->languages as $language) {
                    Language::create([
                        'locale' => $language['locale'],
                        'name' => $language['name'],
                        'default' => $language['default'],
                        'backend' => $language['backend'],
                        'frontend' => $language['frontend']
                    ]);
                }
                $command->comment("Languages table seeding completed successfully!");
            } else {
                $command->error("There isn't any languages table");
                return null;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding languages table!");
            logError('Languages seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }
    }
}
