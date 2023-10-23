<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\SubscriberActivity;
use App\Models\UserAppVersion;
use Log;
use DB;
use Illuminate\Http\Request;

class UserAppVersionController extends BaseController
{
    public function scriptToUpdatePlatform(Request $request)
    {
        $users = UserAppVersion::get();
        foreach ($users as $val) {
            $current_plateform = SubscriberActivity::where('user_id', $val->id)->limit(1)->orderby('created_at', 'desc')->get(['plateform']);
            $others_plateform = SubscriberActivity::where('user_id', $val->id)->groupby('user_id')->get(['user_id', DB::raw("GROUP_CONCAT(DISTINCT(plateform)) as plateform")]);

            if (count($current_plateform) > 0) {
                UserAppVersion::where('user_id', $val->id)->update(['current_plateform' => $current_plateform[0]->plateform]);
                if (str_contains($others_plateform, "iPhone-app")) {
                    UserAppVersion::where('user_id', $val->id)->update(['has_ios' => 1]);
                }
                if (str_contains($others_plateform, "mobile-app")) {
                    UserAppVersion::where('user_id', $val->id)->update(['has_android' => 1]);
                }
                if (str_contains($others_plateform, "web")) {
                    UserAppVersion::where('user_id', $val->id)->update(['has_web' => 1]);
                }
            }
        }
        return "data updated successfully";
    }
}
