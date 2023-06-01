<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    protected array $seeders = [
        LanguagesTableSeeder::class,
        ConfigurationsTableSeeder::class,
        MenusTableSeeder::class,
//        TranslationsTableSeeder::class,
        TemplatesTableSeeder::class,
    ];


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run($command = null, $options = [])
    {
        try {
            Model::unguard();

            $command = $command ?: $this->command;
            $command->info("Starting general database seeder!");

            foreach ($this->seeders ?? [] as $seeder) {
                $this->callWith($seeder, [$command]);
            }

            $command->comment("General database seeding completed successfully!");
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            $command->error("Error seeding database tables!");
            logError('Database seeder: ' . $e->getMessage() . ' at line ' . $e->getTraceAsString());
        }


    }
}
