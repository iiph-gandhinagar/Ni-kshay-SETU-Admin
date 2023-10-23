<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\Subscriber;
use App\Models\SurveyMaster;
use App\Models\UserDeviceToken;
use Exception;
use Log;


class SurveyMasterObserver
{
    /**
     * Handle the SurveyMaster "created" event.
     *
     * @param  \App\Models\SurveyMaster  $surveyMaster
     * @return void
     */
    public function created(SurveyMaster $surveyMaster)
    {
        try {
            if ($surveyMaster->active == 1) {
                $subscrier = Subscriber::whereRaw("find_in_set('" . $surveyMaster->cadre_id . "',cadre_id)")
                    ->whereRaw("find_in_set('" . $surveyMaster->state_id . "',state_id)")
                    ->orWhereRaw("find_in_set(country_id, ?)", [$surveyMaster->country_id])
                    ->orWhereRaw("find_in_set(district_id, ?)", [$surveyMaster->district_id])
                    ->pluck('id');
                SurveyMaster::where('id', $surveyMaster->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Survey Added";
                $notification['description'] = "$surveyMaster->title Survey Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
                SendNotificationController::surveyForms($notification, $device_id);
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the SurveyMaster "updated" event.
     *
     * @param  \App\Models\SurveyMaster  $surveyMaster
     * @return void
     */
    public function updated(SurveyMaster $surveyMaster)
    {
        try {
            if ($surveyMaster->isDirty('active') && $surveyMaster->active == 1 && $surveyMaster->send_initial_notification == 0) {
                $subscrier = Subscriber::whereRaw("find_in_set('" . $surveyMaster->cadre_id . "',cadre_id)")
                    ->whereRaw("find_in_set('" . $surveyMaster->state_id . "',state_id)")
                    ->orWhereRaw("find_in_set(country_id, ?)", [$surveyMaster->country_id])
                    ->orWhereRaw("find_in_set(district_id, ?)", [$surveyMaster->district_id])
                    ->pluck('id');
                SurveyMaster::where('id', $surveyMaster->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Survey Added";
                $notification['description'] = "$surveyMaster->title Survey Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
                SendNotificationController::surveyForms($notification, $device_id);
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in Survey Update processing observer");
        }
    }

    /**
     * Handle the SurveyMaster "deleted" event.
     *
     * @param  \App\Models\SurveyMaster  $surveyMaster
     * @return void
     */
    public function deleted(SurveyMaster $surveyMaster)
    {
        //
    }

    /**
     * Handle the SurveyMaster "restored" event.
     *
     * @param  \App\Models\SurveyMaster  $surveyMaster
     * @return void
     */
    public function restored(SurveyMaster $surveyMaster)
    {
        //
    }

    /**
     * Handle the SurveyMaster "force deleted" event.
     *
     * @param  \App\Models\SurveyMaster  $surveyMaster
     * @return void
     */
    public function forceDeleted(SurveyMaster $surveyMaster)
    {
        //
    }
}