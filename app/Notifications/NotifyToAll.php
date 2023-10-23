<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Log;
use Config;

class NotifyToAll
{

    public function send($notifiable, Notification $notification)
    {
        Log::info("inside send");
        $message = "";
        $dataToSend = $notification->toSms($notifiable);

        $Textlocal = new TextLocal(false, false, Config::get('app.GENERAL.sms_api_key_promotion'));
        // Log::info($dataToSend);
        // $sender = 'DGFLUX';
        $sender = 617548;
        $numbers = [];

        foreach ($dataToSend as $record) {
            // Log::info($record);
            array_push($numbers, intval('91' . $record['phone_no'])); //[intval('91' . $record['phone_no'])];
        }
        $message =  $dataToSend['notification_message'];
        Log::info("sending SMS to Following numbers" . $message);
        Log::info($numbers);
        $response = $Textlocal->sendSms($numbers, $message, $sender);
        return $notifiable->routeNotificationFor('sms');
    }
}
