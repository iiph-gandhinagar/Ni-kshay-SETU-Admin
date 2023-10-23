<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\DiagnosesAlgorithm;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class DiagnosesAlgorithmObserver
{
    /**
     * Handle the DiagnosesAlgorithm "created" event.
     *
     * @param  \App\Models\DiagnosesAlgorithm  $diagnosesAlgorithm
     * @return void
     */
    public function created(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        try {
            if ($diagnosesAlgorithm->parent_id == 0 && $diagnosesAlgorithm->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$diagnosesAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$diagnosesAlgorithm['state_id']])->pluck('id');
                DiagnosesAlgorithm::where('id', $diagnosesAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$diagnosesAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_DIAGNOSIS_ALGORITHM/Diagnosis Algorithm/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DiagnosesAlgorithm "updated" event.
     *
     * @param  \App\Models\DiagnosesAlgorithm  $diagnosesAlgorithm
     * @return void
     */
    public function updated(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        try {
            if (($diagnosesAlgorithm->isDirty('parent_id') || $diagnosesAlgorithm->isDirty('activated')) && $diagnosesAlgorithm->parent_id == 0 && $diagnosesAlgorithm->activated == 1 && $diagnosesAlgorithm->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$diagnosesAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$diagnosesAlgorithm['state_id']])->pluck('id');
                DiagnosesAlgorithm::where('id', $diagnosesAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$diagnosesAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_DIAGNOSIS_ALGORITHM/Diagnosis Algorithm/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DiagnosesAlgorithm "deleted" event.
     *
     * @param  \App\Models\DiagnosesAlgorithm  $diagnosesAlgorithm
     * @return void
     */
    public function deleted(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        //
    }

    /**
     * Handle the DiagnosesAlgorithm "restored" event.
     *
     * @param  \App\Models\DiagnosesAlgorithm  $diagnosesAlgorithm
     * @return void
     */
    public function restored(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        //
    }

    /**
     * Handle the DiagnosesAlgorithm "force deleted" event.
     *
     * @param  \App\Models\DiagnosesAlgorithm  $diagnosesAlgorithm
     * @return void
     */
    public function forceDeleted(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        //
    }
}