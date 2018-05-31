<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\OrchUsersController;
use App\Modules\OpenData\Controllers\OpenDataController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //Run everyday at 00 AM - Topics Near Deadline Notification
        $schedule->call('\App\Http\Controllers\TopicsController@topicsNearVoteEndingAlert')->dailyAt('00:01');
        
        /* Anonymize Users Queue - run every 5 minutes */
        $schedule->call(function() {
            OrchUsersController::anonymizeUsers();
        })->everyFiveMinutes();

        /* Export Open Data - run every midnight */
        $schedule->call(function() {
            OpenDataController::exportToDb();
        })->dailyAt("00:00");
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
