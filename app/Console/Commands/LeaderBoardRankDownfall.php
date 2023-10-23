<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\AutomaticNotification;
use App\Models\LbSubscriberRanking;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Config;

class LeaderBoardRankDownfall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard-rank:downfall';

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
        $this->line("LeaderBoard Rank Downfall Match Criteria Start!!!");
        Log::info(Carbon::today()->subDays(15));
        Log::info(Carbon::today());
        $subscriber = LbSubscriberRanking::whereDate('updated_at', '>=', Carbon::today()->subDays(15))->pluck('subscriber_id');
        // Log::info($subscriber);
        $subscribers_15_days = Subscriber::whereHas('lb_subscriber_rankings', function ($query) {
            $query->where('level_id', '!=', 6);
        })->whereNotIn('id', $subscriber)->pluck('id');
        Log::info($subscribers_15_days);
        if (isset($subscribers_15_days) && count($subscribers_15_days) > 0) {
            $notification['title'] = "Competition Increased";
            $notification['description'] = "Your Leaderboard rank is dropping, click here to check your competition.";
            $notification['type'] = "Leader Board";
            $notification['subscriber_id'] = implode(',', $subscribers_15_days->toArray());
            $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Leaderboard";
            $notification['created_by'] = 1;
            AutomaticNotification::create($notification);
            $device_id = UserDeviceToken::whereIn('user_id', $subscribers_15_days)->get('notification_token');
            return SendNotificationController::leaderBoardBadge($notification, $device_id);
        }

        $this->info("LeaderBoard Rank Downfall  Match Criteria done!!!");
        return 0;
    }
}
