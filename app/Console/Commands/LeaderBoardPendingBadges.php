<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\AutomaticNotification;
use App\Models\LbSubscriberRanking;
use App\Models\UserDeviceToken;
use DateTime;
use Illuminate\Console\Command;
use Log;
use Config;

class LeaderBoardPendingBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard-pending:badges';

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
        $this->line("LeaderBoard Pending Badge Match Criteria Start!!!");
        $leaderboard = LbSubscriberRanking::get(['subscriber_id', 'level_id', 'badge_id', 'updated_at']);
        $subscribers_ids = collect([]);
        foreach ($leaderboard as $lb) {
            if ($lb->level_id != 6) {
                // $subscriberHistory = LbSubscriberRankingHistory::where('subscriber_id',$lb->subscriber_id)->orderBy('created_at','desc')->limit(1)->get(['created_at']);
                // if(count($subscriberHistory) > 0){
                $dateTime1 = new DateTime($lb->updated_at);
                $dateTime2 = new DateTime();
                $interval = $dateTime1->diff($dateTime2);
                $days = $interval->format('%a');
                // Log::info($dateTime1->format('Y-m-d H:i:s'));
                // Log::info($dateTime2->format('Y-m-d H:i:s'));
                // Log::info($days);

                if ($days > 10) {
                    $subscribers_ids->push($lb->subscriber_id);
                }
                // }
            }
        }
        // Log::info($subscribers_ids);
        if (isset($subscribers_ids) && count($subscribers_ids) > 0) {
            $notification['title'] = "Leaderboard Task";
            $notification['description'] = "You have some pending tasks to achieve next Level, Check out now";
            $notification['type'] = "Leader Board";
            $notification['subscriber_id'] = $subscribers_ids->implode(',');
            $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Tasks";
            $notification['created_by'] = 1;
            AutomaticNotification::create($notification);
            $device_id = UserDeviceToken::whereIn('user_id', $subscribers_ids)->get('notification_token');
            return SendNotificationController::leaderBoardBadge($notification, $device_id);
        }

        $this->info("LeaderBoard Pending Badge Match Criteria done!!!");
        return 0;
    }
}
