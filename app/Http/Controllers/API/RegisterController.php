<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Subscriber;
use App\Models\UserDeviceToken;
use App\Models\Otp;
use App\Models\TemporaryToken;
use Notification;
use App\Notifications\OtpNotification;
use Validator;
use Log;
use Hash;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceV2;
use App\Models\LbSubscriberRanking;
use App\Models\LbTaskList;
use Config;

class RegisterController extends BaseController
{
    public function store(Request $request)
    {

        $newRequest = $request->all();

        $rules = [
            'name' => 'required',
            'phone_no' => 'required|unique:subscribers',
            'password' => 'required',
            'cadre_type' => 'required',
            'cadre_id' => 'required',
            // 'block_id' => 'required',
            // 'state_id' => 'required',
            // 'district_id' => 'required',
            // 'health_facility_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $newRequest['api_token'] = Str::random(60);
            $newRequest['password'] =  Hash::make($request['password']);

            $subscriber = Subscriber::create($newRequest);

            $newRequest['subscriber_id'] = $subscriber->id;
            $newRequest['level_id'] = 1;
            $newRequest['badge_id'] = 1;
            $newRequest['mins_spent_count'] = 0;
            $newRequest['chatbot_usage_count'] = 0;
            $newRequest['resource_material_accessed_count'] = 0;
            $newRequest['sub_module_usage_count'] = 1;
            $newRequest['App_opended_count'] = 0;
            $newRequest['total_task_count'] = 0;
            LbSubscriberRanking::create($newRequest);

            $data['api_token'] =  $subscriber->api_token;
            $data['id'] =  $subscriber->id;
            $data['name'] = $subscriber->name;

            $success = true;
            return ['status' => $success, 'data' => $data, 'code' => 200];
        }
    }

    public function storeV2(Request $request)
    {

        $newRequest = $request->all();

        $rules = [
            'name' => 'required',
            'phone_no' => 'required|unique:subscribers',
            'password' => 'required',
            'cadre_type' => 'required',
            'cadre_id' => 'required',
            // 'block_id' => 'required',
            // 'state_id' => 'required',
            // 'district_id' => 'required',
            // 'health_facility_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $newRequest['api_token'] = Str::random(60);
            $newRequest['password'] =  Hash::make($request['password']);

            $subscriber = Subscriber::create($newRequest);
            if ($request->hasFile('profile_image') && $request->file('profile_image')[0]->isValid()) {
                $subscriber->addMediaFromRequest('profile_image')->toMediaCollection('profile_image', 's3');
                // $yourModel->addMedia($smallFile)->toMediaCollection('downloads', 's3');  
            }
            // $subscriber->getPath('thumb_100'); 
            $newRequest['subscriber_id'] = $subscriber->id;
            $newRequest['level_id'] = 1;
            $newRequest['badge_id'] = 1;
            $newRequest['mins_spent_count'] = 0;
            $newRequest['chatbot_usage_count'] = 0;
            $newRequest['resource_material_accessed_count'] = 0;
            $newRequest['sub_module_usage_count'] = 1;
            $newRequest['App_opended_count'] = 0;
            $newRequest['total_task_count'] = 0;
            LbSubscriberRanking::create($newRequest);

            $data['api_token'] =  $subscriber->api_token;
            $data['id'] =  $subscriber->id;
            $data['name'] = $subscriber->name;

            $success = true;
            return ['status' => $success, 'data' => $data, 'code' => 200];
        }
    }

