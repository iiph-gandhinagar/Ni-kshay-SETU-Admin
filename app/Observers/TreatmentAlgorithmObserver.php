<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
use App\Models\UserDeviceToken;
use Exception;
use Config;
use Log;

class TreatmentAlgorithmObserver
{
    /**
     * Handle the TreatmentAlgorithm "created" event.
     *
     * @param  \App\Models\TreatmentAlgorithm  $treatmentAlgorithm
     * @return void
     */
    public function created(TreatmentAlgorithm $treatmentAlgorithm)
    {
        try {
            if ($treatmentAlgorithm->parent_id == 0 && $treatmentAlgorithm->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$treatmentAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$treatmentAlgorithm['state_id']])->pluck('id');
                TreatmentAlgorithm::where('id', $treatmentAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$treatmentAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_TREATMENT_ALGORITHM/Treatment Algorithm/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the TreatmentAlgorithm "updated" event.
     *
     * @param  \App\Models\TreatmentAlgorithm  $treatmentAlgorithm
     * @return void
     */
    public function updated(TreatmentAlgorithm $treatmentAlgorithm)
    {
        try {
            if (($treatmentAlgorithm->isDirty('parent_id') || $treatmentAlgorithm->isDirty('activated')) && $treatmentAlgorithm->parent_id == 0 && $treatmentAlgorithm->activated == 1 && $treatmentAlgorithm->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$treatmentAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$treatmentAlgorithm['state_id']])->pluck('id');
                TreatmentAlgorithm::where('id', $treatmentAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$treatmentAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_TREATMENT_ALGORITHM/Treatment Algorithm/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the TreatmentAlgorithm "deleted" event.
     *
     * @param  \App\Models\TreatmentAlgorithm  $treatmentAlgorithm
     * @return void
     */
    public function deleted(TreatmentAlgorithm $treatmentAlgorithm)
    {
        //
    }

    /**
     * Handle the TreatmentAlgorithm "restored" event.
     *
     * @param  \App\Models\TreatmentAlgorithm  $treatmentAlgorithm
     * @return void
     */
    public function restored(TreatmentAlgorithm $treatmentAlgorithm)
    {
        //
    }

    /**
     * Handle the TreatmentAlgorithm "force deleted" event.
     *
     * @param  \App\Models\TreatmentAlgorithm  $treatmentAlgorithm
     * @return void
     */
    public function forceDeleted(TreatmentAlgorithm $treatmentAlgorithm)
    {
        //
    }
}