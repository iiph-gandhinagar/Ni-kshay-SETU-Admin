<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\CaseDefinition;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class CaseDefinitionObserver
{
    /**
     * Handle the CaseDefinition "created" event.
     *
     * @param  \App\Models\CaseDefinition  $caseDefinition
     * @return void
     */
    public function created(CaseDefinition $caseDefinition)
    {
        try {
            if ($caseDefinition->parent_id == 0 && $caseDefinition->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$caseDefinition['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$caseDefinition['state_id']])->pluck('id');
                CaseDefinition::where('id', $caseDefinition->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$caseDefinition->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_CASE_DEFINITION/Case Definition/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the CaseDefinition "updated" event.
     *
     * @param  \App\Models\CaseDefinition  $caseDefinition
     * @return void
     */
    public function updated(CaseDefinition $caseDefinition)
    {
        try {
            if (($caseDefinition->isDirty('parent_id') || $caseDefinition->isDirty('activated')) && $caseDefinition->parent_id == 0 && $caseDefinition->activated == 1 && $caseDefinition->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$caseDefinition['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$caseDefinition['state_id']])->pluck('id');
                CaseDefinition::where('id', $caseDefinition->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$caseDefinition->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::sendNotification($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_CASE_DEFINITION/Case Definition/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the CaseDefinition "deleted" event.
     *
     * @param  \App\Models\CaseDefinition  $caseDefinition
     * @return void
     */
    public function deleted(CaseDefinition $caseDefinition)
    {
        //
    }

    /**
     * Handle the CaseDefinition "restored" event.
     *
     * @param  \App\Models\CaseDefinition  $caseDefinition
     * @return void
     */
    public function restored(CaseDefinition $caseDefinition)
    {
        //
    }

    /**
     * Handle the CaseDefinition "force deleted" event.
     *
     * @param  \App\Models\CaseDefinition  $caseDefinition
     * @return void
     */
    public function forceDeleted(CaseDefinition $caseDefinition)
    {
        //
    }
}