    public function resetPassword(Request $request)
    {

        $request_data = $request->all();
        $data = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'phone_no', 'password']);

        $rules = [
            'phone_no' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ];
        $messages = [
            'new_password.required' => 'Please enter New password',
        ];
        $validator = Validator::make($request_data, $rules, $messages);

        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {

            if ($data[0]['phone_no'] == $request_data['phone_no'] && Hash::check($request_data['old_password'], $data[0]['password'])) { //$data[0]['password'] == $request['old_password']

                $subscriber_id = $data[0]['id'];
                if (isset($subscriber_id)) {
                    Subscriber::where('id', $subscriber_id)->update(['password' => Hash::make($request['new_password'])]);
                    $success = true;
                    return ['status' => $success, 'data' => 'Your Password is updated successfully.', 'code' => 200];
                } else {
                    $success = true;
                    return ['status' => $success, 'data' => 'User not Valid', 'code' => 200];
                }
            } else {
                $success = false;
                return ['status' => $success, 'data' => 'Your Contact No. is not valid or Your Old Password is not match', 'code' => 400];
            }
        }
    }

    public function login(Request $request)
    {

        $newRequest = $request->all();

        $rules = [
            'phone_no' => 'required',
            'password' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $getSubscriber = Subscriber::where('phone_no', $request['phone_no'])->get();
            if (count($getSubscriber) > 0) {
                $password = $getSubscriber[0]['password'];

                if ($getSubscriber[0]['phone_no'] == $newRequest['phone_no'] && Hash::check($request['password'], $password)) {
                    if ($getSubscriber[0]['is_verified'] == 1) {
                        $data['api_token'] = $getSubscriber[0]['api_token'];
                        $data['id'] = $getSubscriber[0]['id'];

                        $success = true;
                        return ['status' => $success, 'data' => $data, 'code' => 200];
                    } else {
                        $success = false;
                        $result['api_token'] = $getSubscriber[0]['api_token'];
                        $result['message'] = "Contact No. is Not Verified!,\nPlease verifiy Your Number Before Login";
                        return ['status' => $success, 'data' => $result, 'code' => 401];
                    }
                } else {
                    $success = false;
                    return ['status' => $success, 'data' => 'Invalid Password', 'code' => 400];
                }
            } else {
                $success = false;
                return ['status' => $success, 'data' => 'Invalid Contact no.', 'code' => 400];
            }
        }
    }

    public function storeUserDeviceToken(Request $request)
    {
        $newRequest = $request->all();

        $rules = [
            'device_id' => 'required',
            'notification_token' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
            if (UserDeviceToken::where('device_id', '=', $newRequest['device_id'])->exists()) {
                UserDeviceToken::where('device_id', $newRequest['device_id'])->update(['notification_token' => $newRequest['notification_token'], 'user_id' => $id[0]['id']]);
                $success = true;
                return ['status' => $success, 'data' => 'User Device Token Is Already Stored', 'code' => 200];
            } else {
                $newRequest['user_id'] = $id[0]['id'];
                $newRequest['is_active'] = 1;
                UserDeviceToken::create($newRequest);
                $success = true;
                return ['status' => $success, 'data' => 'User Device Token Store Successfully', 'code' => 200];
            }
        }
    }

    public function forgotPassword(Request $request)
    {

        $request_data = $request->all();

        $rules = [
            'phone_no' => 'required',
            // 'new_password' => 'required|same:new_password',
        ];
        // $messages = [
        //     'new_password.required' => 'Please enter password',
        // ];
        $validator = Validator::make($request_data, $rules);

        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $data = Subscriber::where('phone_no', $request_data['phone_no'])->get();
            if (count($data) == 0) {
                $success = false;
                return ['status' => $success, 'data' => 'Phone no. is not valid', 'code' => 400];
                // return $this->sendError('Error', 'Phone no is not valid', 400);
            } else {
                $subscriber_id = $data[0]['id'];
                //temporary token generate 
                $newRequest = $request->all();
                $newRequest['temp_token'] = Str::random(60);
                $newRequest['phone_no'] = $data[0]['phone_no'];
                $newRequest['user_id'] = $subscriber_id;
                $newRequest['name'] = $data[0]['name'];
                TemporaryToken::create($newRequest);

                //otp generate
                $six_digit_random_number = random_int(100000, 999999);
                $request_data = $request->all();
                $request_data['phone_no'] = $request_data['phone_no'];
                $request_data['user_id'] = $subscriber_id;
                $request_data['otp'] = $six_digit_random_number;
                $request_data['is_verified'] = 0;
                $request_data['message_body'] = 'Hello ' . $data[0]['name'] . ', Your Otp For Forgot Password in T.B. App is ' . $six_digit_random_number . '.';
                $request_data['via'] = "SMS";
                Otp::create($request_data);

                Notification::send($data, new OtpNotification($six_digit_random_number));

                $success = true;
                return ['status' => $success, 'data' => $newRequest['temp_token'], 'code' => 200];
            }
        }
    }

    public function getUser(Request $request)
    {

        $data = Subscriber::where('api_token', $request->bearerToken())
            ->with(['cadre', 'state', 'district', 'block', 'health_facility'])
            ->get();
        $success = true;
        return ['status' => $success, 'data' => UserResource::collection($data), 'code' => 200];
    }

    public function getUserV2(Request $request)
    {

        $data = Subscriber::where('api_token', $request->bearerToken())
            ->with(['cadre', 'state', 'district', 'block', 'health_facility', 'country'])
            ->get();
        $success = true;
        return ['status' => $success, 'data' => UserResourceV2::collection($data), 'code' => 200];
    }

    public function getUserV3(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        // if (Cache::has('db_config')) {
        //     $dbConfig = Cache::get('db_config');

        // } else {

        //     $dbConfig = AppConfig::all();
        //     Cache::put('db_config', $dbConfig, Config::get('app.GENERAL.app_config_cache_time_out'));
        // }
        $data = Subscriber::with(['media'])->where('api_token', $request->bearerToken())
            ->with(['cadre', 'state', 'district', 'block', 'health_facility', 'country'])
            ->get();
        $leaderboard = LbSubscriberRanking::with('lb_level')->where('subscriber_id', $data[0]['id'])->get();
        $lbTaskList = LbTaskList::sum('total_task');
        $data[0]['level'] = $leaderboard[0]['level_id'];
        $data[0]['level_title'] = isset($leaderboard) && $leaderboard[0]['level_id'] < 6 ? $leaderboard[0]['lb_level']['level'] : 'Exper Level';
        $data[0]['percentage'] = $leaderboard[0]['total_task_count'] * 100 / $lbTaskList;
        $success = true;
        return ['status' => $success, 'data' => UserResourceV2::collection($data), 'code' => 200];
    }

    public function verifyForgotPasswordOtp(Request $request)
    {
        $rules = [
            'otp' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
            'temp_token' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {

            $subscriber = TemporaryToken::where('temp_token', $request['temp_token'])->get();
            $otp = Otp::where('user_id', $subscriber[0]['user_id'])->where('is_verified', 0)->orderBy('created_at', 'DESC')->limit(1)->get(['otp', 'created_at']);
            if (isset($otp) && count($otp) == 0) {
                $success = false;
                return ['success' => $success, 'data' => 'Your OTP Details Not Found', 'code' => 400];
            } else {
                if ($otp[0]['otp'] == $request['otp']) {

                    $now = date("i", strtotime(date('Y-m-d H:i:s'))); // or your date as well
                    $your_date = date("i", strtotime(date($otp[0]['created_at'])));
                    $minDifference = $now - $your_date;

                    if ($minDifference > 5) {
                        $success = false;
                        return ['success' => $success, 'data' => 'Your OTP has been Expired', 'code' => 400];
                    } else {

                        Otp::where('user_id', $subscriber[0]['user_id'])->where('otp', $request['otp'])->update(['is_verified' => 1]);
                        Subscriber::where('id', $subscriber[0]['user_id'])->update(['password' => Hash::make($request['new_password'])]);
                        TemporaryToken::destroy($subscriber[0]['id']);
                        $success = true;
                        return ['success' => $success, 'data' => 'Your Mobile Number is Successfully Verified and Password Updated Successfully', 'code' => 200];
                    }
                } else {
                    $success = false;
                    return ['success' => $success, 'data' => 'Your Entered OTP is invalid', 'code' => 400];
                }
            }
        }
    }

    public function updateUserDetails(Request $request)
    {
        $newRequest = $request->all();
        $rules = [
            'cadre_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
            if (isset($user_id) && count($user_id) > 0) {
                Subscriber::where('id', $user_id[0]['id'])->update($newRequest);
                $success = true;
                return ['success' => $success, 'data' => 'Your Profile Data Updated!!', 'code' => 200];
            } else {
                $success = false;
                return ['success' => $success, 'data' => 'User Not Found', 'code' => 400];
            }
        }
    }

    public function updateUserDetailsV2(Request $request)
    {
        $newRequest = $request->all();
        unset($newRequest['profile_image']);

        $rules = [
            'cadre_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
            if (isset($user_id) && count($user_id) > 0) {
                Subscriber::where('id', $user_id[0]['id'])->update($newRequest);
                $user_detail = Subscriber::where('id', $user_id[0]['id'])->get()[0];
                if ($request->hasFile('profile_image') && $request->file('profile_image')[0]->isValid()) {
                    $user_detail->clearMediaCollection('profile_image', 's3');
                    $user_detail->addMediaFromRequest('profile_image')->toMediaCollection('profile_image', 's3');
                    // $user_detail->save();
                    // $yourModel->addMedia($smallFile)->toMediaCollection('downloads', 's3');
                }
                // $user_detail->getPath('thumb_100'); 
                $success = true;
                return ['success' => $success, 'data' => 'Your Profile Data Updated!!', 'code' => 200];
            } else {
                $success = false;
                return ['success' => $success, 'data' => 'User Not Found', 'code' => 400];
            }
        }
    }

    public function removeNotificationToken(Request $request)
    {
        $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id'])[0];
        // UserDeviceToken::where('user_id',$user_id->id)->where('device_id',$request['device_id'])->update(['notification_token' => ""]);
        UserDeviceToken::where('user_id', $user_id->id)->where('device_id', $request['device_id'])->delete();
        $success = true;
        return ['success' => $success, 'data' => 'Remove Notification Token', 'code' => 200];
    }
}
