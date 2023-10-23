<?php

namespace App\Observers;

use App\Models\DynamicAlgoMaster;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use App\Http\Controllers\Admin\SendNotificationController;
use Exception;
use Log;
use Config;

class DynamicAlgoMasterObserver
{
    /**
     * Handle the DynamicAlgoMaster "created" event.
     *
     * @param  \App\Models\DynamicAlgoMaster  $dynamicAlgoMaster
     * @return void
     */
    public function created(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        try {
            if ($dynamicAlgoMaster->active == 1) {
                $subscrier = Subscriber::pluck('id'); //whereRaw("find_in_set('" . $dynamicAlgoMaster->cadre_id . "',cadre_id)")->whereRaw("find_in_set('" . $dynamicAlgoMaster->country_id . "',country_id)")->
                DynamicAlgoMaster::where('id', $dynamicAlgoMaster->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$dynamicAlgoMaster->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
                SendNotificationController::sendNotification($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/$dynamicAlgoMaster->name/Dynamic/$dynamicAlgoMaster->id");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DynamicAlgoMaster "updated" event.
     *
     * @param  \App\Models\DynamicAlgoMaster  $dynamicAlgoMaster
     * @return void
     */
    public function updated(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        try {
            if ($dynamicAlgoMaster->isDirty('active') && $dynamicAlgoMaster->active == 1 && $dynamicAlgoMaster->send_initial_notification == 0) {
                $subscrier = Subscriber::pluck('id'); //whereRaw("find_in_set('" . $dynamicAlgoMaster->cadre_id . "',cadre_id)")->whereRaw("find_in_set('" . $dynamicAlgoMaster->country_id . "',country_id)")->
                DynamicAlgoMaster::where('id', $dynamicAlgoMaster->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$dynamicAlgoMaster->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
                SendNotificationController::sendNotification($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/$dynamicAlgoMaster->name/Dynamic/$dynamicAlgoMaster->id");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the DynamicAlgoMaster "deleted" event.
     *
     * @param  \App\Models\DynamicAlgoMaster  $dynamicAlgoMaster
     * @return void
     */
    public function deleted(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        //
    }

    /**
     * Handle the DynamicAlgoMaster "restored" event.
     *
     * @param  \App\Models\DynamicAlgoMaster  $dynamicAlgoMaster
     * @return void
     */
    public function restored(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        //
    }

    /**
     * Handle the DynamicAlgoMaster "force deleted" event.
     *
     * @param  \App\Models\DynamicAlgoMaster  $dynamicAlgoMaster
     * @return void
     */
    public function forceDeleted(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        //
    }
}
