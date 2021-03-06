<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\CronJob'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call('\App\Http\Controllers\BotController@notification')->timezone('Asia/Bangkok')->dailyAt('18:00');
        $schedule->call('\App\Http\Controllers\BotController@notification_homework')->cron('* * * * *');
        $schedule->call('\App\Http\Controllers\BotController@flex_result_push')->cron('* * * * *');
        $schedule->call('\App\Http\Controllers\BotController@add_null_to_exp_log')->cron('* * * * *');
        
        // $schedule->call('\App\Http\Controllers\BotController@notification_homework_exp_date')->timezone('Asia/Bangkok')->dailyAt('07:30');
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
