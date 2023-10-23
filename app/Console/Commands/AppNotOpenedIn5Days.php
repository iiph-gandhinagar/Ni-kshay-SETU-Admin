<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\Subscriber;
use App\Models\SubscriberActivity;
use App\Models\UserDeviceToken;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AppNotOpenedIn5Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-not-opended:in-5-days';

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
        $this->line("App Not Opened in 5 Days Condition Match Criteria Start!!!");

        $app_not_opened_for_5_days = SubscriberActivity::where('action', 'user_home_page_visit')
            ->whereBetween('created_at', [Carbon::today()->subDays(5), Carbon::today()])
            ->groupBy('user_id')
            ->pluck('user_id');

        $app_not_opened_user_for_10_days = SubscriberActivity::where('action', 'user_home_page_visit')
            ->whereBetween('created_at', [Carbon::today()->subDays(10), Carbon::today()])
            ->groupBy('user_id')
            ->pluck('user_id');

        $app_not_opened_user_for_14_days = SubscriberActivity::where('action', 'user_home_page_visit')
            ->whereBetween('created_at', [Carbon::today()->subDays(15), Carbon::today()])
            ->groupBy('user_id')
            ->pluck('user_id');

        $user_15_days = Subscriber::whereNotIn('id', $app_not_opened_user_for_14_days)->pluck('id');
        $user_10_days = Subscriber::whereNotIn('id', $app_not_opened_user_for_10_days)->pluck('id');
        $not_opened_app_user = collect(array_merge($app_not_opened_for_5_days->toArray(), $user_15_days->toArray(), $user_10_days->toArray()))->unique()->values();

        $subscribers_5_days = Subscriber::whereNotIn('id', $not_opened_app_user)->pluck('id');

        // Log::info("subscribers not opened app in 5 days ------------------------------------------>");
        // Log::info($subscribers_5_days);
        if (count($subscribers_5_days) > 0) {
            $notification['title'] = "App Not Opened";
            $notification['description'] = "It's been a while... your app is not opened within 5 days";

            $device_id = UserDeviceToken::whereIn('user_id', $subscribers_5_days)->get('notification_token');
            return SendNotificationController::sendNotification($notification, $device_id);
        }
        $this->info("App Not Opened in 5 Days Condition Match Criteria done!!!");
        return  0;
    }
}