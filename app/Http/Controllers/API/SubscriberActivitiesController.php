<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\UserAppVersion;
use App\Models\SubscriberActivity;
use Validator;
use Jenssegers\Agent\Agent;

class SubscriberActivitiesController extends BaseController
{
    public function storeUserActivity(Request $request)
    {

        $agent = new Agent();


        $rules = [
            'action' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'name']);
            $ip_address = $this->getUserIp();
            $payload = json_encode($request->all());
            $header = $request->header('platform');
            if (isset($header) && $header != "") {
                $plateform = $request->header('platform');
            } else {
                if ($agent->isMobile()) {
                    $plateform = 'app';
                } elseif ($agent->isPhone() || $agent->is('iPhone')) {
                    $plateform = 'iPhone-app';
                } elseif ($agent->isDesktop()) {
                    $plateform = 'web';
                } else {
                    $plateform = 'mobile-app';
                }
            }
            if (strpos($request['action'], 'user_App_Version') !== false) {

                $find_record = UserAppVersion::where('user_id', $user_id[0]['id'])->get();
                // $user_app_version = explode("||", $request['action']);
                $version = explode("==", $request['action']);

                if (count($find_record) > 0) {
                    if ($plateform == "iPhone-app") {
                        UserAppVersion::where('user_id', $user_id[0]['id'])->update(['app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_ios' => 1]);
                    } elseif ($plateform == "mobile-app") {
                        UserAppVersion::where('user_id', $user_id[0]['id'])->update(['app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_android' => 1]);
                    } elseif ($plateform == "web") {
                        UserAppVersion::where('user_id', $user_id[0]['id'])->update(['app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_web' => 1]);
                    }
                } else {
                    if ($plateform == "iPhone-app") {
                        UserAppVersion::create(['user_id' => $user_id[0]['id'], 'user_name' => $user_id[0]['name'], 'app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_ios' => 1]);
                    } elseif ($plateform == "mobile-app") {
                        UserAppVersion::create(['user_id' => $user_id[0]['id'], 'user_name' => $user_id[0]['name'], 'app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_android' => 1]);
                    } elseif ($plateform == "web") {
                        UserAppVersion::create(['user_id' => $user_id[0]['id'], 'user_name' => $user_id[0]['name'], 'app_version' => trim($version[1]), 'current_plateform' => $plateform, 'has_web' => 1]);
                    }
                }
            }
            SubscriberActivity::create(['user_id' => $user_id[0]['id'], 'ip_address' => $ip_address, 'action' => $request['action'], 'plateform' => $plateform, 'payload' => $payload]);
        }
        $success = true;
        return ['status' => $success, 'data' => 'User Activity store successfully', 'code' => 200];
    }

    public function storeActivity($data)
    {
        $ip_address = $this->getUserIp();
        SubscriberActivity::create(['user_id' => $data['user_id'], 'ip_address' => $ip_address, 'action' => $data['action'], 'plateform' => $data['plateform'], 'payload' => $data['payload']]);
        return true;
    }

    public function getUserIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
