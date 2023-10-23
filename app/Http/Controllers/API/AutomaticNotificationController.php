<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\AutomaticNotification;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Helpers\RequestHelpers;

class AutomaticNotificationController extends BaseController
{
    public function getNotification(Request $request){
        $paginationParams = RequestHelpers::getPaginationParams($request);
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get()[0];
    
        $all_notification = AutomaticNotification::whereRaw("find_in_set('" . $subscriber->id . "',subscriber_id)")
                                        ->orderBy("created_at","desc")
                                        ->paginate($paginationParams['per_page']);
    
       $success = true;
       return ['status' => $success, 'data' => $all_notification, 'code' => 200];
    }
}
