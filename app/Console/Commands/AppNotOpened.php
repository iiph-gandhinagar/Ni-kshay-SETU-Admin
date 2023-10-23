<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use App\Models\SubscriberActivity;
use App\Models\UserDeviceToken;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Http\Controllers\Admin\SendNotificationController;

class AppNotOpened extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-not-opened:in-14-days';

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
        $this->line("App Not Opened Condition Match Criteria Start!!!");

        $app_not_opened_user_for_14_days = SubscriberActivity::where('action', 'user_home_page_visit')
            ->whereBetween('created_at', [Carbon::today()->subDays(15), Carbon::today()])
            ->groupBy('user_id')
            ->pluck('user_id'); //get(['user_id'])->
        $subscriber_14_days = Subscriber::whereNotIn('id', $app_not_opened_user_for_14_days)->pluck('id'); //->get('id')
        // Log::info($app_not_opened_user_for_14_days);
        // Log::info("subscribers not opened app in 15 days ------------------------------------------>");
        // Log::info($subscriber_14_days);

        if (count($subscriber_14_days) > 0) {
            $notification['title'] = "App Not Opened";
            $notification['description'] = "It's been a while... your app is not opened within 14 days";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber_14_days)->get('notification_token');
            return SendNotificationController::sendNotification($notification, $device_id);
        }

        $this->info("App Not Opened Condition Match Criteria done!!!");
        return  0;
    }
}
