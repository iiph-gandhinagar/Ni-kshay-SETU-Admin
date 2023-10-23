<?php

namespace App\Observers;

use App\Models\LbSubscriberRanking;
use App\Models\SubscriberActivity;
use Exception;
use Log;

class SubscriberActivityObserver
{
    /**
     * Handle the SubscriberActivity "created" event.
     *
     * @param  \App\Models\SubscriberActivity  $subscriberActivity
     * @return void
     */
    public function created(SubscriberActivity $subscriberActivity)
    {
        try {
            $lb_subscriber_ranking = LbSubscriberRanking::where('subscriber_id', $subscriberActivity->user_id)->get();
            if (count($lb_subscriber_ranking) > 0) {
                if ($subscriberActivity->action == "user_home_page_visit") {
                    LbSubscriberRanking::where('subscriber_id', $lb_subscriber_ranking[0]['subscriber_id'])
                        ->update(['App_opended_count' => $lb_subscriber_ranking[0]['App_opended_count'] + 1]);
                }

                if ($subscriberActivity->action == "Chat Questions By Keyword Fetched") {
                    LbSubscriberRanking::where('subscriber_id', $lb_subscriber_ranking[0]['subscriber_id'])
                        ->update(['chatbot_usage_count' => $lb_subscriber_ranking[0]['chatbot_usage_count'] + 1]);
                }

                if ($subscriberActivity->action == "Search By Keyword Fetched") {
                    LbSubscriberRanking::where('subscriber_id', $lb_subscriber_ranking[0]['subscriber_id'])
                        ->update(['chatbot_usage_count' => $lb_subscriber_ranking[0]['chatbot_usage_count'] + 1]);
                }

                if ($subscriberActivity->action == "Open_Resource_Materials") {
                    LbSubscriberRanking::where('subscriber_id', $lb_subscriber_ranking[0]['subscriber_id'])
                        ->update(['resource_material_accessed_count' => $lb_subscriber_ranking[0]['resource_material_accessed_count'] + 1]);
                }
            } else {
                $newRequest['subscriber_id'] = $subscriberActivity->user_id;
                $newRequest['level_id'] = 1;
                $newRequest['badge_id'] = 1;
                $newRequest['mins_spent_count'] = 0;
                $newRequest['chatbot_usage_count'] = 0;
                $newRequest['resource_material_accessed_count'] = 0;
                $newRequest['sub_module_usage_count'] = 1;
                $newRequest['App_opended_count'] = 0;
                $newRequest['total_task_count'] = 0;
                LbSubscriberRanking::create($newRequest);
            }
        } catch (Exception $e) {
            Log::error($e);
            // Log::channel('slack')->info("Catch error '\n" . $e);
            // Log::channel('slack')->info("subscriberActivity data---> '\n" . $subscriberActivity);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the SubscriberActivity "updated" event.
     *
     * @param  \App\Models\SubscriberActivity  $subscriberActivity
     * @return void
     */
    public function updated(SubscriberActivity $subscriberActivity)
    {
        //
    }

    /**
     * Handle the SubscriberActivity "deleted" event.
     *
     * @param  \App\Models\SubscriberActivity  $subscriberActivity
     * @return void
     */
    public function deleted(SubscriberActivity $subscriberActivity)
    {
        //
    }

    /**
     * Handle the SubscriberActivity "restored" event.
     *
     * @param  \App\Models\SubscriberActivity  $subscriberActivity
     * @return void
     */
    public function restored(SubscriberActivity $subscriberActivity)
    {
        //
    }

    /**
     * Handle the SubscriberActivity "force deleted" event.
     *
     * @param  \App\Models\SubscriberActivity  $subscriberActivity
     * @return void
     */
    public function forceDeleted(SubscriberActivity $subscriberActivity)
    {
        //
    }
}
