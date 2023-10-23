<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\Otp;
use Notification;
use App\Notifications\OtpNotification;
use Validator;

class NotificationController extends BaseController
{
    public function sendRegistrationOtp(Request $request)
    {
        $six_digit_random_number = random_int(100000, 999999);
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->first();

        $newRequest = $request->all();
        $newRequest['phone_no'] = $subscriber['phone_no'];
        $newRequest['user_id'] = $subscriber['id'];
        $newRequest['otp'] = $six_digit_random_number;
        $newRequest['is_verified'] = 0;
        $newRequest['message_body'] = 'Hello ' . $subscriber['name'] . ', Your Otp For Registration in T.B. App is ' . $six_digit_random_number . '.';
        $newRequest['via'] = "SMS";
        Otp::create($newRequest);

        Notification::send($subscriber, new OtpNotification($six_digit_random_number));
        $success = true;
        return ['status' => $success, 'data' => $request, 'code' => 200];
    }
    public function verifyRegisterOtp(Request $request)
    {
        $rules = [
            'otp' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $subscriber = Subscriber::where('api_token', $request->bearerToken())->get();
            $otp = Otp::where('user_id', $subscriber[0]['id'])->where('is_verified', 0)->orderBy('created_at', 'DESC')->limit(1)->get(['otp', 'created_at']);
            if (isset($otp) && count($otp) == 0) {
                $success = false;
                return ['success' => $success, 'data' => 'Your OTP Details Not Found', 'code' => 400];
            } else {
                $now = date("i", strtotime(date('Y-m-d H:i:s'))); // or your date as well
                $your_date = date("i", strtotime(date($otp[0]['created_at'])));
                $minDifference = $now - $your_date;

                if ($minDifference > 5) {
                    $success = false;
                    return ['success' => $success, 'data' => 'Your OTP has been Expired', 'code' => 400];
                } else {
                    if ($otp[0]['otp'] == $request['otp']) {
                        Otp::where('user_id', $subscriber[0]['id'])->where('otp', $request['otp'])->update(['is_verified' => 1]);
                        Subscriber::where('id', $subscriber[0]['id'])->update(['is_verified' => 1]);
                        $success = true;
                        return ['success' => $success, 'data' => 'Your Mobile Number is Successfully Verified', 'code' => 200];
                    } else {
                        $success = false;
                        return ['success' => $success, 'data' => 'Your Entered OTP is invalid', 'code' => 400];
                    }
                }
            }
        }
    }

    public function sendForgotOtp($request)
    {
        $six_digit_random_number = random_int(100000, 999999);

        $newRequest['phone_no'] = $request->phone_no;
        $newRequest['user_id'] = $request->id;
        $newRequest['otp'] = $six_digit_random_number;
        $newRequest['is_verified'] = 0;
        $newRequest['message_body'] = 'Hello ' . $request->name . ', Your Otp For Registration in T.B. App is ' . $six_digit_random_number . '.';
        $newRequest['via'] = "SMS";
        Otp::create($newRequest);

        Notification::send($request, new OtpNotification($six_digit_random_number));
        $success = true;
        return ['status' => $success, 'data' => $request, 'code' => 200];
    }
}
