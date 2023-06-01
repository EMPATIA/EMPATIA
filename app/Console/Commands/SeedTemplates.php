<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TemplatesTableSeeder;
use Illuminate\Console\Command;

class SeedTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:templates
    {--c|clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed Templates datatable with options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DatabaseSeeder())->callWith(TemplatesTableSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
