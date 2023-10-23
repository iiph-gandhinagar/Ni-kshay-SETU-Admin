<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;
use DB;

class sendForgotPasswordOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forgot-password:send-otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $this->line("Forogot Password send otp Start!!!");
        Log::info("carbon now date before 10 minutes --->");
        Log::info(Carbon::now()->subMinutes(10)->format('Y-m-d H:i'));
        $subscribers = Subscriber::where('is_verified', 0)->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d %H:%i'))"), Carbon::now()->subMinutes(10)->format('Y-m-d H:i'))->get();
        Log::info($subscribers);
        if (count($subscribers) > 0) {
            foreach ($subscribers as $user) {
                Log::info("Forgot Password Scheduling For loop ---->");
                Log::info($user->id . "->" . $user->name);
                app('App\Http\Controllers\API\NotificationController')->sendForgotOtp($user);
                Subscriber::where('id', $user->id)->update(['forgot_otp_time' => Carbon::now()->format('Y-m-d H:i')]);
            }
        }
        $this->info("Forogot Password send otp Done!!!");
        return 0;
    }
}
