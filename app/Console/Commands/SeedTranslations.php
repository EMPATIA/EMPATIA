<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TranslationsTableSeeder;
use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:translations
    {--c|clear}
    {--l|language=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed Translations datatable with options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DatabaseSeeder())->callWith(TranslationsTableSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
