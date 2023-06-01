<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MenusTableSeeder;
use Illuminate\Console\Command;

class SeedMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:menus
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
        (new DatabaseSeeder())->callWith(MenusTableSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
