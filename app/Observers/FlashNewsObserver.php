<?php

namespace App\Observers;

use App\Models\FlashNews;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use App\Http\Controllers\Admin\SendNotificationController;

class FlashNewsObserver
{
    /**
     * Handle the FlashNews "created" event.
     *
     * @param  \App\Models\FlashNews  $flashNews
     * @return void
     */
    public function created(FlashNews $flashNews)
    {
        if ($flashNews->active == 1) {
            $subscrier = Subscriber::pluck('id'); //whereRaw("find_in_set('" . $dynamicAlgoMaster->cadre_id . "',cadre_id)")->whereRaw("find_in_set('" . $dynamicAlgoMaster->country_id . "',country_id)")->
            $notification['title'] = "New News Article Added";
            $notification['description'] = "$flashNews->title News Article Added";

            $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
            SendNotificationController::sendNotification($notification, $device_id);
        }
    }

    /**
     * Handle the FlashNews "updated" event.
     *
     * @param  \App\Models\FlashNews  $flashNews
     * @return void
     */
    public function updated(FlashNews $flashNews)
    {
        if ($flashNews->isDirty('active') && $flashNews->active == 1) {
            $subscrier = Subscriber::pluck('id'); //whereRaw("find_in_set('" . $dynamicAlgoMaster->cadre_id . "',cadre_id)")->whereRaw("find_in_set('" . $dynamicAlgoMaster->country_id . "',country_id)")->
            $notification['title'] = "New News Article Added";
            $notification['description'] = "$flashNews->title News Article Added";

            $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
            SendNotificationController::sendNotification($notification, $device_id);
        }
    }

    /**
     * Handle the FlashNews "deleted" event.
     *
     * @param  \App\Models\FlashNews  $flashNews
     * @return void
     */
    public function deleted(FlashNews $flashNews)
    {
        //
    }

    /**
     * Handle the FlashNews "restored" event.
     *
     * @param  \App\Models\FlashNews  $flashNews
     * @return void
     */
    public function restored(FlashNews $flashNews)
    {
        //
    }

    /**
     * Handle the FlashNews "force deleted" event.
     *
     * @param  \App\Models\FlashNews  $flashNews
     * @return void
     */
    public function forceDeleted(FlashNews $flashNews)
    {
        //
    }
}