<?php

namespace App\Console\Commands;

use Database\Seeders\ConfigurationsTableSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;

class SeedConfigurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:configurations
    {--c|clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed Configurations datatable with options';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DatabaseSeeder())->callWith(ConfigurationsTableSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
