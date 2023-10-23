<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\DifferentialCareAlgorithm;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class DifferentialCareAlgorithmObserver
{
    /**
     * Handle the DifferentialCareAlgorithm "created" event.
     *
     * @param  \App\Models\DifferentialCareAlgorithm  $differentialCareAlgorithm
     * @return void
     */
    public function created(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        try {
            if ($differentialCareAlgorithm->parent_id == 0 && $differentialCareAlgorithm->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$differentialCareAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$differentialCareAlgorithm['state_id']])->pluck('id');
                DifferentialCareAlgorithm::where('id', $differentialCareAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$differentialCareAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_DIFFERENTIANTED_CARE/Differentiated Care Of TB Patients/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DifferentialCareAlgorithm "updated" event.
     *
     * @param  \App\Models\DifferentialCareAlgorithm  $differentialCareAlgorithm
     * @return void
     */
    public function updated(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        try {
            if (($differentialCareAlgorithm->isDirty('parent_id') || $differentialCareAlgorithm->isDirty('activated')) && $differentialCareAlgorithm->parent_id == 0 && $differentialCareAlgorithm->activated == 1 && $differentialCareAlgorithm->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$differentialCareAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$differentialCareAlgorithm['state_id']])->pluck('id');
                DifferentialCareAlgorithm::where('id', $differentialCareAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$differentialCareAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_DIFFERENTIANTED_CARE/Differentiated Care Of TB Patients/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DifferentialCareAlgorithm "deleted" event.
     *
     * @param  \App\Models\DifferentialCareAlgorithm  $differentialCareAlgorithm
     * @return void
     */
    public function deleted(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        //
    }

    /**
     * Handle the DifferentialCareAlgorithm "restored" event.
     *
     * @param  \App\Models\DifferentialCareAlgorithm  $differentialCareAlgorithm
     * @return void
     */
    public function restored(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        //
    }

    /**
     * Handle the DifferentialCareAlgorithm "force deleted" event.
     *
     * @param  \App\Models\DifferentialCareAlgorithm  $differentialCareAlgorithm
     * @return void
     */
    public function forceDeleted(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        //
    }
}