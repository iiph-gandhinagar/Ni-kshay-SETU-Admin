<?php

namespace App\Console\Commands;

use App\Models\LbBadge;
use App\Models\LbSubscriberRanking;
use App\Models\LbSubscriberRankingHistory;
use App\Models\LbTaskList;
use App\Models\UserDeviceToken;
use Illuminate\Console\Command;
use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\LbLevel;
use Carbon\Carbon;
use Exception;
use Log;

class LeaderBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriber-leaderboard:update';

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
        $this->line("LeaderBoard Condition Match Criteria Start!!!");
        Log::info("5 minute before time ----------->");
        Log::info(Carbon::now()->subMinutes(5));
        $last_leaderboard_activity = LbSubscriberRanking::where('updated_at', '>=', Carbon::now()->subMinutes(5))->get();
        // Log::info($last_leaderboard_activity);
        foreach ($last_leaderboard_activity as $activity) {
            // Log::info("foreach call ----->");
            $this->subscrierLeaderBoardCalculation($activity);
        }

        $this->info("LeaderBoard Condition Match Criteria done!!!");
        return 1;
    }

    public function subscrierLeaderBoardCalculation($activity)
    {
        // Log::info("activity ---->");
        // Log::info($activity);
        $task_list = LbTaskList::where('badges', $activity->badge_id)->get();
        // Log::info("task list data --->");
        // Log::info($task_list);
        // Log::info("user_id ---->".$activity->subscriber_id);
        // Log::info("mins spent ---->".$activity->mins_spent_count >= $task_list[0]['mins_spent']);
        // Log::info("sub module --->".$activity->sub_module_usage_count >= $task_list[0]['sub_module_usage_count'] );
        // Log::info("app opened count---->".$activity->App_opended_count >= $task_list[0]['App_opended_count']);
        // Log::info("chatbot usage----->".$activity->chatbot_usage_count >= $task_list[0]['chatbot_usage_count']);
        // Log::info("resource material count----->".$activity->resource_material_accessed_count >= $task_list[0]['resource_material_accessed_count']);
        if ((isset($task_list) && count($task_list) > 0) && ($activity->mins_spent_count) / 60 >= $task_list[0]['mins_spent'] && $activity->sub_module_usage_count >= $task_list[0]['sub_module_usage_count'] && $activity->App_opended_count >= $task_list[0]['App_opended_count'] && $activity->chatbot_usage_count >= $task_list[0]['chatbot_usage_count'] && $activity->resource_material_accessed_count >= $task_list[0]['resource_material_accessed_count']) {
            Log::info("inside task list for subscriber ---->");
            Log::info($activity->subscriber_id);
            try {
                $newRequest['lb_subscriber_rankings_id'] = $activity->id;
                $newRequest['subscriber_id'] = $activity->subscriber_id;
                $newRequest['level_id'] = $activity->level_id;
                $newRequest['badge_id'] = $activity->badge_id;
                $newRequest['mins_spent_count'] = $activity->mins_spent_count;
                $newRequest['chatbot_usage_count'] = $activity->chatbot_usage_count;
                $newRequest['resource_material_accessed_count'] = $activity->resource_material_accessed_count;
                $newRequest['sub_module_usage_count'] = $activity->sub_module_usage_count;
                $newRequest['App_opended_count'] = $activity->App_opended_count;
                // Log::info($newRequest);
                LbSubscriberRankingHistory::create($newRequest);
                $get_level = LbBadge::where('id', ($activity->badge_id + 1))->get(['id', 'level_id']);
                // Log::info($get_level);
                // Log::info($get_level);
                $userNotification['title'] = "New Achivement";
                $userNotification['description'] = "You got new Badge";
                $userNotification['old_level'] = LbLevel::where('id', $activity->level_id)->get(['level'])[0]->level;
                $userNotification['current_level'] = isset($get_level) && count($get_level) > 0 ? LbLevel::where('id', $get_level[0]['level_id'])->get(['level'])[0]->level : "Expert";
                $userNotification['old_badge'] = LbBadge::where('id', $activity->badge_id)->get(['badge'])[0]->badge;
                $userNotification['current_badge'] = isset($get_level) && count($get_level) > 0 ? LbBadge::where('id', $get_level[0]['id'])->get(['badge'])[0]->badge : "Gold";
                // Log::info("user notification ------>");
                // Log::info($userNotification);

                if (isset($get_level) && count($get_level) > 0) {
                    $updated_activity = LbSubscriberRanking::where('subscriber_id', $activity->subscriber_id)->update(['level_id' => $get_level[0]['level_id'], 'badge_id' => $get_level[0]['id'], 'total_task_count' => $activity->total_task_count + $task_list[0]['total_task']]);
                    Log::info("after update in if ---------------------------->");
                } else {
                    $updated_activity = LbSubscriberRanking::where('subscriber_id', $activity->subscriber_id)->update(['level_id' => 6, 'badge_id' => 16, 'total_task_count' => $activity->total_task_count + $task_list[0]['total_task']]);
                    Log::info("after update in else part ---------------------------->");
                }

                $response = $this->sendNotification($userNotification, [$activity->subscriber_id]);

                $update_subscriber_leaderbard = LbSubscriberRanking::where('subscriber_id', $activity->subscriber_id)->get();
                $this->subscrierLeaderBoardCalculation($update_subscriber_leaderbard[0]);
            } catch (Exception $e) {
                Log::info("error" . $e);
                // return $e->getMessage();
                return ['error' => $e->getMessage()];
            }
        }
        return 0;
    }

    public function sendNotification($notification, $user)
    {
        // Log::info("inside notification controller ");
        $device_id = UserDeviceToken::whereIn('user_id', $user)->get('notification_token');
        return SendNotificationController::deepLinkingNotification($notification, $device_id);
    }
}
