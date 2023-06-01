<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LanguagesTableSeeder;
use Illuminate\Console\Command;

class SeedLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:languages
    {--c|clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed Menus datatable with options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DatabaseSeeder())->callWith(LanguagesTableSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
