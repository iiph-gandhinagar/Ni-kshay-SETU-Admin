<?php

namespace App\Observers;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\LatentTbInfection;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use Exception;
use Log;
use Config;

class LatentTbInfectionObserver
{
    /**
     * Handle the LatentTbInfection "created" event.
     *
     * @param  \App\Models\LatentTbInfection  $latentTbInfection
     * @return void
     */
    public function created(LatentTbInfection $latentTbInfection)
    {
        try {
            if ($latentTbInfection->parent_id == 0 && $latentTbInfection->activated == 1) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$latentTbInfection['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$latentTbInfection['state_id']])->pluck('id');
                LatentTbInfection::where('id', $latentTbInfection->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$latentTbInfection->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_LATENT_TB_INFECTION/Latent TB Infection/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the LatentTbInfection "updated" event.
     *
     * @param  \App\Models\LatentTbInfection  $latentTbInfection
     * @return void
     */
    public function updated(LatentTbInfection $latentTbInfection)
    {
        try {
            if (($latentTbInfection->isDirty('parent_id') || $latentTbInfection->isDirty('activated')) && $latentTbInfection->parent_id == 0 && $latentTbInfection->activated == 1 && $latentTbInfection->send_initial_notification == 0) {
                $subscribers = Subscriber::whereRaw("find_in_set(cadre_id, ?)", [$latentTbInfection['cadre_id']])
                    ->orWhereRaw("find_in_set(state_id, ?)", [$latentTbInfection['state_id']])->pluck('id');
                LatentTbInfection::where('id', $latentTbInfection->id)->update(['send_initial_notification' => 1]);
                $notification['title'] = "New Module Added";
                $notification['description'] = "$latentTbInfection->title Module Added";

                $device_id = UserDeviceToken::whereIn('user_id', $subscribers)->get('notification_token');
                SendNotificationController::newModules($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/AlgorithmList/TITLE_LATENT_TB_INFECTION/Latent TB Infection/null");
            }
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing observer");
        }
    }

    /**
     * Handle the LatentTbInfection "deleted" event.
     *
     * @param  \App\Models\LatentTbInfection  $latentTbInfection
     * @return void
     */
    public function deleted(LatentTbInfection $latentTbInfection)
    {
        //
    }

    /**
     * Handle the LatentTbInfection "restored" event.
     *
     * @param  \App\Models\LatentTbInfection  $latentTbInfection
     * @return void
     */
    public function restored(LatentTbInfection $latentTbInfection)
    {
        //
    }

    /**
     * Handle the LatentTbInfection "force deleted" event.
     *
     * @param  \App\Models\LatentTbInfection  $latentTbInfection
     * @return void
     */
    public function forceDeleted(LatentTbInfection $latentTbInfection)
    {
        //
    }
}