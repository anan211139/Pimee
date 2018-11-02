<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Http\Controllers\BotController;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cronjob';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'description command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // {{ BotController::notification_for_test(); }}
        
        DB::table('exchanges')->insert([
            'line_code' => 1,
            'send' => 1,
            'code_id' => 1,
            'time' => Carbon::now()
        ]);

        //$this -> call('App\Http\Controllers\ฺBotController@notification_for_test');
    }
}
