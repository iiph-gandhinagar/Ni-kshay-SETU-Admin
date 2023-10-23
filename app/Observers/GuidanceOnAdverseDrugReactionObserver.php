<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class GuidanceOnAdverseDrugReactionObserver
{
    /**
     * Handle the GuidanceOnAdverseDrugReaction "created" event.
     *
     * @param  \App\Models\GuidanceOnAdverseDrugReaction  $guidanceOnAdverseDrugReaction
     * @return void
     */
    public function created(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        try {
            if ($guidanceOnAdverseDrugReaction->parent_id == 0 && $guidanceOnAdverseDrugReaction->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$guidanceOnAdverseDrugReaction['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$guidanceOnAdverseDrugReaction['state_id']])->pluck('id');
                GuidanceOnAdverseDrugReaction::where('id', $guidanceOnAdverseDrugReaction->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$guidanceOnAdverseDrugReaction->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_GUIDANCE_ON_ADR/Guidance on ADR/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the GuidanceOnAdverseDrugReaction "updated" event.
     *
     * @param  \App\Models\GuidanceOnAdverseDrugReaction  $guidanceOnAdverseDrugReaction
     * @return void
     */
    public function updated(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        try {
            if (($guidanceOnAdverseDrugReaction->isDirty('parent_id') || $guidanceOnAdverseDrugReaction->isDirty('activated')) && $guidanceOnAdverseDrugReaction->parent_id == 0 && $guidanceOnAdverseDrugReaction->activated == 1 && $guidanceOnAdverseDrugReaction->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$guidanceOnAdverseDrugReaction['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$guidanceOnAdverseDrugReaction['state_id']])->pluck('id');
                GuidanceOnAdverseDrugReaction::where('id', $guidanceOnAdverseDrugReaction->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$guidanceOnAdverseDrugReaction->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_GUIDANCE_ON_ADR/Guidance on ADR/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the GuidanceOnAdverseDrugReaction "deleted" event.
     *
     * @param  \App\Models\GuidanceOnAdverseDrugReaction  $guidanceOnAdverseDrugReaction
     * @return void
     */
    public function deleted(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        //
    }

    /**
     * Handle the GuidanceOnAdverseDrugReaction "restored" event.
     *
     * @param  \App\Models\GuidanceOnAdverseDrugReaction  $guidanceOnAdverseDrugReaction
     * @return void
     */
    public function restored(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        //
    }

    /**
     * Handle the GuidanceOnAdverseDrugReaction "force deleted" event.
     *
     * @param  \App\Models\GuidanceOnAdverseDrugReaction  $guidanceOnAdverseDrugReaction
     * @return void
     */
    public function forceDeleted(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        //
    }
}