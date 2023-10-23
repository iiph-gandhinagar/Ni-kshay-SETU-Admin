<?php

namespace App\Observers;

use App\Models\ResourceMaterial;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use App\Http\Controllers\Admin\SendNotificationController;
use Config;

class ResourceMaterialObserver
{
    /**
     * Handle the ResourceMaterial "created" event.
     *
     * @param  \App\Models\ResourceMaterial  $resourceMaterial
     * @return void
     */
    public function created(ResourceMaterial $resourceMaterial)
    {
        // if($resourceMaterial->active == 1){
        $subscrier = Subscriber::whereRaw("find_in_set('" . $resourceMaterial->cadre_id . "',cadre_id)")
            ->whereRaw("find_in_set('" . $resourceMaterial->country_id . "',country_id)")
            ->whereRaw("find_in_set('" . $resourceMaterial->state_id . "',state_id)")->pluck('id');
        $notification['title'] = "New Resource Material Added";
        $notification['description'] = "$resourceMaterial->title Material Added";
        $material_parentid = ResourceMaterial::where('parent_id', $resourceMaterial->parent_id)->get(['title', 'type_of_materials'])[0];
        $device_id = UserDeviceToken::whereIn('user_id', $subscrier)->get('notification_token');
        SendNotificationController::resourceMaterial($notification, $device_id, Config::get('app.GENERAL.backend_url') . "/Materials/$resourceMaterial->parent_id/$material_parentid->type_of_materials/$material_parentid->title");
        // }
    }

    /**
     * Handle the ResourceMaterial "updated" event.
     *
     * @param  \App\Models\ResourceMaterial  $resourceMaterial
     * @return void
     */
    public function updated(ResourceMaterial $resourceMaterial)
    {
        // if($resourceMaterial->isDirty('active') && $resourceMaterial->active == 1){
        //     $subscrier = Subscriber::whereRaw("find_in_set('" . $resourceMaterial[0]['cadre_id'] . "',cadre_id)")
        //     ->whereRaw("find_in_set('" . $resourceMaterial[0]['country_id'] . "',country_id)")->get('id')->pluck('id');
        //     $notification['title'] = "New Resource Material Added";
        //     $notification['description'] = "$resourceMaterial->title Material Added";

        //     $device_id = UserDeviceToken::whereIn('user_id',$subscrier)->get('notification_token');
        //     return SendNotificationController::resourceMaterial($notification,$device_id,Config::get('app.GENERAL.backend_url')."/Materials/$resourceMaterial->id/$resourceMaterial->type_of_materials/'$resourceMaterial->title'");
        // }
    }

    /**
     * Handle the ResourceMaterial "deleted" event.
     *
     * @param  \App\Models\ResourceMaterial  $resourceMaterial
     * @return void
     */
    public function deleted(ResourceMaterial $resourceMaterial)
    {
        //
    }

    /**
     * Handle the ResourceMaterial "restored" event.
     *
     * @param  \App\Models\ResourceMaterial  $resourceMaterial
     * @return void
     */
    public function restored(ResourceMaterial $resourceMaterial)
    {
        //
    }

    /**
     * Handle the ResourceMaterial "force deleted" event.
     *
     * @param  \App\Models\ResourceMaterial  $resourceMaterial
     * @return void
     */
    public function forceDeleted(ResourceMaterial $resourceMaterial)
    {
        //
    }
}
