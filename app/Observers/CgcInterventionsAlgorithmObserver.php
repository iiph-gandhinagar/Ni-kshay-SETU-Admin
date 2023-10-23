<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class CgcInterventionsAlgorithmObserver
{
    /**
     * Handle the CgcInterventionsAlgorithm "created" event.
     *
     * @param  \App\Models\CgcInterventionsAlgorithm  $cgcInterventionsAlgorithm
     * @return void
     */
    public function created(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        try {
            if ($cgcInterventionsAlgorithm->parent_id == 0 && $cgcInterventionsAlgorithm->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$cgcInterventionsAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$cgcInterventionsAlgorithm['state_id']])->pluck('id');
                CgcInterventionsAlgorithm::where('id', $cgcInterventionsAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$cgcInterventionsAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/Algorithms/TITLE_CGC_INTERVENTION/NTEP");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the CgcInterventionsAlgorithm "updated" event.
     *
     * @param  \App\Models\CgcInterventionsAlgorithm  $cgcInterventionsAlgorithm
     * @return void
     */
    public function updated(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        try {
            if (($cgcInterventionsAlgorithm->isDirty('parent_id') || $cgcInterventionsAlgorithm->isDirty('activated')) && $cgcInterventionsAlgorithm->parent_id == 0 && $cgcInterventionsAlgorithm->activated == 1 && $cgcInterventionsAlgorithm->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$cgcInterventionsAlgorithm['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$cgcInterventionsAlgorithm['state_id']])->pluck('id');
                CgcInterventionsAlgorithm::where('id', $cgcInterventionsAlgorithm->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$cgcInterventionsAlgorithm->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::sendNotification($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/Algorithms/TITLE_CGC_INTERVENTION/NTEP");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the CgcInterventionsAlgorithm "deleted" event.
     *
     * @param  \App\Models\CgcInterventionsAlgorithm  $cgcInterventionsAlgorithm
     * @return void
     */
    public function deleted(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        //
    }

    /**
     * Handle the CgcInterventionsAlgorithm "restored" event.
     *
     * @param  \App\Models\CgcInterventionsAlgorithm  $cgcInterventionsAlgorithm
     * @return void
     */
    public function restored(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        //
    }

    /**
     * Handle the CgcInterventionsAlgorithm "force deleted" event.
     *
     * @param  \App\Models\CgcInterventionsAlgorithm  $cgcInterventionsAlgorithm
     * @return void
     */
    public function forceDeleted(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        //
    }
}