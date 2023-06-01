<?php

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed entire database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DatabaseSeeder())->callWith(DatabaseSeeder::class, [$this, $this->options()]);
        return 0;
    }
}
